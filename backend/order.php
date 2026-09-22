<?php
require 'db.php';
ensure_session();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
$user_id = (int)$_SESSION['user_id'];

function fetch_item_names(mysqli $conn, int $orderId): array
{
    $stmt = $conn->prepare('SELECT m.name FROM order_items oi JOIN menu m ON oi.menu_id = m.id WHERE oi.order_id = ?');
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row['name'];
    }
    return $items;
}

switch ($action) {
    case 'report_by_date':
        $date = sanitize_text($_GET['date'] ?? '');
        if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            handle_json_error('Missing or invalid date parameter');
        }

        $orders = [];
        $total_sales = 0.0;
        $item_sales = [];
        $order_count = 0;

        $stmt = $conn->prepare('SELECT o.id, u.username, o.total_price, o.date_created FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE DATE(o.date_created) = ? AND o.status = "paid"');
        $stmt->bind_param('s', $date);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
            $total_sales += (float)$row['total_price'];
            $order_count++;
        }

        $stmt2 = $conn->prepare('SELECT m.name, SUM(oi.qty) AS total_qty, SUM(oi.qty * oi.price) AS total_price FROM order_items oi JOIN menu m ON oi.menu_id = m.id JOIN orders o ON oi.order_id = o.id WHERE DATE(o.date_created) = ? AND o.status = "paid" GROUP BY oi.menu_id, m.name ORDER BY total_qty DESC');
        $stmt2->bind_param('s', $date);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        while ($row2 = $result2->fetch_assoc()) {
            $item_sales[] = $row2;
        }

        echo json_encode([
            'orders' => $orders,
            'total_sales' => $total_sales,
            'items' => $item_sales,
            'order_count' => $order_count,
        ]);
        exit();

    case 'list':
        $result = $conn->query('SELECT o.*, u.username, u.role FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.date_created DESC');
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $row['items'] = fetch_item_names($conn, (int)$row['id']);
            $data[] = $row;
        }
        echo json_encode($data);
        break;

    case 'add':
        $rawItems = $_POST['items'] ?? '[]';
        $items = json_decode((string)$rawItems, true);
        $total_price = (float)($_POST['total_price'] ?? 0);
        $discount_amount = (float)($_POST['discount_amount'] ?? 0);
        $note = sanitize_text($_POST['note'] ?? '');

        if (!is_array($items) || $items === []) {
            handle_json_error('No items were provided for this order');
        }

        $subtotal = $total_price + $discount_amount;
        $stmt = $conn->prepare('INSERT INTO orders (user_id, total_price, discount_amount, subtotal, note, status) VALUES (?, ?, ?, ?, ?, "paid")');
        $stmt->bind_param('iddds', $user_id, $total_price, $discount_amount, $subtotal, $note);

        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'Order insert failed: ' . $stmt->error]);
            exit();
        }

        $order_id = $conn->insert_id;

        foreach ($items as $item) {
            $menu_id = (int)($item['menu_id'] ?? 0);
            $qty = max(0, (int)($item['qty'] ?? 0));
            $price = (float)($item['price'] ?? 0);
            $discount = (float)($item['discount'] ?? 0);

            if ($menu_id <= 0 || $qty <= 0 || $price < 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid item data in order']);
                exit();
            }

            $itemStmt = $conn->prepare('INSERT INTO order_items (order_id, menu_id, qty, price, discount) VALUES (?, ?, ?, ?, ?)');
            $itemStmt->bind_param('iiidd', $order_id, $menu_id, $qty, $price, $discount);
            if (!$itemStmt->execute()) {
                echo json_encode(['success' => false, 'message' => 'Order item insert failed: ' . $itemStmt->error]);
                exit();
            }

            $updateStmt = $conn->prepare('UPDATE menu SET qty = qty - ? WHERE id = ?');
            $updateStmt->bind_param('ii', $qty, $menu_id);
            if (!$updateStmt->execute()) {
                echo json_encode(['success' => false, 'message' => 'Menu update failed: ' . $updateStmt->error]);
                exit();
            }
        }

        echo json_encode(['success' => true, 'order_id' => $order_id]);
        break;

    case 'paid_today':
        $today = date('Y-m-d');
        $stmt = $conn->prepare('SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE DATE(o.date_created) = ? AND o.status = "paid" ORDER BY o.date_created DESC');
        $stmt->bind_param('s', $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $row['items'] = fetch_item_names($conn, (int)$row['id']);
            $data[] = $row;
        }
        echo json_encode($data);
        break;

    case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid order id']);
            exit();
        }

        $conn->begin_transaction();
        try {
            $deleteItems = $conn->prepare('DELETE FROM order_items WHERE order_id = ?');
            $deleteItems->bind_param('i', $id);
            $deleteItems->execute();

            $deleteOrder = $conn->prepare('DELETE FROM orders WHERE id = ?');
            $deleteOrder->bind_param('i', $id);
            $deleteOrder->execute();
            $conn->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        break;

    case 'get':
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['error' => 'Invalid order id']);
            exit();
        }

        $stmt = $conn->prepare('SELECT o.*, u.username, u.role FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        if (!$order) {
            echo json_encode(['error' => 'Order not found']);
            exit();
        }

        $itemsStmt = $conn->prepare('SELECT oi.menu_id, m.name, oi.qty, oi.price, oi.discount FROM order_items oi JOIN menu m ON oi.menu_id = m.id WHERE oi.order_id = ?');
        $itemsStmt->bind_param('i', $id);
        $itemsStmt->execute();
        $itemsResult = $itemsStmt->get_result();
        $items = [];
        while ($itemRow = $itemsResult->fetch_assoc()) {
            $itemRow['qty'] = (int)$itemRow['qty'];
            $itemRow['price'] = (float)$itemRow['price'];
            $itemRow['discount'] = isset($itemRow['discount']) ? (float)$itemRow['discount'] : 0.0;
            $items[] = $itemRow;
        }

        $order['items'] = $items;
        $order['subtotal'] = isset($order['subtotal']) ? (float)$order['subtotal'] : null;
        $order['discount_amount'] = isset($order['discount_amount']) ? (float)$order['discount_amount'] : null;
        echo json_encode($order);
        break;

    case 'report_today':
        $today = date('Y-m-d');
        $orders = [];
        $total_sales = 0.0;
        $stmt = $conn->prepare('SELECT o.id, u.username, o.total_price, o.date_created, o.status FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE DATE(o.date_created) = ? AND o.status = "paid" ORDER BY o.date_created DESC');
        $stmt->bind_param('s', $today);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
            $total_sales += (float)$row['total_price'];
        }
        echo json_encode(['orders' => $orders, 'total_sales' => $total_sales]);
        exit();

    default:
        echo json_encode(['error' => 'Invalid action']);
}

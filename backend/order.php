<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
header('Content-Type: application/json');
$action = $_GET['action'] ?? '';
$user_id = $_SESSION['user_id'];

switch ($action) {
    case 'list':
        $result = $conn->query("SELECT o.*, u.username, u.role FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.date_created DESC");
        $data = [];
        while ($row = $result->fetch_assoc()) {
            // Fetch item names for this order
            $order_id = $row['id'];
            $itemsRes = $conn->query("SELECT m.name FROM order_items oi JOIN menu m ON oi.menu_id = m.id WHERE oi.order_id = $order_id");
            $itemNames = [];
            while ($itemRow = $itemsRes->fetch_assoc()) {
                $itemNames[] = $itemRow['name'];
            }
            $row['items'] = $itemNames;
            $data[] = $row;
        }
        echo json_encode($data);
        break;
    case 'add':
        $items = json_decode($_POST['items'], true);
        $total_price = (float)$_POST['total_price'];
        $discount_amount = isset($_POST['discount_amount']) ? (float)$_POST['discount_amount'] : 0;
        $note = isset($_POST['note']) ? $conn->real_escape_string($_POST['note']) : '';
        $subtotal = $total_price + $discount_amount;
        $order_sql = "INSERT INTO orders (user_id, total_price, discount_amount, subtotal, note, status) VALUES ($user_id, $total_price, $discount_amount, $subtotal, '$note', 'paid')";
        if (!$conn->query($order_sql)) {
            echo json_encode(['success' => false, 'message' => 'Order insert failed: ' . $conn->error]);
            exit();
        }
        $order_id = $conn->insert_id;
        foreach ($items as $item) {
            $menu_id = (int)$item['menu_id'];
            $qty = (int)$item['qty'];
            $price = (float)$item['price'];
            $discount = isset($item['discount']) ? (float)$item['discount'] : 0;
            $item_sql = "INSERT INTO order_items (order_id, menu_id, qty, price, discount) VALUES ($order_id, $menu_id, $qty, $price, $discount)";
            if (!$conn->query($item_sql)) {
                echo json_encode(['success' => false, 'message' => 'Order item insert failed: ' . $conn->error]);
                exit();
            }
            $update_sql = "UPDATE menu SET qty = qty - $qty WHERE id = $menu_id";
            if (!$conn->query($update_sql)) {
                echo json_encode(['success' => false, 'message' => 'Menu update failed: ' . $conn->error]);
                exit();
            }
        }
        echo json_encode(['success' => true, 'order_id' => $order_id]);
        break;
    case 'paid_today':
        $today = date('Y-m-d');
        $result = $conn->query("SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE DATE(o.date_created) = '$today' AND o.status='paid' ORDER BY o.date_created DESC");
        $data = [];
        while ($row = $result->fetch_assoc()) {
            // Fetch item names for this order
            $order_id = $row['id'];
            $itemsRes = $conn->query("SELECT m.name FROM order_items oi JOIN menu m ON oi.menu_id = m.id WHERE oi.order_id = $order_id");
            $itemNames = [];
            while ($itemRow = $itemsRes->fetch_assoc()) {
                $itemNames[] = $itemRow['name'];
            }
            $row['items'] = $itemNames;
            $data[] = $row;
        }
        echo json_encode($data);
        break;
    case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $conn->query("DELETE FROM order_items WHERE order_id=$id");
            $conn->query("DELETE FROM orders WHERE id=$id");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid order id']);
        }
        break;
    case 'get':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $orderRes = $conn->query("SELECT o.*, u.username, u.role FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = $id");
            $order = $orderRes->fetch_assoc();
            // Fetch detailed items for this order
            $itemsRes = $conn->query("SELECT oi.menu_id, m.name, oi.qty, oi.price, oi.discount FROM order_items oi JOIN menu m ON oi.menu_id = m.id WHERE oi.order_id = $id");
            $items = [];
            while ($itemRow = $itemsRes->fetch_assoc()) {
                $itemRow['qty'] = (int)$itemRow['qty'];
                $itemRow['price'] = (float)$itemRow['price'];
                $itemRow['discount'] = isset($itemRow['discount']) ? (float)$itemRow['discount'] : 0;
                $items[] = $itemRow;
            }
            $order['items'] = $items;
            $order['subtotal'] = isset($order['subtotal']) ? (float)$order['subtotal'] : null;
            $order['discount_amount'] = isset($order['discount_amount']) ? (float)$order['discount_amount'] : null;
            echo json_encode($order);
        } else {
            echo json_encode(['error' => 'Invalid order id']);
        }
        break;
    // Add print/receipt logic as needed
    default:
        echo json_encode(['error' => 'Invalid action']);
}

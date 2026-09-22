<?php
require 'db.php';
ensure_session();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo 'Unauthorized';
    exit();
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($order_id <= 0) {
    echo 'Invalid order ID';
    exit();
}

$orderStmt = $conn->prepare('SELECT * FROM orders WHERE id = ?');
$orderStmt->bind_param('i', $order_id);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();
if (!$order) {
    echo 'Order not found';
    exit();
}

$user_name = '';
if (isset($order['user_id'])) {
    $userStmt = $conn->prepare('SELECT username, role FROM users WHERE id = ?');
    $userStmt->bind_param('i', $order['user_id']);
    $userStmt->execute();
    $userRow = $userStmt->get_result()->fetch_assoc();
    if ($userRow) {
        $user_name = $userRow['username'] . ' (' . $userRow['role'] . ')';
    }
}

$itemsStmt = $conn->prepare('SELECT oi.*, m.name FROM order_items oi JOIN menu m ON oi.menu_id = m.id WHERE oi.order_id = ?');
$itemsStmt->bind_param('i', $order_id);
$itemsStmt->execute();
$items = $itemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo htmlspecialchars((string)$order_id); ?></title>
    <style>
        body {
            font-family: monospace;
        }

        .receipt {
            max-width: 320px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 16px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 4px;
            border-bottom: 1px solid #eee;
        }

        .total {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <h2>Restaurant Receipt</h2>
        <p>Order #: <?php echo htmlspecialchars((string)$order_id); ?><br>Date: <?php echo htmlspecialchars((string)$order['date_created']); ?></p>
        <p>Ordered by: <strong><?php echo htmlspecialchars($user_name); ?></strong></p>
        <table>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
            </tr>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars((string)$item['name']); ?></td>
                    <td><?php echo htmlspecialchars((string)$item['qty']); ?></td>
                    <td><?php echo number_format((float)$item['price'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total">
                <td colspan="2">Total</td>
                <td><?php echo number_format((float)$order['total_price'], 2); ?></td>
            </tr>
        </table>
    </div>
    <script>
        window.print();
    </script>
</body>

</html>
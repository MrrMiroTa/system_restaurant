<?php
require 'db.php';
session_start();
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
$res = $conn->query("SELECT * FROM orders WHERE id = $order_id");
$order = $res->fetch_assoc();
// Get user/admin name
$user_name = '';
if ($order && isset($order['user_id'])) {
    $user_res = $conn->query("SELECT username, role FROM users WHERE id = " . (int)$order['user_id']);
    if ($user_res && $user_row = $user_res->fetch_assoc()) {
        $user_name = $user_row['username'] . ' (' . $user_row['role'] . ')';
    }
}
$res_items = $conn->query("SELECT oi.*, m.name FROM order_items oi JOIN menu m ON oi.menu_id = m.id WHERE oi.order_id = $order_id");
$items = [];
while ($row = $res_items->fetch_assoc()) {
    $items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo $order_id; ?></title>
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
        <p>Order #: <?php echo $order_id; ?><br>Date: <?php echo $order['date_created']; ?></p>
        <p>Ordered by: <strong><?php echo htmlspecialchars($user_name); ?></strong></p>
        <table>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
            </tr>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $item['qty']; ?></td>
                    <td><?php echo number_format($item['price'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total">
                <td colspan="2">Total</td>
                <td><?php echo number_format($order['total_price'], 2); ?></td>
            </tr>
        </table>
    </div>
    <script>
        window.print();
    </script>
</body>

</html>
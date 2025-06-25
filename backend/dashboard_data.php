<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
header('Content-Type: application/json');

// Total sales today
$today = date('Y-m-d');
$res = $conn->query("SELECT SUM(total_price) as total, COUNT(*) as qty FROM orders WHERE DATE(date_created) = '$today' AND status='paid'");
$row = $res->fetch_assoc();
$sales_today = $row['total'] ?? 0;
$qty_today = $row['qty'] ?? 0;

// Total sales this month
$month = date('Y-m');
$res = $conn->query("SELECT SUM(total_price) as total, COUNT(*) as qty FROM orders WHERE DATE_FORMAT(date_created, '%Y-%m') = '$month' AND status='paid'");
$row = $res->fetch_assoc();
$sales_month = $row['total'] ?? 0;
$qty_month = $row['qty'] ?? 0;

// Top ordered menu
$res = $conn->query("SELECT m.name, SUM(oi.qty) as total_qty FROM order_items oi JOIN menu m ON oi.menu_id = m.id GROUP BY oi.menu_id ORDER BY total_qty DESC LIMIT 5");
$top_menu = [];
while ($row = $res->fetch_assoc()) {
    $top_menu[] = $row;
}

// Low stock alert
$res = $conn->query("SELECT * FROM stock WHERE qty < 5");
$low_stock = [];
while ($row = $res->fetch_assoc()) {
    $low_stock[] = $row;
}

// Most ordered today
$res = $conn->query("SELECT m.name, SUM(oi.qty) as total_qty FROM order_items oi JOIN menu m ON oi.menu_id = m.id JOIN orders o ON oi.order_id = o.id WHERE DATE(o.date_created) = '$today' AND o.status='paid' GROUP BY oi.menu_id ORDER BY total_qty DESC LIMIT 1");
$most_ordered_today = $res->fetch_assoc();

// Debug: log most ordered today to error log
if ($most_ordered_today) {
    error_log('Most Ordered Today: ' . print_r($most_ordered_today, true));
}

if (isset($_GET['action']) && $_GET['action'] === 'sales_chart') {
    $days = 7;
    $data = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $res = $conn->query("SELECT SUM(total_price) as total FROM orders WHERE DATE(date_created) = '$date' AND status='paid'");
        $row = $res->fetch_assoc();
        $data[] = [
            'date' => $date,
            'total' => $row['total'] ? round($row['total'], 2) : 0
        ];
    }
    echo json_encode($data);
    exit();
}

echo json_encode([
    'sales_today' => $sales_today,
    'qty_today' => $qty_today,
    'sales_month' => $sales_month,
    'qty_month' => $qty_month,
    'top_menu' => $top_menu,
    'low_stock' => $low_stock,
    'most_ordered_today' => $most_ordered_today
]);

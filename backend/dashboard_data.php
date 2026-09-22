<?php
require 'db.php';
ensure_session();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');
$today = date('Y-m-d');
$month = date('Y-m');

$summaryStmt = $conn->prepare('SELECT SUM(total_price) AS total, COUNT(*) AS qty FROM orders WHERE DATE(date_created) = ? AND status = "paid"');
$summaryStmt->bind_param('s', $today);
$summaryStmt->execute();
$row = $summaryStmt->get_result()->fetch_assoc();
$sales_today = (float)($row['total'] ?? 0);
$qty_today = (int)($row['qty'] ?? 0);

$monthStmt = $conn->prepare('SELECT SUM(total_price) AS total, COUNT(*) AS qty FROM orders WHERE DATE_FORMAT(date_created, "%Y-%m") = ? AND status = "paid"');
$monthStmt->bind_param('s', $month);
$monthStmt->execute();
$row = $monthStmt->get_result()->fetch_assoc();
$sales_month = (float)($row['total'] ?? 0);
$qty_month = (int)($row['qty'] ?? 0);

$topMenuStmt = $conn->prepare('SELECT m.name, SUM(oi.qty) AS total_qty FROM order_items oi JOIN menu m ON oi.menu_id = m.id GROUP BY oi.menu_id, m.name ORDER BY total_qty DESC LIMIT 5');
$topMenuStmt->execute();
$top_menu = [];
foreach ($topMenuStmt->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
    $top_menu[] = $row;
}

$lowStockStmt = $conn->prepare('SELECT * FROM stock WHERE qty < 5');
$lowStockStmt->execute();
$low_stock = $lowStockStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$mostOrderedStmt = $conn->prepare('SELECT m.id, m.name, SUM(oi.qty) AS total_qty FROM order_items oi JOIN menu m ON oi.menu_id = m.id JOIN orders o ON oi.order_id = o.id WHERE DATE(o.date_created) = ? AND o.status = "paid" GROUP BY oi.menu_id, m.name, m.id ORDER BY total_qty DESC');
$mostOrderedStmt->bind_param('s', $today);
$mostOrderedStmt->execute();
$most_ordered_today = [];
foreach ($mostOrderedStmt->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
    $row['total_qty'] = (int)$row['total_qty'];
    $most_ordered_today[] = $row;
}

if (isset($_GET['action']) && $_GET['action'] === 'sales_chart') {
    $days = 7;
    $data = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $chartStmt = $conn->prepare('SELECT SUM(total_price) AS total FROM orders WHERE DATE(date_created) = ? AND status = "paid"');
        $chartStmt->bind_param('s', $date);
        $chartStmt->execute();
        $row = $chartStmt->get_result()->fetch_assoc();
        $data[] = [
            'date' => $date,
            'total' => $row['total'] ? round((float)$row['total'], 2) : 0,
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
    'most_ordered_today' => $most_ordered_today,
]);

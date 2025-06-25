<?php
// stock_in_out.php
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

function log_stock_history($conn, $stock_id, $user_id, $type, $qty)
{
    // Get price at the time of event
    $price = 0;
    $res = $conn->query("SELECT price FROM stock WHERE id = $stock_id");
    if ($row = $res->fetch_assoc()) {
        $price = $row['price'];
    }
    $stmt = $conn->prepare("INSERT INTO stock_history (stock_id, user_id, type, qty, price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('iisid', $stock_id, $user_id, $type, $qty, $price);
    $stmt->execute();
}

switch ($action) {
    case 'in':
        $stock_id = (int)$_POST['stock_id'];
        $qty = (int)$_POST['qty'];
        if ($qty <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid quantity']);
            exit();
        }
        $sql = "UPDATE stock SET qty = qty + $qty WHERE id = $stock_id";
        if ($conn->query($sql)) {
            log_stock_history($conn, $stock_id, $user_id, 'in', $qty);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        break;
    case 'out':
        $stock_id = (int)$_POST['stock_id'];
        $qty = (int)$_POST['qty'];
        if ($qty <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid quantity']);
            exit();
        }
        // Prevent negative stock
        $result = $conn->query("SELECT qty FROM stock WHERE id = $stock_id");
        $row = $result->fetch_assoc();
        if (!$row || $row['qty'] < $qty) {
            echo json_encode(['success' => false, 'error' => 'Not enough stock']);
            exit();
        }
        $sql = "UPDATE stock SET qty = qty - $qty WHERE id = $stock_id";
        if ($conn->query($sql)) {
            log_stock_history($conn, $stock_id, $user_id, 'out', $qty);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        break;
    case 'history':
        $stock_id = intval($_GET['stock_id'] ?? 0);
        $res = $conn->query("SELECT h.*, u.username FROM stock_history h JOIN users u ON h.user_id = u.id WHERE h.stock_id = $stock_id ORDER BY h.date DESC");
        $history = [];
        while ($row = $res->fetch_assoc()) {
            $history[] = $row;
        }
        echo json_encode($history);
        exit();
    default:
        echo json_encode(['error' => 'Invalid action']);
}

<?php
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
        $result = $conn->query("SELECT m.*, u.username as created_by_name FROM menu m LEFT JOIN users u ON m.created_by = u.id ORDER BY m.date_created DESC");
        $data = [];
        while ($row = $result->fetch_assoc()) {
            // fallback: if no username, try to get by created_by
            if (!$row['created_by_name'] && $row['created_by']) {
                $uid = (int)$row['created_by'];
                $ures = $conn->query("SELECT username FROM users WHERE id = $uid LIMIT 1");
                if ($ures && $urow = $ures->fetch_assoc()) {
                    $row['created_by_name'] = $urow['username'];
                }
            }
            $data[] = $row;
        }
        echo json_encode($data);
        break;
    case 'add':
        $name = $conn->real_escape_string($_POST['name']);
        $category = $conn->real_escape_string($_POST['category']);
        $description = $conn->real_escape_string($_POST['description']);
        $qty = (int)$_POST['qty'];
        $price = (float)$_POST['price'];
        $picture = '';
        if (!empty($_FILES['picture']['name'])) {
            $target = '../uploads/' . basename($_FILES['picture']['name']);
            if (move_uploaded_file($_FILES['picture']['tmp_name'], $target)) {
                $picture = $target;
            }
        }
        $sql = "INSERT INTO menu (name, picture, category, description, qty, price, created_by) VALUES ('$name', '$picture', '$category', '$description', $qty, $price, $user_id)";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        break;
    case 'categories':
        $result = $conn->query("SELECT DISTINCT category FROM menu WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['category'];
        }
        echo json_encode($categories);
        break;
    case 'update':
        $id = (int)$_GET['id'];
        $data = json_decode(file_get_contents('php://input'), true);
        $fields = [];
        if (isset($data['name'])) $fields[] = "name='" . $conn->real_escape_string($data['name']) . "'";
        if (isset($data['category'])) $fields[] = "category='" . $conn->real_escape_string($data['category']) . "'";
        if (isset($data['description'])) $fields[] = "description='" . $conn->real_escape_string($data['description']) . "'";
        if (isset($data['qty'])) $fields[] = "qty=" . (int)$data['qty'];
        if (isset($data['price'])) $fields[] = "price=" . (float)$data['price'];
        if (!count($fields)) {
            echo json_encode(['success' => false, 'error' => 'No fields to update']);
            exit();
        }
        $sql = "UPDATE menu SET " . implode(',', $fields) . " WHERE id=$id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        break;
    case 'delete':
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid ID']);
            exit();
        }
        // Try to delete, catch foreign key errors
        $sql = "DELETE FROM menu WHERE id=$id";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true]);
        } else {
            // Check for foreign key constraint error (MySQL error code 1451)
            if (strpos($conn->error, 'foreign key constraint') !== false || strpos($conn->error, 'a foreign key constraint fails') !== false || $conn->errno == 1451) {
                echo json_encode(['success' => false, 'error' => 'Cannot delete: This menu item is used in orders and cannot be deleted.']);
            } else {
                echo json_encode(['success' => false, 'error' => $conn->error]);
            }
        }
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

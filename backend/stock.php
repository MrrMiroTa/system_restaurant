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
        $result = $conn->query("SELECT * FROM stock ORDER BY date_created DESC");
        $data = [];
        while ($row = $result->fetch_assoc()) {
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
        $sql = "INSERT INTO stock (name, picture, category, description, qty, price, created_by) VALUES ('$name', '$picture', '$category', '$description', $qty, $price, $user_id)";
        if ($conn->query($sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        break;
    // Add update/delete as needed
    default:
        echo json_encode(['error' => 'Invalid action']);
}

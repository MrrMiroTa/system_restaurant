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

switch ($action) {
    case 'list':
        $result = $conn->query('SELECT * FROM stock ORDER BY date_created DESC');
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
        break;

    case 'add':
        $name = sanitize_text($_POST['name'] ?? '');
        $category = sanitize_text($_POST['category'] ?? '');
        $description = sanitize_text($_POST['description'] ?? '');
        $qty = max(0, (int)($_POST['qty'] ?? 0));
        $price = (float)($_POST['price'] ?? 0);

        if ($name === '' || $price < 0) {
            handle_json_error('Invalid stock item data');
        }

        $picture = '';
        if (!empty($_FILES['picture']['name'])) {
            $picture = handle_upload($_FILES['picture'], '../uploads');
            if ($picture === '') {
                handle_json_error('Invalid image upload');
            }
        }

        $stmt = $conn->prepare('INSERT INTO stock (name, picture, category, description, qty, price, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssidi', $name, $picture, $category, $description, $qty, $price, $user_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}

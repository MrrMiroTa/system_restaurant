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
        $result = $conn->query('SELECT m.*, u.username as created_by_name FROM menu m LEFT JOIN users u ON m.created_by = u.id ORDER BY m.date_created DESC');
        $data = [];
        while ($row = $result->fetch_assoc()) {
            if (!$row['created_by_name'] && !empty($row['created_by'])) {
                $uid = (int)$row['created_by'];
                $userStmt = $conn->prepare('SELECT username FROM users WHERE id = ? LIMIT 1');
                $userStmt->bind_param('i', $uid);
                $userStmt->execute();
                $userRow = $userStmt->get_result()->fetch_assoc();
                $row['created_by_name'] = $userRow['username'] ?? null;
            }
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
            handle_json_error('Invalid menu item data');
        }

        $picture = '';
        if (!empty($_FILES['picture']['name'])) {
            $picture = handle_upload($_FILES['picture'], '../uploads');
            if ($picture === '') {
                handle_json_error('Invalid image upload');
            }
        }

        $stmt = $conn->prepare('INSERT INTO menu (name, picture, category, description, qty, price, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssidi', $name, $picture, $category, $description, $qty, $price, $user_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        break;

    case 'categories':
        $result = $conn->query('SELECT DISTINCT category FROM menu WHERE category IS NOT NULL AND category != "" ORDER BY category ASC');
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['category'];
        }
        echo json_encode($categories);
        break;

    case 'update':
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            handle_json_error('Invalid ID');
        }

        $data = json_decode((string)file_get_contents('php://input'), true);
        if (!is_array($data) || $data === []) {
            handle_json_error('No fields to update');
        }

        $fields = [];
        $params = [];
        $types = '';

        if (array_key_exists('name', $data)) {
            $fields[] = 'name = ?';
            $params[] = sanitize_text((string)$data['name']);
            $types .= 's';
        }
        if (array_key_exists('category', $data)) {
            $fields[] = 'category = ?';
            $params[] = sanitize_text((string)$data['category']);
            $types .= 's';
        }
        if (array_key_exists('description', $data)) {
            $fields[] = 'description = ?';
            $params[] = sanitize_text((string)$data['description']);
            $types .= 's';
        }
        if (array_key_exists('qty', $data)) {
            $fields[] = 'qty = ?';
            $params[] = max(0, (int)$data['qty']);
            $types .= 'i';
        }
        if (array_key_exists('price', $data)) {
            $fields[] = 'price = ?';
            $params[] = (float)$data['price'];
            $types .= 'd';
        }

        if ($fields === []) {
            handle_json_error('No fields to update');
        }

        $sql = 'UPDATE menu SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $conn->prepare($sql);
        $params[] = $id;
        $types .= 'i';
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        break;

    case 'delete':
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($id <= 0) {
            handle_json_error('Invalid ID');
        }

        $stmt = $conn->prepare('DELETE FROM menu WHERE id = ?');
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            $error = $stmt->error;
            if (stripos($error, 'foreign key constraint') !== false || $conn->errno == 1451) {
                echo json_encode(['success' => false, 'error' => 'Cannot delete: This menu item is used in orders and cannot be deleted.']);
            } else {
                echo json_encode(['success' => false, 'error' => $error]);
            }
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}

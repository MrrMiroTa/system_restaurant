<?php
require 'db.php';
ensure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    safe_redirect('../frontend/index.php');
}

$login = sanitize_text($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($login === '' || $password === '') {
    safe_redirect('../frontend/index.php?login=failed');
}

$stmt = $conn->prepare('SELECT id, username, password, role FROM users WHERE username = ? OR email = ? LIMIT 1');
$stmt->bind_param('ss', $login, $login);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    safe_redirect('../frontend/index.php?login=failed');
}

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['role'] = $user['role'];
$_SESSION['username'] = $user['username'];

if (!empty($_POST['remember_me'])) {
    $token = bin2hex(random_bytes(32));
    setcookie('rememberme', $token, [
        'expires' => time() + (86400 * 30),
        'path' => '/',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    $updateStmt = $conn->prepare('UPDATE users SET remember_token = ? WHERE id = ?');
    $updateStmt->bind_param('si', $token, $user['id']);
    $updateStmt->execute();
}

if ($user['role'] === 'admin') {
    safe_redirect('../frontend/dashboard.php');
}

safe_redirect('../frontend/customer_menu.php');

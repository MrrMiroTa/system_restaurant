<?php
require_once '../backend/db.php';
ensure_session();

if (!isset($_SESSION['user_id']) && isset($_COOKIE['rememberme'])) {
    $token = sanitize_text($_COOKIE['rememberme']);
    $stmt = $conn->prepare('SELECT id, role, username FROM users WHERE remember_token = ? LIMIT 1');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row) {
        $_SESSION['user_id'] = (int)$row['id'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['username'] = $row['username'];
        setcookie('rememberme', $token, [
            'expires' => time() + (86400 * 30),
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}

<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    safe_redirect('../frontend/index.php');
}

$username = sanitize_text($_POST['username'] ?? '');
$email = sanitize_text($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (strlen($username) < 3 || strlen($username) > 50 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
    safe_redirect('../frontend/index.php?register=failed');
}

$stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
$stmt->bind_param('ss', $username, $email);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    safe_redirect('../frontend/index.php?register=exists');
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$insertStmt = $conn->prepare('INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, "user")');
$insertStmt->bind_param('sss', $username, $hashedPassword, $email);
$insertStmt->execute();

safe_redirect('../frontend/index.php?register=success');

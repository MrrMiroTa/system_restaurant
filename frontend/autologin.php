<?php
session_start();
require_once '../backend/db.php';
// Auto-login with remember me cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['rememberme'])) {
    $token = $_COOKIE['rememberme'];
    $res = $conn->query("SELECT * FROM users WHERE remember_token='" . $conn->real_escape_string($token) . "'");
    if ($res && $row = $res->fetch_assoc()) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
        // Optionally refresh cookie expiry
        setcookie('rememberme', $token, time() + (86400 * 30), "/", "", false, true);
    }
}
// ...existing code for login page redirect or content...

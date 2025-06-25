<?php
require 'db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $conn->real_escape_string($_POST['username']); // can be username or email
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE username='$login' OR email='$login'";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $row['username'];
            // Remember Me implementation
            if (!empty($_POST['remember_me'])) {
                $token = bin2hex(random_bytes(32));
                setcookie('rememberme', $token, time() + (86400 * 30), "/", "", false, true); // 30 days, httpOnly
                // Store token in DB
                $conn->query("UPDATE users SET remember_token='$token' WHERE id=" . $row['id']);
            }
            if ($row['role'] === 'admin') {
                header('Location: ../frontend/dashboard.php');
            } else {
                header('Location: ../frontend/customer_menu.php');
            }
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
}

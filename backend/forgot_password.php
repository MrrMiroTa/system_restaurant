<?php
// backend/forgot_password.php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $res = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($res && $user = $res->fetch_assoc()) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $conn->query("UPDATE users SET remember_token='$token' WHERE id=" . $user['id']);
        // In production, send email. For demo, show link.
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/reset_password.php?token=$token";
        echo "<div style='padding:20px;background:#fff;border-radius:8px;max-width:400px;margin:40px auto;text-align:center;'>";
        echo "Password reset link (valid 1 hour):<br><a href='$reset_link'>$reset_link</a>";
        echo "<br><br><a href='../frontend/index.php'>Back to Login</a></div>";
        exit();
    } else {
        echo "<div style='padding:20px;background:#fff;border-radius:8px;max-width:400px;margin:40px auto;text-align:center;color:red;'>Email not found.<br><a href='../frontend/forgot_password.php'>Try again</a></div>";
        exit();
    }
}
header('Location: ../frontend/forgot_password.php');
exit();

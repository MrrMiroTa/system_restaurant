<?php
// frontend/reset_password.php
require_once '../backend/db.php';
$token = $_GET['token'] ?? '';
if (!$token) {
    echo '<div style="padding:20px;background:#fff;border-radius:8px;max-width:400px;margin:40px auto;text-align:center;color:red;">Invalid or missing token.</div>';
    exit();
}
$res = $conn->query("SELECT * FROM users WHERE remember_token='" . $conn->real_escape_string($token) . "'");
if (!$res || !$user = $res->fetch_assoc()) {
    echo '<div style="padding:20px;background:#fff;border-radius:8px;max-width:400px;margin:40px auto;text-align:center;color:red;">Invalid or expired token.</div>';
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $newpass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $conn->query("UPDATE users SET password='$newpass', remember_token=NULL WHERE id=" . $user['id']);
    echo '<div style="padding:20px;background:#fff;border-radius:8px;max-width:400px;margin:40px auto;text-align:center;color:green;">Password reset successful!<br><a href=\'index.php\'>Login</a></div>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h2>Reset Password</h2>
        <form method="POST">
            <input type="password" name="password" placeholder="New password" required><br>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>

</html>
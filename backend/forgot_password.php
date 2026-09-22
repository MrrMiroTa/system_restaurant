<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    safe_redirect('../frontend/forgot_password.php');
}

$email = sanitize_text($_POST['email'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    safe_redirect('../frontend/forgot_password.php?error=invalid_email');
}

$stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    safe_redirect('../frontend/forgot_password.php?error=not_found');
}

$token = bin2hex(random_bytes(32));
$updateStmt = $conn->prepare('UPDATE users SET remember_token = ? WHERE id = ?');
$updateStmt->bind_param('si', $token, $user['id']);
$updateStmt->execute();

$scriptRoot = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$reset_link = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $scriptRoot . '/../frontend/reset_password.php?token=' . urlencode($token);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Link</title>
    <link rel="stylesheet" href="../frontend/style.css">
</head>
<body>
    <div class="container" style="max-width:500px; margin:60px auto; padding:24px; border-radius:12px; background:#fff; box-shadow:0 10px 25px rgba(0,0,0,0.08);">
        <h2>Password reset link</h2>
        <p>Use the link below to reset your password.</p>
        <p><a href="<?= htmlspecialchars($reset_link) ?>"><?= htmlspecialchars($reset_link) ?></a></p>
        <p><a href="../frontend/index.php">Back to login</a></p>
    </div>
</body>
</html>

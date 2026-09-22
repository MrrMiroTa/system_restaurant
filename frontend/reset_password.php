<?php
require_once '../backend/db.php';

$token = sanitize_text($_GET['token'] ?? '');
if ($token === '') {
    echo '<div style="padding:20px;background:#fff;border-radius:8px;max-width:400px;margin:40px auto;text-align:center;color:red;">Invalid or missing token.</div>';
    exit();
}

$stmt = $conn->prepare('SELECT id, username FROM users WHERE remember_token = ? LIMIT 1');
$stmt->bind_param('s', $token);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo '<div style="padding:20px;background:#fff;border-radius:8px;max-width:400px;margin:40px auto;text-align:center;color:red;">Invalid or expired token.</div>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'] ?? '';
    if (strlen($newPassword) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } else {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $conn->prepare('UPDATE users SET password = ?, remember_token = NULL WHERE id = ?');
        $updateStmt->bind_param('si', $hashed, $user['id']);
        $updateStmt->execute();
        echo '<div style="padding:20px;background:#fff;border-radius:8px;max-width:400px;margin:40px auto;text-align:center;color:green;">Password reset successful!<br><a href="index.php">Login</a></div>';
        exit();
    }
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
        <?php if (!empty($error)): ?>
            <div class="error-message" style="color:red; margin-bottom:12px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="New password" required minlength="8"><br>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
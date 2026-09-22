<?php
require_once '../backend/db.php';
ensure_session();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['role'] !== 'admin') {
    header('Location: customer_menu.php');
    exit();
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $username = sanitize_text($_POST['username'] ?? '');
    $email = sanitize_text($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = in_array($_POST['role'] ?? '', ['admin', 'user'], true) ? $_POST['role'] : 'user';
    $created_by = (int)$_SESSION['user_id'];

    if (strlen($username) < 3 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $msg = 'Please provide valid username, email, and a password with at least 8 characters.';
    } else {
        $existsStmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
        $existsStmt->bind_param('ss', $username, $email);
        $existsStmt->execute();
        if ($existsStmt->get_result()->fetch_assoc()) {
            $msg = 'Username or email already exists.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $conn->prepare('INSERT INTO users (username, password, email, role, created_by) VALUES (?, ?, ?, ?, ?)');
            $insertStmt->bind_param('ssssi', $username, $passwordHash, $email, $role, $created_by);
            $msg = $insertStmt->execute() ? ucfirst($role) . ' created successfully.' : 'Error: ' . $insertStmt->error;
        }
    }
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id != $_SESSION['user_id']) {
        $deleteStmt = $conn->prepare('DELETE FROM users WHERE id = ?');
        $deleteStmt->bind_param('i', $id);
        $deleteStmt->execute();
    }
}

$result = $conn->query('SELECT u.id, u.username, u.email, u.role, u.created_at, a.username AS creator FROM users u LEFT JOIN users a ON u.created_by = a.id');
$users = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="icon" href="./image/U.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <?php include 'navmenu.php'; ?>
    <div class="main-content">
        <h1>User Management</h1>
        <?php if (!empty($msg)) echo '<div class="success-message">' . htmlspecialchars($msg) . '</div>'; ?>
        <button id="show-create-form-btn" class="add-btn" style="margin-bottom:16px;display:inline-block;">+ Create User/Admin</button>
        <form id="create-user-form" method="POST" style="display:none;max-width:400px;margin:0 auto 24px auto;background:#f9f9f9;border-radius:8px;box-shadow:0 2px 8px #eee;padding:16px;flex-direction:column;gap:10px;">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="email" name="email" placeholder="Email" required>
            <select name="role" required style="padding:8px;border-radius:4px;border:1px solid #ccc;">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" name="create_user">Create</button>
            <button type="button" id="hide-create-form-btn" style="margin-top:8px;background:#ccc;color:#222;border:none;border-radius:4px;padding:6px 0;">Cancel</button>
        </form>
        <h2>All Users</h2>
        <table style="width:100%;border-collapse:collapse;background:#fff;border-radius:8px;box-shadow:0 2px 8px #eee;overflow:hidden;">
            <thead style="background:#f5f5f5;">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Created By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= $user['role'] ?></td>
                        <td><?= $user['created_at'] ?></td>
                        <td><?= $user['creator'] ? htmlspecialchars($user['creator']) : '-' ?></td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="user_management.php?delete=<?= $user['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
                            <?php else: ?>
                                (You)
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script>
        const showBtn = document.getElementById('show-create-form-btn');
        const form = document.getElementById('create-user-form');
        const hideBtn = document.getElementById('hide-create-form-btn');
        if (showBtn && form && hideBtn) {
            showBtn.onclick = function() {
                form.style.display = 'flex';
                showBtn.style.display = 'none';
            };
            hideBtn.onclick = function() {
                form.style.display = 'none';
                showBtn.style.display = 'inline-block';
            };
        }
        // Refresh and hide form after successful create
        if (window.location.search.includes('created=1')) {
            form.style.display = 'none';
            showBtn.style.display = 'inline-block';
        }
        // Intercept form submit to refresh page and hide form
        if (form) {
            form.onsubmit = function() {
                setTimeout(function() {
                    window.location.href = window.location.pathname + '?created=1';
                }, 100);
            };
        }
    </script>
</body>

</html>
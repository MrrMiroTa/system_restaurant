<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="./image/U.png">

    <title>Admin Login - Restaurant System</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if (isset($_GET['register']) && $_GET['register'] === 'success'): ?>
            <div class="success-message">Signup successful! Please login.</div>
        <?php endif; ?>
        <?php if (isset($_GET['login']) && $_GET['login'] === 'success'): ?>
            <div class="success-message">Login successful!</div>
        <?php endif; ?>
        <form action="../backend/login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>

</html>
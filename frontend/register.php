<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register - Restaurant System</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="register-container">
        <h2>Admin Register</h2>
        <?php if (isset($_GET['register']) && $_GET['register'] === 'success'): ?>
            <div class="success-message">Signup successful! Please login.</div>
        <?php endif; ?>
        <form action="../backend/register.php" method="POST">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>

</html>
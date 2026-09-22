<?php
// forgot_password.php (frontend)
// Simple form for user to request password reset
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="icon" href="./image/U.png">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form action="../backend/forgot_password.php" method="POST">
            <input type="email" name="email" placeholder="Enter your email" required><br>
            <button type="submit">Send Reset Link</button>
        </form>
        <p><a href="index.php">Back to Login</a></p>
    </div>
</body>

</html>

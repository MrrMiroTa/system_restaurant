<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration - Restaurant System</title>
    <link rel="icon" href="./image/U.png">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url("./image/background.png") no-repeat center center fixed;
            background-size: cover;
        }

        .container {
            padding: 40px;
            border-radius: 20px;
            border: 8px solid #223243;
            box-shadow: -5px -5px 15px rgba(255, 255, 255, 0.1),
                5px 5px 15px rgba(0, 0, 0, 0.35),
                inset -5px -5px 15px rgba(255, 255, 255, 0.1),
                inset 5px 5px 15px rgba(0, 0, 0, 0.35);
            background: #223243;
        }

        .container .form {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 25px;
        }

        .container .form.signin,
        .container.signinForm .form.signup {
            display: none;
        }

        .container.signinForm .form.signin {
            display: flex;
        }

        .container .form h2 {
            color: #fff;
            font-weight: 500;
            letter-spacing: 0.1em;
        }

        .container .form .inputBox {
            position: relative;
            width: 300px;
        }

        .container .form .inputBox input {
            padding: 12px 10px 12px 48px;
            border: none;
            width: 100%;
            background: #223243;
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #fff;
            font-weight: 300;
            border-radius: 25px;
            font-size: 1em;
            box-shadow: -5px -5px 15px rgba(255, 255, 255, 0.1),
                5px 5px 15px rgba(0, 0, 0, 0.35);
            transition: 0.5s;
            outline: none;
        }

        .container .form .inputBox span {
            position: absolute;
            left: 0;
            padding: 12px 10px 12px 48px;
            pointer-events: none;
            font-size: 1em;
            font-weight: 300;
            transition: 0.5s;
            letter-spacing: 0.05em;
            color: rgba(255, 255, 255, 0.5);
        }

        .container .form .inputBox input:valid~span,
        .container .form .inputBox input:focus~span {
            color: #00dfc4;
            border: 1px solid #00dfc4;
            background: #223243;
            transform: translateX(25px) translateY(-7px);
            font-size: 0.6em;
            padding: 0 8px;
            border-radius: 10px;
            letter-spacing: 0.1em;
        }

        .container .form .inputBox input:valid,
        .container .form .inputBox input:focus {
            border: 1px solid #00dfc4;
        }

        .container .form .inputBox i {
            position: absolute;
            top: 15px;
            left: 16px;
            width: 25px;
            padding: 2px 0;
            padding-right: 8px;
            color: #00dfc4;
            border-right: 1px solid #00dfc4;
        }

        .container .form .inputBox input[type="submit"] {
            background: #00dfc4;
            color: #223243;
            padding: 10px 0;
            font-weight: 500;
            cursor: pointer;
            box-shadow: -5px -5px 15px rgba(255, 255, 255, 0.1),
                5px 5px 15px rgba(0, 0, 0, 0.35),
                inset -5px -5px 15px rgba(255, 255, 255, 0.1),
                inset 5px 5px 15px rgba(0, 0, 0, 0.35);
        }

        .container .form p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.75em;
            font-weight: 300;
        }

        .container .form p a {
            font-weight: 500;
            color: #fff;
        }

        .success-message {
            color: #00dfc4;
            background: #fff;
            border-radius: 8px;
            padding: 8px 0;
            margin-bottom: 12px;
            text-align: center;
        }

        a {
            text-decoration: none;
            color: #00dfc4;
        }

        a:hover {
            color: #00dfc4;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (isset($_GET['register']) && $_GET['register'] === 'success'): ?>
            <div class="success-message">Signup successful! Please login.</div>
        <?php endif; ?>
        <?php if (isset($_GET['login']) && $_GET['login'] === 'success'): ?>
            <div class="success-message">Login successful!</div>
        <?php endif; ?>

        <div class="form signin">
            <h2>Sign In</h2>
            <form action="../backend/login.php" method="POST">
                <div class="inputBox">
                    <input type="email" name="username" required="required" />
                    <i class="fa-regular fa-user"></i>
                    <span>Email</span>
                </div>
                <div class="inputBox">
                    <input type="password" name="password" required="required" />
                    <i class="fa-solid fa-lock"></i>
                    <span>password</span>
                </div>
                <div class="inputBox" style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="remember_me" id="remember_me" style="width:auto;">
                    <label for="remember_me" style="color:#fff;font-size:0.95em;">Remember Me</label>
                </div>
                <div class="inputBox">
                    <input type="submit" value="Login" />
                </div>
            </form>
            <p>Not Registered ? <a href="#" class="create">Create an account</a></p>
        </div>
        <div class="form signup">
            <h2>Sign Up</h2>
            <form action="../backend/register.php" method="POST">
                <div class="inputBox">
                    <input type="text" name="username" required="required" />
                    <i class="fa-regular fa-user"></i>
                    <span>username</span>
                </div>
                <div class="inputBox">
                    <input type="email" name="email" required="required" />
                    <i class="fa-regular fa-envelope"></i>
                    <span>email address</span>
                </div>
                <div class="inputBox">
                    <input type="password" name="password" required="required" />
                    <i class="fa-solid fa-lock"></i>
                    <span>create password</span>
                </div>
                <div class="inputBox">
                    <input type="submit" value="Create Account" />
                </div>
            </form>
            <p>Already a member ? <a href="#" class="login">Log in</a></p>
        </div>
        
    </div>
    <script>
        let login = document.querySelector(".login");
        let create = document.querySelector(".create");
        let container = document.querySelector(".container");

        login.onclick = function() {
            container.classList.add("signinForm");
        };

        create.onclick = function() {
            container.classList.remove("signinForm");
        };
    </script>
</body>

</html>
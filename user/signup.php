<?php
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    if (isset($_SESSION['user_id'])) {
        header("Location: account.php");
        exit;
    }

    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        // Basic validation
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            $error = "All fields are required.";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } else {
            // Simulate user registration (replace with database logic)
            $_SESSION['user_id'] = uniqid();
            $_SESSION['username'] = $username;
            header("Location: signin.php");
            exit;
        }
    }
} else {
    die("Session failed to start.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign up for Tech Giants to access premium gaming hardware.">
    <link rel="icon" href="https://img.icons8.com/ios-filled/50/000000/controller.png" type="image/png">
    <title>Sign Up - Tech Giants</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            color: #222;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #ff6600;
            margin-bottom: 20px;
            cursor: pointer;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .btn-primary {
            background: #ff6600;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            width: 100%;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-primary:hover {
            background: #e65c00;
        }
        .error {
            color: #ff0000;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .login-link {
            margin-top: 15px;
            color: #333;
            text-decoration: none;
        }
        .login-link:hover {
            color: #ff6600;
        }
        @media (max-width: 480px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">Tech Giants</div>
        <h2>Sign Up</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn-primary">Sign Up</button>
        </form>
        <p>Already have an account? <a href="signin.php" class="login-link">Sign In</a></p>
    </div>
    <script>
        document.querySelector('.logo').addEventListener('click', () => {
            window.location.href = 'home.php';
        });
    </script>
</body>
</html>
<?php
session_start();
require_once 'components/db.php';

// Session timeout (30 minutes inactivity)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) { // 30 minutes
    session_destroy();
    header("Location: /E-commerce-Website/index.php");
    exit;
}
$_SESSION['last_activity'] = time();

if (session_status() !== PHP_SESSION_ACTIVE) {
    die("Session failed to start.");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email && $password) {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Debug: Check if user is found
            if ($user) {
                echo "<!-- User found: " . print_r($user, true) . " -->";
            } else {
                echo "<!-- No user found for email: $email -->";
            }

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                // Debug: Confirm session set
                echo "<!-- Session set: user_id = " . $_SESSION['user_id'] . ", username = " . $_SESSION['username'] . " -->";
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid email or password.";
                // Debug: Password verification failed
                echo "<!-- Password verify failed for email: $email -->";
            }
        } catch (PDOException $e) {
            $error = "An error occurred. Please try again: " . $e->getMessage();
            // Debug: Database error
            echo "<!-- Database error: " . $e->getMessage() . " -->";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign in to Tech Giants for access to your account.">
    <link rel="icon" href="https://img.icons8.com/ios-filled/50/000000/controller.png" type="image/png">
    <title>Sign In - Tech Giants</title>
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
            position: relative;
        }
        .back-arrow {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 24px;
            color: #ff6600;
            cursor: pointer;
            text-decoration: none;
            transition: color 0.3s;
        }
        .back-arrow:hover {
            color: #e65c00;
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
        .signup-link {
            margin-top: 15px;
            color: #333;
            text-decoration: none;
        }
        .signup-link:hover {
            color: #ff6600;
        }
        @media (max-width: 480px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
            .back-arrow {
                top: 5px;
                left: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-arrow">←</a>
        <div class="logo" onclick="window.location.href='index.php'">Tech Giants</div>
        <h2>Sign In</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-primary">Sign In</button>
        </form>
        <p>Don't have an account? <a href="signup.php" class="signup-link">Sign Up</a></p>
    </div>
    <script>
        document.querySelector('.logo').addEventListener('click', () => {
            window.location.href = 'index.php';
        });
    </script>
</body>
</html>
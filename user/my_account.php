<?php
session_start();
require_once '../components/db.php'; // Adjusted path to move up one directory

// ✅ Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../signin.php");
    exit;
}

// ✅ Fetch user details from DB
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    // If user not found, force logout
    header("Location: ../logout.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Account - Tech Giants</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; color: #222; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08); }
        h1 { color: #ff6600; }
        .user-info { margin: 20px 0; }
        .user-info p { margin: 10px 0; }
        .logout-btn { background: #ff6600; color: #fff; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Account</h1>
        <div class="user-info">
            <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        </div>
        <form method="POST" action="logout.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</body>
</html>
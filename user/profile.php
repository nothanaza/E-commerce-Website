<?php
session_start();
if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

// Adjusted path to point to the root components directory
require_once '../components/db.php';

// Debug: Check if PDO is set
if (!isset($pdo) || !$pdo) {
    die("Database connection failed. Check components/db.php.");
}

// Fetch user data (using only username and email)
$user_id = $_SESSION['user_id'];
// var_dump($user_id); // Commented out as per your request
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
try {
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// If no user data is found, set defaults
if (!$user) {
    $user = [
        'username' => 'Unknown User',
        'email' => 'no-email@techgiants.com'
    ];
}
$_SESSION['user'] = $user;

// Calculate gaming statistics (assuming orders table might be missing)
$stmt = $pdo->prepare("SELECT SUM(total) as total_spent, COUNT(*) as order_count FROM orders WHERE user_id = ?");
try {
    $stmt->execute([$user_id]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $stats = ['total_spent' => 0, 'order_count' => 0];
}
$stats = $stats ?: ['total_spent' => 0, 'order_count' => 0];

// Calculate membership years (using a default dob since it's missing)
$dob = new DateTime('1995-06-15'); // Default DOB since column is missing
$today = new DateTime();
$years_member = $dob->diff($today)->y;

// Determine VIP status and rating
$rating = 5.0; // Hardcoded for now
$vip_status = $stats['total_spent'] > 500 || $years_member > 2 ? 'VIP' : 'Regular';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage your account and gaming preferences at Tech Giants.">
    <link rel="icon" href="https://img.icons8.com/ios-filled/50/000000/controller.png" type="image/png">
    <title>Tech Giants - My Profile</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

        body {
            margin: 0;
            font-family: 'Roboto', Arial, sans-serif;
            background: #f9f9f9;
            color: #222;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 20px 0;
        }
        .profile-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 1000px;
            text-align: center;
        }
        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .profile-title {
            display: flex;
            align-items: center;
        }
        .profile-title h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 0 12px;
            color: #222;
        }
        .vip-rating {
            font-size: 18px;
            color: #ff6600;
            font-weight: 600;
        }
        .nav-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .nav-tabs button {
            background: #fff;
            border: 2px solid #ddd;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
        }
        .nav-tabs button.active {
            background: #ff6600;
            color: #fff;
            border-color: #ff6600;
        }
        .nav-tabs button:hover {
            background: #f9f9f9;
            border-color: #ff6600;
        }
        .section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 0 auto;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: left;
        }
        .personal-info h2, .gaming-stats h2 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #222;
        }
        .info-item, .stat-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .info-item span, .stat-item span {
            margin-right: 12px;
            font-size: 20px;
        }
        .edit-btn {
            color: #ff6600;
            cursor: pointer;
            text-decoration: underline;
            font-size: 16px;
        }
        .stat-item p {
            margin: 0;
            font-size: 16px;
            color: #666;
        }
        .vip-status {
            color: #ff6600;
            font-weight: 700;
            font-size: 16px;
        }
        @media (max-width: 768px) {
            .profile-container {
                margin: 10px;
                padding: 20px;
            }
            .section {
                grid-template-columns: 1fr;
            }
            .nav-tabs {
                flex-direction: column;
                align-items: center;
            }
            .nav-tabs button {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-title">
                <span>👤</span>
                <h1>My Profile</h1>
            </div>
            <div class="vip-rating">
                <?php echo $vip_status === 'VIP' ? 'VIP Gamer' : 'Gamer'; ?> ★ <?= number_format($rating, 1) ?> Rating
            </div>
        </div>
        <div class="nav-tabs">
            <button class="active">Profile Info</button>
            <button>Order History</button>
            <button>Wishlist</button>
            <button>Settings</button>
        </div>
        <div class="section">
            <div class="card personal-info">
                <h2>Personal Information</h2>
                <div class="info-item"><span>👤</span> <?= htmlspecialchars($user['username']) ?></div>
                <div class="info-item"><span>📧</span> <?= htmlspecialchars($user['email']) ?></div>
                <div class="info-item"><span>📍</span> <?= htmlspecialchars('Not Provided') // Address column missing ?></div>
                <div class="info-item"><span>📅</span> <?= htmlspecialchars('Not Provided') // DOB column missing ?></div>
                <div class="info-item"><span class="edit-btn">✏️ Edit</span></div>
            </div>
            <div class="card gaming-stats">
                <h2>Gaming Statistics</h2>
                <div class="stat-item"><span>R<?= number_format($stats['total_spent'], 2) ?></span><p>Total Spent</p></div>
                <div class="stat-item"><span><?= $stats['order_count'] ?></span><p>Orders</p></div>
                <div class="stat-item"><span><?= $years_member ?></span><p>Years Member</p></div>
                <div class="stat-item"><span class="vip-status"><?= $vip_status ?></span><p>VIP Status</p></div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.nav-tabs button').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.nav-tabs button').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                // Add logic to load content for each tab
            });
        });
        document.querySelector('.edit-btn').addEventListener('click', () => {
            alert('Edit functionality to be implemented. Redirect to edit profile page.');
        });
    </script>
</body>
</html>
<?php
session_start();
if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

// Adjusted path to point to the root components directory
require_once '../components/db.php';

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT u.username, u.email, 
    (SELECT COUNT(*) FROM orders WHERE user_id = u.id) AS total_orders,
    (SELECT COUNT(*) FROM orders WHERE user_id = u.id AND status = 'pending') AS pending_orders,
    (SELECT COUNT(*) FROM wishlist WHERE user_id = u.id) AS wishlist_items,
    COALESCE(AVG(r.rating), 0) AS rating
    FROM users u
    LEFT JOIN reviews r ON u.id = r.user_id
    WHERE u.id = ?
    GROUP BY u.id");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

$user_name = htmlspecialchars($user['username']);
$total_orders = (int)$user['total_orders'];
$pending_orders = (int)$user['pending_orders'];
$wishlist_items = (int)$user['wishlist_items'];
$rating = floatval($user['rating']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Tech Giants</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #222;
        }

        .account-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to right, #ffe7d1, #fff1e6);
            padding: 20px 40px;
            border-bottom: 2px solid #ff6600;
        }
        .account-header h1 {
            font-size: 26px;
            color: #222;
            margin: 0;
        }
        .account-header h1 span {
            color: #ff6600;
            font-weight: 700;
        }
        .vip-badge {
            background: #ff6600;
            color: #fff;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
        }

        .account-container {
            display: flex;
            gap: 20px;
            margin: 30px;
        }

        .sidebar {
            width: 270px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 20px 0;
        }
        .sidebar a {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: #222;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }
        .sidebar a i {
            margin-right: 12px;
            font-size: 18px;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #ff6600;
            color: #fff;
            border-radius: 0 25px 25px 0;
        }
        .logout {
            color: #ff0000 !important;
            font-weight: 600;
        }

        .dashboard {
            flex: 1;
        }

        .stats {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .stat-card {
            background: #fff;
            flex: 1;
            min-width: 200px;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            text-align: center;
        }
        .stat-card i {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .stat-card:nth-child(1) i { color: #ff6600; }
        .stat-card:nth-child(2) i { color: #007bff; }
        .stat-card:nth-child(3) i { color: #ff0099; }
        .stat-card:nth-child(4) i { color: #00b894; }
        .stat-card h2 {
            margin: 5px 0;
            font-size: 22px;
        }

        .recent-orders {
            margin-top: 40px;
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }
        .recent-orders h3 {
            margin-bottom: 20px;
        }
        .order {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .order:last-child {
            border-bottom: none;
        }
        .order img {
            width: 80px;
            border-radius: 8px;
            object-fit: cover;
        }
        .order-info {
            flex: 1;
            margin-left: 15px;
        }
        .order-status {
            font-weight: 600;
        }
        .order-status.delivered { color: #00b894; }
        .order-status.in-transit { color: #007bff; }

        .bottom-widgets {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            gap: 20px;
        }
        .widget {
            flex: 1;
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }
        .widget i {
            font-size: 30px;
            margin-right: 15px;
        }
        .wishlist i { color: #ff0099; background: #ffe5f2; border-radius: 50%; padding: 10px; }
        .browse i { color: #ff6600; background: #fff2e5; border-radius: 50%; padding: 10px; }

        @media (max-width: 768px) {
            .account-container {
                flex-direction: column;
                margin: 20px;
            }
            .sidebar {
                width: 100%;
            }
            .stats {
                flex-direction: column;
            }
            .bottom-widgets {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="account-header">
        <div>
            <h1>Welcome back, <span><?php echo $user_name; ?>!</span></h1>
            <p>Manage your account and track your orders</p>
        </div>
        <div class="vip-badge"><i class="fa-solid fa-crown"></i> VIP Member</div>
    </div>

    <div class="account-container">
        <div class="sidebar">
            <a href="profile.php" class="active"><i class="fa-solid fa-user"></i> Account Overview</a>
            <a href="orders.php"><i class="fa-solid fa-box"></i> My Orders</a>
            <a href="wishlist.php"><i class="fa-solid fa-heart"></i> Wishlist</a>
            <a href="profile_settings.php"><i class="fa-solid fa-gear"></i> Profile Settings</a>
            <a href="addresses.php"><i class="fa-solid fa-location-dot"></i> Addresses</a>
            <a href="logout.php" class="logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</a>
        </div>

        <div class="dashboard">
            <div class="stats">
                <div class="stat-card">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <h2><?php echo $total_orders; ?></h2>
                    <p>Total Orders</p>
                </div>
                <div class="stat-card">
                    <i class="fa-solid fa-clock"></i>
                    <h2><?php echo $pending_orders; ?></h2>
                    <p>Pending</p>
                </div>
                <div class="stat-card">
                    <i class="fa-solid fa-heart"></i>
                    <h2><?php echo $wishlist_items; ?></h2>
                    <p>Wishlist</p>
                </div>
                <div class="stat-card">
                    <i class="fa-solid fa-star"></i>
                    <h2><?php echo number_format($rating, 1); ?></h2>
                    <p>Rating</p>
                </div>
            </div>

            <div class="recent-orders">
                <h3>Recent Orders</h3>
                <?php
                $stmt = $pdo->prepare("SELECT o.id, o.order_date, o.total, o.status, p.name AS product_name, p.image AS product_image
                    FROM orders o
                    JOIN order_items oi ON o.id = oi.order_id
                    JOIN products p ON oi.product_id = p.id
                    WHERE o.user_id = ?
                    ORDER BY o.order_date DESC
                    LIMIT 2");
                $stmt->execute([$user_id]);
                $orders = $stmt->fetchAll();

                if ($orders) {
                    foreach ($orders as $order) {
                        $statusClass = strtolower($order['status']) === 'delivered' ? 'delivered' : 'in-transit';
                        echo "<div class='order'>";
                        echo "<img src='" . htmlspecialchars($order['product_image'] ?? 'https://placehold.co/80x80/ff6a00/fff?text=Image') . "' alt='" . htmlspecialchars($order['product_name']) . "' onerror=\"this.src='https://placehold.co/80x80/ff6a00/fff?text=Image+Error'\">";
                        echo "<div class='order-info'>";
                        echo "<h4>ORD-" . str_pad($order['id'], 6, '0', STR_PAD_LEFT) . "</h4>";
                        echo "<p>" . htmlspecialchars($order['product_name']) . "</p>";
                        echo "<small>" . date('Y-m-d', strtotime($order['order_date'])) . "</small>";
                        echo "</div>";
                        echo "<div>";
                        echo "<p class='order-status " . $statusClass . "'>" . ucfirst($order['status']) . "</p>";
                        echo "<strong>R" . number_format($order['total'], 2) . "</strong>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No recent orders found.</p>";
                }
                ?>
            </div>

            <div class="bottom-widgets">
                <div class="widget wishlist">
                    <i class="fa-solid fa-heart"></i>
                    <div>
                        <h3>Your Wishlist</h3>
                        <p><?php echo $wishlist_items; ?> items saved</p>
                    </div>
                </div>
                <div class="widget browse">
                    <i class="fa-solid fa-cube"></i>
                    <div>
                        <h3>Browse Products</h3>
                        <p>Discover new gaming gear</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
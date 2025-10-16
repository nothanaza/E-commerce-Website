<?php
session_start();
if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION['user_id'])) {
    header("Location: /E-commerce-Website/signin.php");
    exit;
}

// Explicitly define $user_id from session
$user_id = $_SESSION['user_id'];

// Adjusted path to point to the components directory within the E-commerce-Website folder
require_once 'components/db.php';

// Fetch user data
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
$email = htmlspecialchars($user['email']);
$phone = htmlspecialchars($user['phone'] ?? '');
$id_number = htmlspecialchars($user['id_number'] ?? '');
$total_orders = (int)$user['total_orders'];
$pending_orders = (int)$user['pending_orders'];
$wishlist_items = (int)$user['wishlist_items'];
$rating = floatval($user['rating']);

// Get cart count
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
}

// Determine active section
$section = isset($_GET['section']) ? $_GET['section'] : 'overview';
$edit_mode = isset($_GET['edit']) && $_GET['edit'] === 'true';

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $new_name = $_POST['name'];
    $new_surname = $_POST['surname'];
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];
    $new_id_number = $_POST['id_number'];
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, phone = ?, id_number = ? WHERE id = ?");
    $stmt->execute([$new_name, $new_email, $new_phone, $new_id_number, $user_id]);
    header("Location: /E-commerce-Website/profile.php?section=profile_settings");
    exit;
}
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

        .header {
            background: #fff;
            border-bottom: 1px solid #ddd;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
            color: #ff6a00;
            cursor: pointer;
        }

        .nav a {
            text-decoration: none;
            font-weight: bold;
            margin: 0 10px;
            font-size: 16px;
            color: #333;
            transition: color 0.3s;
        }

        .nav a:hover {
            color: #ff6a00;
        }

        .user-actions {
            display: flex;
            align-items: center;
        }

        .account-link, .cart-link {
            text-decoration: none;
            color: #333;
            margin-left: 10px;
            transition: color 0.3s;
        }

        .account-link:hover, .cart-link:hover {
            color: #ff6a00;
        }

        .cart-badge {
            background: #ff6a00;
            color: #fff;
            padding: 3px 8px;
            border-radius: 50%;
            font-size: 12px;
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
            box-shadow: 0 2px 8px rgba(169, 98, 98, 0.1);
            transform: translateX(5px);
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

        .section-content {
            margin-top: 40px;
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }
        .section-content h3 {
            margin-bottom: 20px;
        }
        .order-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 15px;
            background: #f9f9f9;
        }
        .order-container img {
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
        .edit-icon {
            cursor: pointer;
            color: #ff6600;
            font-size: 20px;
        }

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

        /* Footer Styles */
        .site-footer {
            background-color: #000;
            color: #f3f4f6;
            padding-top: 3rem;
            font-size: 0.875rem;
            margin-top: 40px;
        }

        .footer-top {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            padding: 0 1rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-col h4 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .footer-col ul {
            list-style: none;
            padding: 0;
        }

        .footer-col ul li {
            margin-bottom: 0.5rem;
        }

        .footer-col ul li a {
            color: #d1d5db;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .footer-col ul li a:hover {
            color: #f97316;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .logo-box {
            width: 2.5rem;
            height: 2.5rem;
            background: #f97316;
            color: #fff;
            font-weight: 700;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
        }

        .brand-name {
            font-weight: 700;
            font-size: 1.2rem;
        }

        .footer-description {
            margin-bottom: 1rem;
            color: #9ca3af;
        }

        .footer-contact li {
            margin-bottom: 0.3rem;
            color: #d1d5db;
        }

        .footer-middle {
            border-top: 1px solid #374151;
            padding: 1rem;
            text-align: center;
            font-size: 0.85rem;
            color: #9ca3af;
            margin: 0 5rem 0 5rem;
        }

        .footer-links {
            margin: 0.5rem 0;
        }

        .footer-links a {
            margin: 0 0.75rem;
            color: #9ca3af;
            text-decoration: none;
        }

        .footer-links a:hover {
            color: #f97316;
        }

        .powered {
            margin-top: 0.5rem;
        }

        .powered span {
            color: #f97316;
            font-weight: 600;
        }

        .footer-newsletter {
            background: #111827;
            color: #fff;
            text-align: center;
            padding: 2rem 1rem;
            margin: 2rem 0 0 5rem;
            border-radius: 0.5rem 0.5rem 0 0;
            max-width: 1200px;
        }

        .footer-newsletter h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .footer-newsletter p {
            color: #d1d5db;
            margin-bottom: 1rem;
        }

        .newsletter-form {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .newsletter-form input {
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            border: 1px solid #374151;
            background: #1f2937;
            color: #f3f4f6;
            flex: 1;
            max-width: 250px;
        }

        .newsletter-form button {
            padding: 0.75rem 1.5rem;
            background: #f97316;
            color: #fff;
            border: none;
            border-radius: 0.375rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .newsletter-form button:hover {
            background: #ea580c;
        }

        @media (min-width: 768px) {
            .footer-top {
                grid-template-columns: repeat(4, 1fr);
            }

            .footer-middle {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: left;
            }

            .footer-links {
                margin: 0;
            }
        }

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
            .header {
                flex-direction: column;
                gap: 10px;
            }
            .nav {
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo" onclick="window.location.href='/E-commerce-Website/index.php'">Tech Giants</div>
        <nav class="nav">
            <a href="/E-commerce-Website/index.php">Home</a>
            <a href="/E-commerce-Website/shop.php">Shop</a>
            <a href="/E-commerce-Website/about.php">About Us</a>
            <a href="/E-commerce-Website/contact.php">Contact</a>
        </nav>
        <div class="user-actions">
            <a href="/E-commerce-Website/profile.php" class="account-link">👤 My Account</a>
            <a href="/E-commerce-Website/cart.php" class="cart-link">🛒 <span class="cart-badge"><?= htmlspecialchars($cart_count) ?: 0 ?></span></a>
        </div>
    </header>

    <div class="account-header">
        <div>
            <h1>Welcome back, <span><?php echo $user_name; ?>!</span></h1>
            <p>Manage your account and track your orders</p>
        </div>
        <div class="vip-badge"><i class="fa-solid fa-crown"></i> VIP Member</div>
    </div>

    <div class="account-container">
        <div class="sidebar">
            <a href="/E-commerce-Website/profile.php" class="<?php echo $section === 'overview' ? 'active' : ''; ?>"><i class="fa-solid fa-user"></i> Account Overview</a>
            <a href="/E-commerce-Website/profile.php?section=orders" class="<?php echo $section === 'orders' ? 'active' : ''; ?>"><i class="fa-solid fa-box"></i> My Orders</a>
            <a href="/E-commerce-Website/profile.php?section=wishlist" class="<?php echo $section === 'wishlist' ? 'active' : ''; ?>"><i class="fa-solid fa-heart"></i> Wishlist</a>
            <a href="/E-commerce-Website/profile.php?section=profile_settings" class="<?php echo $section === 'profile_settings' ? 'active' : ''; ?>"><i class="fa-solid fa-gear"></i> Profile Settings</a>
            <a href="/E-commerce-Website/logout.php" class="logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</a>
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

            <div class="section-content">
                <?php
                if ($section === 'overview') {
                    echo "<h3>Recent Orders</h3>";
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
                            echo "<div class='order-container'>";
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
                } elseif ($section === 'orders') {
                    echo "<h3>Order History</h3>";
                    $stmt = $pdo->prepare("SELECT o.id, o.order_date, o.total, o.status, p.name AS product_name, p.image AS product_image
                        FROM orders o
                        JOIN order_items oi ON o.id = oi.order_id
                        JOIN products p ON oi.product_id = p.id
                        WHERE o.user_id = ?
                        ORDER BY o.order_date DESC");
                    $stmt->execute([$user_id]);
                    $orders = $stmt->fetchAll();
                    if ($orders) {
                        foreach ($orders as $order) {
                            $statusClass = strtolower($order['status']) === 'delivered' ? 'delivered' : 'in-transit';
                            echo "<div class='order-container'>";
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
                        echo "<p>No order history found.</p>";
                    }
                } elseif ($section === 'wishlist') {
                    echo "<h3>My Wishlist</h3>";
                    $stmt = $pdo->prepare("SELECT p.name, p.image, p.price FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ?");
                    $stmt->execute([$user_id]);
                    $wishlist = $stmt->fetchAll();
                    if ($wishlist) {
                        foreach ($wishlist as $item) {
                            echo "<div class='order-container'>";
                            echo "<img src='" . htmlspecialchars($item['image'] ?? 'https://placehold.co/80x80/ff6a00/fff?text=Image') . "' alt='" . htmlspecialchars($item['name']) . "' onerror=\"this.src='https://placehold.co/80x80/ff6a00/fff?text=Image+Error'\">";
                            echo "<div class='order-info'>";
                            echo "<h4>" . htmlspecialchars($item['name']) . "</h4>";
                            echo "<p>Price: R" . number_format($item['price'], 2) . "</p>";
                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>Your wishlist is empty.</p>";
                    }
                } elseif ($section === 'profile_settings') {
                    echo "<h3>Profile Settings</h3>";
                    if ($edit_mode) {
                        echo "<form method='post' action='/E-commerce-Website/profile.php?section=profile_settings'>";
                        echo "<div class='mb-4'>";
                        echo "<label class='block text-sm font-medium mb-1'>Name</label>";
                        echo "<input type='text' name='name' value='" . htmlspecialchars($user_name) . "' class='w-full p-2 border rounded' required>";
                        echo "</div>";
                        echo "<div class='mb-4'>";
                        echo "<label class='block text-sm font-medium mb-1'>Surname</label>";
                        echo "<input type='text' name='surname' value='' class='w-full p-2 border rounded' required>";
                        echo "</div>";
                        echo "<div class='mb-4'>";
                        echo "<label class='block text-sm font-medium mb-1'>Email</label>";
                        echo "<input type='email' name='email' value='" . $email . "' class='w-full p-2 border rounded' required>";
                        echo "</div>";
                        echo "<div class='mb-4'>";
                        echo "<label class='block text-sm font-medium mb-1'>Phone Number</label>";
                        echo "<input type='tel' name='phone' value='" . $phone . "' class='w-full p-2 border rounded'>";
                        echo "</div>";
                        echo "<div class='mb-4'>";
                        echo "<label class='block text-sm font-medium mb-1'>Identification Number</label>";
                        echo "<input type='text' name='id_number' value='" . $id_number . "' class='w-full p-2 border rounded'>";
                        echo "</div>";
                        echo "<button type='submit' name='save_profile' class='bg-ff6600 text-white p-2 rounded'>Save Changes</button>";
                        echo "</form>";
                    } else {
                        echo "<div class='mb-4'>";
                        echo "<p><strong>Name:</strong> " . $user_name . "</p>";
                        echo "<p><strong>Surname:</strong> N/A</p>";
                        echo "<p><strong>Email:</strong> " . $email . "</p>";
                        echo "<p><strong>Phone Number:</strong> " . ($phone ?: 'N/A') . "</p>";
                        echo "<p><strong>Identification Number:</strong> " . ($id_number ?: 'N/A') . "</p>";
                        echo "</div>";
                        echo "<a href='/E-commerce-Website/profile.php?section=profile_settings&edit=true' class='edit-icon'><i class='fa-solid fa-edit'></i></a>";
                    }
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

    <footer class="site-footer">
        <div class="footer-top">
            <div class="footer-col">
                <div class="footer-logo">
                    <div class="logo-box">TG</div>
                    <span class="brand-name">Tech Giants</span>
                </div>
                <p class="footer-description">
                    South Africa's premier destination for gaming hardware and accessories. 
                    We provide cutting-edge technology for serious gamers who demand the best performance.
                </p>
                <ul class="footer-contact">
                    <li>📍 Pretoria, Gauteng</li>
                    <li>📞 +27 21 123 4567</li>
                    <li>✉️ info@techgiants.co.za</li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
               <ul>
                 <li><a href="index.php">Home</a></li>
                 <li><a href="about.php">Why Choose Us</a></li>
                 <li><a href="shop.php">Shop</a></li>
                  <li><a href="contact.php">Contact Us</a></li>
             </ul>
            </div>
            <div class="footer-col">
                <h4>Categories</h4>
                <ul>
                     <li><a href="gaming-pcs.php">Gaming PCs</a></li>
                    <li><a href="graphic-cards.php">Graphics Cards</a></li>
                    <li><a href="audio.php">Audio</a></li>
                   <li><a href="monitors.php">Monitors</a></li>
                   <li><a href="motherboards.php">Motherboards</a></li>
                   <li><a href="peripherals.php">Peripherals</a></li>
              </ul>
            </div>
            <div class="footer-col">
                <h4>Connect With Us</h4>
                <ul>
                    <li>📸 @techgiants</li>
                    <li>🌍 techgiants.co.za</li>
                    <li>🎵 @techgiants</li>
                </ul>
                <ul class="footer-support">
                    <li><a href="/E-commerce-Website/contact.php">Customer Support</a></li>
                    <li><a href="#">Warranty Claims</a></li>
                    <li><a href="#">Return Policy</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-middle">
            <p>© 2024 Tech Giants. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Shipping Info</a>
            </div>
            <p class="powered">Powered by <span>Gaming Excellence</span></p>
        </div>
        <div class="footer-newsletter">
            <h3>Stay Updated with Tech Giants</h3>
            <p>Get the latest gaming hardware news, exclusive deals, and product launches delivered to your inbox.</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Enter your email" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </footer>

    <script>
        document.querySelector('.logo').addEventListener('click', () => {
            window.location.href = '/E-commerce-Website/index.php';
        });
        document.querySelectorAll('.nav a').forEach(a => {
            a.addEventListener('click', () => {
                const page = a.getAttribute('href');
                console.log('Navigating to:', page);
                window.location.href = page;
            });
        });
        document.querySelector('.account-link').addEventListener('click', () => {
            window.location.href = '/E-commerce-Website/profile.php';
        });
        document.querySelector('.cart-link').addEventListener('click', () => {
            window.location.href = '/E-commerce-Website/cart.php';
        });
        document.querySelectorAll('.sidebar a').forEach(a => {
            a.addEventListener('click', (e) => {
                e.preventDefault();
                const page = a.getAttribute('href');
                console.log('Sidebar navigating to:', page);
                if (page !== '/E-commerce-Website/logout.php') {
                    window.location.href = page;
                } else {
                    if (confirm('Are you sure you want to log out?')) {
                        window.location.href = page;
                    }
                }
            });
        });
        document.querySelectorAll('footer a').forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                if (href) window.location.href = href;
            });
        });
        document.querySelector('.newsletter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input').value;
            const message = document.createElement('p');
            message.id = 'message';
            this.appendChild(message);
            if (!email.includes('@')) {
                message.textContent = "❌ Please enter a valid email.";
                message.className = "text-red-500 text-sm mt-2";
            } else {
                message.textContent = "✅ Thank you for subscribing!";
                message.className = "text-green-500 text-sm mt-2";
                this.querySelector('input').value = "";
            }
        });
    </script>
</body>
</html>                                                                                                                                                                                                                                                                                                                                                                                    
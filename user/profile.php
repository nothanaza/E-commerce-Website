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

// Placeholder for cart count (define this based on your cart system)
$cart_count = isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0;

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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: #f9f9f9;
            color: #222;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header {
            background: #fff;
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 22px;
            font-weight: 700;
            color: #ff6a00;
            cursor: pointer;
        }

        .nav a {
            text-decoration: none;
            font-weight: 600;
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

        main {
            flex: 1 0 auto;
            width: 100%;
        }

        .profile-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 1200px;
            margin: 20px auto;
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

        .edit-btn, .save-btn {
            color: #ff6600;
            cursor: pointer;
            text-decoration: underline;
            font-size: 16px;
            border: none;
            background: none;
            padding: 0;
        }

        .save-btn {
            margin-left: 10px;
        }

        .edit-mode .info-item input {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            font-size: 16px;
            width: 200px;
            margin-left: 10px;
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

        footer {
            background-color: #000;
            color: #f3f4f6;
            padding: 3rem 0;
            width: 100%;
        }

        footer .container {
            padding: 0 20px 2rem;
        }

        .footer-top {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
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
            padding: 1rem 0;
            text-align: center;
            font-size: 0.85rem;
            color: #9ca3af;
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
            padding: 2rem 0;
            border-radius: 0.5rem 0.5rem 0 0;
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
            .section {
                grid-template-columns: 1fr 1fr;
            }
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
            header {
                padding: 10px 20px;
            }
            .nav {
                display: none; /* Hide nav on mobile, add toggle if needed */
            }
            .user-actions {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo" onclick="window.location.href='index.php'">Tech Giants</div>
            <nav class="nav">
                <a href="index.php">Home</a>
                <a href="shop.php">Shop</a>
                <a href="about.php">About Us</a>
                <a href="contact.php">Contact</a>
            </nav>
            <div class="user-actions">
                <a href="#" class="account-link">👤 My Account</a>
                <a href="cart.php" class="cart-link">🛒 <span class="cart-badge"><?= htmlspecialchars($cart_count) ?></span></a>
            </div>
        </div>
    </header>

    <main>
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
                    <div class="info-item" data-field="username"><span>👤</span> <span class="field-value"><?= htmlspecialchars($user['username']) ?></span><input type="text" class="edit-field" value="<?= htmlspecialchars($user['username']) ?>" style="display: none;"></div>
                    <div class="info-item" data-field="email"><span>📧</span> <span class="field-value"><?= htmlspecialchars($user['email']) ?></span><input type="email" class="edit-field" value="<?= htmlspecialchars($user['email']) ?>" style="display: none;"></div>
                    <div class="info-item"><span>📍</span> <span class="field-value"><?= htmlspecialchars('Not Provided') ?></span></div>
                    <div class="info-item"><span>📅</span> <span class="field-value"><?= htmlspecialchars('Not Provided') ?></span></div>
                    <div class="info-item"><span class="edit-btn">✏️ Edit</span><span class="save-btn" style="display: none;">💾 Save</span></div>
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
    </main>

    <footer>
        <div class="container">
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
                    <h4>About Us</h4>
                    <ul>
                        <li><a href="#">Our Story</a></li>
                        <li><a href="#">Why Choose Us</a></li>
                        <li><a href="#">Gaming Community</a></li>
                        <li><a href="#">Expert Reviews</a></li>
                        <li><a href="#">Careers</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#">Gaming PCs</a></li>
                        <li><a href="#">Graphics Cards</a></li>
                        <li><a href="#">Gaming Peripherals</a></li>
                        <li><a href="#">Special Deals</a></li>
                        <li><a href="#">Build Configurator</a></li>
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
                        <li><a href="#">Customer Support</a></li>
                        <li><a href="#">Warranty Claims</a></li>
                        <li><a href="#">Return Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-middle">
                <p>© 2025 Tech Giants. All rights reserved.</p>
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
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const editBtn = document.querySelector('.edit-btn');
            const saveBtn = document.querySelector('.save-btn');
            const infoItems = document.querySelectorAll('.personal-info .info-item');
            let isEditing = false;

            editBtn.addEventListener('click', () => {
                if (!isEditing) {
                    isEditing = true;
                    editBtn.style.display = 'none';
                    saveBtn.style.display = 'inline';
                    infoItems.forEach(item => {
                        const fieldValue = item.querySelector('.field-value');
                        const editField = item.querySelector('.edit-field');
                        if (fieldValue && editField) {
                            fieldValue.style.display = 'none';
                            editField.style.display = 'inline';
                        }
                    });
                    document.querySelector('.personal-info').classList.add('edit-mode');
                }
            });

            saveBtn.addEventListener('click', () => {
                if (isEditing) {
                    isEditing = false;
                    editBtn.style.display = 'inline';
                    saveBtn.style.display = 'none';
                    infoItems.forEach(item => {
                        const fieldValue = item.querySelector('.field-value');
                        const editField = item.querySelector('.edit-field');
                        if (fieldValue && editField) {
                            fieldValue.textContent = editField.value;
                            fieldValue.style.display = 'inline';
                            editField.style.display = 'none';
                        }
                    });
                    document.querySelector('.personal-info').classList.remove('edit-mode');
                    alert('Changes saved! (Note: This is a demo. Update the code to save to the database.)');
                }
            });
        });

        document.querySelectorAll('.nav-tabs button').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.nav-tabs button').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                // Add logic to load content for each tab
            });
        });
    </script>
</body>
</html>
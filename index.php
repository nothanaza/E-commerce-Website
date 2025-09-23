<?php
session_start();
if (session_status() === PHP_SESSION_ACTIVE) {
    require_once 'components/db.php';

    // Get categories
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll() ?: [];

    // Get products
    $stmt = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id");
    $products = $stmt->fetchAll() ?: [];

    // Add to Cart
    if (isset($_POST['add_to_cart'])) {
        $product_id = $_POST['product_id'] ?? '';
        $product_name = $_POST['product_name'] ?? '';
        $product_price = isset($_POST['product_price']) ? floatval($_POST['product_price']) : 0;
        $product_image = $_POST['image'] ?? '';

        if ($product_id) {
            // Check if product exists in database
            $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product_exists = $stmt->fetch() !== false;

            if ($product_exists) {
                $session_id = session_id();
                try {
                    // Insert or update cart item in database
                    $stmt = $pdo->prepare("INSERT INTO carts (session_id, product_id, quantity) VALUES (?, ?, 1) 
                                         ON DUPLICATE KEY UPDATE quantity = quantity + 1");
                    $stmt->execute([$session_id, $product_id]);

                    // Sync session data
                    if (!isset($_SESSION['cart'])) {
                        $_SESSION['cart'] = [];
                    }
                    if (isset($_SESSION['cart'][$product_id])) {
                        $_SESSION['cart'][$product_id]['quantity']++;
                    } else {
                        $_SESSION['cart'][$product_id] = [
                            'name' => $product_name,
                            'price' => $product_price,
                            'quantity' => 1,
                            'image' => $product_image
                        ];
                    }
                } catch (PDOException $e) {
                    die("Database error: " . $e->getMessage());
                }
            }
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Get cart count
    $cart_count = 0;
    if (isset($_SESSION['cart'])) {
        $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
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
    <meta name="description" content="Premium gaming hardware store featuring PCs, graphics cards, motherboards, and monitors.">
    <link rel="icon" href="https://img.icons8.com/ios-filled/50/000000/controller.png" type="image/png">
    <title>The Tech Giants - Gaming Hardware Store</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f9f9f9;
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

        .hero-section {
            position: relative;
            display: flex;
            align-items: center;
            color: #fff;
            padding: 80px 40px;
            gap: 40px;
            background: url("https://images.unsplash.com/photo-1663419523419-038d7a8eb31f?auto=format&fit=crop&w=1600&q=80") no-repeat center/cover;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: auto;
        }

        .hero-text {
            max-width: 50%;
        }

        .badge {
            display: inline-block;
            background: #ff6600;
            color: #fff;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .hero-text h1 {
            font-size: 44px;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 15px;
        }

        .highlight {
            color: #ff6600;
        }

        .hero-text p {
            color: #ccc;
            font-size: 18px;
            line-height: 1.5;
            margin-bottom: 25px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        .btn-primary {
            background: #ff6600;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background: #e65c00;
        }

        .btn-secondary {
            background: #fff;
            color: #000;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn-secondary:hover {
            background: #f2f2f2;
        }

        .hero-image {
            border: 2px solid #ff6600;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(255, 102, 0, 0.4);
            max-width: 480px;
        }

        .hero-image img {
            width: 100%;
            display: block;
        }

        .categories {
            padding: 50px 20px;
            text-align: center;
        }

        .categories h2 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .categories p {
            color: #555;
            margin-bottom: 30px;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 900px;
            margin: auto;
        }

        .category-box {
            background: #fff;
            border: 1px solid #eceef2;
            border-radius: 16px;
            text-align: center;
            padding: 30px 20px;
            transition: transform 0.25s, box-shadow 0.25s;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }

        .category-box:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .category-icon {
            background: #ff6600;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .category-icon img {
            width: 26px;
            height: 26px;
        }

        .category-name {
            font-weight: 600;
            color: #222;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .category-count {
            font-size: 13px;
            color: #666;
        }

        .featured-products-section {
            background: #f7f7f8;
            padding: 50px 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 28px;
            font-weight: 800;
        }

        .view-all {
            background: #fff;
            border: 1px solid #ddd;
            padding: 12px 22px;
            border-radius: 8px;
            font-weight: 600;
            color: #222;
            text-decoration: none;
            transition: background 0.3s, color 0.3s, border-color 0.3s;
        }

        .view-all:hover {
            background: #ff6a00;
            color: #fff;
            border-color: #ff6a00;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            max-width: 1300px;
            margin: auto;
        }

        .product-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff6600;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .product-details {
            padding: 18px;
        }

        .product-details h3 {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 8px;
        }

        .category {
            font-size: 13px;
            color: #666;
            margin: 0 0 5px;
        }

        .rating {
            color: #f39c12;
            margin: 0 0 5px;
        }

        .rating-count {
            color: #777;
            font-size: 13px;
        }

        .price {
            font-weight: 700;
            margin: 5px 0;
        }

        .original-price {
            text-decoration: line-through;
            color: #777;
            font-size: 14px;
            margin-left: 5px;
        }

        .add-to-cart {
            width: 100%;
            padding: 12px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .add-to-cart:hover {
            background: #ff6600;
        }

        .cta-section {
            background: #ff6600;
            color: #fff;
            text-align: center;
            padding: 60px 20px;
            margin-top: 60px;
        }

        .cta-section h2 {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .cta-section p {
            font-size: 18px;
            margin-bottom: 25px;
        }

        .cta-link {
            background: #fff;
            color: #ff6600;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            transition: background 0.3s;
        }

        .cta-link:hover {
            background: #f2f2f2;
        }

        @media (max-width: 1200px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .hero-content {
                flex-direction: column;
                text-align: center;
            }
            .hero-text {
                max-width: 100%;
            }
            .hero-image {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
            .category-grid {
                grid-template-columns: 1fr;
            }
            .header {
                flex-direction: column;
                gap: 10px;
            }
            .nav {
                margin: 10px 0;
            }
            .hero-section {
                padding: 40px 20px;
            }
            .hero-text h1 {
                font-size: 32px;
            }
        }

        /*Footer Styles*/
        .site-footer {
            background-color: #000;
            color: #f3f4f6;
            padding-top: 3rem;
            font-size: 0.875rem;
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
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <header class="header">
    <div class="logo" onclick="window.location.href='index.php'">Tech Giants</div>
     <nav class="nav">
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
    </nav>

     <div class="user-actions">
            <a href="signin.php" class="account-link">👤 My Account</a>
            <a href="cart.php" class="cart-link">🛒 <span class="cart-badge"><?= htmlspecialchars($cart_count) ?: 0 ?></span></a>
     </div>
    </header>


    <main>
        <!-- HERO -->
        <section class="hero-section">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="hero-text">
                    <span class="badge">New Generation Gaming</span>
                    <h1>Unleash Your <span class="highlight">Gaming Potential</span></h1>
                    <p>Premium gaming hardware and accessories for serious gamers. Experience ultimate performance with our cutting-edge technology.</p>
                    <div class="hero-buttons">
                        <a href="shop.php?category=gaming-pcs" class="btn-primary">Shop Gaming PCs →</a>
                        <a href="#categories" class="btn-secondary">Browse Components</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="https://images.unsplash.com/photo-1636914011676-039d36b73765?auto=format&fit=crop&w=1080&q=80" alt="Gaming Desk Setup" onerror="this.src='https://placehold.co/480x300/ff6a00/fff?text=Image+Error'">
                </div>
            </div>
        </section>

        <!-- SHOP BY CATEGORY -->
        <section id="categories" class="categories">
            <h2>Shop by Category</h2>
            <p>Find exactly what you're looking for</p>
            <div class="category-grid">
                <?php
                $iconMap = [
                    "gaming-pcs" => "https://img.icons8.com/ios-filled/50/ffffff/computer.png",
                    "graphics-cards" => "https://img.icons8.com/ios-filled/50/ffffff/video-card.png",
                    "motherboards" => "https://img.icons8.com/ios-filled/50/ffffff/motherboard.png",
                    "monitors" => "https://img.icons8.com/ios-filled/50/ffffff/monitor.png",
                    "peripherals" => "https://img.icons8.com/ios-filled/50/ffffff/keyboard.png",
                    "audio" => "https://img.icons8.com/ios-filled/50/ffffff/headphones.png"
                ];
                foreach ($categories as $cat): ?>
                    <div class="category-box" data-cat="<?= htmlspecialchars($cat['id']) ?>">
                        <div class="category-icon"><img src="<?= $iconMap[$cat['id']] ?>" alt="<?= htmlspecialchars($cat['name']) ?> Icon"></div>
                        <div class="category-name"><?= htmlspecialchars($cat['name']) ?></div>
                        <div class="category-count">Products</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- FEATURED PRODUCTS -->
        <section class="featured-products-section">
            <div class="section-header">
                <h2 class="section-title">Featured Products</h2>
                <a href="shop.php" class="view-all">View All Products →</a>
            </div>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='https://placehold.co/300x200/ff6a00/fff?text=Image+Error'">
                            <?php if ($product['discount']): ?>
                                <span class="discount-badge"><?= htmlspecialchars($product['discount']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-details">
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="category"><?= htmlspecialchars($product['category_name']) ?></p>
                            <p class="rating">★★★★☆ <span class="rating-count">(<?= $product['reviews'] ?>)</span></p>
                            <p class="price">R<?= number_format($product['price'], 2) ?>
                                <?php if ($product['old_price']): ?>
                                    <span class="original-price">R<?= number_format($product['old_price'], 2) ?></span>
                                <?php endif; ?>
                            </p>
                            <form method="post" action="">
                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                                <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                                <input type="hidden" name="product_price" value="<?= htmlspecialchars($product['price']) ?>">
                                <input type="hidden" name="image" value="<?= htmlspecialchars($product['image']) ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart">🛒 Add to Cart</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- READY TO GAME CTA -->
        <section class="cta-section">
            <h2>Ready to Upgrade Your Gaming?</h2>
            <p>Take your setup to the next level with our exclusive products.</p>
            <a href="shop.php" class="cta-link">Shop Now →</a>
        </section>
    </main>

    <!-- Footer -->
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
            window.location.href = 'index.php';
        });
        document.querySelectorAll('.nav a').forEach(a => {
            a.addEventListener('click', () => {
                const page = a.getAttribute('href');
                window.location.href = page;
            });
        });
        document.querySelector('.account-link').addEventListener('click', () => {
            window.location.href = 'signin.php';
        });
        document.querySelector('.cart-link').addEventListener('click', () => {
            window.location.href = 'cart.php';
        });
        document.querySelectorAll('.category-box').forEach(box => {
            box.addEventListener('click', () => {
                const category = box.getAttribute('data-cat');
                window.location.href = `shop.php?category=${category}`;
            });
        });
        document.querySelector('.view-all').addEventListener('click', () => {
            window.location.href = 'shop.php';
        });
        document.querySelector('.cta-link').addEventListener('click', () => {
            window.location.href = 'shop.php';
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
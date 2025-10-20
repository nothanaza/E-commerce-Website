<?php
session_start();
require_once 'components/db.php';

// Session timeout (30 minutes inactivity)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_destroy();
    header("Location: /E-commerce-Website/index.php");
    exit;
}
$_SESSION['last_activity'] = time();

// Get categories
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll() ?: [];

// Get products
$stmt = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id");
$products = $stmt->fetchAll() ?: [];

// Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['id'] ?? '';
    $product_name = $_POST['name'] ?? '';
    $product_price = isset($_POST['price']) ? floatval(str_replace(['R', ','], '', $_POST['price'])) : 0;
    $product_image = $_POST['image'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 1);

    if ($product_id) {
        // Check if product exists in database
        $stmt = $pdo->prepare("SELECT id, in_stock FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product_exists = $stmt->fetch();

        if ($product_exists && $product_exists['in_stock']) {
            if (isset($_SESSION['user_id'])) {
                $session_id = session_id(); // Note: Consider using user_id instead for consistency
                try {
                    // Insert or update cart item in database
                    $stmt = $pdo->prepare("INSERT INTO carts (session_id, product_id, quantity) VALUES (?, ?, ?) 
                                         ON DUPLICATE KEY UPDATE quantity = quantity + ?");
                    $stmt->execute([$session_id, $product_id, $quantity, $quantity]);

                    // Sync session data
                    if (!isset($_SESSION['cart'])) {
                        $_SESSION['cart'] = [];
                    }
                    if (isset($_SESSION['cart'][$product_id])) {
                        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                    } else {
                        $_SESSION['cart'][$product_id] = [
                            'id' => $product_id,
                            'name' => $product_name,
                            'price' => 'R ' . number_format($product_price, 2),
                            'image' => $product_image,
                            'quantity' => $quantity
                        ];
                    }
                    // Redirect with anchor to maintain position
                    $referer = $_SERVER['HTTP_REFERER'] ?? '/E-commerce-Website/peripherals.php';
                    $anchor = strpos($referer, '#') !== false ? parse_url($referer, PHP_URL_FRAGMENT) : 'product-' . $product_id;
                    header("Location: /E-commerce-Website/peripherals.php#" . $anchor);
                    exit;
                } catch (PDOException $e) {
                    die("Database error: " . $e->getMessage());
                }
            } else {
                header("Location: /E-commerce-Website/signin.php?message=Please log in to add items to your cart.");
                exit;
            }
        }
    }
}
// Get cart count
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Gaming Hardware Store - Peripherals</title>
<link rel="stylesheet" href="/E-commerce-Website/style.css"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://kit.fontawesome.com/a2e0f1f6c2.js" crossorigin="anonymous"></script>
<style>
    :root {
        --page-bg: #f5f6f8;
        --card-bg: #ffffff;
        --card-border: rgba(16,24,40,0.06);
        --card-shadow: 0 6px 18px rgba(16,24,40,0.04);
        --halo: #fff0e8;
        --peach: #ffd9c4;
        --icon-color: #ff7a00;
        --title-color: #0f1724;
        --sub-color: #6b7280;
        --radius: 16px;
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

        .account-link, .cart-link, .logout {
            text-decoration: none;
            color: #333;
            margin-left: 10px;
            transition: color 0.3s;
        }

        .account-link:hover, .cart-link:hover, .logout:hover {
            color: #ff6a00;
        }

        .cart-badge {
            background: #ff6a00;
            color: #fff;
            padding: 3px 8px;
            border-radius: 50%;
            font-size: 12px;
        }

    .features-wrap {
        background: var(--page-bg);
        padding: 28px 12px;
        font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    }

    .hero{
        background: var(--title-color);
        padding: 80px 20px;
        color: white;
        text-align: center;
    }

    .hero .peripherals {
        display: inline-block;
        background: #ff6600;
        color: #fff;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .hero h1 {
        text-align: center;
        color: white;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 26px;
        align-items: stretch;
    }

    .feature-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 34px 22px;
        text-align: center;
        box-shadow: 0 0 0 1px var(--card-border), var(--card-shadow);
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        min-height: 170px;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .feature-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 10px 30px rgba(16,24,40,0.07);
    }

    .icon-wrap {
        width: 78px;
        height: 78px;
        border-radius: 50%;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
    }

    .icon-wrap::before {
        content: "";
        position: absolute;
        width: 78px;
        height: 78px;
        border-radius: 50%;
        background: var(--halo);
        z-index: 0;
        filter: blur(0.4px);
    }

    .icon-inner {
        position: relative;
        z-index: 1;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--peach);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: var(--icon-color);
        box-shadow: 0 1px 0 rgba(255,255,255,0.6) inset;
    }

    .icon-inner i {
        display: block;
        line-height: 1;
    }

    .feature-title {
        margin: 0;
        font-weight: 700;
        font-size: 20px;
        color: var(--title-color);
        margin-bottom: 8px;
        letter-spacing: -0.2px;
    }

    .feature-sub {
        margin: 0;
        font-weight: 400;
        font-size: 13px;
        color: var(--sub-color);
        opacity: 0.95;
        max-width: 220px;
    }

    @media (max-width: 1000px) {
        .features-grid { grid-template-columns: repeat(2, 1fr); gap: 18px; }
        .feature-card { min-height: 150px; padding: 28px 18px; }
    }

    @media (max-width: 520px) {
        .features-grid { grid-template-columns: 1fr; gap: 14px; }
        .container { padding: 0 12px; }
        .feature-card { padding: 20px; min-height: auto; }
        .icon-wrap { width: 66px; height: 66px; }
        .icon-wrap::before { width: 66px; height: 66px; }
        .icon-inner { width: 44px; height: 44px; font-size: 18px; }
        .feature-title { font-size: 18px; }
        .feature-sub { font-size: 13px; }
    }

    .product-section {
        padding: 40px 60px;
    }

    .product-section h2 {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 8px;
        text-align: center;
    }

    .tagline {
        color: #555;
        font-size: 15px;
        margin-bottom: 28px;
        text-align: center;
    }

    .products-grid {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: stretch;
        gap: 24px;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 10px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .product-card {
        flex: 0 0 350px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 0 0 1px rgba(0,0,0,0.06), 0 8px 18px rgba(0,0,0,0.05);
        overflow: hidden;
        transition: all 0.3s ease;
        text-align: left;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }

    .product-image {
        position: relative;
    }

    .product-image a {
        display: block;
        cursor: pointer;
    }

    .product-image img {
        width: 100%;
        height: auto;
        display: block;
    }

    .discount-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #ff5a00;
        color: #fff;
        font-weight: 600;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 6px;
    }

    .product-info {
        padding: 18px 20px 22px;
    }

    .product-title {
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 6px;
    }

    .category {
        font-size: 13px;
        color: #6b7280;
        margin: 0 0 10px;
    }

    .rating {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 12px;
    }

    .stars i {
        color: #fbbf24;
        font-size: 14px;
    }

    .reviews {
        font-size: 13px;
        color: #555;
    }

    .price {
        margin-bottom: 16px;
    }

    .price .current {
        font-size: 20px;
        font-weight: 700;
        color: #111;
        margin-right: 10px;
    }

    .price .old {
        font-size: 14px;
        color: #999;
        text-decoration: line-through;
    }

    .add-to-cart {
        width: 100%;
        background: #000;
        color: #fff;
        border: none;
        padding: 12px 0;
        font-weight: 600;
        font-size: 15px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .add-to-cart:hover {
        background: #222;
    }

    .add-to-cart i {
        margin-right: 8px;
    }

    @media (max-width: 1100px) {
        .products-grid {
            flex-wrap: wrap;
        }
    }

    .guide-section {
        text-align: center;
        padding: 60px 20px;
        background: #fff5e6;
        font-family: 'Inter', sans-serif;
    }

    .guide-section h2 {
        font-size: 28px;
        margin-bottom: 40px;
        color: #111;
    }

    .guide-cards {
        display: flex;
        justify-content: center;
        align-items: stretch;
        gap: 24px;
        flex-wrap: wrap;
    }

    .guide-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        padding: 24px;
        width: 300px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        text-align: left;
    }

    .guide-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.1);
    }

    .guide-card h3 {
        font-size: 18px;
        margin-bottom: 16px;
        color: #f58301ff;
    }

    .guide-card ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .guide-card li {
        font-size: 14px;
        color: #555;
        margin-bottom: 8px;
        padding-left: 16px;
        position: relative;
    }

    .guide-card li::before {
        content: "•";
        color: #ff7b00ff;
        position: absolute;
        left: 0;
    }

    @media (max-width: 900px) {
        .guide-cards {
            flex-direction: column;
            align-items: center;
        }
    }

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
</head>
<body>
    <!-- Header -->
     <header class="header">
        <div class="logo" onclick="window.location.href='index.php'">Tech Giants</div>
        <nav class="nav">
            <a href="index.php">Home</a>
            <a href="shop.php">Shop</a>
            <a href="about.php">About Us</a>
            <a href="contact.php">Contact</a>
        </nav>
        <div class="user-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="account-link">👤 <?= htmlspecialchars($_SESSION['username']) ?></a>
            <?php else: ?>
                <a href="signin.php" class="account-link">👤 My Account</a>
            <?php endif; ?>
            <a href="cart.php" class="cart-link">🛒 <span class="cart-badge"><?= htmlspecialchars($cart_count) ?: 0 ?></span></a>
        </div>
    </header>

    <!-- HERO -->
    <section class="hero">
        <div class="container">
            <span class="peripherals">Premium Gaming Gear</span>
            <h1>Gaming Peripherals</h1>
            <p>Complete your gaming setup with premium peripherals designed for competitive gaming</p>
        </div>
    </section>

    <!-- Features -->
    <section class="features-wrap">
        <div class="container">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="icon-wrap">
                        <div class="icon-inner"><i class="fas fa-mouse"></i></div>
                    </div>
                    <h3 class="feature-title">Precision Control</h3>
                    <p class="feature-sub">High DPI sensors for accuracy</p>
                </div>
                <div class="feature-card">
                    <div class="icon-wrap">
                        <div class="icon-inner"><i class="fas fa-keyboard"></i></div>
                    </div>
                    <h3 class="feature-title">Mechanical Keys</h3>
                    <p class="feature-sub">Tactile feedback & durability</p>
                </div>
                <div class="feature-card">
                    <div class="icon-wrap">
                        <div class="icon-inner"><i class="fas fa-headphones"></i></div>
                    </div>
                    <h3 class="feature-title">Immersive Audio</h3>
                    <p class="feature-sub">7.1 surround sound</p>
                </div>
                <div class="feature-card">
                    <div class="icon-wrap">
                        <div class="icon-inner"><i class="fas fa-desktop"></i></div>
                    </div>
                    <h3 class="feature-title">RGB Lighting</h3>
                    <p class="feature-sub">Customizable aesthetics</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Products -->
    <section class="product-section">
        <h2>Essential Gaming Accessories</h2>
        <p class="tagline">Mice, keyboards, headsets, and more - everything you need for the perfect gaming setup</p>
        <div class="products-grid">
            <!-- Product 1 -->
            <div class="product-card">
                <div class="product-image">
                    <a href="/E-commerce-Website/product.php?id=5">
                        <img src="https://images.unsplash.com/photo-1629429408719-a64b3ae484e5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBrZXlib2FyZCUyMG1vdXNlfGVufDF8fHx8MTc1Njk0OTQ4OXww&ixlib=rb-4.1.0&q=80&w=1080" alt="RGB Gaming Keyboard & Mouse">
                    </a>
                </div>
                <div class="product-info">
                    <h3 class="product-title">RGB Gaming Keyboard & Mouse</h3>
                    <p class="category">peripherals</p>
                    <div class="rating">
                        <span class="stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <i class="fa-regular fa-star"></i>
                        </span>
                        <span class="reviews">(321)</span>
                    </div>
                    <div class="price">
                        <span class="current">R2,799.99</span>
                    </div>
                    <form method="POST" action="/E-commerce-Website/peripherals.php">
                        <input type="hidden" name="id" value="5">
                        <input type="hidden" name="name" value="RGB Gaming Keyboard & Mouse">
                        <input type="hidden" name="price" value="R2,799.99">
                        <input type="hidden" name="image" value="https://images.unsplash.com/photo-1629429408719-a64b3ae484e5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBrZXlib2FyZCUyMG1vdXNlfGVufDF8fHx8MTc1Njk0OTQ4OXww&ixlib=rb-4.1.0&q=80&w=1080">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" name="add_to_cart" class="add-to-cart">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </form>
                </div>
            </div>
            <!-- Product 2 -->
            <div class="product-card">
                <div class="product-image">
                    <a href="/E-commerce-Website/product.php?id=5">
                        <img src="https://images.unsplash.com/photo-1629429408719-a64b3ae484e5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBrZXlib2FyZCUyMG1vdXNlfGVufDF8fHx8MTc1Njk0OTQ4OXww&ixlib=rb-4.1.0&q=80&w=1080" alt="Gaming Headset Pro">
                    </a>
                    <span class="discount-badge">Out of Stock</span>
                </div>
                <div class="product-info">
                    <h3 class="product-title">Gaming Headset Pro</h3>
                    <p class="category">peripherals</p>
                    <div class="rating">
                        <span class="stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                            <i class="fa-regular fa-star"></i>
                        </span>
                        <span class="reviews">(178)</span>
                    </div>
                    <div class="price">
                        <span class="current">R1,899.99</span>
                    </div>
                    <form method="POST" action="/E-commerce-Website/peripherals.php">
                        <input type="hidden" name="id" value="5">
                        <input type="hidden" name="name" value="Gaming Headset Pro">
                        <input type="hidden" name="price" value="R1,899.99">
                        <input type="hidden" name="image" value="https://images.unsplash.com/photo-1629429408719-a64b3ae484e5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBrZXlib2FyZCUyMG1vdXNlfGVufDF8fHx8MTc1Njk0OTQ4OXww&ixlib=rb-4.1.0&q=80&w=1080">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" name="add_to_cart" class="add-to-cart">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </form>
                </div>
            </div>
            <!-- Product 3 -->
            <div class="product-card">
                <div class="product-image">
                    <a href="/E-commerce-Website/product.php?id=5">
                        <img src="https://images.unsplash.com/photo-1629429408719-a64b3ae484e5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBrZXlib2FyZCUyMG1vdXNlfGVufDF8fHx8MTc1Njk0OTQ4OXww&ixlib=rb-4.1.0&q=80&w=1080" alt="27&quot; QHD 240Hz Gaming Monitor">
                    </a>
                </div>
                <div class="product-info">
                    <h3 class="product-title">27&quot; QHD 240Hz Gaming Monitor</h3>
                    <p class="category">peripherals</p>
                    <div class="rating">
                        <span class="stars">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <i class="fas fa-star"></i><i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </span>
                        <span class="reviews">(245)</span>
                    </div>
                    <div class="price">
                        <span class="current">R8,999.99</span>
                    </div>
                    <form method="POST" action="/E-commerce-Website/peripherals.php">
                        <input type="hidden" name="id" value="5">
                        <input type="hidden" name="name" value="27&quot; QHD 240Hz Gaming Monitor">
                        <input type="hidden" name="price" value="R8,999.99">
                        <input type="hidden" name="image" value="https://images.unsplash.com/photo-1629429408719-a64b3ae484e5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBrZXlib2FyZCUyMG1vdXNlfGVufDF8fHx8MTc1Njk0OTQ4OXww&ixlib=rb-4.1.0&q=80&w=1080">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" name="add_to_cart" class="add-to-cart">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Guide Section -->
    <section class="guide-section">
        <h2>Why Choose Our Peripherals?</h2>
        <div class="guide-cards">
            <div class="guide-card">
                <h3>Mice & Keyboards</h3>
                <ul>
                    <li>High DPI sensors (up to 25,000)</li>
                    <li>Mechanical switches</li>
                    <li>Customizable RGB lighting</li>
                    <li>Programmable buttons</li>
                </ul>
            </div>
            <div class="guide-card">
                <h3>Audio & Headsets</h3>
                <ul>
                    <li>7.1 surround sound</li>
                    <li>Noise-canceling microphones</li>
                    <li>Comfortable for long sessions</li>
                    <li>Multi-platform support</li>
                </ul>
            </div>
            <div class="guide-card">
                <h3>Premium Quality</h3>
                <ul>
                    <li>Durable construction</li>
                    <li>Extended warranties</li>
                    <li>Professional-grade performance</li>
                    <li>Trusted brands only</li>
                </ul>
            </div>
        </div>
    </section>

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
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="/E-commerce-Website/index.php">Home</a></li>
                    <li><a href="/E-commerce-Website/about.php">Why Choose Us</a></li>
                    <li><a href="/E-commerce-Website/shop.php">Shop</a></li>
                    <li><a href="/E-commerce-Website/contact.php">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Categories</h4>
                <ul>
                    <li><a href="gaming-pcs.php">Gaming PCs</a></li>
                    <li><a href="graphics-cards.php">Graphics Cards</a></li>
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
       
    </footer>

    <script>
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mouseenter', () => { card.style.transform = 'translateY(-6px) scale(1.01)'; });
            card.addEventListener('mouseleave', () => { card.style.transform = ''; });
        });
    </script>
</body>
</html>
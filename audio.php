<?php
session_start();
require_once 'components/db.php';

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
            $session_id = session_id();
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
            } catch (PDOException $e) {
                die("Database error: " . $e->getMessage());
            }
        }
    }
    header("Location: shop.php");
    exit;
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
<title>Gaming Hardware Store</title>
<link rel="stylesheet" href="style.css"/>
 <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://kit.fontawesome.com/a2e0f1f6c2.js" crossorigin="anonymous"></script>
<style>
    :root{
  --page-bg: #f5f6f8;       /* light off-white background like screenshot */
  --card-bg: #ffffff;
  --card-border: rgba(16,24,40,0.06); /* subtle thin border */
  --card-shadow: 0 6px 18px rgba(16,24,40,0.04);
  --halo: #fff0e8;         /* faint peach halo behind icon */
  --peach: #ffd9c4;        /* inner peach circle */
  --icon-color: #ff7a00;   /* orange icon */
  --title-color: #0f1724;  /* almost black title */
  --sub-color: #6b7280;    /* muted grey subtitle */
  --radius: 16px;
}

/* Page / wrapper */
.features-wrap {
  background: var(--page-bg);
  padding: 28px 12px; /* top/bottom spacing like screenshot */
  font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
}

/* centered container with same visual width as screenshot */
.container {
  max-width: 1220px;
  margin: 0 auto;
}

.container h1{
    text-align: center;
    color:white;
}
/* four equal cards layout */
.features-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 26px; /* space between cards */
  align-items: stretch;
}

/* Card */
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
  min-height: 170px; /* ensures uniform height */
  transition: transform .18s ease, box-shadow .18s ease;
}

/* Hover lift */
.feature-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 30px rgba(16,24,40,0.07);
}

/* Icon wrapper: faint halo circle behind the peach circle */
.icon-wrap {
  width: 78px;
  height: 78px;
  border-radius: 50%;
  position: relative;
  display:flex;
  align-items:center;
  justify-content:center;
  margin-bottom: 18px;
}

/* halo using ::before */
.icon-wrap::before{
  content: "";
  position: absolute;
  width: 78px;
  height: 78px;
  border-radius: 50%;
  background: var(--halo);
  z-index: 0;
  filter: blur(0.4px);
}

/* inner circle containing icon */
.icon-inner {
  position: relative;
  z-index: 1;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: var(--peach);
  display:flex;
  align-items:center;
  justify-content:center;
  font-size: 22px;
  color: var(--icon-color);
  box-shadow: 0 1px 0 rgba(255,255,255,0.6) inset;
}

/* icon element */
.icon-inner i {
  display:block;
  line-height:1;
}

/* Title */
.feature-title {
  margin: 0;
  font-weight: 700;
  font-size: 20px;
  color: var(--title-color);
  margin-bottom: 8px;
  letter-spacing: -0.2px;
}

/* subtitle/description */
.feature-sub {
  margin: 0;
  font-weight: 400;
  font-size: 13px;
  color: var(--sub-color);
  opacity: 0.95;
  max-width: 220px;
}

/* Responsive - 2 columns on tablet, single on small screens */
@media (max-width: 1000px) {
  .features-grid { grid-template-columns: repeat(2, 1fr); gap: 18px; }
  .feature-card { min-height: 150px; padding: 28px 18px; }
}

@media (max-width: 520px){
  .features-grid { grid-template-columns: 1fr; gap: 14px; }
  .container { padding: 0 12px; }
  .feature-card { padding: 20px; min-height: auto; }
  .icon-wrap { width: 66px; height: 66px; }
  .icon-wrap::before { width: 66px; height: 66px; }
  .icon-inner { width: 44px; height: 44px; font-size: 18px; }
  .feature-title { font-size: 18px; }
  .feature-sub { font-size: 13px; }
}

        .audio {
            display: inline-block;
            background: #ff6600;
            color: #fff;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 10px;
        }
/* Layout for the product section */

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

    /* ---- Products Grid ---- */
    .products-grid {
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: stretch;
      gap: 24px;
      flex-wrap: nowrap; /* keeps them in one line */
      overflow-x: auto;  /* allows scrolling if too wide */
      padding-bottom: 10px;
      max-width: 1200px;
      margin: 0 auto; 
    }

    /* ---- Product Card ---- */
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

    /* ---- Image ---- */
    .product-image {
      position: relative;
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

    /* ---- Info ---- */
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

    /* ---- Rating ---- */
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

    /* ---- Price ---- */
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

    /* ---- Button ---- */
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

    /* ---- Responsive ---- */
    @media (max-width: 1100px) {
      .products-grid {
        flex-wrap: wrap;
      }
    }

    
/* ===== Section Styling ===== */
.guide-section {
  max-width: auto;
  margin: 60px auto;
  padding: 40px 20px;
  text-align: center;
  background: #fff5e6;
}

.guide-section h2 {
  font-size: 22px;
  font-weight: 700;
  margin-bottom: 36px;
}

/* ===== Cards Container ===== */
.guide-cards {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  gap: 40px;
  flex-wrap: wrap;
}

/* ===== Each Card ===== */
.guide-card {
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  padding: 24px 28px;
  flex: 1 1 380px;
  max-width: 420px;
  text-align: left;
}

.guide-card h3 {
  color: #ff6600;
  font-size: 18px;
  margin-bottom: 16px;
}

.guide-card ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.guide-card ul li {
  color: #374151;
  font-size: 15px;
  line-height: 1.8;
  position: relative;
  padding-left: 14px;
}

.guide-card ul li::before {
  content: "•";
  color: #ff6600;
  position: absolute;
  left: 0;
}

/* ===== Responsive ===== */
@media (max-width: 900px) {
  .guide-cards {
    flex-direction: column;
    align-items: center;
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
    padding: 2rem 1rem 2rem;
    margin: 2rem auto 0 auto; /* Center horizontally */
    border-radius: 0.5rem 0.5rem 0 0;
    max-width: 1000px; /* Optional: make it narrower for better centering */
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
            <a href="signin.php" class="account-link">👤 My Account</a>
            <a href="cart.php" class="cart-link">🛒 <span class="cart-badge"><?= htmlspecialchars($cart_count) ?: 0 ?></span></a>
     </div>
    </header>

<!-- HERO -->
<section class="hero">
  <div class="container">
     <span class="audio">Premium Gaming Audio</span>
    <h1>Gaming Audio</h1>
    <p>Immerse yourself in crystal-clear gaming audio with premium headsets, microphones, and speakers</p>
    </div>
</section>

<!--Features-->
<section class="features-wrap">
  <div class="container">
    <div class="features-grid">
      <div class="feature-card">
        <div class="icon-wrap">
          <div class="icon-inner"><i class="fas fa-headphones"></i></div>
        </div>
        <h3 class="feature-title">7.1 Surround</h3>
        <p class="feature-sub">Immersive spatial audio</p>
      </div>

      <div class="feature-card">
        <div class="icon-wrap">
          <div class="icon-inner"><i class="fas fa-microphone-alt"></i></div>
        </div>
        <h3 class="feature-title">Clear Comms</h3>
        <p class="feature-sub">Noise-canceling mics</p>
      </div>

      <div class="feature-card">
        <div class="icon-wrap">
          <div class="icon-inner"><i class="fas fa-volume-up"></i></div>
        </div>
        <h3 class="feature-title">Premium Sound</h3>
        <p class="feature-sub">Studio-quality drivers</p>
      </div>

      <div class="feature-card">
        <div class="icon-wrap">
          <div class="icon-inner"><i class="fas fa-wifi"></i></div>
        </div>
        <h3 class="feature-title">Low Latency</h3>
        <p class="feature-sub">Wireless freedom</p>
      </div>
    </div>
  </div>
</section>

<section class="product-section">
    <h2>Premium Audio Equipment</h2>
    <p class="tagline"> Headsets, microphones, and speakers engineered for professional gaming and streaming</p>

    <div class="products-grid">

      <!-- Product 1 -->
      <div class="product-card">
        <div class="product-image">
          <img src="https://images.unsplash.com/photo-1616081118924-20e97edd3b3d?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBoZWFkc2V0JTIwYXVkaW98ZW58MXx8fHwxNzU3MDE3NjE1fDA&ixlib=rb-4.1.0&q=80&w=1080" alt="Audio ">
       <span class="discount-badge">Out of stock</span>
        </div>

        <div class="product-info">
          <h3 class="product-title"> Gaming Headset Pro</h3>
          <p class="category">audio</p>

          <div class="rating">
            <span class="stars">
              <i class="fas fa-star"></i><i class="fas fa-star"></i>
              <i class="fas fa-star"></i><i class="fas fa-star"></i>
              <i class="fa-regular fa-star"></i>
            </span>
            <span class="reviews">(178)</span>
          </div>

          <div class="price">
            <span class="current">R1,999.99</span>
            
          </div>

          <form method="POST" action="shop.php">
            <input type="hidden" name="id" value="1">
            <input type="hidden" name="name" value="TechGiant Gaming PC Elite">
            <input type="hidden" name="price" value="R44,999.99">
            <input type="hidden" name="image" value="your-image.jpg">
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
          <img src="https://images.unsplash.com/photo-1616081118924-20e97edd3b3d?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBoZWFkc2V0JTIwYXVkaW98ZW58MXx8fHwxNzU3MDE3NjE1fDA&ixlib=rb-4.1.0&q=80&w=1080" alt="Audio">
         <span class="discount-badge">-14%</span>
        </div>

        <div class="product-info">
          <h3 class="product-title">TechGiant Wireless Pro Headset</h3>
          <p class="category">audio</p>

          <div class="rating">
            <span class="stars">
              <i class="fas fa-star"></i><i class="fas fa-star"></i>
              <i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
              <i class="fa-regular fa-star"></i>
            </span>
            <span class="reviews">(289)</span>
          </div>

          <div class="price">
            <span class="current">R2,999.99</span>
         <span class="old">R3,599.99</span>
          </div>

          <form method="POST" action="shop.php">
            <input type="hidden" name="id" value="2">
            <input type="hidden" name="name" value="TechGiant Gaming PC Ultra">
            <input type="hidden" name="price" value="R59,999.99">
            <input type="hidden" name="image" value="your-image2.jpg">
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
          <img src="https://images.unsplash.com/photo-1696710240292-05aad88b94b8?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBtb25pdG9yJTIwc2V0dXB8ZW58MXx8fHwxNzU3MDE3NjE0fDA&ixlib=rb-4.1.0&q=80&w=1080" alt="Gaming Monitors">
        
        </div>

        <div class="product-info">
          <h3 class="product-title">TechGinat Studio Streaming Mic</h3>
          <p class="category">audio</p>

          <div class="rating">
            <span class="stars">
              <i class="fas fa-star"></i><i class="fas fa-star"></i>
              <i class="fas fa-star"></i><i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </span>
            <span class="reviews">(245)</span>
          </div>

          <div class="price">
            <span class="current">R3,499.99</span>
          </div>

          <form method="POST" action="shop.php">
            <input type="hidden" name="id" value="3">
            <input type="hidden" name="name" value="TechGiant Gaming PC Titan">
            <input type="hidden" name="price" value="R74,999.99">
            <input type="hidden" name="image" value="your-image3.jpg">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" name="add_to_cart" class="add-to-cart">
              <i class="fas fa-shopping-cart"></i> Add to Cart
            </button>
          </form>
        </div>
      </div>

    </div>
  </section>

<section class="guide-section">
  <h2>Gaming Monitor Buying Guide</h2>

  <div class="guide-cards">
    <!-- Left Card -->
    <div class="guide-card">
      <h3>Resolution & Performance</h3>
      <ul>
        <li>1080p: Best for competitive gaming (240Hz+)</li>
        <li>1440p: Sweet spot for most gamers</li>
        <li> 4K: Ultimate visual quality (high-end GPU needed)</li>
        <li> Ultrawide: Immersive gaming experience</li>
      </ul>
    </div>

    <!-- Right Card -->
    <div class="guide-card">
      <h3>Key Features</h3>
      <ul>
        <li>High refresh rate for smooth gameplay</li>
        <li> Low response time (1ms ideal)</li>
        <li>G-Sync/FreeSync for no screen tearing</li>
        <li>HDR for enhanced color & contrast</li>
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
/* optional micro interaction: subtle scale in addition to hover lift */
document.querySelectorAll('.feature-card').forEach(card => {
  card.addEventListener('mouseenter', () => { card.style.transform = 'translateY(-6px) scale(1.01)'; });
  card.addEventListener('mouseleave', () => { card.style.transform = ''; });
});
</script>
</body> 
</html>
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

 .peripherals {
            display: inline-block;
            background: #ff6600;
            color: #fff;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 10px;
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

/* ---- Cards Container ---- */
.guide-cards {
  display: flex;
  justify-content: center;
  align-items: stretch;
  gap: 24px;
  flex-wrap: wrap; /* Makes it responsive */
}

/* ---- Each Card ---- */
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
/* ===== Responsive ===== */
@media (max-width: 900px) {
  .guide-cards {
    flex-direction: column;
    align-items: center;
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
     <span class="peripherals">Premium Gaming Gear</span>
    <h1>Gaming Peripherals</h1>
    <p>Complete your gaming setup with premium peripherals designed for competitive gaming</p>
    </div>
</section>

<!--Features-->
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

<section class="product-section">
    <h2>Essential Gaming Accessories</h2>
    <p class="tagline">Mice, keyboards, headsets, and more - everything you need for the perfect gaming setup</p>

    <div class="products-grid">

      <!-- Product 1 -->
      <div class="product-card">
        <div class="product-image">
          <img src="https://images.unsplash.com/photo-1629429408719-a64b3ae484e5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBrZXlib2FyZCUyMG1vdXNlfGVufDF8fHx8MTc1Njk0OTQ4OXww&ixlib=rb-4.1.0&q=80&w=1080" alt="Gaming Monitors">
       
        </div>

        <div class="product-info">
          <h3 class="product-title"> RGB Gaming Keyboard & Mouse </h3>
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
          <img src="https://images.unsplash.com/photo-1629429408719-a64b3ae484e5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBrZXlib2FyZCUyMG1vdXNlfGVufDF8fHx8MTc1Njk0OTQ4OXww&ixlib=rb-4.1.0&q=80&w=1080" alt="Gaming Monitors">
         <span class="discount-badge"> Out of Stock</span>
        </div>

        <div class="product-info">
          <h3 class="product-title">Gaming Headset Pro</h3>
          <p class="category">audio</p>

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
          <img src="https://images.unsplash.com/photo-1629429408719-a64b3ae484e5?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBrZXlib2FyZCUyMG1vdXNlfGVufDF8fHx8MTc1Njk0OTQ4OXww&ixlib=rb-4.1.0&q=80&w=1080">
        
        </div>

        <div class="product-info">
          <h3 class="product-title">27" QHD 240Hz Gaming monitor</h3>
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
  <h2>Why Choose Our Peripherals?</h2>

  <div class="guide-cards">
    <!-- Left Card -->
    <div class="guide-card">
      <h3>Mice & Keyboards</h3>
      <ul>
        <li>High DPI sensors (up to 25,000)</li>
        <li>Mechanical switches</li>
        <li>Customizable RGB lighting</li>
        <li>Programmable buttons</li>
      </ul>
    </div>

    <!-- Middle Card -->
    <div class="guide-card">
      <h3>Audio & Headsets</h3>
      <ul>
        <li>7.1 surround sound</li>
        <li>Noise-canceling microphones</li>
        <li>Comfortable for long sessions</li>
        <li>Multi-platform support</li>
      </ul>
    </div>

    <!-- Right Card -->
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

<script>
/* optional micro interaction: subtle scale in addition to hover lift */
document.querySelectorAll('.feature-card').forEach(card => {
  card.addEventListener('mouseenter', () => { card.style.transform = 'translateY(-6px) scale(1.01)'; });
  card.addEventListener('mouseleave', () => { card.style.transform = ''; });
});
</script>
</body> 
</html>
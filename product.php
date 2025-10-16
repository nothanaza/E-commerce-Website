<?php
session_start();
require_once 'components/db.php';

// Get product ID from URL
$id = $_GET['id'] ?? null;
$product = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}

// Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['id'] ?? '';
    $product_name = $_POST['name'] ?? '';
    $product_price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $product_image = $_POST['image'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 1);

    if ($product_id) {
        // Check if product exists and is in stock
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
    header("Location: product.php?id=$product_id");
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
  <title>
    <?= $product ? htmlspecialchars($product['name']) : 'Product Not Found' ?> - Gaming Hardware Store
  </title>
  <link rel="stylesheet" href="style.css" />
  <script src="https://cdn.tailwindcss.com"></script>
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

<div class="container" style="padding:40px 0">
  <?php if ($product): ?>
    <a href="shop.php" class="back-link">← Back to Shop</a>

    
      <div class="product-detail">

  <!-- LEFT: Image + Thumbnails -->
  <div class="product-detail-img">
    <img id="main-img" src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
    <?php if (!$product['in_stock']): ?>
      <div class="badge out">Out of Stock</div>
    <?php elseif ($product['discount']): ?>
      <div class="badge"><?= htmlspecialchars($product['discount']) ?></div>
    <?php endif; ?>

    <!-- Thumbnails -->
    <div class="thumbs">
      <img src="<?= htmlspecialchars($product['image']) ?>" alt="thumb 1" onclick="swapImage(this)">
      <img src="<?= htmlspecialchars($product['image']) ?>" alt="thumb 2" onclick="swapImage(this)">
      <img src="<?= htmlspecialchars($product['image']) ?>" alt="thumb 3" onclick="swapImage(this)">
    </div>
  </div>

  <!-- RIGHT: Product Info -->
  <div class="product-detail-info">
    <h1><?= htmlspecialchars($product['name']) ?></h1>
    <div class="cat"><?= htmlspecialchars($product['category_name']) ?></div>

    <!-- Rating -->
    <div class="rating">
      <?= str_repeat("⭐", floor($product['stars'])) ?>
      <?php if ($product['stars'] - floor($product['stars']) >= 0.5): ?>⭐<?php endif; ?>
      <span class="reviews">(<?= $product['reviews'] ?> reviews)</span>
    </div>

    <!-- Price -->
    <div class="price-wrap" style="display:flex;align-items:center;gap:16px;">
      <div class="price" id="total-price" data-unit="<?= $product['price'] ?>">
  R<?= number_format($product['price'], 2) ?>
</div>
      <?php if ($product['old_price']): ?>
        <div class="old">R<?= number_format($product['old_price'], 2) ?></div>
        <div class="badge save">Save <?= htmlspecialchars($product['discount']) ?></div>
      <?php endif; ?>
    </div>

    <!-- Short Description -->
    <p class="short-desc">
      Ultimate gaming performance with the latest hardware. 
      Built for serious gamers who demand the best performance in AAA titles and competitive gaming.
    </p>

    <!-- Key Features -->
    <div class="features">
      <h3>Key Features</h3>
      <ul>
        <li>Latest Intel Core i9 processor</li>
        <li>RTX 4080 Graphics Card</li>
        <li>32GB DDR5 RAM</li>
        <li>1TB NVMe SSD</li>
        <li>Liquid cooling system</li>
        <li>RGB lighting</li>
        <li>Pre-installed Windows 11</li>
      </ul>
    </div>

    <!-- Quantity + Add to Cart -->
    <?php if ($product['in_stock']): ?>
      <form method="POST" action="product.php?id=<?= htmlspecialchars($product['id']) ?>" class="cart-form">
        <label for="qty">Quantity:</label>
        <div class="qty-control">
          <button type="button" class="qty-bt" onclick="changeQty(-1)">-</button>
          <input type="number" id="qty" name="quantity" value="1" min="1">
          <button type="button" class="qty-bt" onclick="changeQty(1)">+</button>
        </div>

        <input type="hidden" name="add_to_cart" value="1">
        <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
        <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']) ?>">
        <input type="hidden" name="price" value="<?= $product['price'] ?>">
        <input type="hidden" name="image" value="<?= htmlspecialchars($product['image']) ?>">

        <div class="cart-actions">
          <button type="submit" class="bt primary">
  🛒 Add to Cart - <span id="button-price">R<?= number_format($product['price'], 2) ?></span>
</button>

          <button type="button" class="bt icon">♡</button>
          <button type="button" class="bt icon">⤴</button>
        </div>
      </form>

      <p class="stock-info in">🟢 In Stock - Ready to Ship</p>
    <?php else: ?>
      <button class="bt disabled" disabled>Out of Stock</button>
    <?php endif; ?>
  </div>
</div>



  <?php else: ?>
    <h2>Product not found ❌</h2>
    <p><a href="shop.php">← Back to shop</a></p>
  <?php endif; ?>
</div>
<!-- Tabs Section -->
<div class="tabs-wrapper">
  <div class="tab-buttons">
    <button class="tab-btn active" onclick="openTab(event, 'tab-specs')">Specifications</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-features')">Features</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-reviews')">Reviews</button>
  </div>

  <div class="tab-panels">

    <!-- Specifications -->
    <div class="tab-panel active" id="tab-specs">
      <div class="spec-grid">
        <div><strong>Switch Type:</strong><span>Mechanical Blue</span></div>
        <div><strong>Key Layout:</strong><span>104-key</span></div>
        <div><strong>Mouse DPI:</strong><span>12000</span></div>
        <div><strong>Polling Rate:</strong><span>1000Hz</span></div>
        <div><strong>Cable:</strong><span>USB 2.0</span></div>
        <div><strong>Compatibility:</strong><span>Windows, Mac, Linux</span></div>
      </div>
    </div>

    <!-- Features -->
    <div class="tab-panel" id="tab-features">
      <h4>Product Features</h4>
      <ul class="feature-list">
        <li>Mechanical Blue Switches</li>
        <li>Per-key RGB Lighting</li>
        <li>12000 DPI Gaming Mouse</li>
        <li>Programmable Macros</li>
        <li>Braided Cables</li>
        <li>Gaming Software Included</li>
      </ul>
    </div>

    <!-- Reviews -->
    <div class="tab-panel" id="tab-reviews">
      <p>⭐ This product is amazing! – by GamerX</p>
      <p>⭐⭐⭐ Solid performance, worth the price. – by ElitePlayer</p>
      <p>⭐⭐⭐⭐⭐ Absolutely love it! – by StreamQueen</p>
    </div>

  </div>
</div>

<!-- Footer -->
  <footer class="site-footer">
  <div class="footer-top">
    <!-- Column 1: Logo + Info -->
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

    <!-- Column 2: About -->
    <div class="footer-col">
      <h4>Quick links</h4>
      <ul>
        <li><a href="#">Our Story</a></li>
        <li><a href="#">Why Choose Us</a></li>
        <li><a href="#">Gaming Community</a></li>
        <li><a href="#">Expert Reviews</a></li>
        <li><a href="#">Careers</a></li>
      </ul>
    </div>

    <!-- Column 3: Quick Links -->
    <div class="footer-col">
      <h4>Categories</h4>
      <ul>
        <li><a href="#">Gaming PCs</a></li>
        <li><a href="#">Graphics Cards</a></li>
        <li><a href="#">Gaming Peripherals</a></li>
        <li><a href="#">Special Deals</a></li>
        <li><a href="#">Build Configurator</a></li>
      </ul>
    </div>

    <!-- Column 4: Connect -->
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

  <!-- Middle Row -->
  <div class="footer-middle">
    <p>© 2024 Tech Giants. All rights reserved.</p>
    <div class="footer-links">
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
      <a href="#">Shipping Info</a>
    </div>
    <p class="powered">Powered by <span>Gaming Excellence</span></p>
  </div>

  <!-- Newsletter -->
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
function openTab(evt, tabId) {
  // Hide all panels
  document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
  
  // Remove active from all buttons
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

  // Show selected panel and button
  document.getElementById(tabId).classList.add('active');
  evt.currentTarget.classList.add('active');
}


function changeQty(change) {
  const qtyInput = document.getElementById('qty');
  let qty = parseInt(qtyInput.value) || 1;
  qty += change;
  if (qty < 1) qty = 1;
  qtyInput.value = qty;

  updatePrice(qty);  
}

function updatePrice(qty) {
  const priceEl = document.getElementById('total-price');
  const buttonPriceEl = document.getElementById('button-price'); 

  if (!priceEl || !buttonPriceEl) return;

  const unit = parseFloat(priceEl.dataset.unit);
  if (isNaN(unit)) return;

  const total = unit * qty;
  const formatted = "R" + total.toFixed(2);

  priceEl.textContent = formatted;
  buttonPriceEl.textContent = formatted;
}

function swapImage(element) {
  const mainImg = document.getElementById('main-img');
  mainImg.src = element.src;
}

tailwind.config = {
      theme: {
        extend: {
          colors: {
            secondary: "#ff6a00", // green
            "secondary-foreground": "#000"
          }
        }
      }
    }
 
    document.getElementById('newsletterForm')?.addEventListener('submit', function(e) {
      e.preventDefault();
      const email = document.getElementById('email')?.value;
      const message = document.getElementById('message') || document.createElement('p');
      message.id = 'message';
      this.appendChild(message);

      if (!email || !email.includes('@')) {
        message.textContent = "❌ Please enter a valid email.";
        message.className = "text-red-500 text-sm mt-2";
      } else {
        message.textContent = "✅ Thank you for subscribing!";
        message.className = "text-green-500 text-sm mt-2";
        document.getElementById('email').value = "";
      }
    });

</script>


</body>
</html>
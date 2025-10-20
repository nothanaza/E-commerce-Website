<?php
// Start session
session_start();
include 'components/db.php';

$session_id = session_id();

// Load cart from DB
try {
    $stmt = $pdo->prepare("SELECT c.id AS cart_id, p.id AS product_id, p.name, p.price, p.image, c.quantity 
                           FROM carts c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.session_id = ?");
    $stmt->execute([$session_id]);
    $cart_items_db = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Sync session
$_SESSION['cart'] = [];
foreach ($cart_items_db as $item) {
    $_SESSION['cart'][$item['product_id']] = [
        'id' => $item['product_id'],
        'name' => $item['name'],
        'price' => $item['price'],
        'image' => $item['image'],
        'quantity' => $item['quantity']
    ];
}
$cart_items = $_SESSION['cart'];

// Update quantity
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'] ?? '';
    $new_quantity = intval($_POST['quantity'] ?? 0);

    if ($product_id && $new_quantity >= 0) {
        if ($new_quantity <= 0) {
            $stmt = $pdo->prepare("DELETE FROM carts WHERE session_id = ? AND product_id = ?");
            $stmt->execute([$session_id, $product_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO carts (session_id, product_id, quantity) VALUES (?, ?, ?)
                                   ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)");
            $stmt->execute([$session_id, $product_id, $new_quantity]);
        }
    }
    header("Location: cart.php");
    exit;
}

// Remove item
if (isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'] ?? '';
    if ($product_id) {
        $stmt = $pdo->prepare("DELETE FROM carts WHERE session_id = ? AND product_id = ?");
        $stmt->execute([$session_id, $product_id]);
    }
    header("Location: cart.php");
    exit;
}

// Totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = $subtotal > 5000 ? 0 : 200;
$total = $subtotal + $shipping;
$cart_count = array_sum(array_column($cart_items, 'quantity'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shopping Cart</title>
 <link rel="stylesheet" href="style.css">
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
      <a href="cart.php" class="cart-link">🛒 <span class="cart-badge"><?= $cart_count ?></span></a>
    </div>
  </header>

  <main class="cart-page">
    <div class="cart-header">
      <a href="shop.php" class="back-link">← Back</a>
      <h1>Shopping Cart</h1>
      <p><?= $cart_count ?> item(s) in your cart</p>
    </div>

    <?php if (empty($cart_items)): ?>
      <div class="empty-cart">
        <p>🛒 Your cart is empty</p>
        <a href="shop.php" class="btn-primary">Browse Products</a>
      </div>
    <?php else: ?>
      <div class="cart-layout">
        
        <!-- Cart Items -->
        <section class="cart-items">
          <?php foreach ($cart_items as $item): ?>
            <div class="cart-item">
              <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">

              <div class="item-info">
                <h3><?= htmlspecialchars($item['name']) ?></h3>
                <p class="category">Gaming Gear</p>

                <div class="quantity-controls">
                  <form method="POST">
                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                    <input type="hidden" name="quantity" value="<?= max(0, $item['quantity'] - 1) ?>">
                    <button type="submit" name="update_quantity">−</button>
                  </form>
                  <span><?= $item['quantity'] ?></span>
                  <form method="POST">
                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                    <input type="hidden" name="quantity" value="<?= $item['quantity'] + 1 ?>">
                    <button type="submit" name="update_quantity">+</button>
                  </form>
                </div>
              </div>

              <div class="item-price">
                <strong>R <?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                <p>R <?= number_format($item['price'], 2) ?> each</p>
              </div>

              <form method="POST">
                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                <button type="submit" name="remove_item" class="remove-btn">🗑</button>
              </form>
            </div>
          <?php endforeach; ?>
        </section>

        <!-- Order Summary -->
        <aside class="order-summary">
          <h2>Order Summary</h2>
          <div class="summary-row">
            <span>Subtotal</span>
            <span>R <?= number_format($subtotal, 2) ?></span>
          </div>
          <div class="summary-row">
            <span>Shipping</span>
            <span class="<?= $shipping == 0 ? 'free' : '' ?>">
              <?= $shipping == 0 ? 'FREE' : 'R ' . number_format($shipping, 2) ?>
            </span>
          </div>
          <div class="summary-row total">
            <span>Total</span>
            <span>R <?= number_format($total, 2) ?></span>
          </div>
          <a href="checkout.php" class="checkout-btn">Proceed to Checkout →</a>
          <p class="note">
            <?= $shipping == 0 ? "You qualify for free shipping!" : "Add R " . number_format(5000 - $subtotal, 2) . " more for free shipping" ?>
          </p>
        </aside>

      </div>
    <?php endif; ?>
  </main>
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
      <h4>Quick Links</h4>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">Why Choose Us</a></li>
        <li><a href="shop.php">Shop</a></li>
         <li><a href="contact.php">Contact Us</a></li>
      </ul>
    </div>

    <!-- Column 3: Quick Links -->
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
  
</footer>
</body>
</html>

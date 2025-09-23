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


<style>
body {
  font-family: 'Inter', sans-serif;
  margin: 0;
  background: #f9fafb;
  color: #111827;
}

.header {
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo { font-weight: 700; color: #f97316; cursor: pointer; }

.nav a {
  margin: 0 10px;
  text-decoration: none;
  font-weight: 500;
  color: #374151;
}

.nav a:hover { color: #f97316; }

.user-actions a { margin-left: 1rem; text-decoration: none; color: #374151; }

.cart-badge {
  background: #f97316;
  color: #fff;
  border-radius: 50%;
  padding: 2px 8px;
  font-size: 0.8rem;
}

.cart-page { padding: 2rem; }

.cart-header { margin-bottom: 1.5rem; }
.cart-header h1 { margin: 0; font-size: 1.75rem; }
.cart-header p { color: #6b7280; }

.cart-layout {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
}

/* Cart Item */
.cart-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #fff;
  padding: 1rem;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  margin-bottom: 1rem;
}

.cart-item img {
  width: 80px; height: 80px; border-radius: 0.5rem; object-fit: cover;
}

.item-info { flex: 1; margin-left: 1rem; }
.item-info h3 { margin: 0; font-size: 1.1rem; font-weight: 600; }
.item-info .category { color: #6b7280; font-size: 0.9rem; }

.quantity-controls {
  display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;
}

.quantity-controls button {
  background: #f3f4f6; border: 1px solid #d1d5db; padding: 0.3rem 0.75rem;
  border-radius: 0.375rem; cursor: pointer; font-weight: 600;
}

.item-price { text-align: right; }
.item-price strong { color: #f97316; display: block; }
.item-price p { color: #6b7280; font-size: 0.9rem; }

.remove-btn { border: none; background: none; color: #ef4444; cursor: pointer; }

/* Order Summary */
.order-summary {
  background: #fff;
  padding: 1.5rem;
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
}

.order-summary h2 { margin-bottom: 1rem; }

.summary-row {
  display: flex; justify-content: space-between; margin: 0.5rem 0;
}

.summary-row.total { font-weight: 700; border-top: 1px solid #e5e7eb; padding-top: 0.5rem; }

.summary-row .free { color: #10b981; font-weight: 600; }

.checkout-btn {
  width: 100%; 
  background: #f97316; 
  color: #fff; 
  border: none;
  padding: 0.75rem; 
  border-radius: 0.5rem; 
  font-weight: 600;
  cursor: pointer; 
  margin-top: 1rem;
}
.checkout-btn:hover { background: #ea580c; }

.note { margin-top: 1rem; font-size: 0.9rem; color: #374151; }

</style>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shopping Cart</title>
  <link rel="stylesheet" href="styles/cart.css">
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
</body>
</html>

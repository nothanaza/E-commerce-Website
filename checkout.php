<?php
session_start();
include 'components/db.php';

$cart_items = $_SESSION['cart'] ?? [];
if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($cart_items as $item) {
    $price = floatval(str_replace(['R ', ','], '', $item['price']));
    $subtotal += $price * $item['quantity'];
}
$shipping = $subtotal > 5000 ? 0 : 200;
$total = $subtotal + $shipping;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout - Tech Giants</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="checkout-page">
    <h1>Checkout</h1>

    <!-- Shipping Form -->
    <form method="POST" action="process_checkout.php" class="checkout-form">
      <h2>Shipping Information</h2>
      <input type="text" name="fullname" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="text" name="address" placeholder="Street Address" required>
      <input type="text" name="city" placeholder="City" required>
      <input type="text" name="zip" placeholder="ZIP / Postal Code" required>

      <button type="submit">Proceed to Payment →</button>
    </form>

    <!-- Order Summary -->
    <aside class="order-summary">
      <h2>Order Summary</h2>
      <ul>
        <?php foreach ($cart_items as $item): ?>
          <li>
            <span><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
            <span><?= htmlspecialchars($item['price']) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="summary-row">
        <span>Subtotal</span>
        <span>R <?= number_format($subtotal, 2) ?></span>
      </div>
      <div class="summary-row">
        <span>Shipping</span>
        <span><?= $shipping == 0 ? 'FREE' : 'R ' . number_format($shipping, 2) ?></span>
      </div>
      <div class="summary-row total">
        <span>Total</span>
        <span>R <?= number_format($total, 2) ?></span>
      </div>
    </aside>
  </div>
</body>
</html>

<?php
require 'data.php';

// Get product ID from URL
$id = $_GET['id'] ?? null;
$product = null;

if ($id) {
    foreach ($products as $p) {
        if ($p['id'] === $id) {
            $product = $p;
            break;
        }
    }
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
</head>
<body>

<div class="container" style="padding:40px 0">
  <?php if ($product): ?>
    <a href="shop.php" class="back-link">← Back to Shop</a>

    <div class="product-detail">
      
      <!-- LEFT: Product Image -->
      <div class="product-detail-img">
        <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        <?php if (!$product['in_stock']): ?>
          <div class="badge out">Out of Stock</div>
        <?php elseif ($product['discount']): ?>
          <div class="badge"><?= $product['discount'] ?></div>
        <?php endif; ?>
      </div>

      <!-- RIGHT: Product Info -->
      <div class="product-detail-info">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <div class="cat"><?= htmlspecialchars($product['category']) ?></div>

        <!-- Rating -->
        <div class="rating">
          <?= str_repeat("⭐", floor($product['stars'])) ?>
          <?php if ($product['stars'] - floor($product['stars']) >= 0.5): ?>⭐<?php endif; ?>
          <span class="reviews">(<?= $product['reviews'] ?> reviews)</span>
        </div>

        <!-- Price -->
        <div class="price-wrap">
          <div class="price">R<?= number_format($product['price'], 2) ?></div>
          <?php if ($product['old_price']): ?>
            <div class="old">R<?= number_format($product['old_price'], 2) ?></div>
          <?php endif; ?>
        </div>

        <!-- Stock Status -->
        <p class="status">
          <strong>Status:</strong> <?= $product['in_stock'] ? "In Stock ✅" : "Out of Stock ❌" ?>
        </p>

        <!-- Add to Cart -->
        <!-- Add to Cart Form -->
<?php if ($product['in_stock']): ?>
  <form method="POST" action="cart.php">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">
    <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']) ?>">
    <input type="hidden" name="price" value="<?= $product['price'] ?>">
    <input type="hidden" name="image" value="<?= $product['image'] ?>">
    <input type="hidden" name="quantity" value="1">
    <button type="submit" class="btn">Add to Cart</button>
  </form>
<?php else: ?>
  <button class="btn disabled" disabled>Out of Stock</button>
<?php endif; ?>


        <!-- Description -->
        <div class="description">
          <h2>Product Description</h2>
          <p>
            The <?= htmlspecialchars($product['name']) ?> is built for gamers and professionals 
            who demand top performance. Enjoy next-level power, speed, and reliability for your setup.
          </p>
        </div>
      </div>
    </div>

  <?php else: ?>
    <h2>Product not found ❌</h2>
    <p><a href="shop.php">← Back to shop</a></p>
  <?php endif; ?>
</div>

</body>
</html>

<?php
// Start session at the very top
session_start();
include 'components/db.php';

// Sync session cart with database
$session_id = session_id();

// Load cart items from database with error handling
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

// Sync session with database data
$_SESSION['cart'] = [];
foreach ($cart_items_db as $item) {
    $_SESSION['cart'][$item['product_id']] = [
        'id' => $item['product_id'],
        'name' => $item['name'],
        'price' => 'R ' . number_format($item['price'], 2),
        'image' => $item['image'],
        'quantity' => $item['quantity']
    ];
}
$cart_items = $_SESSION['cart'];

// Handle quantity updates
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'] ?? '';
    $new_quantity = intval($_POST['quantity'] ?? 0);

    if ($product_id && $new_quantity >= 0) {
        if ($new_quantity <= 0) {
            $stmt = $pdo->prepare("DELETE FROM carts WHERE session_id = ? AND product_id = ?");
            $stmt->execute([$session_id, $product_id]);
        } else {
            $stmt = $pdo->prepare("SELECT quantity FROM carts WHERE session_id = ? AND product_id = ?");
            $stmt->execute([$session_id, $product_id]);
            $existing = $stmt->fetch();

            if ($existing) {
                $stmt = $pdo->prepare("UPDATE carts SET quantity = ? WHERE session_id = ? AND product_id = ?");
                $stmt->execute([$new_quantity, $session_id, $product_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO carts (session_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$session_id, $product_id, $new_quantity]);
            }
        }
    }
    header("Location: cart.php");
    exit;
}

// Handle item removal
if (isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'] ?? '';
    if ($product_id) {
        $stmt = $pdo->prepare("DELETE FROM carts WHERE session_id = ? AND product_id = ?");
        $stmt->execute([$session_id, $product_id]);
    }
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
$cart_count = array_sum(array_column($cart_items, 'quantity'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <style>
    /* [Your existing CSS remains unchanged] */
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

    .cart-page {
        min-height: 100vh;
        background-color: rgba(236, 236, 240, 0.5);
    }

    .dark .cart-page {
        background-color: rgba(66, 66, 66, 0.5);
    }

    .cart-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .empty-cart {
        text-align: center;
        max-width: 28rem;
        margin: 0 auto;
        padding: 4rem 0;
    }

    .empty-cart-icon {
        width: 6rem;
        height: 6rem;
        background-color: var(--muted);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .empty-cart-icon svg {
        width: 3rem;
        height: 3rem;
        color: var(--muted-foreground);
    }

    .empty-cart-title {
        font-size: 1.875rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--foreground);
    }

    .empty-cart-description {
        color: var(--muted-foreground);
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .empty-cart-buttons {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        justify-content: center;
    }

    .empty-cart-button {
        padding: 0.75rem 2rem;
        border-radius: 0.375rem;
        font-weight: var(--font-weight-medium);
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .empty-cart-button-primary {
        background-color: white;
        color: var(--secondary-foreground);
        border: none;
    }

    .empty-cart-button-primary:hover {
        background-color: rgba(255, 102, 0, 0.9);
    }

    .empty-cart-button-outline {
        background-color: transparent;
        color: var(--foreground);
        border: 1px solid var(--border);
    }

    .empty-cart-button-outline:hover {
        background-color: var(--accent);
        color: var(--accent-foreground);
    }

    .cart-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .back-button {
        background: transparent;
        border: none;
        color: var(--foreground);
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-weight: var(--font-weight-medium);
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .back-button:hover {
        background-color: var(--accent);
        color: var(--accent-foreground);
    }

    .cart-header-info h1 {
        font-size: 1.875rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .cart-header-info p {
        color: var(--muted-foreground);
    }

    .cart-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .cart-items-section {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .cart-card {
        background-color: var(--card);
        color: var(--card-foreground);
        border-radius: 0.5rem;
        border: 1px solid var(--border);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    .cart-card-header {
        padding: 1.5rem 1.5rem 0;
    }

    .cart-card-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--card-foreground);
    }

    .cart-card-content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .cart-item {
        display: flex;
        gap: 1rem;
        padding: 1rem;
        border: 1px solid var(--border);
        border-radius: 0.5rem;
    }

    .cart-item-image {
        width: 5rem;
        height: 5rem;
        object-fit: cover;
        border-radius: 0.5rem;
        flex-shrink: 0;
    }

    .cart-item-content {
        flex: 1;
    }

    .cart-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }

    .cart-item-info h3 {
        font-weight: var(--font-weight-medium);
        color: var(--card-foreground);
        cursor: pointer;
        transition: color 0.3s ease;
        margin-bottom: 0.25rem;
    }

    .cart-item-info h3:hover {
        color: var(--secondary);
    }

    .cart-item-category {
        font-size: 0.875rem;
        color: var(--muted-foreground);
    }

    .cart-item-badge {
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: var(--font-weight-medium);
        margin-top: 0.25rem;
        display: inline-block;
    }

    .cart-item-badge-destructive {
        background-color: var(--destructive);
        color: var(--destructive-foreground);
    }

    .remove-button {
        background: transparent;
        border: none;
        color: var(--destructive);
        padding: 0.25rem;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .remove-button:hover {
        color: var(--destructive);
        background-color: var(--destructive);
        color: var(--destructive-foreground);
    }

    .cart-item-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .quantity-button {
        background-color: transparent;
        border: 1px solid var(--border);
        color: var(--foreground);
        padding: 0.25rem;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
    }

    .quantity-button:hover {
        background-color: var(--accent);
        color: var(--accent-foreground);
    }

    .quantity-button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .quantity-button svg {
        width: 0.75rem;
        height: 0.75rem;
    }

    .quantity-display {
        width: 3rem;
        text-align: center;
        font-weight: var(--font-weight-medium);
    }

    .price-display {
        text-align: right;
    }

    .price-total {
        font-weight: var(--font-weight-medium);
        color: var(--secondary);
        margin-bottom: 0.25rem;
    }

    .price-each {
        font-size: 0.875rem;
        color: var(--muted-foreground);
    }

    .promo-section {
        display: flex;
        gap: 0.5rem;
    }

    .promo-input {
        flex: 1;
        padding: 0.5rem 1rem;
        border: 1px solid var(--border);
        border-radius: 0.375rem;
        background-color: var(--input-background);
        color: var(--foreground);
        font-size: 0.875rem;
    }

    .promo-input:focus {
        outline: none;
        border-color: var(--ring);
        box-shadow: 0 0 0 2px rgba(255, 102, 0, 0.1);
    }

    .promo-input:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .promo-button {
        background-color: var(--primary);
        color: var(--primary-foreground);
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: var(--font-weight-medium);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .promo-button:hover {
        background-color: rgba(0, 0, 0, 0.9);
    }

    .promo-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .promo-success {
        font-size: 0.875rem;
        color: #059669;
        margin-top: 0.5rem;
    }

    .order-summary {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .summary-section {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .summary-shipping-free {
        color: #059669;
    }

    .summary-discount {
        color: #059669;
    }

    .summary-separator {
        border: none;
        border-top: 1px solid var(--border);
        margin: 0.5rem 0;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1.125rem;
        font-weight: var(--font-weight-medium);
    }

    .summary-total-amount {
        color: var(--secondary);
    }

    .checkout-button {
        width: 100%;
        background-color: var(--primary);
        color: var(--primary-foreground);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        font-weight: var(--font-weight-medium);
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .checkout-button:hover {
        background-color: rgba(0, 0, 0, 0.9);
    }

    .shipping-notice {
        font-size: 0.875rem;
        color: var(--muted-foreground);
        text-align: center;
    }

    @media (min-width: 1024px) {
        .cart-layout {
            grid-template-columns: 2fr 1fr;
        }
        .cart-items-section {
            grid-column: span 1;
        }
        .order-summary {
            grid-column: span 1;
        }
    }
   </style>
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

    <div class="cart-page">
        <div class="cart-container">
            <div class="cart-header">
                <a href="shop.php" class="back-button">← Back</a>
                <div class="cart-header-info">
                    <h1>Shopping Cart</h1>
                    <p><?= $cart_count ?> item(s) in your cart</p>
                </div>
            </div>

            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <div class="empty-cart-icon">🛒</div>
                    <h2 class="empty-cart-title">Your cart is empty</h2>
                    <p class="empty-cart-description">Looks like you haven’t added anything yet. Start shopping now!</p>
                    <div class="empty-cart-buttons">
                        <a href="shop.php" class="empty-cart-button empty-cart-button-primary">Browse Products</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="cart-layout">
                    <section class="cart-items-section">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item">
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-item-image">
                                <div class="cart-item-content">
                                    <div class="cart-item-header">
                                        <div class="cart-item-info">
                                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                                            <p class="cart-item-category">Gaming Gear</p>
                                        </div>
                                        <form method="POST">
                                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['id']) ?>">
                                            <button type="submit" name="remove_item" class="remove-button">🗑</button>
                                        </form>
                                    </div>
                                    <div class="cart-item-footer">
                                        <div class="quantity-controls">
                                            <form method="POST">
                                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['id']) ?>">
                                                <input type="hidden" name="quantity" value="<?= max(1, $item['quantity'] - 1) ?>">
                                                <button type="submit" name="update_quantity" class="quantity-button">-</button>
                                            </form>
                                            <span class="quantity-display"><?= $item['quantity'] ?></span>
                                            <form method="POST">
                                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['id']) ?>">
                                                <input type="hidden" name="quantity" value="<?= $item['quantity'] + 1 ?>">
                                                <button type="submit" name="update_quantity" class="quantity-button">+</button>
                                            </form>
                                        </div>
                                        <div class="price-display">
                                            <div class="price-total">R <?= number_format(floatval(str_replace(['R ', ','], '', $item['price'])) * $item['quantity'], 2) ?></div>
                                            <div class="price-each"><?= htmlspecialchars($item['price']) ?> each</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </section>
                    <aside class="order-summary">
                        <div class="summary-section">
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span>R <?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span class="<?= $shipping == 0 ? 'summary-shipping-free' : '' ?>">
                                    <?= $shipping == 0 ? 'FREE' : 'R ' . number_format($shipping, 2) ?>
                                </span>
                            </div>
                            <hr class="summary-separator">
                            <div class="summary-total">
                                <span>Total</span>
                                <span class="summary-total-amount">R <?= number_format($total, 2) ?></span>
                            </div>
                        </div>
                        <button class="checkout-button">Proceed to Checkout →</button>
                        <p class="shipping-notice">
                            <?php if ($shipping == 0): ?>
                                🎉 You qualify for free shipping!
                            <?php else: ?>
                                Add R <?= number_format(5000 - $subtotal, 2) ?> more for free shipping
                            <?php endif; ?>
                        </p>
                    </aside>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
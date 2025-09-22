<?php
session_start();
include 'components/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $total = array_reduce($_SESSION['cart'], fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);

    $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, total) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $name, $email, $total);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    foreach ($_SESSION['cart'] as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }

    $_SESSION['cart'] = []; // Clear cart
    header("Location: thank_you.php");
    exit;
}
?>


<?php include 'components/header.php'; ?>

<div class="container mx-auto px-4 py-8">
  <h1 class="text-3xl font-bold mb-6">Checkout</h1>
  
  <form method="POST" action="checkout.php" class="space-y-4 max-w-lg">
    <input type="text" name="name" placeholder="Full Name" required class="input w-full">
    <input type="email" name="email" placeholder="Email Address" required class="input w-full">
    
    <button type="submit" class="btn btn-primary w-full">Place Order</button>
  </form>
</div>

<?php include 'components/footer.php'; ?>

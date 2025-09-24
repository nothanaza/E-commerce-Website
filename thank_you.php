<?php
$order_id = $_GET['order_id'] ?? 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Complete</title>
   <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="thank-you-page">
    <h1>Thank You!</h1>
    <p>Your payment was successful.</p>
    <p>Order reference: <strong><?= htmlspecialchars($order_id) ?></strong></p>
    <a href="shop.php">Continue Shopping</a>
  </div>
</body>
</html>

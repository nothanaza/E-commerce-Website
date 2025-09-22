<?php
session_start();

// If cart does not exist, create it
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if Add to Cart button was clicked
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['id'];
    $product_name = $_POST['name'];
    $product_price = $_POST['price'];
    $product_image = $_POST['image'];

    $found = false;

    // Check if product already exists in cart
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity'] += 1; // increase quantity
            $found = true;
            break;
        }
    }

    // If product not in cart, add new
    if (!$found) {
        $_SESSION['cart'][] = [
            "id" => $product_id,
            "name" => $product_name,
            "price" => $product_price,
            "image" => $product_image,
            "quantity" => 1
        ];
    }
}

// Redirect back to cart page
header("Location: cart.php");
exit;

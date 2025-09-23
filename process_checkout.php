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

// PayFast Sandbox credentials
$merchant_id = "10000100"; 
$merchant_key = "46f0cd694581a"; 
$return_url = "http://localhost/e-commerce/thank_you.php";
$cancel_url = "http://localhost/e-commerce/cart.php";
$notify_url = "http://localhost/e-commerce/payfast_notify.php";


// Generate unique order ID
$order_id = uniqid("TG-");

// Customer details
$name = $_POST['fullname'] ?? "Customer";
$email = $_POST['email'] ?? "test@example.com";

// Prepare data for PayFast
$payfast_data = [
    'merchant_id'   => $merchant_id,
    'merchant_key'  => $merchant_key,
    'return_url'    => $return_url,
    'cancel_url'    => $cancel_url,
    'notify_url'    => $notify_url,
    'name_first'    => $name,
    'email_address' => $email,
    'm_payment_id'  => $order_id,
    'amount'        => number_format($total, 2, '.', ''),
    'item_name'     => "Tech Giants Order #$order_id"
];

// Build query string
$query_string = http_build_query($payfast_data);

// Redirect to PayFast
header("Location: https://sandbox.payfast.co.za/eng/process?" . $query_string);
exit;

<?php
session_start();
require_once 'components/db.php';

// Log errors to a file for debugging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');
error_log("add_to_wishlist.php accessed at " . date('Y-m-d H:i:s'));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['wishlist_message'] = "Please log in to add to wishlist.";
    header("Location: /E-commerce-Website/signin.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? null;
$redirect_url = $_POST['redirect_url'] ?? '/E-commerce-Website/product.php?id=' . $product_id;

if (!$product_id) {
    $_SESSION['wishlist_message'] = "No product selected.";
    error_log("No product_id provided in add_to_wishlist.php");
    header("Location: $redirect_url");
    exit;
}

try {
    // Check if product exists
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    if (!$stmt->fetch()) {
        $_SESSION['wishlist_message'] = "Product not found.";
        error_log("Product ID $product_id not found in products table");
        header("Location: $redirect_url");
        exit;
    }

    // Check if product already exists in wishlist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $exists = $stmt->fetchColumn();

    if (!$exists) {
        // Insert product into wishlist
        $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $product_id]);
        $_SESSION['wishlist_message'] = "Product added to wishlist!";
    } else {
        $_SESSION['wishlist_message'] = "Product already in wishlist.";
    }
} catch (PDOException $e) {
    $_SESSION['wishlist_message'] = "Error adding to wishlist. Please try again.";
    error_log("Wishlist error: " . $e->getMessage() . " | File: " . __FILE__ . " | Line: " . __LINE__);
} catch (Exception $e) {
    $_SESSION['wishlist_message'] = "Unexpected error. Please try again.";
    error_log("Unexpected error: " . $e->getMessage() . " | File: " . __FILE__ . " | Line: " . __LINE__);
}

// Redirect back to the product page
header("Location: $redirect_url");
exit;
?>
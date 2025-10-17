<?php
session_start();
require_once 'components/db.php';

// Log errors to a file for debugging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');
error_log("remove_from_wishlist.php accessed at " . date('Y-m-d H:i:s'));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['wishlist_message'] = "Please log in to manage your wishlist.";
    header("Location: /E-commerce-Website-main/E-commerce-Website/signin.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? null;
$redirect_url = $_POST['redirect_url'] ?? '/E-commerce-Website-main/E-commerce-Website/product.php?id=' . $product_id;

if (!$product_id) {
    $_SESSION['wishlist_message'] = "No product selected.";
    error_log("No product_id provided in remove_from_wishlist.php");
    header("Location: $redirect_url");
    exit;
}

try {
    // Check if product exists in wishlist
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        // Remove product from wishlist
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $_SESSION['wishlist_message'] = "Product removed from wishlist!";
    } else {
        $_SESSION['wishlist_message'] = "Product not found in wishlist.";
    }
} catch (PDOException $e) {
    $_SESSION['wishlist_message'] = "Error removing from wishlist. Please try again.";
    error_log("Wishlist removal error: " . $e->getMessage() . " | File: " . __FILE__ . " | Line: " . __LINE__);
} catch (Exception $e) {
    $_SESSION['wishlist_message'] = "Unexpected error. Please try again.";
    error_log("Unexpected error: " . $e->getMessage() . " | File: " . __FILE__ . " | Line: " . __LINE__);
}

// Redirect back to the product page
header("Location: $redirect_url");
exit;
?>
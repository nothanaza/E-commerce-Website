<?php
include 'components/db.php';

// STEP 1: Read POST from PayFast
$pfData = $_POST;

// STEP 2: Validate (skipping full security for demo)
if ($pfData['payment_status'] === "COMPLETE") {
    $order_id = $pfData['m_payment_id'];
    $amount = $pfData['amount_gross'];

    // Save order to DB
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, shipping, status) VALUES (?, ?, ?, 'Paid')");
    $stmt->execute([null, $amount, 0]);
}

// Respond to PayFast
header("HTTP/1.0 200 OK");
flush();
?>

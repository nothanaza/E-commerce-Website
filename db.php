<?php
$host = "localhost";
$user = "root"; // change if using hosting
$pass = "";     // your password
$dbname = "ecommerce";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

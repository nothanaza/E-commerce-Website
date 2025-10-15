<?php
$host = "localhost";   // XAMPP always runs MySQL on localhost
$user = "root";        // Default XAMPP MySQL username
$pass = "";            // Default password is blank
$dbname = "tech_giants"; // Your database name
$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

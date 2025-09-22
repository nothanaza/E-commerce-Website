<?php
$host = "localhost";   // XAMPP always runs MySQL on localhost
$user = "root";        // Default XAMPP MySQL username
$pass = "";            // Default password is blank
$dbname = "techgiants"; // Your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

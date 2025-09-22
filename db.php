<?php
$host = "localhost";
$user = "root";  // Default XAMPP MySQL user
$pass = "";      // Default is empty
$dbname = "techgiants";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

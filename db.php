<?php
$host = "localhost";
<<<<<<< HEAD
$user = "root"; // change if using hosting
$pass = "";     // your password
$dbname = "ecommerce";

$conn = new mysqli($host, $user, $pass, $dbname);
=======
$user = "root";   // default XAMPP user
$pass = "";       // default is empty
$db   = "techgiants";

$conn = new mysqli($host, $user, $pass, $db);
>>>>>>> 26f731eeeebb0137cf7e0a1c842f4fe51294e281

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

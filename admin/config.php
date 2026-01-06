<?php
// Database configuration for Rani Beauty Clinic CMS
// Adjust credentials to match your local XAMPP MySQL setup if needed.
$host = "localhost";            // Typically `localhost` on XAMPP
$user = "root";                 // Default XAMPP MySQL user
$pass = "";                     // Default XAMPP MySQL has no password
$dbname = "ranicms";            // Database that stores CMS data

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

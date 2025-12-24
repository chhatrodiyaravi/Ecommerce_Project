<?php
// Database Configuration
$host = "localhost";       // Server host (default for XAMPP)
$user = "root";            // MySQL username (default root)
$pass = "";                // MySQL password (default empty in XAMPP)
$dbname = "camera_store";  // Database name

// Create Connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Optional: Set character set to UTF-8
$conn->set_charset("utf8");

// Optional: Turn off error display in production (for college, keep it ON)
error_reporting(E_ALL);
ini_set('display_errors', 1);

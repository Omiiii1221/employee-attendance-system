<?php
// Enable error reporting (for development only, remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database credentials
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'employee_attendance';

// Establish database connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Log the error and show a user-friendly message
    error_log("Database connection failed: " . $conn->connect_error);
    die("An error occurred. Please try again later.");
}

// Set the charset to avoid special character issues
$conn->set_charset("utf8mb4");
?>

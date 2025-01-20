<?php
// Start the session to maintain user login state
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in; if not, redirect to login page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : "Employee Attendance System"; ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <!-- Add any additional CSS or meta tags here -->
</head>
<body>

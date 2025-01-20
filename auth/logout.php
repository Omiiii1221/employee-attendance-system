<?php
// Start the session to access session variables
session_start();

// Check if the session is already started, and then clear all session data
if (isset($_SESSION['user_id'])) {
    // Unset session variables
    session_unset();
    session_destroy(); // Destroy the session

    // Redirect the user to the login page
    header("Location:../index.php");
    exit(); // Make sure to exit after header redirect
} else {
    // If no user is logged in, redirect them to the login page directly
    header("Location:../index.php");
    exit();
}
?>

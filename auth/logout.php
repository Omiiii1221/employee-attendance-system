<?php
// Start the session
session_start();

// Clear all session data
session_unset();
session_destroy();

// Clear the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// Redirect to the login page
header("Location: ../index.php");
exit(); // Ensure no further code runs
?>

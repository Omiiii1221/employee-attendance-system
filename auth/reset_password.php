<?php
session_start();
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get token and new password from POST
    $token = mysqli_real_escape_string($conn, $_POST['token']);
    $password = $_POST['password'];

    // Validate password strength (e.g., minimum 8 characters, one uppercase, one number)
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = "Password must be at least 8 characters long, with at least one uppercase letter and one number.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update the password in the database
        $query = "UPDATE users SET password='$hashed_password', reset_token=NULL WHERE reset_token='$token'";
        if (mysqli_query($conn, $query)) {
            $success = "Password reset successful. You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Invalid token or database error.";
        }
    }
} else if (isset($_GET['token'])) {
    // If a token is provided in the URL
    $token = mysqli_real_escape_string($conn, $_GET['token']);

    // Check if the token exists in the database
    $query = "SELECT * FROM users WHERE reset_token='$token'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) == 0) {
        $error = "Invalid or expired token.";
    }
} else {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <form action="" method="POST">
        <h2>Reset Password</h2>

        <!-- Display error or success messages -->
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

        <!-- Hidden token field -->
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

        <!-- Password input field -->
        <input type="password" name="password" placeholder="New Password" required>

        <!-- Submit button -->
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>

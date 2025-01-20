<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $token = bin2hex(random_bytes(50));
        $query = "UPDATE users SET reset_token='$token' WHERE email='$email'";
        mysqli_query($conn, $query);

        $resetLink = "http://localhost/employee-attendance-system/auth/reset_password.php?token=$token";
        // Code to send email with $resetLink (use PHPMailer or similar)
        echo "Password reset link has been sent to your email.";
    } else {
        $error = "Email does not exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <form action="" method="POST">
        <h2>Forgot Password</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Send Reset Link</button>
    </form>
</body>
</html>

<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $token = bin2hex(random_bytes(50));  // Generate a random token
        $query = "UPDATE users SET reset_token='$token' WHERE email='$email'";
        if (mysqli_query($conn, $query)) {
            // Create the password reset link
            $resetLink = "https://localhost/employee-attendance-system/auth/reset_password.php?token=$token";
            
            // Send the reset link via email (You can replace this with a better email library like PHPMailer)
            $subject = "Password Reset Request";
            $message = "Hello,\n\nClick the link below to reset your password:\n$resetLink\n\nIf you didn't request this, please ignore this email.";
            $headers = "From: no-reply@yourdomain.com\r\n" .
                       "Reply-To: no-reply@yourdomain.com\r\n" .
                       "X-Mailer: PHP/" . phpversion();

            // Using mail() to send the email
            if (mail($email, $subject, $message, $headers)) {
                echo "Password reset link has been sent to your email.";
            } else {
                echo "Error sending email. Please try again later.";
            }
        } else {
            echo "Error updating token in the database.";
        }
    } else {
        $error = "Email does not exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

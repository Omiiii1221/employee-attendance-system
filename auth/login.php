<?php
session_start();
include '../config/db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role']; // 'admin' or 'employee'

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../employee/dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "User does not exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <form action="" method="POST">
        <h2>Login</h2>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <a href="forgot_password.php">Forgot Password?</a>
    </form>
</body>
</html>

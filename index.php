<?php
session_start();
include './config/db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query based on selected role
    $query = "SELECT * FROM users WHERE username='$username' AND role='$role'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: ./admin/dashboard.php");
            } else {
                header("Location: ./employee/dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid credentials or role.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance System</title>
    <link rel="stylesheet" href="./assets/css/index.css">
</head>
<body>
    <section class="landing-page">
        <div class="landing-container">
            <div class="landing-content">
                <h1>Welcome to Employee Attendance System</h1>
                <p>Manage attendance, track time, and more—seamlessly.</p>
                <form action="" method="POST" class="login-form">
                    <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
                    <label for="role">Login as:</label>
                    <select name="role" id="role" required>
                        <option value="admin">Admin</option>
                        <option value="employee">Employee</option>
                    </select>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Login</button>
                </form>
                <div class="auth-links">
                    <a href="./auth/forgot_password.php">Forgot Password?</a>
                    <p>Don't have an account? <a href="./auth/register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
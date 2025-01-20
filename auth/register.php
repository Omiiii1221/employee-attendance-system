<?php
session_start();
include '../config/db.php'; // Include database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']); // 'admin' or 'employee'

    // Check if the username or email already exists
    $check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $error = "Username or email already exists.";
    } else {
        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the insert query
        $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$hashed_password', '$role')";
        
        if (mysqli_query($conn, $query)) {
            $success = "User registered successfully.";
            // Redirect to index.php (login page) after successful registration
            header("Location: ../index.php");
            exit(); // Always call exit after header redirect
        } else {
            $error = "Error registering user: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        /* Global reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body and background styling */
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to right, #ff7e5f, #feb47b);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

/* Form container */
form {
    background-color: #fff;
    border-radius: 8px;
    padding: 30px;
    width: 300px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* Form heading */
h2 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
}

/* Input fields */
input[type="text"],
input[type="email"],
input[type="password"],
select {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

/* Focus effect on input fields */
input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
select:focus {
    border-color: #4e91f1;
    outline: none;
}

/* Submit button */
button[type="submit"] {
    width: 100%;
    padding: 14px;
    background-color: #4e91f1;
    border: none;
    color: #fff;
    font-size: 18px;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

/* Button hover effect */
button[type="submit"]:hover {
    background-color: #357ab9;
}

/* Error and success messages */
p {
    text-align: center;
    font-size: 14px;
}

.error-message {
    color: #ff0000;
}

.success-message {
    color: #28a745;
}

/* Link styles */
.auth-links {
    text-align: center;
    margin-top: 15px;
}

.auth-links a {
    text-decoration: none;
    color: #4e91f1;
}

.auth-links a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
    <form action="" method="POST">
        <h2>Register</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

        <!-- Username -->
        <input type="text" name="username" placeholder="Username" required>

        <!-- Email -->
        <input type="email" name="email" placeholder="Email" required>

        <!-- Password -->
        <input type="password" name="password" placeholder="Password" required>

        <!-- Role Selection -->
        <select name="role" required>
            <option value="employee">Employee</option>
            <option value="admin">Admin</option>
        </select>

        <!-- Submit Button -->
        <button type="submit">Register</button>
    </form>
</body>
</html>

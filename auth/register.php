<?php
include '../config/db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']); // 'admin' or 'employee'

    // Check if email or username already exists
    $check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $error = "Username or email already exists.";
    } else {
        // Insert user into database
        $query = "INSERT INTO users (username, email, password, role) VALUES ('$username', '$email', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            $success = "User registered successfully.";
        } else {
            $error = "Error registering user: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
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

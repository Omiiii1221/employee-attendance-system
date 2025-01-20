<?php
session_start();
include '../config/db.php'; // Include database connection
include '../includes/header.php'; // Include the header

// Handle password change
session_start();
$employee_id = $_SESSION['employee_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch the current password from the database
    $stmt = $conn->prepare("SELECT password FROM employees WHERE id = ?");
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        // Verify current password
        if (password_verify($current_password, $employee['password'])) {
            if ($new_password === $confirm_password) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE employees SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $hashed_password, $employee_id);

                if ($update_stmt->execute()) {
                    echo "<p>Password updated successfully!</p>";
                } else {
                    echo "<p>Error updating password: " . $update_stmt->error . "</p>";
                }
                $update_stmt->close();
            } else {
                echo "<p>New password and confirm password do not match.</p>";
            }
        } else {
            echo "<p>Current password is incorrect.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?> <!-- Include navigation bar -->

    <div class="container">
        <h1>Change Your Password</h1>

        <!-- Password Change Form -->
        <form action="" method="POST">
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" id="current_password" required>

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" required>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <button type="submit" class="btn">Change Password</button>
        </form>

        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>

    <?php include '../includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>

<?php
// Close database connection
$conn->close();
?>

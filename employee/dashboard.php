<?php
include '../config/db.php'; // Include database connection
include '../includes/header.php'; // Include the header

// Fetch employee details (assuming employee_id is stored in the session)
session_start();
$employee_id = $_SESSION['employee_id'];

$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $employee = $result->fetch_assoc();
} else {
    echo "<p>Employee not found.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?> <!-- Include navigation bar -->

    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($employee['name']); ?></h1>
        <p>Here is your dashboard.</p>

        <div class="dashboard-cards">
            <div class="card">
                <h2>Your Attendance</h2>
                <p>View your attendance records and status.</p>
                <a href="attendance.php" class="btn">View Attendance</a>
            </div>
            <div class="card">
                <h2>Your Profile</h2>
                <p>View and update your personal details.</p>
                <a href="profile.php" class="btn">View Profile</a>
            </div>
            <div class="card">
                <h2>Change Password</h2>
                <p>Update your account password.</p>
                <a href="change_password.php" class="btn">Change Password</a>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>

<?php
// Close database connection
$conn->close();
?>

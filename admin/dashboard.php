<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php"); // Redirect to login if not authenticated
    exit;
}

// Set the page title
$page_title = "Admin Dashboard";

// Include database connection and header
include '../config/db.php';
include '../includes/header.php';
?>

<?php include '../includes/navbar.php'; ?> <!-- Include navigation bar -->

<div class="container">
    <h1>Admin Dashboard</h1>
    <div class="dashboard-cards">
        <!-- Total Employees -->
        <div class="card">
            <h2>Total Employees</h2>
            <p>
                <?php
                $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees");
                if ($result) {
                    $data = mysqli_fetch_assoc($result);
                    echo $data['total'];
                } else {
                    echo "Error fetching data.";
                }
                ?>
            </p>
            <a href="add_employee.php" class="btn">Add New Employee</a>
        </div>

        <!-- Total Attendance Records -->
        <div class="card">
            <h2>Total Attendance Records</h2>
            <p>
                <?php
                $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM attendance");
                if ($result) {
                    $data = mysqli_fetch_assoc($result);
                    echo $data['total'];
                } else {
                    echo "Error fetching data.";
                }
                ?>
            </p>
            <a href="manage_attendance.php" class="btn">Manage Attendance</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?> <!-- Include footer -->

<?php
include '../config/db.php'; // Include database connection
include '../includes/header.php'; // Include the header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?> <!-- Include navigation bar -->
    
    <div class="container">
        <h1>Admin Dashboard</h1>
        <div class="dashboard-cards">
            <div class="card">
                <h2>Total Employees</h2>
                <p>
                    <?php
                    // Query to count employees
                    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees");
                    $data = mysqli_fetch_assoc($result);
                    echo $data['total'];
                    ?>
                </p>

                <a href="add_employee.php" class="btn">Add New Employee</a>


            </div>
            <div class="card">
                <h2>Total Attendance Records</h2>
                <p>
                    <?php
                    // Query to count attendance
                    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM attendance");
                    $data = mysqli_fetch_assoc($result);
                    echo $data['total'];
                    ?>
                </p>
                
                <a href="manage_attendance.php" class="btn">Add New attendance</a>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>

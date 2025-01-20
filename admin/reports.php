<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php"); // Redirect to login if not authenticated
    exit;
}
include '../config/db.php'; // Include database connection
include '../includes/header.php'; // Include the header

// Handle form submission
$report_type = isset($_POST['report_type']) ? $_POST['report_type'] : '';
$selected_month = isset($_POST['month']) ? $_POST['month'] : date('m'); // Default to current month
$selected_year = isset($_POST['year']) ? $_POST['year'] : date('Y'); // Default to current year

// Initialize the query result
$report_result = null;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Employee report
    if ($report_type == "employee_report") {
        $query = "
            SELECT e.name, e.department, e.email, e.phone, e.date_of_joining 
            FROM employees e
        ";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $report_result = $stmt->get_result();
    }

    // Attendance report
    elseif ($report_type == "attendance_report") {
        $query = "
            SELECT e.name, COUNT(a.id) AS total_attendance, 
                   SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS total_absent,
                   SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) AS total_late
            FROM attendance a
            JOIN employees e ON a.employee_id = e.id
            WHERE MONTH(a.date) = ? AND YEAR(a.date) = ?
            GROUP BY e.id
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $selected_month, $selected_year);
        $stmt->execute();
        $report_result = $stmt->get_result();
    }

    // Department-wise attendance report
    elseif ($report_type == "department_report") {
        $query = "
            SELECT e.department, COUNT(a.id) AS total_attendance,
                   SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS total_absent,
                   SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) AS total_late
            FROM attendance a
            JOIN employees e ON a.employee_id = e.id
            WHERE MONTH(a.date) = ? AND YEAR(a.date) = ?
            GROUP BY e.department
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $selected_month, $selected_year);
        $stmt->execute();
        $report_result = $stmt->get_result();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Attendance Reports</title>
    <link rel="stylesheet" href="../assets/css/reports.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?> <!-- Include navigation bar -->
    
    <div class="container">
        <h1>Generate Attendance Report</h1>

        <!-- Report Generation Form -->
        <form action="" method="POST">
            <label for="report_type">Select Report Type:</label>
            <select name="report_type" id="report_type" required>
                <option value="" disabled selected>Select report type</option>
                <option value="employee_report" <?php echo $report_type == "employee_report" ? 'selected' : ''; ?>>Employee Report</option>
                <option value="attendance_report" <?php echo $report_type == "attendance_report" ? 'selected' : ''; ?>>Attendance Report</option>
                <option value="department_report" <?php echo $report_type == "department_report" ? 'selected' : ''; ?>>Department-wise Attendance Report</option>
            </select>

            <label for="month">Select Month:</label>
            <select name="month" id="month" required>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $month_name = date('F', mktime(0, 0, 0, $m, 1)); // Get month name
                    echo "<option value='$m'" . ($m == $selected_month ? ' selected' : '') . ">$month_name</option>";
                }
                ?>
            </select>

            <label for="year">Select Year:</label>
            <select name="year" id="year" required>
                <?php
                for ($y = 2020; $y <= date('Y'); $y++) {
                    echo "<option value='$y'" . ($y == $selected_year ? ' selected' : '') . ">$y</option>";
                }
                ?>
            </select>

            <button type="submit">Generate Report</button>
        </form>

        <?php if ($report_result) { ?>
            <h2>Report for <?php echo date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)); ?></h2>

            <!-- Display Report -->
            <?php if ($report_type == "employee_report") { ?>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Date of Joining</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $report_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['date_of_joining']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>

            <?php if ($report_type == "attendance_report" || $report_type == "department_report") { ?>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Employee Name/Department</th>
                            <th>Total Attendance</th>
                            <th>Total Absent</th>
                            <th>Total Late</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $report_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name'] ?? $row['department']); ?></td>
                                <td><?php echo $row['total_attendance']; ?></td>
                                <td><?php echo $row['total_absent']; ?></td>
                                <td><?php echo $row['total_late']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        <?php } ?>
        
    </div>

    <?php include '../includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>

<?php
// Close database connection
$conn->close();
?>

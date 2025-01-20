<?php
include '../config/db.php'; // Include database connection
include '../includes/header.php'; // Include the header

// Handle fetching reports data
// You can adjust the query to fit your reporting needs
$attendance_query = "
    SELECT e.name, COUNT(a.id) AS total_attendance, 
           SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS total_absent,
           SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) AS total_late
    FROM attendance a
    JOIN employees e ON a.employee_id = e.id
    GROUP BY e.id
";
$stmt = $conn->prepare($attendance_query);
if ($stmt === false) {
    die('Error preparing SQL statement: ' . $conn->error);
}
$stmt->execute();
$attendance_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Reports</title>
    <link rel="stylesheet" href="../assets/css/man_att.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?> <!-- Include navigation bar -->
    
    <div class="container">
        <h1>Attendance Reports</h1>
        
        <!-- Report Table -->
        <table border="1">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Total Attendance</th>
                    <th>Total Absent</th>
                    <th>Total Late</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $attendance_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo $row['total_attendance']; ?></td>
                        <td><?php echo $row['total_absent']; ?></td>
                        <td><?php echo $row['total_late']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Add more reports or filters here as needed -->

        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>

    <?php include '../includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>

<?php
// Close database connection
$conn->close();
?>

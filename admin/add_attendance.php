<?php
include '../config/db.php'; // Include database connection
include '../includes/header.php'; // Include the header

// Handle form submission to add attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $employee_id = $_POST['employee_id'];
    $attendance_date = $_POST['attendance_date'];
    $status = $_POST['status'];

    // Prepare and execute the SQL query to insert attendance record
    $stmt = $conn->prepare("INSERT INTO attendance (employee_id, date, status) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die('Error preparing SQL statement: ' . $conn->error);
    }
    $stmt->bind_param("iss", $employee_id, $attendance_date, $status);

    if ($stmt->execute()) {
        echo "<p>Attendance record added successfully!</p>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Fetch employee list to display in the dropdown
$stmt = $conn->prepare("SELECT id, name FROM employees");
if ($stmt === false) {
    die('Error preparing SQL statement: ' . $conn->error);
}
$stmt->execute();
$employees_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Attendance</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?> <!-- Include navigation bar -->
    
    <div class="container">
        <h1>Add Attendance</h1>

        <form action="add_attendance.php" method="POST">
            <div class="form-group">
                <label for="employee_id">Select Employee</label>
                <select id="employee_id" name="employee_id" required>
                    <option value="">Select an employee</option>
                    <?php while ($employee = $employees_result->fetch_assoc()) { ?>
                        <option value="<?php echo $employee['id']; ?>"><?php echo htmlspecialchars($employee['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="attendance_date">Attendance Date</label>
                <input type="date" id="attendance_date" name="attendance_date" required>
            </div>
            
            <div class="form-group">
                <label for="status">Attendance Status</label>
                <select id="status" name="status" required>
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                    <option value="Late">Late</option>
                    <option value="On Leave">On Leave</option>
                </select>
            </div>
            
            <button type="submit" class="btn">Add Attendance</button>
        </form>
        
        <a href="manage_attendance.php" class="btn">Back to Attendance Management</a>
    </div>
    
    <?php include '../includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>

<?php
// Close database connection
$conn->close();
?>

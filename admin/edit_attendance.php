<?php
include '../config/db.php'; // Include database connection
include '../includes/header.php'; // Include the header

// Fetch attendance record for editing
if (isset($_GET['id'])) {
    $attendance_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM attendance WHERE id = ?");
    $stmt->bind_param("i", $attendance_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = $result->fetch_assoc();
    $stmt->close();
}

// Handle the form submission to update attendance record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendance_id = $_POST['attendance_id'];
    $employee_id = $_POST['employee_id'];
    $date = $_POST['date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE attendance SET employee_id = ?, date = ?, status = ? WHERE id = ?");
    $stmt->bind_param("issi", $employee_id, $date, $status, $attendance_id);
    
    if ($stmt->execute()) {
        echo "<p class='success'>Attendance record updated successfully!</p>";
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Attendance</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?> <!-- Include navigation bar -->

    <div class="container">
        <h1>Edit Attendance Record</h1>
        
        <!-- Form to edit attendance -->
        <form action="" method="post">
            <input type="hidden" name="attendance_id" value="<?php echo $attendance['id']; ?>">

            <label for="employee_id">Employee ID</label>
            <input type="number" id="employee_id" name="employee_id" value="<?php echo $attendance['employee_id']; ?>" required>

            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="<?php echo $attendance['date']; ?>" required>

            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="Present" <?php echo $attendance['status'] == 'Present' ? 'selected' : ''; ?>>Present</option>
                <option value="Absent" <?php echo $attendance['status'] == 'Absent' ? 'selected' : ''; ?>>Absent</option>
            </select>

            <button type="submit">Update Attendance</button>
        </form>
    </div>

    <?php include '../includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>

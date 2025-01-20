<?php
include '../config/db.php'; // Include database connection
include '../includes/header.php'; // Include the header

// Check if 'id' parameter is set
if (isset($_GET['id'])) {
    $employee_id = $_GET['id'];

    // Fetch current employee details
    $stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if employee exists
    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
    } else {
        echo "<p>Employee not found.</p>";
        exit;
    }
} else {
    echo "<p>Invalid request.</p>";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $department = $_POST['department'];

    // Update employee details
    $update_stmt = $conn->prepare("UPDATE employees SET name = ?, email = ?, contact_number = ?, department = ? WHERE id = ?");
    $update_stmt->bind_param("ssssi", $name, $email, $contact_number, $department, $employee_id);
    
    if ($update_stmt->execute()) {
        echo "<p>Employee details updated successfully!</p>";
    } else {
        echo "<p>Error updating employee details: " . $update_stmt->error . "</p>";
    }
    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?> <!-- Include navigation bar -->

    <div class="container">
        <h1>Edit Employee Details</h1>

        <!-- Employee Edit Form -->
        <form action="" method="POST">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>

            <label for="contact_number">Contact Number:</label>
            <input type="text" name="contact_number" id="contact_number" value="<?php echo htmlspecialchars($employee['contact_number']); ?>" required>

            <label for="department">Department:</label>
            <input type="text" name="department" id="department" value="<?php echo htmlspecialchars($employee['department']); ?>" required>

            <button type="submit" class="btn">Update Employee</button>
        </form>

        <a href="manage_employees.php" class="btn">Back to Employee List</a>
    </div>

    <?php include '../includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>

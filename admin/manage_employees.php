<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php"); // Redirect to login if not authenticated
    exit;
}

include '../config/db.php'; // Include database connection
include '../includes/header.php'; // Include the header

// Handle deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Validate delete_id to prevent SQL injection
    if (is_numeric($delete_id)) {
        $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
        $stmt->bind_param("i", $delete_id);

        if ($stmt->execute()) {
            header("Location: manage_employees.php?success=1"); // Redirect after deletion to show success message
            exit();
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Invalid employee ID.</p>";
    }
}

// Fetch all employees using prepared statements for security
$stmt = $conn->prepare("SELECT * FROM employees");
if ($stmt === false) {
    die('Error preparing SQL statement: ' . $conn->error);
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
} else {
    die('Error executing query: ' . $stmt->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?> <!-- Include navigation bar -->

    <div class="container">
        <h1>Manage Employees</h1>

        <?php
        // Display success message if the deletion was successful
        if (isset($_GET['success'])) {
            echo "<p style='color: green;'>Employee deleted successfully!</p>";
        }
        ?>

        <table border="1">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Date of Joining</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['department']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_of_joining']); ?></td>
                        <td>
                            <a href="edit_employee.php?id=<?php echo $row['id']; ?>">Edit</a> |
                            <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="add_employee.php" class="btn">Add New Employee</a>
    </div>

    <?php include '../includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>

<?php
// Close database connection
$conn->close();
?>

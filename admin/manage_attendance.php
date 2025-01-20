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
        $stmt = $conn->prepare("DELETE FROM attendance WHERE id = ?");
        if ($stmt === false) {
            die('Error preparing SQL statement: ' . $conn->error);
        }
        $stmt->bind_param("i", $delete_id);

        if ($stmt->execute()) {
            echo "<p>Attendance record deleted successfully!</p>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>Invalid attendance ID.</p>";
    }
}

// Fetch attendance records
$stmt = $conn->prepare("SELECT a.id, e.name, a.date, a.status FROM attendance a JOIN employees e ON a.employee_id = e.id");
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
    <title>Manage Attendance</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?> <!-- Include navigation bar -->
    
    <div class="container">
        <h1>Manage Attendance</h1>
        
        <table border="1">
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Attendance Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <!-- Edit and Delete actions -->
                            <a href="edit_attendance.php?id=<?php echo $row['id']; ?>">Edit</a> |
                            <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        
        <a href="add_attendance.php" class="btn">Add New Attendance</a>
    </div>
    
    <?php include '../includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>

<?php
// Close database connection
$conn->close();
?>

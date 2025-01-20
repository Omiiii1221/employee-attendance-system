<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php"); // Redirect to login if not authenticated
    exit;
}
include '../config/db.php'; // Include database connection

// Check if 'id' parameter is set
if (isset($_GET['id'])) {
    $employee_id = $_GET['id'];

    // Delete employee from the database
    $delete_stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $delete_stmt->bind_param("i", $employee_id);

    if ($delete_stmt->execute()) {
        echo "<p>Employee record deleted successfully!</p>";
    } else {
        echo "<p>Error deleting employee record: " . $delete_stmt->error . "</p>";
    }
    $delete_stmt->close();
} else {
    echo "<p>Invalid request.</p>";
}

// Redirect back to the employee management page
header("Location: manage_employees.php");
exit;

?>

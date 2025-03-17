<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php");
    exit;
}

include '../config/db.php'; // Database connection
include '../includes/header.php'; // Header

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if 'id' parameter is set and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid employee ID.";
    header("Location: manage_employees.php");
    exit;
}

$employee_id = intval($_GET['id']); // Ensure it's an integer

// Fetch employee details
$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Employee not found.";
    header("Location: manage_employees.php");
    exit;
}

$employee = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);
    $department = trim($_POST['department']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($contact_number) || empty($department)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: edit_employee.php?id=$employee_id");
        exit;
    }

    // Update employee details
    $update_stmt = $conn->prepare("UPDATE employees SET name = ?, email = ?, phone = ?, department = ? WHERE id = ?");
    if (!$update_stmt) {
        die("SQL Error: " . $conn->error);
    }
    $update_stmt->bind_param("ssssi", $name, $email, $contact_number, $department, $employee_id);

    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Employee details updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating employee details: " . $update_stmt->error;
    }

    $update_stmt->close();
    header("Location: edit_employee.php?id=$employee_id");
    exit;
}

$conn->close();
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
    <?php include '../includes/navbar.php'; ?>

    <div class="container">
        <h1>Edit Employee Details</h1>

        <!-- Display session messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <p class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php elseif (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($employee['name'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($employee['email'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <label for="contact_number">Contact Number:</label>
            <input type="text" name="contact_number" id="contact_number" value="<?php echo htmlspecialchars($employee['phone'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <label for="department">Department:</label>
            <input type="text" name="department" id="department" value="<?php echo htmlspecialchars($employee['department'], ENT_QUOTES, 'UTF-8'); ?>" required>

            <button type="submit" class="btn">Update Employee</button>
        </form>

        <a href="manage_employees.php" class="btn",styles="margin-top:10px;">Back to Employee List</a>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

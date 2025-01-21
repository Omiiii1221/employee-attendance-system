<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php"); // Redirect to login if not authenticated
    exit;
}
include '../config/db.php'; // Include database connection
include '../includes/header.php'; // Include the header

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $department = $_POST['department'];
    $date_of_joining = $_POST['date_of_joining'];

    // Insert into the database
    $sql = "INSERT INTO employees (name, email, phone, department, date_of_joining) VALUES ('$name', '$email', '$phone', '$department', '$date_of_joining')";
    if (mysqli_query($conn, $sql)) {
        echo "<p>Employee added successfully!</p>";
    } else {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?> <!-- Include navigation bar -->
    
    <div class="container">
        <h1>Add Employee</h1>
        <form action="" method="post">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" required>

            <label for="department">Department</label>
            <input type="text" id="department" name="department" required>

            <label for="date_of_joining">Date of Joining</label>
            <input type="date" id="date_of_joining" name="date_of_joining" required>

            <button type="submit">Add Employee</button>
        </form>
    </div>
    
    <?php include '../includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>
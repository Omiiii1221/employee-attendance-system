<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $input = file_get_contents('php://input');
    // Decode the JSON data
    $data = json_decode($input, true);

    // Extract the task ID and completion status
    $task_id = mysqli_real_escape_string($conn, $data['task_id']);
    $completed = mysqli_real_escape_string($conn, $data['completed']) ? 1 : 0;

    $query = "UPDATE tasks SET completed = '$completed' WHERE id = '$task_id'";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
}
?>
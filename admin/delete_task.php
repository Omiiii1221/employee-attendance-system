<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $input = file_get_contents('php://input');
    // Decode the JSON data
    $data = json_decode($input, true);

    // Extract the task ID
    $task_id = mysqli_real_escape_string($conn, $data['task_id']);

    $query = "DELETE FROM tasks WHERE id = '$task_id'";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
}
?>
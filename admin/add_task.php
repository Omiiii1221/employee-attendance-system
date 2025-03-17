<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $input = file_get_contents('php://input');
    // Decode the JSON data
    $data = json_decode($input, true);

    // Extract the task text
    $task_text = mysqli_real_escape_string($conn, $data['task_text']);
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO tasks (user_id, task_text) VALUES ('$user_id', '$task_text')";
    if (mysqli_query($conn, $query)) {
        $task_id = mysqli_insert_id($conn);
        echo json_encode(['success' => true, 'task_id' => $task_id]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
}
?>
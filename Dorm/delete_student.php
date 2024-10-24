<?php
session_start();
require('db_connection.php');

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        header("Location: manage_students.php"); // Redirect on success
        exit;
    } else {
        echo "Error deleting student: " . $stmt->error; // Handle the error
    }
    $stmt->close();
}
?>

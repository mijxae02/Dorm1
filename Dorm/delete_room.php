<?php
session_start();
require('db_connection.php');

if (isset($_GET['id'])) {
    $room_id = $_GET['id'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);

    if ($stmt->execute()) {
        header("Location: manage_rooms.php"); // Redirect on success
        exit;
    } else {
        echo "Error deleting room: " . $stmt->error; // Handle the error
    }
    $stmt->close();
}
?>

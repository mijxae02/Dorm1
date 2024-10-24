<?php
session_start();
require('db_connection.php');

$error = ""; // Initialize the error variable
$room_id = $_GET['id'];

// Fetch the current room details
$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();

if (!$room) {
    die("Room not found");
}

// Handle room update
if (isset($_POST['update_room'])) {
    $room_number = $_POST['room_number'];
    $capacity = $_POST['capacity'];
    $room_type = $_POST['room_type'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("UPDATE rooms SET room_number = ?, capacity = ?, room_type = ? WHERE id = ?");
    $stmt->bind_param("sisi", $room_number, $capacity, $room_type, $room_id);

    if ($stmt->execute()) {
        header("Location: manage_rooms.php"); // Redirect on success
        exit;
    } else {
        $error = "Error updating room: " . $stmt->error; // Handle the error
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room</title>
</head>
<body>
    <h2>Edit Room</h2>
    <form action="" method="POST">
        <input type="text" name="room_number" value="<?php echo htmlspecialchars($room['room_number']); ?>" required>
        <input type="number" name="capacity" value="<?php echo htmlspecialchars($room['capacity']); ?>" required>
        <input type="text" name="room_type" value="<?php echo htmlspecialchars($room['room_type']); ?>" required>
        <input type="submit" name="update_room" value="Update Room">
    </form>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p> <!-- Display error message -->
    <?php endif; ?>
</body>
</html>

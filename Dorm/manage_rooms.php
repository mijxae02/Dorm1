<?php
session_start();
require('db_connection.php');

$error = ""; // Initialize the error variable

// Handle room registration
if (isset($_POST['add_room'])) {
    $room_number = $_POST['room_number'];
    $capacity = $_POST['capacity'];
    $room_type = $_POST['room_type'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO rooms (room_number, capacity, room_type) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $room_number, $capacity, $room_type);

    if ($stmt->execute()) {
        header("Location: manage_rooms.php"); // Redirect on success
        exit;
    } else {
        $error = "Error adding room: " . $stmt->error; // Handle the error
    }
    $stmt->close();
}

// Fetch existing rooms for display
$rooms = $conn->query("SELECT * FROM rooms");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 20px;
        }
        h2, h3 {
            color: #1e88e5; /* Headings color */
        }
        form {
            background-color: #ffffff; /* Form background */
            padding: 20px;
            border-radius: 10px; /* Rounded corners for the form */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Shadow for the form */
            margin-bottom: 20px; /* Space below the form */
        }
        input[type="text"], input[type="number"] {
            width: calc(100% - 24px); /* Full width minus padding */
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #1e88e5;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #1565c0; /* Darker shade on hover */
        }
        .error {
            color: red;
            margin: 10px 0; /* Space above the error message */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px; /* Space above the table */
            background-color: #ffffff; /* Table background */
            border-radius: 10px; /* Rounded corners for the table */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Shadow for the table */
        }
        table, th, td {
            border: 1px solid #ddd; /* Table border */
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #1e88e5; /* Header color */
            color: white; /* Header text color */
        }
        tr:hover {
            background-color: #f5f5f5; /* Row hover effect */
        }
        .back-button {
            margin: 20px 0; /* Space around the button */
            padding: 10px 15px; /* Padding for button */
            background-color: #1e88e5; /* Button color */
            color: white; /* Text color */
            border: none; /* No border */
            border-radius: 5px; /* Rounded corners */
            text-decoration: none; /* No underline */
            display: inline-block; /* Inline block for margin */
            transition: background-color 0.3s; /* Transition for hover effect */
        }
        .back-button:hover {
            background-color: #1565c0; /* Darker shade on hover */
        }
    </style>
</head>
<body>
    <h2>Add Room</h2>
    <form action="" method="POST">
        <input type="text" name="room_number" placeholder="Room Number" required>
        <input type="number" name="capacity" placeholder="Room Capacity" required>
        <input type="text" name="room_type" placeholder="Room Type" required>
        <input type="submit" name="add_room" value="Add Room">
    </form>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p> <!-- Display error message -->
    <?php endif; ?>

    <a href="admin_dashboard.php" class="back-button">Back to Dashboard</a> <!-- Back button -->

    <h3>Existing Rooms</h3>
    <table>
        <tr>
            <th>Room Number</th>
            <th>Capacity</th>
            <th>Room Type</th>
            <th>Actions</th>
        </tr>
        <?php while ($room = $rooms->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                <td>
                    <a href="edit_room.php?id=<?php echo $room['id']; ?>">Edit</a> |
                    <a href="delete_room.php?id=<?php echo $room['id']; ?>">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

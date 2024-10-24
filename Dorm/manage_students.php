<?php
session_start();
require('db_connection.php'); // Ensure your database connection file is included

$error = ""; // Initialize the error variable

// Handle student registration
if (isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $room_id = $_POST['room_id']; // Room ID from form
    $start_date = $_POST['start_date']; // Start date from form

    // Check if the room_id exists
    $roomCheck = $conn->prepare("SELECT id FROM rooms WHERE id = ?");
    $roomCheck->bind_param("i", $room_id);
    $roomCheck->execute();
    $roomCheckResult = $roomCheck->get_result();

    if ($roomCheckResult->num_rows > 0) {
        // Prepare and execute SQL statement
        $stmt = $conn->prepare("INSERT INTO students (name, email, phone, room_id, start_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $name, $email, $phone, $room_id, $start_date);

        if ($stmt->execute()) {
            header("Location: manage_students.php"); // Redirect on success
            exit;
        } else {
            $error = "Error adding student: " . $stmt->error; // Handle the error
        }
        $stmt->close();
    } else {
        $error = "Error: Room ID does not exist."; // Error message for invalid room_id
    }
}

// Fetch existing students for display
$students = $conn->query("SELECT s.*, r.room_number FROM students s LEFT JOIN rooms r ON s.room_id = r.id");

// Fetch available rooms for dropdown
$rooms = $conn->query("SELECT id, room_number FROM rooms"); // Place it here

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>
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
        input[type="text"], input[type="email"], input[type="date"], select {
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
            background-color: #1565c0;
        }
        .error {
            color: red;
            margin: 10px 0;
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
    </style>
</head>
<body>
    <h2>Add Student</h2>
    <form action="" method="POST">
        <input type="text" name="name" placeholder="Student Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <input type="date" name="start_date " placeholder="Start Date" required>
        <select name="room_id" required>
            <option value="">Select Room</option>
            <?php while ($room = $rooms->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($room['id']); ?>"><?php echo htmlspecialchars($room['room_number']); ?></option>
            <?php endwhile; ?>
        </select>
        <input type="submit" name="add_student" value="Add Student">
    </form>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p> <!-- Display error message -->
    <?php endif; ?>

    <a href="admin_dashboard.php" class="back-button">Back to Dashboard</a> <!-- Back button -->

    <h3>Existing Students</h3>
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Room</th>
            <th>Start Date (yyyy/mm/dd)</th>
            <th>Actions</th>
        </tr>
        <?php while ($student = $students->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['name']); ?></td>
                <td><?php echo htmlspecialchars($student['email']); ?></td>
                <td><?php echo htmlspecialchars($student['phone']); ?></td>
                <td><?php echo htmlspecialchars($student['room_number']); ?></td>
                <td><?php echo htmlspecialchars($student['start_date']); ?></td> <!-- Display start date -->
                <td>
                    <a href="edit_student.php?id=<?php echo $student['student_id']; ?>">Edit</a> |
                    <a href="delete_student.php?id=<?php echo $student['student_id']; ?>">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

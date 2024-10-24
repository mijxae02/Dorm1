<?php
session_start();
require('db_connection.php');

$error = ""; // Initialize the error variable
$student_id = $_GET['id'];

// Fetch the current student details
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student not found");
}

// Handle student update
if (isset($_POST['update_student'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $room_id = $_POST['room_id'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, phone = ?, room_id = ? WHERE student_id = ?");
    $stmt->bind_param("ssiii", $name, $email, $phone, $room_id, $student_id);

    if ($stmt->execute()) {
        header("Location: manage_students.php"); // Redirect on success
        exit;
    } else {
        $error = "Error updating student: " . $stmt->error; // Handle the error
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
</head>
<body>
    <h2>Edit Student</h2>
    <form action="" method="POST">
        <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required>
        <input type="number" name="room_id" value="<?php echo htmlspecialchars($student['room_id']); ?>" required>
        <input type="submit" name="update_student" value="Update Student">
    </form>

    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p> <!-- Display error message -->
    <?php endif; ?>
</body>
</html>

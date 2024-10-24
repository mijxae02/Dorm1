<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Database connection
$mysqli = new mysqli("localhost", "root", "", "dorm_management");

// Check if the connection works
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch logged-in user's info
$student_number = $_SESSION['student_number'];
$user_result = $mysqli->query("SELECT name, student_number FROM students WHERE student_number = '$student_number'");

// Check if the user data is found
if ($user_result && $user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

// Handle user edit request
if (isset($_POST['edit_user'])) {
    $new_name = $_POST['new_name'];
    $new_number = $_POST['new_number'];

    // Update user's details in the database
    $stmt = $mysqli->prepare("UPDATE students SET name = ?, student_number = ? WHERE student_number = ?");
    $stmt->bind_param("sss", $new_name, $new_number, $student_number);
    
    if ($stmt->execute()) {
        // Update session variables
        $_SESSION['username'] = $new_name;
        $_SESSION['student_number'] = $new_number;
        echo "<p>User details updated successfully!</p>";
    } else {
        echo "<p>Failed to update user details.</p>";
    }
}

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Details</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Your Details</h2>

        <!-- Display user details -->
        <?php if ($user_data): ?>
            <form action="students.php" method="POST">
                <div class="mb-3">
                    <label for="new_name" class="form-label">Student Name</label>
                    <input type="text" class="form-control" name="new_name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="new_number" class="form-label">Student Number</label>
                    <input type="text" class="form-control" name="new_number" value="<?php echo htmlspecialchars($user_data['student_number']); ?>" required>
                </div>
                <button type="submit" name="edit_user" class="btn btn-primary">Save changes</button>
            </form>
        <?php else: ?>
            <p>No user details found.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

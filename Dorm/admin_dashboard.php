<?php
session_start();
require('db_connection.php'); // Ensure this path is correct

// Check if the admin is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Fetch total students
$total_students_query = "SELECT COUNT(*) AS total_students FROM students";
$total_students_result = $conn->query($total_students_query);
$total_students_row = $total_students_result->fetch_assoc();
$students_count = $total_students_row['total_students'];

// Fetch total rooms
$total_rooms_query = "SELECT COUNT(*) AS total_rooms FROM rooms";
$total_rooms_result = $conn->query($total_rooms_query);
$total_rooms_row = $total_rooms_result->fetch_assoc();
$rooms_count = $total_rooms_row['total_rooms'];

// Count occupied rooms
$occupied_rooms_query = "SELECT COUNT(DISTINCT room_id) AS occupied_rooms FROM students WHERE room_id IS NOT NULL";
$occupied_rooms_result = $conn->query($occupied_rooms_query);
$occupied_rooms_row = $occupied_rooms_result->fetch_assoc();
$occupied_rooms_count = $occupied_rooms_row['occupied_rooms'];

// Fetch user reservations
$reservations_query = "SELECT rentals.student_number, rooms.room_number, rentals.full_name 
                       FROM rentals 
                       JOIN rooms ON rentals.room_id = rooms.id";
$reservations_result = $conn->query($reservations_query);

// Fetch specific student details if 'student_number' is passed
$student_number = isset($_GET['student_number']) ? $_GET['student_number'] : null;
$student = null; // Initialize student variable
if ($student_number) {
    // Adjusted query to fetch the student directly from the students table
    $student_query = "SELECT * FROM students WHERE student_number = ?";
    $stmt = $conn->prepare($student_query);
    $stmt->bind_param('s', $student_number); // Binding student_number
    $stmt->execute();
    $student_result = $stmt->get_result();
    $student = $student_result->fetch_assoc();
    if (!$student) {
        echo "Student not found.";
        exit;
    }
}

// Update student information if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_student'])) {
    $updated_name = $_POST['student_name'];
    $updated_number = $_POST['student_number'];

    // Ensure we update the correct student based on student_number
    $update_query = "UPDATE students SET name = ?, student_number = ? WHERE student_number = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('sss', $updated_name, $updated_number, $student_number);

    if ($stmt->execute()) {
        // Redirect back to the dashboard after updating
        header("Location: admin_dashboard.php?student_number=" . $updated_number);
        exit;
    } else {
        echo "Error updating student information.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .dashboard {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .dashboard h1 {
            margin: 0;
            color: #1e88e5;
            text-align: center;
        }
        .stats {
            margin-top: 20px;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        .stat-item {
            flex: 1;
            min-width: 200px;
            padding: 15px;
            margin: 10px;
            background-color: #e3f2fd;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        .links {
            margin-top: 20px;
            text-align: center;
        }
        .links a {
            margin: 0 10px;
            color: #1e88e5;
            text-decoration: none;
            padding: 10px 15px;
            border: 1px solid #1e88e5;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        .links a:hover {
            background-color: #1e88e5;
            color: white;
        }
        .student-info-form {
            margin-top: 40px;
            text-align: center;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
        }
        .student-info-form h2 {
            color: #1e88e5;
        }
        .student-info-form input[type="text"] {
            padding: 10px;
            width: 80%;
            margin: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .student-info-form input[type="submit"] {
            padding: 10px 20px;
            background-color: #1e88e5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>Admin Dashboard</h1>
        <div class="stats">
            <div class="stat-item">
                <h2>Total Students</h2>
                <p><?php echo $students_count; ?></p>
            </div>
            <div class="stat-item">
                <h2>Total Rooms</h2>
                <p><?php echo $rooms_count; ?></p>
            </div>
            <div class="stat-item">
                <h2>Occupied Rooms</h2>
                <p><?php echo $occupied_rooms_count; ?></p>
            </div>
        </div>

        <div class="container mt-5">
            <h2>User Reservations</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Student Number</th>
                        <th>Full Name</th>
                        <th>Room Number</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($reservation = $reservations_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['student_number']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['room_number']); ?></td>
                            <td>
                                <a href="admin_dashboard.php?student_number=<?php echo htmlspecialchars($reservation['student_number']); ?>" class="btn btn-primary">Edit</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="links">
            <a href="manage_students.php">Manage Students</a>
            <a href="manage_rooms.php">Manage Rooms</a>
            <a href="view_reservations.php">View Reservations</a>
            <a href="manage_payments.php">Manage Payments</a>
        </div>

        <!-- Student Info Form at the Bottom -->
        <?php if ($student_number && isset($student)): ?>
        <div class="student-info-form">
            <h2>Edit Student Information</h2>
            <form method="post" action="">
                <input type="text" name="student_name" value="<?php echo htmlspecialchars($student['name']); ?>" required><br>
                <input type="text" name="student_number" value="<?php echo htmlspecialchars($student['student_number']); ?>" required><br>
                <input type="submit" name="update_student" value="Update Student Info">
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

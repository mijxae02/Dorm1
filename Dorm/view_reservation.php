<?php
session_start();
require('db_connection.php');

// Check if the user is logged in as admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_dashboard.php"); // Redirect if not an admin
    exit;
}

// Fetch all rentals
$rental_query = "SELECT r.*, ro.room_number 
                 FROM rentals r 
                 JOIN rooms ro ON r.room_id = ro.id 
                 ORDER BY rental_date DESC";

$reservations_result = $conn->query($rental_query);

// Check for query execution success
if (!$reservations_result) {
    die("Error fetching reservations: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reservations</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class="container my-4">
        <h1>All Reservations</h1>
        
        <?php if ($reservations_result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student Number</th>
                        <th>Room Number</th>
                        <th>Full Name</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Age</th>
                        <th>Rental Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($reservation = $reservations_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['student_number']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['room_number']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['address']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['contact_number']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['age']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['rental_date']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-info">No reservations found.</p>
        <?php endif; ?>
        
        <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</body>
</html>

<?php
session_start();
require('db_connection.php'); // Ensure your database connection file is included

$error = ""; // Initialize the error variable
$success = ""; // Initialize the success variable

// Handle payment registration
if (isset($_POST['add_payment'])) {
    $student_id = $_POST['student_id'];
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $status = 'paid'; // Change status based on your needs

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO payments (student_id, amount, payment_date, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idsi", $student_id, $amount, $payment_date, $status);

    if ($stmt->execute()) {
        $success = "Payment added successfully."; // Handle success
    } else {
        $error = "Error adding payment: " . $stmt->error; // Handle the error
    }
    $stmt->close();
}

// Fetch existing students for dropdown
$students = $conn->query("SELECT student_id AS id, name FROM students");

// Fetch payments for display
$payments = $conn->query("SELECT p.*, s.name FROM payments p JOIN students s ON p.student_id = s.student_id");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        h2, h3 {
            color: #333;
        }
        form {
            margin-bottom: 20px;
            padding: 10px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type="number"], input[type="date"], select {
            margin: 5px 0;
            padding: 10px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background: #5cb85c;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #4cae4c;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #f8f8f8;
        }
    </style>
</head>
<body>
    <h2>Add Payment</h2>
    <form action="" method="POST">
        <select name="student_id" required>
            <option value="">Select Student</option>
            <?php while ($student = $students->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($student['id']); ?>"><?php echo htmlspecialchars($student['name']); ?></option>
            <?php endwhile; ?>
        </select>
        <input type="number" step="0.01" name="amount" placeholder="Amount" required>
        <input type="date" name="payment_date" required>
        <input type="submit" name="add_payment" value="Add Payment">
    </form>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p> <!-- Display error message -->
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <p class="success"><?php echo $success; ?></p> <!-- Display success message -->
    <?php endif; ?>

    <h3>Payments</h3>
    <table>
        <tr>
            <th>Student Name</th>
            <th>Amount</th>
            <th>Payment Date</th>
            <th>Status</th>
        </tr>
        <?php while ($payment = $payments->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($payment['name']); ?></td>
                <td><?php echo htmlspecialchars($payment['amount']); ?></td>
                <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                <td><?php echo htmlspecialchars($payment['status']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>

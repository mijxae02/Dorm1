<?php
session_start();
require('db_connection.php'); // Ensure this path is correct
require __DIR__ . '/vendor/autoload.php'; // Make sure you have Composer dependencies installed

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_number = $_POST['student_number'];

    // Fetch the user's email
    $email_query = "SELECT email FROM users WHERE student_number = ?";
    $stmt = $conn->prepare($email_query);
    $stmt->bind_param('s', $student_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $email = $user['email'];

        // Debug: Print the email address
        echo "Sending notification to: $email"; // Debugging output

        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email address.";
            exit; // Stop execution if the email is invalid
        }

        // Now set up PHPMailer to send the email
        $mail = new PHPMailer(true);
        
        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.example.com'; // Set the SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@example.com'; // SMTP username
            $mail->Password = 'your_email_password'; // SMTP password
            $mail->SMTPSecure = 'tls'; // Enable TLS encryption
            $mail->Port = 587; // TCP port to connect to
            
            // Email content
            $mail->setFrom('your_email@example.com', 'Dorm Management'); // Your sender email
            $mail->addAddress($email); // Add the recipient's email address
            $mail->Subject = 'Room Reservation Notification';
            $mail->Body = 'Your room has been successfully reserved. Thank you!';

            // Send the email
            $mail->send();
            echo "Notification sent to $email.";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "No user found with that student number.";
    }

    $stmt->close();
}

$conn->close();
?>

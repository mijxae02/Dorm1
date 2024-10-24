<?php
// Start the session
session_start();

// Include the Composer autoload file
require 'vendor/autoload.php'; // Ensure the path to 'vendor/autoload.php' is correct

// Use PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Now you can create a function to send emails or directly use PHPMailer in your script

function sendEmail($recipientEmail, $recipientName, $subject, $body) {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP(); // Set mailer to use SMTP
        $mail->Host = 'smtp.example.com'; // Specify main and backup SMTP servers
        $mail->SMTPAuth = true; // Enable SMTP authentication
        $mail->Username = 'your_email@example.com'; // SMTP username
        $mail->Password = 'your_password'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
        $mail->Port = 587; // TCP port to connect to

        // Recipients
        $mail->setFrom('your_email@example.com', 'Your Name'); // Your email and name
        $mail->addAddress($recipientEmail, $recipientName); // Add a recipient

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject; // Email subject
        $mail->Body = $body; // Email body

        // Send the email
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Example usage
$recipientEmail = 'student@example.com'; // Replace with actual recipient email
$recipientName = 'John Doe'; // Replace with actual recipient name
$subject = 'Reservation Confirmation';
$body = '<p>Dear ' . $recipientName . ',</p><p>Your reservation has been successfully processed!</p>';

sendEmail($recipientEmail, $recipientName, $subject, $body);
?>

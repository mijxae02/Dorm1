<?php
session_start();

// Database connection
$mysqli = new mysqli("localhost", "root", "", "dorm_management"); // Update with your database credentials

// Check if the connection works
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Initialize variables for error/success messages
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the submitted form data
    $full_name = $mysqli->real_escape_string(trim($_POST['full_name'])); // Capture full name
    $username = $mysqli->real_escape_string(trim($_POST['username']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $student_number = $mysqli->real_escape_string(trim($_POST['student_number'])); // Capture student number
    $email = $mysqli->real_escape_string(trim($_POST['email'])); // Capture email

    // Check if the fields are empty
    if (empty($full_name) || empty($username) || empty($password) || empty($confirm_password) || empty($student_number) || empty($email)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        // Check if passwords match
        $error = "Passwords do not match.";
    } else {
        // Check if username or student number already exists
        $sql = "SELECT id FROM users WHERE username = ? OR student_number = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $username, $student_number);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or student number already exists. Please choose another.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user data into the database including email
            $sql = "INSERT INTO users (full_name, username, password, student_number, email) VALUES (?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sssss", $full_name, $username, $hashed_password, $student_number, $email); // Include email here

            if ($stmt->execute()) {
                // Successful registration
                $success = "Registration successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Error: Could not register the user. Please try again.";
            }
        }

        $stmt->close();
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Dorm Management System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-container {
            width: 350px;
            padding: 40px;
            background-color: #ffffff;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            text-align: center;
        }
        .register-container h2 {
            margin-bottom: 15px;
            color: #1e88e5;
        }
        .register-container input[type="text"], 
        .register-container input[type="password"],
        .register-container input[type="email"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .register-container input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #1e88e5;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }
        .register-container input[type="submit"]:hover {
            background-color: #1565c0;
        }
        .register-container .error {
            color: red;
            margin: 10px 0;
        }
        .register-container .success {
            color: green;
            margin: 10px 0;
        }
        .register-container .links {
            margin-top: 15px;
        }
        .register-container a {
            color: #1e88e5;
            text-decoration: none;
        }
        .register-container a:hover {
            text-decoration: underline;
        }
        @media (max-width: 400px) {
            .register-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<div class="register-container">
    <h2>Create an Account</h2>
    
    <!-- Display error/success messages -->
    <?php if (!empty($error)) echo '<p class="error">' . $error . '</p>'; ?>
    <?php if (!empty($success)) echo '<p class="success">' . $success . '</p>'; ?>
    
    <form action="register.php" method="POST">
        <input type="text" name="full_name" placeholder="Enter your full name" required> <!-- Full name field -->
        <input type="text" name="username" placeholder="Enter your username" required>
        <input type="email" name="email" placeholder="Enter your email" required> <!-- Email field -->
        <input type="text" name="student_number" placeholder="Enter your student number" required>
        <input type="password" name="password" placeholder="Enter your password" required>
        <input type="password" name="confirm_password" placeholder="Confirm your password" required>
        <input type="submit" value="Register">
    </form>
    <div class="links">
        <a href="login.php">Already have an account? Login here</a>
    </div>
</div>

</body>
</html>

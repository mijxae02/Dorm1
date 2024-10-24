<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dorm Management System Login</title>
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
        .login-container {
            width: 350px;
            padding: 40px;
            background-color: #ffffff;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 15px;
            color: #1e88e5;
        }
        .login-container img {
            width: 100px;
            margin-bottom: 15px;
        }
        .login-container input[type="text"], 
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .login-container input[type="submit"] {
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
        .login-container input[type="submit"]:hover {
            background-color: #1565c0;
        }
        .login-container .error {
            color: red;
            margin: 10px 0;
        }
        .login-container .links {
            margin-top: 15px;
        }
        .login-container a {
            color: #1e88e5;
            text-decoration: none;
        }
        .login-container a:hover {
            text-decoration: underline;
        }
        @media (max-width: 400px) {
            .login-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Dorm Management System</h2>
    <?php
    if (isset($_GET['error'])) {
        echo '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
    }
    ?>
    <!-- Change the action to point to login.php -->
    <form action="login.php" method="POST">
        <input type="text" name="username" placeholder="Enter your username" required>
        <input type="password" name="password" placeholder="Enter your password" required>
        <input type="submit" name="login" value="Login">
    </form>
    <div class="links">
        <a href="register.php">Create an account</a> 
        <a href="forgot-password.php">Forgot password?</a>
    </div>
</div>

</body>
</html>

<?php
session_start();

// Database connection
$mysqli = new mysqli("localhost", "root", "", "dorm_management"); // Update with your database credentials

// Check if the connection works
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $mysqli->real_escape_string(trim($_POST['username']));
    $password = trim($_POST['password']);

    // Query to fetch user details
    $stmt = $mysqli->prepare("SELECT id, full_name, password, student_number FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $full_name, $hashed_password, $student_number);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start a new session
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username; // Keep this for future use
            $_SESSION['full_name'] = $full_name; // Store full name
            $_SESSION['student_number'] = $student_number;

            header("Location: renting.php"); // Redirect to the dashboard
            exit;
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that username.";
    }

    $stmt->close();
}

$mysqli->close();
?>
<?php
session_start();
require('db_connection.php'); // Ensure this path is correct

$error = ""; // Initialize the error variable

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ensure to escape the username to prevent SQL injection
    $username = $conn->real_escape_string($username);

    // Query to check if the user exists in the admins table
    $sql = "SELECT * FROM admins WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['loggedin'] = true;

            // Redirect to the admin dashboard
            header("Location: admin_dashboard.php");
            exit;
        } else {
            // Password is incorrect
            $error = "Invalid password";
        }
    } else {
        // Username doesn't exist or user is not an admin
        $error = "Invalid username or not an admin";
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
    <title>Admin Login</title>
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
        .register-container input[type="password"] {
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
    <h2>Admin Login</h2>
    <form action="" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="login" value="Login">
    </form>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <div class="links">
        <p>Don't have an account? <a href="admin_registration.php">Register here</a></p>
    </div>
</div>

</body>
</html>

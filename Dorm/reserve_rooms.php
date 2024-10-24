<?php
session_start();
require('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['reserve_room'])) {
    $room_id = $_POST['room_id'];
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $age = $_POST['age'];
    $email = $_POST['email']; // Get the email from POST data
    $student_number = $_SESSION['student_number']; // Assuming this is stored in session
    $start_date = $_POST['start_date']; // Get start date from POST data

    // Prepare SQL statement to insert reservation
    $stmt = $conn->prepare("INSERT INTO rentals (student_number, room_id, full_name, address, contact_number, age, email, start_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Check if the statement was prepared successfully
    if ($stmt === false) {
        die("Prepare failed: " . htmlspecialchars($conn->error));
    }

    // Bind parameters (Ensure types are correct: s=string, i=integer)
    $stmt->bind_param("sissssss", $student_number, $room_id, $full_name, $address, $contact_number, $age, $email, $start_date);

    if ($stmt->execute()) {
        echo "<p class='alert alert-success'>Room successfully reserved!</p>";
    } else {
        echo "<p class='alert alert-danger'>Error reserving room: " . htmlspecialchars($stmt->error) . "</p>";
    }
    $stmt->close();
}

// Get available rooms for reservation
$rooms_result = $conn->query("SELECT * FROM rooms WHERE id NOT IN (SELECT room_id FROM rentals WHERE status = 'active')");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Room</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>
    <style>
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: black; 
        }
        ::-webkit-scrollbar-thumb {
            background: black; 
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555; 
        }
        @import url("https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap");
        :root{
            --header-height: 3rem;
            --nav-width: 68px;
            --first-color: #2f2f2f;
            --first-color-light: white;
            --white-color: white;
            --body-font: 'Nunito', sans-serif;
            --normal-font-size: 1rem;
            --z-fixed: 100;
        }
        *,::before,::after{
            box-sizing: border-box;
        }
        body{
            position: relative;
            margin: var(--header-height) 0 0 0;
            padding: 0 1rem;
            font-family: var(--body-font);
            font-size: var(--normal-font-size);
            transition: .5s;
        }
        a{
            text-decoration: none;
        }
        .header{
            width: 100%;height: var(--header-height);
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            background-color: var(--white-color);
            z-index: var(--z-fixed);
            transition: .5s;
        }
        .header_toggle{
            color: var(--first-color);
            font-size: 1.5rem;
            cursor: pointer;
        }
        .header_img{
            width: 35px;
            height: 35px;
            display: flex;
            justify-content: center;
            border-radius: 50%;
            overflow: hidden;
        }
        .header_img img{
            width: 40px;
        }
        .l-navbar{
            position: fixed;
            top: 0;
            left: -30%;
            width: var(--nav-width);
            height: 100vh;
            background-color: var(--first-color);
            padding: .5rem 1rem 0 0;
            transition: .5s;
            z-index: var(--z-fixed);
        }
        .nav{
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }
        .nav_logo, .nav_link{
            display: grid;
            grid-template-columns: max-content max-content;
            align-items: center;
            column-gap: 1rem;
            padding: .5rem 0 .5rem 1.5rem;
        }
        .nav_logo{
            margin-bottom: 2rem;
        }
        .nav_logo-icon{
            font-size: 1.25rem;
            color: var(--white-color);
        }
        .nav_logo-name{
            color: var(--white-color);
            font-weight: 700;
        }
        .nav_link{
            position: relative;
            color: var(--first-color-light);
            margin-bottom: 1.5rem;
            transition: .3s;
        }
        .nav_link:hover{
            color: var(--white-color);
        }
        .nav_icon{
            font-size: 1.25rem;
        }
        .show{
            left: 0;
        }
        .body-pd{
            padding-left: calc(var(--nav-width) + 1rem);
        }
        .active{
            color: var(--white-color);
        }
        .active::before{
            content: '';
            position: absolute;
            left: 0;
            width: 2px;
            height: 32px;
            background-color: var(--white-color);
        }
        .height-100{
            height:100vh;
        }
        @media screen and (min-width: 768px){
            body{
                margin: calc(var(--header-height) + 1rem) 0 0 0;
                padding-left: calc(var(--nav-width) + 2rem);
            }
            .header{
                height: calc(var(--header-height) + 1rem);
                padding: 0 2rem 0 calc(var(--nav-width) + 2rem);
            }
            .header_img{
                width: 40px;
                height: 40px;
            }
            .header_img img{
                width: 45px;
            }
            .l-navbar{
                left: 0;
                padding: 1rem 1rem 0 0;
            }
            .show{
                width: calc(var(--nav-width) + 156px);
            }
            .body-pd{
                padding-left: calc(var(--nav-width) + 188px);
            }
        }
    </style>
</head>
<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="renting.php" class="nav_logo"> <i class='bx bx-layer nav_logo-icon'></i> <span class="nav_logo-name">Dorm System</span> </a>
                <div class="nav_list"> 
                    <a href="renting.php" class="nav_link"> <i class='bx bx-grid-alt nav_icon'></i> <span class="nav_name">Dashboard</span> </a>
                    <a href="available_rooms.php" class="nav_link"> <i class='bx bx-building nav_icon'></i> <span class="nav_name">Available Rooms</span> </a>
                    <a href="reserve_rooms.php" class="nav_link active"> <i class='bx bx-bed nav_icon'></i> <span class="nav_name">Reserve Rooms</span> </a>
                    <a href="logout.php" class="nav_link"> <i class='bx bx-log-out nav_icon'></i> <span class="nav_name">Logout</span> </a>
                </div>
            </div>
        </nav>
    </div>

    <div class="container mt-5 pt-5">
        <h1>Reserve a Room</h1>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" name="full_name" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" name="address" required>
            </div>
            <div class="mb-3">
                <label for="contact_number" class="form-label">Contact Number</label>
                <input type="text" class="form-control" name="contact_number" required>
            </div>
            <div class="mb-3">
                <label for="age" class="form-label">Age</label>
                <input type="number" class="form-control" name="age" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label for="room_id" class="form-label">Select Room</label>
                <select name="room_id" class="form-control" required>
                    <?php
                    // Check if any rooms are available
                    if ($rooms_result->num_rows > 0) {
                        while ($room = $rooms_result->fetch_assoc()) {
                            echo "<option value='{$room['id']}'>{$room['room_number']} (Type: {$room['room_type']}, Capacity: {$room['capacity']})</option>";
                        }
                    } else {
                        echo "<option value=''>No available rooms</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_date" required>
            </div>
            <button type="submit" name="reserve_room" class="btn btn-primary">Reserve Room</button>
        </form>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js'></script>
    <script>
        const headerToggle = document.getElementById('header-toggle');
        const navBar = document.getElementById('nav-bar');
        headerToggle.addEventListener('click', () => {
            navBar.classList.toggle('show');
            document.getElementById('body-pd').classList.toggle('body-pd');
        });
    </script>
</body>
</html>

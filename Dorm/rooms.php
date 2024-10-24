<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Database connection
$mysqli = new mysqli("localhost", "root", "", "dorm_management");

// Check if the connection works
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle room rental request
if (isset($_POST['rent_room'])) {
    $room_id = $_POST['room_id'];
    $student_number = $_SESSION['student_number'];

    // Check if the room is available
    $result = $mysqli->query("SELECT * FROM rooms WHERE id = $room_id AND available = 1");
    if ($result && $result->num_rows > 0) {
        // Rent the room (mark it unavailable and add a rental entry)
        $mysqli->query("UPDATE rooms SET available = 0 WHERE id = $room_id");
        $stmt = $mysqli->prepare("INSERT INTO rentals (student_number, room_id) VALUES (?, ?)");
        $stmt->bind_param("si", $student_number, $room_id);
        $stmt->execute();
        echo "<p>Room successfully rented!</p>";
    } else {
        echo "<p>Room is not available.</p>";
    }
}

// Get available rooms
$rooms_result = $mysqli->query("SELECT * FROM rooms WHERE available = 1");

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>
    <style>
::-webkit-scrollbar {
      width: 8px;
    }
                                /* Track */
    ::-webkit-scrollbar-track {
      background: black; 
    }
                                 
                                /* Handle */
    ::-webkit-scrollbar-thumb {
      background: black; 
    }
                                
                                /* Handle on hover */
    :-webkit-scrollbar-thumb:hover {
      background: #555; 
    } @import url("https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap");
    :root{--header-height: 3rem;
      --nav-width: 68px;
      --first-color: #2f2f2f;
      --first-color-light: white;
      --white-color: white;
      --body-font: 'Nunito', sans-serif;
      --normal-font-size: 1rem;
      --z-fixed: 100
    }
    *,::before,::after{
      box-sizing: border-box
    }
    body{
      position: relative;
      margin: var(--header-height) 0 0 0;
      padding: 0 1rem;
      font-family: var(--body-font);
      font-size: var(--normal-font-size);
      transition: .5s
    }
    a{
      text-decoration: none
    }
    .header{
      width: 100%;height: var(--header-height);
      position: fixed;
      top: 0;
      left: 0;display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 1rem;
      background-color: var(--white-color);
      z-index: var(--z-fixed);
      transition: .5s
    }
    .header_toggle{
      color: var(--first-color);
      font-size: 1.5rem;
      cursor: pointer
    }.header_img{
      width: 35px;
      height: 35px;
      display: flex;
      justify-content: center;
      border-radius: 50%;overflow: hidden
    }
    .header_img img{
      width: 40px
    }
    .l-navbar{
      position: fixed;top: 0;left: -30%;
      width: var(--nav-width);
      height: 100vh;
      background-color: var(--first-color);
      padding: .5rem 1rem 0 0;
      transition: .5s;
      z-index: var(--z-fixed)
    }
    .nav{
      height: 100%;display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden
    }
    .nav_logo, .nav_link{
      display: grid;
      grid-template-columns: max-content max-content;
      align-items: center;
      column-gap: 1rem;
      padding: .5rem 0 .5rem 1.5rem
    }.nav_logo{
      margin-bottom: 2rem
    }
    .nav_logo-icon{
      font-size: 1.25rem;
      color: var(--white-color)
    }
    .nav_logo-name{
      color: var(--white-color);
      font-weight: 700
    }
    .nav_link{
      position: relative;
      color: var(--first-color-light);
      margin-bottom: 1.5rem;
      transition: .3s
    }
    .nav_link:hover{
      color: var(--white-color)
    }
    .nav_icon{
      font-size: 1.25rem
    }
    .show{
      left: 0
    }
    .body-pd{
      padding-left: calc(var(--nav-width) + 1rem)
    }
    .active{
      color: var(--white-color)
    }
    .active::before{
      content: '';
      position: absolute;
      left: 0;
      width: 2px;
      height: 32px;
      background-color: var(--white-color)
    }
    .height-100{
      height:100vh
    }
    @media screen and (min-width: 768px){
      body{
        margin: calc(var(--header-height) + 1rem) 0 0 0;
        padding-left: calc(var(--nav-width) + 2rem)
      }
      .header{
        height: calc(var(--header-height) + 1rem);
        padding: 0 2rem 0 calc(var(--nav-width) + 2rem)
      }
      .header_img{
        width: 40px;height: 40px
      }
      .header_img img
      {width: 45px
      }
      .l-navbar{
        left: 0;padding: 1rem 1rem 0 0
      }
      .show{
        width: calc(var(--nav-width) + 156px)
      }
      .body-pd{
        padding-left: calc(var(--nav-width) + 188px)
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
                    <a href="rooms.php" class="nav_link active"> <i class='bx bx-building nav_icon'></i> <span class="nav_name">Rooms</span> </a>
                    <a href="logout.php" class="nav_link"> <i class='bx bx-log-out nav_icon'></i> <span class="nav_name">Logout</span> </a>
                </div>
            </div>
        </nav>
    </div>

    <div class="container mt-5">
        <h2>Available Rooms</h2>
        <?php if ($rooms_result && $rooms_result->num_rows > 0): ?>
            <form action="rooms.php" method="POST">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Room ID</th>
                                <th>Room Name</th> <!-- Check this column name in your database -->
                                <th>Capacity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $rooms_result->fetch_assoc()): ?>
                                <?php print_r($row); ?> <!-- Debugging output to check available columns -->
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['room_name']); ?></td> <!-- Update the column name if needed -->
                                    <td><?php echo htmlspecialchars($row['capacity']); ?></td>
                                    <td>
                                        <button type="submit" name="rent_room" value="<?php echo $row['id']; ?>" class="btn btn-primary">Rent Room</button>
                                        <input type="hidden" name="room_id" value="<?php echo $row['id']; ?>">
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        <?php else: ?>
            <p>No available rooms at the moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>

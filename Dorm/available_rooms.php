<?php
require('db_connection.php');

// Fetch available rooms
$available_rooms_query = "SELECT * FROM rooms WHERE is_available = 1";
$available_rooms = $conn->query($available_rooms_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rooms</title>
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
    } 
    @import url("https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap");
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

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: black;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
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
                    <a href="available_rooms.php" class="nav_link active"> <i class='bx bx-building nav_icon'></i> <span class="nav_name">Available Rooms</span> </a>
                    <a href="reserve_rooms.php" class="nav_link"> <i class='bx bx-bed nav_icon'></i> <span class="nav_name">Reserve Rooms</span> </a>
                    <a href="logout.php" class="nav_link"> <i class='bx bx-log-out nav_icon'></i> <span class="nav_name">Logout</span> </a>
                </div>
            </div>
        </nav>
    </div>
    <h2>Available Rooms</h2>
    <table>
        <tr>
            <th>Room Number</th>
            <th>Capacity</th>
            <th>Room Type</th>
        </tr>
        <?php while ($room = $available_rooms->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                <td><?php echo htmlspecialchars($room['room_type']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const showNavbar = (toggleId, navId, bodyId, headerId) => {
                const toggle = document.getElementById(toggleId),
                    nav = document.getElementById(navId),
                    bodypd = document.getElementById(bodyId),
                    headerpd = document.getElementById(headerId)

                if (toggle && nav && bodypd && headerpd) {
                    toggle.addEventListener('click', () => {
                        nav.classList.toggle('show')
                        toggle.classList.toggle('bx-x')
                        bodypd.classList.toggle('body-pd')
                        headerpd.classList.toggle('body-pd')
                    })
                }
            }

            showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header')
        });
    </script>
</body>
</html>

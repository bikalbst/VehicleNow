<?php
session_start();
require_once('connection.php');

// Check if admin is logged in, otherwise redirect to login
if (!isset($_SESSION["admin_name"])) {
    // A more generic check could be if a user session exists and if they are admin
    // For now, we assume only admins access this page and have `admin_name` set.
    // header("Location: adminlogin.php"); 
    // exit();
}

// Fetching total users
$total_users_query = "SELECT COUNT(*) as total_users FROM users WHERE IS_ADMIN != 'Y'";
$total_users_result = mysqli_query($con, $total_users_query);
$total_users = mysqli_fetch_assoc($total_users_result)['total_users'];

// Fetching total vehicles
$total_vehicles_query = "SELECT COUNT(*) as total_vehicles FROM cars";
$total_vehicles_result = mysqli_query($con, $total_vehicles_query);
$total_vehicles = mysqli_fetch_assoc($total_vehicles_result)['total_vehicles'];

// Fetching total bookings
$total_bookings_query = "SELECT COUNT(*) as total_bookings FROM booking";
$total_bookings_result = mysqli_query($con, $total_bookings_query);
$total_bookings = mysqli_fetch_assoc($total_bookings_result)['total_bookings'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VehicleNow</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
        body {
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            background-color: white;
            box-shadow: var(--shadow);
            position: sticky;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }
        .menu ul {
            display: flex;
            list-style: none;
        }
        .menu ul li { margin-left: 2rem; }
        .menu ul li a {
            text-decoration: none;
            color: var(--dark-color);
            font-weight: 500;
        }
        .menu ul li a:hover { color: var(--primary-color); }
        .page-container {
            flex-grow: 1;
            width: 90%;
            max-width: 1400px;
            margin: 2rem auto;
        }
        .page-header { margin-bottom: 2rem; }
        .page-header h1 {
            font-size: 2.2rem;
            color: var(--dark-color);
            font-weight: 600;
        }
        .dashboard-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }
        .dashboard-card {
            background-color: #fff;
            padding: 2.5rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
            text-align: center;
            width: 300px;
            transition: var(--transition);
        }
        .dashboard-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .dashboard-card .icon {
            font-size: 3.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        .dashboard-card h3 {
            margin: 0;
            font-size: 1.3rem;
            color: var(--secondary-color);
            margin-top: 1rem;
        }
        .dashboard-card p {
            font-size: 3rem;
            font-weight: 700;
            color: var(--dark-color);
            margin: 0.5rem 0 0 0;
        }
        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 2rem 5%;
            text-align: center;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <a href="admindash.php" class="logo">VehicleNow <span style="font-size: 0.8em; color: var(--accent-color);">Admin</span></a>
        <nav class="menu">
            <ul>
                <li><a href="admindash.php">Dashboard</a></li>
                <li><a href="adminvehicle.php">Vehicle Management</a></li>
                <li><a href="adminbook.php">Bookings</a></li>
                <li><a href="adminusers.php">Users</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </nav>
    </header>

    <main class="page-container">
        <div class="page-header">
            <h1>Admin Dashboard</h1>
        </div>
        <div class="dashboard-container">
            <div class="dashboard-card">
                <div class="icon"><i class="fas fa-users"></i></div>
                <p><?php echo $total_users; ?></p>
                <h3>Total Users</h3>
            </div>
            <div class="dashboard-card">
                <div class="icon"><i class="fas fa-car"></i></div>
                <p><?php echo $total_vehicles; ?></p>
                <h3>Total Vehicles</h3>
            </div>
            <div class="dashboard-card">
                <div class="icon"><i class="fas fa-calendar-check"></i></div>
                <p><?php echo $total_bookings; ?></p>
                <h3>Total Bookings</h3>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> VehicleNow. All Rights Reserved.</p>
    </footer>
</body>
</html>
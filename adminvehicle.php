<?php
session_start();
require_once('connection.php');

// Fetch all cars from the database
$query_cars = "SELECT * FROM cars ORDER BY CAR_ID DESC"; // Order by most recent
$result_cars = mysqli_query($con, $query_cars);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vehicles - VehicleNow Admin</title>
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
            --error-color: #e74c3c;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        html {
            scroll-behavior: smooth;
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

        /* Navbar Styles */
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
            font-size: 0.9rem;
            letter-spacing: 1px;
            padding: 0.5rem 0;
            position: relative;
            transition: var(--transition);
        }
        .menu ul li a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--primary-color);
            transition: var(--transition);
        }
        .menu ul li a:hover { color: var(--primary-color); }
        .menu ul li a:hover:after { width: 100%; }
        .auth-links, .welcome-user-container { display: flex; align-items: center; }
        .welcome-user { font-weight: 500; margin-right: 10px; }
        .profile-dropdown { position: relative; display: inline-block; }
        .profile-icon-btn {
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; cursor: pointer; border: none; margin-left: 10px;
        }
        .dropdown-content {
            display: none; position: absolute; right: 0;
            background-color: white; min-width: 180px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1; border-radius: 5px; padding: 0.5rem 0;
        }
        .dropdown-content a {
            color: var(--dark-color); padding: 12px 16px;
            text-decoration: none; display: block; font-size: 0.9rem; margin-left: 0;
        }
        .dropdown-content a:hover { background-color: #f1f1f1; color: var(--primary-color); }
        .dropdown-content.show { display: block; }

        /* Page container & Table Styles */
        .page-container {
            flex-grow: 1;
            width: 90%; /* Wider for tables */
            max-width: 1400px; /* Max width for very large screens */
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 2.2rem;
            color: var(--dark-color);
            font-weight: 600;
        }
        .btn-add {
            background-color: var(--success-color);
            color: white;
            padding: 0.7rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        .btn-add:hover { background-color: #27ae60; }
        .btn-add i { margin-right: 0.5rem; }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        .content-table thead tr {
            background-color: var(--secondary-color);
            color: white;
            text-align: left;
            font-weight: 600;
        }
        .content-table th,
        .content-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        .content-table tbody tr:nth-of-type(even) {
            background-color: #f9f9f9;
        }
        .content-table tbody tr:last-of-type {
            border-bottom: 2px solid var(--primary-color);
        }
        .content-table tbody tr:hover {
            background-color: #f1f1f1;
            color: var(--primary-color);
        }
        .action-btn {
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            margin-right: 5px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }
        .btn-delete {
            background-color: var(--error-color);
            color: white;
        }
        .btn-delete:hover { background-color: #c0392b; }
        .status-yes { color: var(--success-color); font-weight: bold; }
        .status-no { color: var(--error-color); font-weight: bold; }
        .no-data-message {
            text-align: center;
            padding: 2rem;
            font-size: 1.1rem;
            color: #777;
        }

        /* Footer Styles */
        .footer { /* ... Same footer styles as addcar.php ... */ 
            background-color: var(--dark-color);
            color: white;
            padding: 3rem 5% 1.5rem;
            margin-top: auto;
        }
        .footer-content { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem; }
        .footer-col h3 { font-size: 1.2rem; font-weight: 600; margin-bottom: 1rem; position: relative; padding-bottom: 0.5rem; }
        .footer-col h3:after { content: ''; position: absolute; left: 0; bottom: 0; width: 50px; height: 2px; background-color: var(--primary-color); }
        .footer-col p, .footer-links li { margin-bottom: 0.8rem; font-size: 0.9rem; color: #bbb; }
        .footer-links li { list-style: none; }
        .footer-links a { color: #bbb; text-decoration: none; transition: var(--transition); }
        .footer-links a:hover { color: var(--primary-color); padding-left: 5px; }
        .social-links { display: flex; margin-top: 1rem; }
        .social-links a { display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; background-color: rgba(255, 255, 255, 0.1); border-radius: 50%; margin-right: 0.8rem; color: white; text-decoration: none; transition: var(--transition); }
        .social-links a:hover { background-color: var(--primary-color); transform: translateY(-3px); }
        .footer-bottom { text-align: center; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1); font-size: 0.9rem; color: #bbb; }
    </style>
</head>
<body>
    <header class="navbar">
        <a href="admindash.php" class="logo">VehicleNow <span style="font-size: 0.8em; color: var(--accent-color);">Admin</span></a>
        <nav class="menu">
            <ul>
                <li><a href="admindash.php">Dashboard</a></li>
                <li><a href="adminvehicle.php">Vehicles Management</a></li>
                <li><a href="adminbook.php">Bookings</a></li>
                <li><a href="adminusers.php">Users</a></li>
            </ul>
        </nav>
        <div class="auth-links">
            <?php if(isset($_SESSION["user_name"]) || isset($_SESSION["admin_name"])): // Check for user or admin session ?>
                <div class="welcome-user-container">
                    <span class="welcome-user" style="margin-right: 10px;">
                        Admin: <?php echo strtok(isset($_SESSION["admin_name"]) ? $_SESSION["admin_name"] : $_SESSION["user_name"], " "); ?>
                    </span>
                    <div class="profile-dropdown">
                        <button class="profile-icon-btn" id="profileIconBtn">
                            <?php echo substr(isset($_SESSION["admin_name"]) ? $_SESSION["admin_name"] : $_SESSION["user_name"], 0, 1); ?>
                        </button>
                        <div class="dropdown-content" id="profileDropdown">
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="adminlogin.php" class="signin">Admin Login</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="page-container">
        <div class="page-header">
            <h1>Vehicle Fleet Management</h1>
            <a href="addcar.php" class="btn-add"><i class="fas fa-plus"></i> Add New Vehicle</a>
        </div>

        <?php if(mysqli_num_rows($result_cars) > 0): ?>
            <table class="content-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Fuel</th>
                        <th>Capacity</th>
                        <th>Model Year</th>
                        <th>Transmission</th>
                        <th>Price/Day (रु)</th>
                        <th>Available</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($car = mysqli_fetch_assoc($result_cars)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($car['CAR_ID']); ?></td>
                        <td><?php echo htmlspecialchars($car['CAR_NAME']); ?></td>
                        <td><?php echo htmlspecialchars($car['CAR_TYPE']); ?></td>
                        <td><?php echo htmlspecialchars($car['FUEL_TYPE']); ?></td>
                        <td><?php echo htmlspecialchars($car['CAPACITY']); ?></td>
                        <td><?php echo htmlspecialchars($car['MODEL_YEAR']); ?></td>
                        <td><?php echo htmlspecialchars($car['TRANSMISSION']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($car['PRICE'], 2)); ?></td>
                        <td>
                            <span class="status-<?php echo ($car['AVAILABLE'] == 'Y' ? 'yes' : 'no'); ?>">
                                <?php echo ($car['AVAILABLE'] == 'Y' ? 'Yes' : 'No'); ?>
                            </span>
                        </td>
                        <td>
                            <?php if(!empty($car['CAR_IMG'])): ?>
                                <img src="images/<?php echo htmlspecialchars($car['CAR_IMG']); ?>" alt="<?php echo htmlspecialchars($car['CAR_NAME']); ?>" style="width: 80px; height: auto; border-radius: 4px;">
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="deletecar.php?id=<?php echo $car['CAR_ID']; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this car? This action cannot be undone.');">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                            <!-- Add an Edit button later if needed -->
                            <!-- <a href="editcar.php?id=<?php echo $car['CAR_ID']; ?>" class="action-btn btn-edit"><i class="fas fa-edit"></i> Edit</a> -->
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data-message">No vehicles found in the database. <a href="addcar.php">Add the first one!</a></p>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-col">
                <h3>VehicleNow Admin</h3>
                <p>Manage your car rental fleet, bookings, and customers with ease.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="admindash.php">Dashboard</a></li>
                    <li><a href="adminbook.php">Bookings</a></li>
                    <li><a href="adminvehicle.php">Manage Vehicles</a></li>
                    <li><a href="adminusers.php">Registered Users</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Support</h3>
                <p><i class="fas fa-envelope"></i> admin@vehiclenow.com</p>
                <p><i class="fas fa-info-circle"></i> Version 1.0.0</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> VehicleNow Admin Panel. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        const profileIconBtn = document.getElementById('profileIconBtn');
        const profileDropdown = document.getElementById('profileDropdown');
        if (profileIconBtn && profileDropdown) {
            profileIconBtn.onclick = function() {
                profileDropdown.classList.toggle("show");
            }
            window.onclick = function(event) {
                if (!profileIconBtn.contains(event.target) && !event.target.matches('.profile-icon-btn')) {
                    if (profileDropdown.classList.contains('show')) {
                        profileDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>

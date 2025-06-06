<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Vehicle - VehicleNow Admin</title>
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

        /* Navbar Styles (Copied from available-cars.php for consistency) */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            background-color: white;
            box-shadow: var(--shadow);
            position: sticky; /* Changed to sticky for admin pages */
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: var(--transition);
        }
        .navbar.scrolled { /* May not be needed if sticky */
            padding: 0.8rem 5%;
            background-color: rgba(255, 255, 255, 0.95);
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
        .menu ul li {
            margin-left: 2rem;
        }
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
        .menu ul li a:hover {
            color: var(--primary-color);
        }
        .menu ul li a:hover:after {
            width: 100%;
        }
        .auth-links {
            display: flex;
            align-items: center;
        }
        .welcome-user-container {
            display: flex;
            align-items: center;
        }
        .welcome-user {
            font-weight: 500;
            margin-right: 10px;
        }
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }
        .profile-icon-btn {
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            cursor: pointer;
            border: none;
            margin-left: 10px;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 180px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
            padding: 0.5rem 0;
        }
        .dropdown-content a {
            color: var(--dark-color);
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 0.9rem;
            margin-left: 0;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1;
            color: var(--primary-color);
        }
        .dropdown-content.show {
            display: block;
        }
        .auth-links a.signin, .auth-links a.signup { /* For non-logged in users, though admin should be logged in */
            text-decoration: none;
            color: var(--dark-color);
            font-weight: 500;
            padding: 0.5rem 1rem;
            margin-left: 0.5rem;
            transition: var(--transition);
        }
        .auth-links a.signin {
            color: var(--primary-color);
        }
        .auth-links a.signup {
            background-color: var(--primary-color);
            color: white;
            border-radius: 4px;
        }
        .auth-links a:hover {
            opacity: 0.8;
        }

        /* Page container & Form Styles */
        .page-container {
            flex-grow: 1;
            max-width: 800px; /* Adjusted for a wider form */
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 2.2rem;
            color: var(--dark-color);
            font-weight: 600;
        }

        .form-add-car label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--secondary-color);
        }
        .form-add-car input[type="text"],
        .form-add-car input[type="number"],
        .form-add-car input[type="file"],
        .form-add-car select {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color var(--transition);
        }
        .form-add-car input[type="text"]:focus,
        .form-add-car input[type="number"]:focus,
        .form-add-car input[type="file"]:focus,
        .form-add-car select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        .form-add-car .form-row {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .form-add-car .form-group {
            flex: 1;
        }
        .form-add-car .form-group input,
        .form-add-car .form-group select {
            margin-bottom: 0; /* Remove bottom margin as row has it */
        }


        .btn-submit {
            display: inline-block;
            padding: 0.8rem 2rem;
            background-color: var(--success-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            width: 100%;
        }
        .btn-submit:hover {
            background-color: #27ae60;
        }
        .btn-home {
            display: inline-block;
            padding: 0.6rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: var(--transition);
            margin-bottom: 1.5rem;
        }
        .btn-home:hover {
            background-color: #2980b9;
        }


        /* Footer Styles (Copied from available-cars.php for consistency) */
        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 3rem 5% 1.5rem; /* Adjusted padding */
            margin-top: auto; /* Pushes footer to bottom */
        }
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        .footer-col h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        .footer-col h3:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background-color: var(--primary-color);
        }
        .footer-col p, .footer-links li {
            margin-bottom: 0.8rem;
            font-size: 0.9rem;
            color: #bbb;
        }
        .footer-links li { list-style: none; }
        .footer-links a {
            color: #bbb;
            text-decoration: none;
            transition: var(--transition);
        }
        .footer-links a:hover {
            color: var(--primary-color);
            padding-left: 5px;
        }
        .social-links { display: flex; margin-top: 1rem; }
        .social-links a {
            display: flex; align-items: center; justify-content: center;
            width: 36px; height: 36px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            margin-right: 0.8rem;
            color: white;
            text-decoration: none;
            transition: var(--transition);
        }
        .social-links a:hover {
            background-color: var(--primary-color);
            transform: translateY(-3px);
        }
        .footer-bottom {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.9rem;
            color: #bbb;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <a href="admin_dashboard.php" class="logo">VehicleNow <span style="font-size: 0.8em; color: var(--accent-color);">Admin</span></a>
        <nav class="menu">
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="adminbook.php">Bookings</a></li>
                <li><a href="adminvehicle.php">Vehicles</a></li>
                <li><a href="registeredusers.php">Users</a></li>
                <!-- Add more admin specific links if needed -->
            </ul>
        </nav>
        <div class="auth-links">
            <?php if(isset($_SESSION["user_name"])): // Assuming admin uses the same session as regular users ?>
                <div class="welcome-user-container">
                    <span class="welcome-user" style="margin-right: 10px;">Admin: <?php echo strtok($_SESSION["user_name"], " "); ?></span>
                    <div class="profile-dropdown">
                        <button class="profile-icon-btn" id="profileIconBtn">
                            A
                        </button>
                        <div class="dropdown-content" id="profileDropdown">
                            <!-- <a href="admin_profile.php">My Profile</a> -->
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Should not happen for an admin page, but as a fallback -->
                <a href="adminlogin.php" class="signin">Admin Login</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="page-container">
        <div class="page-header">
            <h1>Add New Vehicle Details</h1>
        </div>
        <a href="adminvehicle.php" class="btn-home"><i class="fas fa-arrow-left"></i> Back to Vehicle List</a>

        <form action="upload.php" method="POST" enctype="multipart/form-data" class="form-add-car">
            <div class="form-group">
                <label for="carname">Car Name:</label>
                <input type="text" id="carname" name="carname" placeholder="Enter Car Name" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="ftype">Fuel Type:</label>
                    <input type="text" id="ftype" name="ftype" placeholder="e.g., Petrol, Diesel, Electric" required>
                </div>
                <div class="form-group">
                    <label for="capacity">Capacity (Persons):</label>
                    <input type="number" id="capacity" name="capacity" min="1" placeholder="e.g., 4" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="modelyear">Model Year:</label>
                    <input type="number" id="modelyear" name="modelyear" min="1980" max="<?php echo date('Y'); ?>" placeholder="e.g., 2022" required>
                </div>
                 <div class="form-group">
                    <label for="transmission">Transmission:</label>
                    <select id="transmission" name="transmission" required>
                        <option value="" disabled selected>Select Transmission</option>
                        <option value="Automatic">Automatic</option>
                        <option value="Manual">Manual</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                 <label for="cartype">Car Type/Category:</label>
                <select id="cartype" name="cartype" required>
                    <option value="" disabled selected>Select Car Type</option>
                    <option value="Sedan">Sedan</option>
                    <option value="SUV">SUV</option>
                    <option value="Hatchback">Hatchback</option>
                    <option value="Coupe">Coupe</option>
                    <option value="Minivan">Minivan</option>
                    <option value="Truck">Truck</option>
                    <option value="Luxury">Luxury</option>
                    <option value="Convertible">Convertible</option>
                    <option value="Crossover">Crossover</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Price per Day (रु):</label>
                <input type="number" id="price" name="price" min="1" placeholder="Enter Price per Day" required>
            </div>

            <div class="form-group">
                <label for="image">Car Image:</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <input type="text" id="description" name="description" placeholder="Enter short description" ><br>
            </div>

            <button type="submit" class="btn-submit" name="addcar">ADD VEHICLE TO DATABASE</button>
        </form>
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
                    <li><a href="admin_dashboard.php">Dashboard</a></li>
                    <li><a href="adminbook.php">Bookings</a></li>
                    <li><a href="adminvehicle.php">Manage Vehicles</a></li>
                    <li><a href="registeredusers.php">Registered Users</a></li>
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
        // Profile Dropdown Toggle (if admin is logged in using user session)
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

        // Optional: Script to set max year for model year input dynamically if PHP wasn't used.
        // const currentYear = new Date().getFullYear();
        // document.getElementById('modelyear').setAttribute('max', currentYear);
    </script>
</body>
</html>
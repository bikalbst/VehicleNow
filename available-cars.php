<?php
// Start the session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "vehiclenow";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Available Cars - VehicleNow</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
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
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            background-color: white;
            box-shadow: var(--shadow);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: var(--transition);
        }
        .navbar.scrolled {
            padding: 0.8rem 5%;
            background-color: rgba(255, 255, 255, 0.95);
        }
        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            cursor: pointer;
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
        /* Add styles for .welcome-user-container if not already present from previous merge */
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
        .auth-links a.signin, .auth-links a.signup {
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

        /* Car Grid Styles */
        .cars-list-section {
            padding: 120px 5% 5rem; /* Adjusted padding for fixed navbar */
            text-align: center;
        }
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }
        .section-subtitle {
            font-size: 1.1rem;
            color: #777;
            margin-bottom: 3rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        .car-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        .car-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            text-align: left;
        }
        .car-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        .car-img {
            height: 200px;
            overflow: hidden;
        }
        .car-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }
        .car-card:hover .car-img img {
            transform: scale(1.1);
        }
        .car-info {
            padding: 1.5rem;
        }
        .car-info h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
        .car-details {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .car-detail {
            display: flex;
            align-items: center;
            margin-right: 1rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #777;
        }
        .car-detail i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        .car-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }
        .car-btn {
            display: inline-block;
            padding: 0.6rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: var(--transition);
        }
        .car-btn:hover {
            background-color: #2980b9;
        }

        /* Footer Styles */
        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 4rem 5% 2rem;
        }
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .footer-col h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
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
        .footer-col p {
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #bbb;
        }
        .footer-links li {
            margin-bottom: 0.8rem;
            list-style: none;
        }
        .footer-links a {
            color: #bbb;
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
        }
        .footer-links a:hover {
            color: var(--primary-color);
            padding-left: 5px;
        }
        .social-links {
            display: flex;
            margin-top: 1rem;
        }
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
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
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.9rem;
            color: #bbb;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo">VehicleNow</a>
        <nav class="menu">
            <ul>
                <li><a href="index.php">HOME</a></li>
                <li><a href="index.php#about-us">ABOUT</a></li>
                <li><a href="index.php#services">SERVICES</a></li>
                <li><a href="index.php#contact-us">CONTACT</a></li>
            </ul>
        </nav>
        <div class="auth-links"> 
            <?php if(isset($_SESSION["user_name"])): ?>
                <div class="welcome-user-container">
                    <span class="welcome-user" style="margin-right: 10px;">Welcome, <?php echo strtok($_SESSION["user_name"], " "); ?></span>
                    <div class="profile-dropdown">
                        <button class="profile-icon-btn" id="profileIconBtn">
                            <?php echo substr($_SESSION["user_name"], 0, 1); ?>
                        </button>
                        <div class="dropdown-content" id="profileDropdown">
                            <a href="mybookings.php">My Bookings</a>
                            <a href="editprofile.php">Edit Profile</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="signin">Sign In</a>
                <a href="register.php" class="signup">Sign Up</a>
            <?php endif; ?>
        </div>
    </header>

    <section class="cars-list-section">
        <h2 class="section-title">All Available Cars</h2>
        <p class="section-subtitle">Browse our full collection of available vehicles ready for your next adventure.</p>
        
        <div class="car-grid">
            <?php
            $sql_all_cars = "SELECT * FROM cars WHERE AVAILABLE='Y' ORDER BY CAR_ID DESC";
            $cars_result_all = $conn->query($sql_all_cars);
            
            if ($cars_result_all && $cars_result_all->num_rows > 0) {
                while ($car_item = $cars_result_all->fetch_assoc()) {
            ?>
                <div class="car-card">
                    <div class="car-img">
                        <img src="images/<?php echo htmlspecialchars($car_item['CAR_IMG']); ?>" alt="<?php echo htmlspecialchars($car_item['CAR_NAME']); ?>">
                    </div>
                    <div class="car-info">
                        <h3><?php echo htmlspecialchars($car_item['CAR_NAME']); ?></h3>
                        <div class="car-details">
                            <div class="car-detail">
                                <i class="fas fa-gas-pump"></i>
                                <span><?php echo htmlspecialchars($car_item['FUEL_TYPE']); ?></span>
                            </div>
                            <div class="car-detail">
                                <i class="fas fa-users"></i>
                                <span><?php echo htmlspecialchars($car_item['CAPACITY']); ?> Persons</span>
                            </div>
                        </div>
                        <div class="car-price">
                            रु <?php echo htmlspecialchars($car_item['PRICE']); ?>/- per day
                        </div>
                        <a href="cardetails.php?id=<?php echo $car_item['CAR_ID']; ?>" class="car-btn">View Details</a>
                    </div>
                </div>
            <?php 
                } // End while loop
            } else {
                echo "<p class=\"section-subtitle\">No cars are currently available. Please check back later!</p>";
            }
            // Close connection if it was opened just for this script, or ensure it's closed at the end of overall script execution.
            // $conn->close(); // Commented out as $conn might be used by other includes if this were part of a larger templating system.
            // For a standalone page like this, it's good to close it, but typically done after footer.
            ?>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-col">
                <h3>About VehicleNow</h3>
                <p>At VehicleNow, we offer premium car rental services with a wide range of luxury and comfort vehicles to make your journey memorable and hassle-free.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-google"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php#about-us">About Us</a></li>
                    <li><a href="index.php#services">Services</a></li>
                    <li><a href="index.php#contact-us">Contact Us</a></li>
                    <li><a href="register.php">Sign Up</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Contact Info</h3>
                <p><i class="fas fa-map-marker-alt"></i> 123 Car Street, Car City</p>
                <p><i class="fas fa-phone"></i> +123 456 7890</p>
                <p><i class="fas fa-envelope"></i> info@VehicleNow.com</p>
                <p><i class="fas fa-clock"></i> Mon - Sat: 9AM - 6PM</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date("Y"); ?> VehicleNow. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (navbar) { // Check if navbar exists
                if(window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            }
        });

        // Profile Dropdown Toggle
        const profileIconBtn = document.getElementById('profileIconBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profileIconBtn && profileDropdown) {
            profileIconBtn.onclick = function() {
                profileDropdown.classList.toggle("show");
            }
            // Close the dropdown if the user clicks outside of it
            window.onclick = function(event) {
                if (!event.target.matches('.profile-icon-btn') && !profileIconBtn.contains(event.target)) {
                    if (profileDropdown.classList.contains('show')) {
                        profileDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>
<?php
if ($conn) {
    $conn->close();
}
?> 
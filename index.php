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

// Variable to store login error message
$error_message = '';

// Check if the login form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = md5($_POST["pass"]); // Stored as MD5 hashes in the database

    // Prepare and execute the SQL query
    $sql = "SELECT FNAME, LNAME, EMAIL FROM users WHERE EMAIL = '$email' AND PASSWORD = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Email and password match, retrieve the user's name
        $row = $result->fetch_assoc();
        $user_name = $row["FNAME"] . " " . $row["LNAME"];
        $user_email = $row["EMAIL"]; // Retrieve the user's email

        // Authentication successful
        $_SESSION["user_name"] = $user_name; // Store the user's name in the session
        $_SESSION["email"] = $user_email; // Store the user's email in the session
        header("Location: cardetails.php"); // Redirect to the car-details.php page
        exit();
    } else {
        // Email or password is incorrect
        $error_message = "Invalid email or password.";
    }

    // Close the database connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarsNow - Premium Car Rental</title>
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
        
        .hamburger {
            display: none;
            cursor: pointer;
        }
        
        .auth-links {
            display: flex;
            align-items: center;
        }
        
        .auth-links a {
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
        
        .welcome-user {
            font-weight: 500;
            margin-right: 10px;
        }
        
        .logout-btn {
            color: var(--accent-color);
            cursor: pointer;
        }

        /* Profile Dropdown */
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
            margin-left: 10px; /* Added margin */
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
            margin-left: 0; /* Reset margin for dropdown links */
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
            color: var(--primary-color);
        }

        .dropdown-content.show {
            display: block;
        }
        /* End Profile Dropdown */

        .date-picker-section {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
            max-width: 1000px;
            margin: 2rem auto;
            position: relative;
            z-index: 10;
        }
        
        .date-picker-container h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
            font-size: 1.8rem;
        }

        .date-inputs {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .date-input {
            flex: 1;
            min-width: 200px;
        }
        
        .date-input label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .date-input input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .find-btn {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: block;
            margin: 0 auto;
        }
        
        .find-btn:hover {
            background-color: #c0392b;
        }
        
        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 80vh;
            padding-top: 80px;
            padding-left: 5%;
            padding-right: 5%;
            position: relative;
            background-color: #f5f7fa;
        }
        
        .hero-text {
            flex: 1;
            padding-right: 2rem;
            color: var(--dark-color);
        }
        
        .hero-image {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-image img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
        
        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }
        
        .hero-content h1 span {
            color: var(--primary-color);
        }
        
        .hero-content p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            max-width: 600px;
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            border: 2px solid var(--primary-color);
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: transparent;
            color: var(--primary-color);
        }
        
        .btn-outline {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-outline:hover {
            background-color: white;
            color: var(--dark-color);
        }
        
        .login-container {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .login-form {
            width: 100%;
            max-width: 400px;
            background-color: white;
            padding: 2.5rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }
        
        .login-form h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--dark-color);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }
        
        .login-btn {
            width: 100%;
            padding: 0.8rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .login-btn:hover {
            background-color: #2980b9;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        .cars-overview {
            padding: 5rem 5%;
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
        
        .error-message {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            padding: 0.8rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        @media (max-width: 992px) {
            .hero {
                flex-direction: column;
            }
            
            .hero-text, .login-container {
                width: 100%;
                padding: 3rem 5%;
            }
            
            .login-container {
                order: 2;
                background-color: #f5f7fa;
            }
            
            .hero-text {
                min-height: 60vh;
                justify-content: center;
                text-align: center;
                align-items: center;
            }
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem 5%;
            }
            
            .hamburger {
                display: block;
                font-size: 1.5rem;
                color: var(--dark-color);
            }
            
            .menu {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 70px);
                background-color: white;
                transition: var(--transition);
            }
            
            .menu.active {
                left: 0;
            }
            
            .menu ul {
                flex-direction: column;
                padding: 2rem 5%;
            }
            
            .menu ul li {
                margin: 1rem 0;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .car-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
        
        @media (max-width: 576px) {
            .hero-text h1 {
                font-size: 2.5rem;
            }
            
            .hero-text p {
                font-size: 1rem;
            }
            
            .login-form {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="logo">CarsNow</div>
        <nav class="menu">
            <ul>
                <li><a href="index.php">HOME</a></li>
                <li><a href="aboutus.html">ABOUT</a></li>
                <li><a href="services.php">SERVICES</a></li>
                <li><a href="contactus.html">CONTACT</a></li>
            </ul>
        </nav>
        <div class="auth-links">
            <?php if(isset($_SESSION["user_name"])): ?>
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
            <?php else: ?>
                <a href="login.php" class="signin">Sign In</a>
                <a href="register.php" class="signup">Sign Up</a>
            <?php endif; ?>
        </div>
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
    </header>

    <section class="hero">
        <div class="hero-text">
            <h1>Rent Your <span>Dream Car</span></h1>
            <p>Live the life of Luxury. Just rent a car of your wish from our vast collection. Enjoy every moment with your family and make memories that last a lifetime.</p>
            <div>
                <a href="available-cars.php" class="btn">EXPLORE CARS</a>
                <a href="services.php" class="btn btn-outline">OUR SERVICES</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://source.unsplash.com/random/800x600/?luxury,car" alt="Luxury Car">
        </div>
    </section>

    <div class="date-picker-section">
        <div class="date-picker-container">
            <form action="available-cars.php" method="GET" style="width: 100%;">
                <h2>Rent a car today</h2>
                <div class="date-inputs">
                    <div class="date-input">
                        <label for="start-date">Pick-up Date</label>
                        <input type="date" id="start-date" name="start" required>
                    </div>
                    <div class="date-input">
                        <label for="end-date">Drop-off Date</label>
                        <input type="date" id="end-date" name="end" required>
                    </div>
                </div>
                <button type="submit" class="find-btn">Find Available Cars</button>
            </form>
        </div>
    </div>

    <section id="cars" class="cars-overview">
        <h2 class="section-title">Our Premium Collection</h2>
        <p class="section-subtitle">Choose from our carefully curated collection of luxury and comfort vehicles to make your journey extraordinary.</p>
        
        <div class="car-grid">
            <?php
            require_once('connection.php');
            $sql_cars_index = "SELECT * FROM cars WHERE AVAILABLE='Y' LIMIT 3";
            $cars_result_index = mysqli_query($con, $sql_cars_index);
            $car_count_index = 0;
            if ($cars_result_index) {
                while ($car_item_index = mysqli_fetch_array($cars_result_index)) {
                    $car_count_index++;
            ?>
                <div class="car-card">
                    <div class="car-img">
                        <img src="images/<?php echo $car_item_index['CAR_IMG'] ?>" alt="<?php echo $car_item_index['CAR_NAME'] ?>">
                    </div>
                    <div class="car-info">
                        <h3><?php echo $car_item_index['CAR_NAME'] ?></h3>
                        <div class="car-details">
                            <div class="car-detail">
                                <i class="fas fa-gas-pump"></i>
                                <span><?php echo $car_item_index['FUEL_TYPE'] ?></span>
                            </div>
                            <div class="car-detail">
                                <i class="fas fa-users"></i>
                                <span><?php echo $car_item_index['CAPACITY'] ?> Persons</span>
                            </div>
                        </div>
                        <div class="car-price">
                            रु <?php echo $car_item_index['PRICE'] ?>/- per day
                        </div>
                        <a href="cardetails.php?id=<?php echo $car_item_index['CAR_ID'] ?>" class="car-btn">View Details</a>
                    </div>
                </div>
            <?php 
                }
            }
            ?>
        </div>

        <?php if ($car_count_index > 0):
        ?>
        <div style="text-align: center; margin-top: 2rem;">
            <a href="available-cars.php" class="btn" style="background-color: var(--accent-color); border-color: var(--accent-color);">Explore More Vehicles</a>
        </div>
        <?php endif; ?>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-col">
                <h3>About CarsNow</h3>
                <p>At CarsNow, we offer premium car rental services with a wide range of luxury and comfort vehicles to make your journey memorable and hassle-free.</p>
                <div class="social-links">
                    <a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
                    <a href="https://myaccount.google.com/"><i class="fab fa-google"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="aboutus.html">About Us</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="contactus.html">Contact Us</a></li>
                    <li><a href="register.php">Sign Up</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Contact Info</h3>
                <p><i class="fas fa-map-marker-alt"></i> 123 Car Street, Car City</p>
                <p><i class="fas fa-phone"></i> +123 456 7890</p>
                <p><i class="fas fa-envelope"></i> info@carsnow.com</p>
                <p><i class="fas fa-clock"></i> Mon - Sat: 9AM - 6PM</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 CarsNow. All Rights Reserved.</p>
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
        
        // Mobile menu toggle
        const hamburger = document.querySelector('.hamburger');
        const menu = document.querySelector('.menu');
        
        hamburger.addEventListener('click', function() {
            menu.classList.toggle('active');
        });

        // Set minimum date for date pickers
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start-date').min = today;
        document.getElementById('end-date').min = today;

        // Ensure end date is after start date
        document.getElementById('start-date').addEventListener('change', function() {
            document.getElementById('end-date').min = this.value;
            
            const endDate = document.getElementById('end-date');
            if (endDate.value && endDate.value < this.value) {
                endDate.value = this.value;
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

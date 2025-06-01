<?php
// Start the session
session_start();

// Database connection details
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

// Check if car ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

// Get car details
$car_id = $_GET['id'];
$sql = "SELECT * FROM cars WHERE CAR_ID = $car_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $car = $result->fetch_assoc();
} else {
    header("Location: index.php");
    exit();
}

// Get start and end dates from URL parameters if available
$start_date = isset($_GET['start']) ? $_GET['start'] : '';
$end_date = isset($_GET['end']) ? $_GET['end'] : '';

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $car['CAR_NAME']; ?> - VehicleNow</title>
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
        
        /* Header Styles */
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
        
        /* Auth links */
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
            display: flex;
            align-items: center;
        }
        
        .welcome-user .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: 600;
        }
        
        .logout-btn {
            color: var(--accent-color);
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            margin-left: 15px;
        }
        
        .logout-btn:hover {
            text-decoration: underline;
        }
        
        /* Profile Dropdown CSS - Copied from index.php */
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
            margin-left: 10px; /* Adjusted from index.php for consistency */
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 180px; /* Slightly wider for more items */
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
            /* Ensure these are not overridden by .auth-links a */
            margin-left: 0;
            font-weight: 500; /* Reset from .auth-links a if needed */
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
            color: var(--primary-color);
        }

        .dropdown-content.show {
            display: block;
        }
        /* End Profile Dropdown CSS */
        
        /* Car Details Container */
        .car-details-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 20px 50px;
        }
        
        .car-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        /* Car Image Section */
        .car-image-section {
            position: relative;
        }
        
        .car-image {
            width: 100%;
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        
        .car-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .car-price-tag {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background-color: var(--accent-color);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.2rem;
            box-shadow: var(--shadow);
        }
        
        /* Car Info Section */
        .car-info-section {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: var(--shadow);
        }
        
        .car-name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
        
        .car-type {
            font-size: 1.1rem;
            color: #777;
            margin-bottom: 1.5rem;
        }
        
        .car-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
        }
        
        .feature-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(52, 152, 219, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        
        .feature-icon i {
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        
        .feature-text span {
            display: block;
        }
        
        .feature-name {
            font-size: 0.8rem;
            color: #777;
        }
        
        .feature-value {
            font-weight: 600;
            font-size: 1rem;
        }
        
        .car-description {
            margin-bottom: 2rem;
        }
        
        .car-description h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }
        
        .car-description p {
            color: #666;
            line-height: 1.7;
        }
        
        /* Date Picker and Booking Section */
        .booking-section {
            margin-top: 2rem;
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: var(--shadow);
        }
        
        .booking-heading {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
        }
        
        .dates-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .date-input-group {
            flex: 1;
            min-width: 200px;
        }
        
        .date-input-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .date-input-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .additional-inputs-details { /* For the new text/number fields */
            display: flex;
            flex-direction: column; /* Stack them vertically */
            gap: 1rem; /* Space between these new inputs */
            width: 100%; /* Take full width of the form */
            margin-bottom: 1.5rem; /* Space before total price / button */
        }
        
        .total-price-section {
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark-color);
            text-align: right;
        }
        
        .total-price-section span {
            color: var(--accent-color);
        }
        
        .booking-cta {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .availability {
            font-weight: 600;
            color: <?php echo $car['AVAILABLE'] === 'Y' ? '#2ecc71' : '#e74c3c'; ?>;
        }
        
        .book-now-btn {
            display: inline-block;
            padding: 1rem 2rem;
            background-color: <?php echo $car['AVAILABLE'] === 'Y' ? 'var(--accent-color)' : '#999'; ?>;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            cursor: <?php echo $car['AVAILABLE'] === 'Y' ? 'pointer' : 'not-allowed'; ?>;
        }
        
        .book-now-btn:hover {
            background-color: <?php echo $car['AVAILABLE'] === 'Y' ? '#c0392b' : '#999'; ?>;
        }
        
        /* Related Cars Section */
        .related-cars {
            margin-top: 3rem;
        }
        
        .related-cars h2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
        }
        
        .car-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
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
        
        @media (max-width: 992px) {
            .car-details-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .car-image {
                height: 250px;
            }
            
            .car-features {
                grid-template-columns: 1fr;
            }
            
            .dates-form {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header class="navbar">
        <a href="index.php" class="logo">VehicleNow</a>
        <nav class="menu">
            <ul>
                <li><a href="index.php">HOME</a></li>
                <li><a href="aboutus.html">ABOUT</a></li>
                <li><a href="services.php">SERVICES</a></li>
                <li><a href="contactus.html">CONTACT</a></li>
            </ul>
        </nav>
        <div class="auth-links"> <!-- Container for auth elements -->
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

    <div class="car-details-container">
        <div class="car-details-grid">
            <div class="car-image-section">
                <div class="car-image">
                    <img src="images/<?php echo $car['CAR_IMG']; ?>" alt="<?php echo $car['CAR_NAME']; ?>">
                </div>
                <div class="car-price-tag">
                    रु <?php echo $car['PRICE']; ?>/- per day
                </div>
            </div>
            
            <div class="car-info-section">
                <h1 class="car-name"><?php echo $car['CAR_NAME']; ?></h1>
                <p class="car-type"><?php echo $car['CAR_TYPE']; ?></p>
                
                <div class="car-features">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-gas-pump"></i>
                        </div>
                        <div class="feature-text">
                            <span class="feature-name">Fuel Type</span>
                            <span class="feature-value"><?php echo $car['FUEL_TYPE']; ?></span>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="feature-text">
                            <span class="feature-name">Capacity</span>
                            <span class="feature-value"><?php echo $car['CAPACITY']; ?> Persons</span>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="feature-text">
                            <span class="feature-name">Transmission</span>
                            <span class="feature-value"><?php echo $car['TRANSMISSION']; ?></span>
                        </div>
                    </div>
                    
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="feature-text">
                            <span class="feature-name">Model Year</span>
                            <span class="feature-value"><?php echo $car['MODEL_YEAR']; ?></span>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($car['DESCRIPTION'])): ?>
                <div class="car-description">
                    <h3>Description</h3>
                    <p><?php echo $car['DESCRIPTION']; ?></p>
                </div>
                <?php endif; ?>
                
                <div class="booking-cta">
                    <div class="availability">
                        <?php if ($car['AVAILABLE'] === 'Y'): ?>
                            <i class="fas fa-check-circle"></i> Available Now
                        <?php else: ?>
                            <i class="fas fa-times-circle"></i> Currently Unavailable
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($car['AVAILABLE'] === 'Y'): ?>
                        <a href="#booking-section" class="book-now-btn">Book Now</a>
                    <?php else: ?>
                        <span class="book-now-btn">Not Available</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if ($car['AVAILABLE'] === 'Y'): ?>
        <div id="booking-section" class="booking-section">
            <h2 class="booking-heading">Book This Car</h2>
            <form class="dates-form" id="booking-form">
                <div class="date-input-group">
                    <label for="start-date">Pick-up Date</label>
                    <input type="date" id="start-date" name="start_date" value="<?php echo $start_date; ?>" required>
                </div>
                
                <div class="date-input-group">
                    <label for="end-date">Return Date</label>
                    <input type="date" id="end-date" name="end_date" value="<?php echo $end_date; ?>" required>
                </div>

                <div class="additional-inputs-details"> 
                    <div class="date-input-group">
                        <label for="booking-place">Pick-up Location</label>
                        <input type="text" id="booking-place" name="booking_place" placeholder="Enter city or specific address" required>
                    </div>
                    <div class="date-input-group">
                        <label for="duration">Duration (days)</label>
                        <input type="number" id="duration" name="duration" min="1" placeholder="Calculated automatically or enter manually" readonly required> 
                    </div>
                    <div class="date-input-group">
                        <label for="destination">Main Destination (optional)</label>
                        <input type="text" id="destination" name="destination" placeholder="e.g., National Park, City Center">
                    </div>
                </div>

                <div class="total-price-section">
                    Total Estimated Price: <span id="total-price-display">रु <?php echo $car['PRICE']; ?>/-</span>
                </div>
                
                <button type="button" id="proceed-booking" class="book-now-btn">Proceed to Booking</button>
            </form>
        </div>
        <?php endif; ?>
        
        <div class="related-cars">
            <h2>Similar Cars</h2>
            
            <div class="car-grid">
                <?php
                // Create a new connection
                $conn = new mysqli($servername, $username, $password, $database);
                
                // Get related cars (same type, excluding current car)
                $car_type = $car['CAR_TYPE'];
                $sql = "SELECT * FROM cars WHERE CAR_TYPE = '$car_type' AND CAR_ID != $car_id AND AVAILABLE = 'Y' LIMIT 3";
                $related_result = $conn->query($sql);
                
                if ($related_result->num_rows > 0) {
                    while ($related_car = $related_result->fetch_assoc()) {
                ?>
                    <div class="car-card">
                        <div class="car-img">
                            <img src="images/<?php echo $related_car['CAR_IMG']; ?>" alt="<?php echo $related_car['CAR_NAME']; ?>">
                        </div>
                        <div class="car-info">
                            <h3><?php echo $related_car['CAR_NAME']; ?></h3>
                            <div class="car-details">
                                <div class="car-detail">
                                    <i class="fas fa-gas-pump"></i>
                                    <span><?php echo $related_car['FUEL_TYPE']; ?></span>
                                </div>
                                <div class="car-detail">
                                    <i class="fas fa-users"></i>
                                    <span><?php echo $related_car['CAPACITY']; ?> Persons</span>
                                </div>
                            </div>
                            <div class="car-price">
                                रु <?php echo $related_car['PRICE']; ?>/- per day
                            </div>
                            <a href="cardetails.php?id=<?php echo $related_car['CAR_ID']; ?>" class="car-btn">View Details</a>
                        </div>
                    </div>
                <?php 
                    }
                } else {
                    echo "<p>No similar cars available at the moment.</p>";
                }
                
                // Close the connection
                $conn->close();
                ?>
            </div>
        </div>
    </div>

    <script>
        // Set minimum date for date pickers (today)
        const today = new Date().toISOString().split('T')[0];
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        const durationInput = document.getElementById('duration');
        const totalPriceDisplay = document.getElementById('total-price-display');
        const carPricePerDay = parseFloat(<?php echo $car['PRICE']; ?>);

        if(startDateInput) startDateInput.min = today;
        if(endDateInput) endDateInput.min = today;

        function calculateDurationAndPrice() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            let duration = 0;

            if (startDateInput.value && endDateInput.value && endDate >= startDate) {
                const diffTime = Math.abs(endDate - startDate);
                duration = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                if (duration === 0 && diffTime > 0) { // Same day booking, count as 1 day
                   duration = 1;
                } else if (endDate.getTime() === startDate.getTime()){ // if start and end date are same then duration is 1
                   duration = 1; 
                }
            } else if (startDateInput.value && endDateInput.value && endDate < startDate){
                 duration = 0; // Or handle as an error, for now, duration is 0
            }
            
            durationInput.value = duration > 0 ? duration : ''; // Set duration, or empty if invalid

            if (duration > 0) {
                const totalCost = carPricePerDay * duration;
                totalPriceDisplay.textContent = `रु ${totalCost.toFixed(2)}/-`;
            } else {
                // If dates are invalid or duration is 0, show price for 1 day as default or a message
                totalPriceDisplay.textContent = `रु ${carPricePerDay.toFixed(2)}/- (for 1 day)`; 
            }
        }

        if (startDateInput && endDateInput && durationInput && totalPriceDisplay) {
            startDateInput.addEventListener('change', function() {
                endDateInput.min = this.value;
                if (endDateInput.value && endDateInput.value < this.value) {
                    endDateInput.value = this.value;
                }
                calculateDurationAndPrice();
            });
            endDateInput.addEventListener('change', calculateDurationAndPrice);
            
            // Initial calculation if dates are pre-filled (e.g. from URL)
            if (startDateInput.value && endDateInput.value) {
                calculateDurationAndPrice();
            }
        }
        
        // Proceed to booking button
        document.getElementById('proceed-booking').addEventListener('click', function() {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            const calculatedDuration = durationInput.value;
            const bookingPlace = document.getElementById('booking-place').value;
            const destination = document.getElementById('destination').value;
            
            if (!startDate || !endDate) {
                alert('Please select both pick-up and return dates.');
                return;
            }
            if (!bookingPlace) {
                alert('Please enter a pick-up location.');
                return;
            }
            if (!calculatedDuration || parseInt(calculatedDuration) < 1) {
                alert('Duration must be at least 1 day. Please check your dates.');
                return;
            }
            
            let bookingUrl = `booking.php?car_id=<?php echo $car_id; ?>&start=${startDate}&end=${endDate}&duration=${calculatedDuration}&place=${encodeURIComponent(bookingPlace)}`;
            if (destination) {
                bookingUrl += `&destination=${encodeURIComponent(destination)}`;
            }

            <?php if(isset($_SESSION["user_name"])): ?>
                window.location.href = bookingUrl;
            <?php else: ?>
                alert('Please log in first to continue booking a car.');
                window.location.href = 'login.php?redirect=' + encodeURIComponent(bookingUrl); // Redirect to login then to booking with all params
            <?php endif; ?>
        });

        // JavaScript for Profile Dropdown (from index.php)
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
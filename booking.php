<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php?redirect=booking.php");
    exit();
}

// Check if car ID is provided
if (!isset($_GET['car_id'])) {
    header("Location: index.php");
    exit();
}

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

// Get car details
$car_id = $_GET['car_id'];
$sql = "SELECT * FROM cars WHERE CAR_ID = $car_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $car = $result->fetch_assoc();
} else {
    header("Location: index.php");
    exit();
}

// Get user details
$email = $_SESSION["email"];
$sql = "SELECT * FROM users WHERE EMAIL = '$email'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Get dates from query string or session storage
$start_date = isset($_GET['start']) ? $_GET['start'] : '';
$end_date = isset($_GET['end']) ? $_GET['end'] : '';

// Calculate total days and amount
$total_days = 0;
$total_amount = 0;

if (!empty($start_date) && !empty($end_date)) {
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = $start->diff($end);
    $total_days = $interval->days + 1; // Including both start and end day
    $total_amount = $total_days * $car['PRICE'];
}

// Process booking form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["book_now"])) {
    $from_date = $_POST["from_date"];
    $to_date = $_POST["to_date"];
    $days = $_POST["days"];
    $amount = $_POST["amount"];
    $payment_method = $_POST["payment_method"];
    
    // Insert booking details
    $book_date = date('Y-m-d');
    $sql = "INSERT INTO booking (CAR_ID, EMAIL, BOOK_DATE, FROM_DT, TO_DT, AMOUNT, STATUS, PAYMENT_METHOD) 
            VALUES ($car_id, '$email', '$book_date', '$from_date', '$to_date', $amount, 'Pending', '$payment_method')";
    
    if ($conn->query($sql) === TRUE) {
        $booking_id = $conn->insert_id;
        
        // Update car availability
        $sql = "UPDATE cars SET AVAILABLE = 'N' WHERE CAR_ID = $car_id";
        $conn->query($sql);
        
        // Redirect based on payment method
        if ($payment_method == 'Cash') {
            header("Location: payment-bill.php?id=$booking_id");
        } else if ($payment_method == 'Card') {
            header("Location: card-payment.php?id=$booking_id");
        } else if ($payment_method == 'UPI') {
            header("Location: esewa-payment.php?id=$booking_id");
        } else {
            header("Location: booking-confirmation.php?id=$booking_id");
        }
        exit();
    } else {
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
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
    <title>Book Your Car - VehicleNow</title>
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
        
        /* User profile */
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
            font-weight: 500; 
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
            color: var(--primary-color);
        }

        .dropdown-content.show {
            display: block;
        }
        /* End Profile Dropdown CSS */
        
        /* Booking Container */
        .booking-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 20px 50px;
        }
        
        .booking-title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: var(--dark-color);
        }
        
        .booking-content {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
        }
        
        /* Car Details Section */
        .car-details {
            flex: 1;
            min-width: 300px;
        }
        
        .car-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .car-img {
            height: 250px;
            overflow: hidden;
        }
        
        .car-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .car-info {
            padding: 1.5rem;
        }
        
        .car-name {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }
        
        .car-features {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .car-feature {
            display: flex;
            align-items: center;
        }
        
        .car-feature i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .car-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-top: 1rem;
        }
        
        /* Booking Form Section */
        .booking-form-container {
            flex: 1;
            min-width: 300px;
        }
        
        .booking-form {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 2rem;
        }
        
        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
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
        
        .form-control:disabled {
            background-color: #f9f9f9;
        }
        
        .booking-summary {
            background-color: #f9f9f9;
            padding: 1.5rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
        }
        
        .summary-label {
            font-weight: 500;
        }
        
        .summary-value {
            font-weight: 600;
        }
        
        .summary-total {
            font-size: 1.2rem;
            padding-top: 0.8rem;
            margin-top: 0.8rem;
            border-top: 1px solid #ddd;
        }
        
        .payment-section {
            margin-bottom: 1.5rem;
        }
        
        .payment-options {
            display: flex;
            gap: 1rem;
        }
        
        .payment-option {
            display: flex;
            align-items: center;
        }
        
        .payment-option input {
            margin-right: 0.8rem;
        }
        
        .book-now-btn {
            width: 100%;
            padding: 1rem;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .book-now-btn:hover {
            background-color: #c0392b;
        }
        
        .error-message {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            padding: 0.8rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .booking-content {
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
                <?php // This case should ideally not be reached due to session check at the top of the file
                      // but keeping it for robustness or if the session check is removed/altered.
                ?>
                <a href="login.php" class="signin">Sign In</a>
                <a href="register.php" class="signup">Sign Up</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="booking-container">
        <h1 class="booking-title">Complete Your Booking</h1>
        
        <?php if(isset($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="booking-content">
            <div class="car-details">
                <div class="car-card">
                    <div class="car-img">
                        <img src="images/<?php echo $car['CAR_IMG']; ?>" alt="<?php echo $car['CAR_NAME']; ?>">
                    </div>
                    <div class="car-info">
                        <h2 class="car-name"><?php echo $car['CAR_NAME']; ?></h2>
                        <div class="car-features">
                            <div class="car-feature">
                                <i class="fas fa-gas-pump"></i>
                                <span><?php echo $car['FUEL_TYPE']; ?></span>
                            </div>
                            <div class="car-feature">
                                <i class="fas fa-users"></i>
                                <span><?php echo $car['CAPACITY']; ?> Persons</span>
                            </div>
                            <div class="car-feature">
                                <i class="fas fa-cogs"></i>
                                <span><?php echo $car['TRANSMISSION']; ?></span>
                            </div>
                            <div class="car-feature">
                                <i class="fas fa-calendar-alt"></i>
                                <span><?php echo $car['MODEL_YEAR']; ?></span>
                            </div>
                        </div>
                        <div class="car-price">
                            रु <?php echo $car['PRICE']; ?>/- per day
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="booking-form-container">
                <form class="booking-form" method="POST" action="">
                    <h3 class="form-title">Booking Details</h3>
                    
                    <div class="form-group">
                        <label for="from_date">From Date</label>
                        <input type="date" id="from_date" name="from_date" class="form-control" value="<?php echo $start_date; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="to_date">To Date</label>
                        <input type="date" id="to_date" name="to_date" class="form-control" value="<?php echo $end_date; ?>" required>
                    </div>
                    
                    <div class="booking-summary">
                        <div class="summary-item">
                            <span class="summary-label">Daily Rate:</span>
                            <span class="summary-value">रु <?php echo $car['PRICE']; ?>/-</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Days:</span>
                            <span class="summary-value" id="total-days"><?php echo $total_days; ?></span>
                        </div>
                        <div class="summary-item summary-total">
                            <span class="summary-label">Total Amount:</span>
                            <span class="summary-value" id="total-amount">रु <?php echo $total_amount; ?>/-</span>
                        </div>
                    </div>
                    
                    <div class="payment-section">
                        <h3>Payment Method</h3>
                        <div class="payment-options">
                            <div class="payment-option">
                                <input type="radio" id="payment_cash" name="payment_method" value="Cash" checked>
                                <label for="payment_cash">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>Pay by Cash</span>
                                </label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="payment_card" name="payment_method" value="Card">
                                <label for="payment_card">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Debit Card</span>
                                </label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" id="payment_upi" name="payment_method" value="UPI">
                                <label for="payment_upi">
                                    <i class="fas fa-mobile-alt"></i>
                                    <span>eSewa</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="days" id="days_input" value="<?php echo $total_days; ?>">
                    <input type="hidden" name="amount" id="amount_input" value="<?php echo $total_amount; ?>">
                    
                    <button type="submit" name="book_now" class="book-now-btn">CONFIRM BOOKING</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Get elements
        const fromDateInput = document.getElementById('from_date');
        const toDateInput = document.getElementById('to_date');
        const totalDaysEl = document.getElementById('total-days');
        const totalAmountEl = document.getElementById('total-amount');
        const daysInput = document.getElementById('days_input');
        const amountInput = document.getElementById('amount_input');
        
        // Daily rate
        const dailyRate = <?php echo $car['PRICE']; ?>;
        
        // Set minimum date for date pickers (today)
        const today = new Date().toISOString().split('T')[0];
        fromDateInput.min = today;
        toDateInput.min = today;
        
        // Function to calculate total days and amount
        function calculateTotal() {
            if (fromDateInput.value && toDateInput.value) {
                const fromDate = new Date(fromDateInput.value);
                const toDate = new Date(toDateInput.value);
                
                // Check if dates are valid
                if (fromDate > toDate) {
                    toDateInput.value = fromDateInput.value;
                    return calculateTotal();
                }
                
                // Calculate difference in days
                const diffTime = Math.abs(toDate - fromDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Including both start and end day
                
                // Calculate total amount
                const totalAmount = diffDays * dailyRate;
                
                // Update elements
                totalDaysEl.textContent = diffDays;
                totalAmountEl.textContent = `रु ${totalAmount}/-`;
                
                // Update hidden inputs
                daysInput.value = diffDays;
                amountInput.value = totalAmount;
            }
        }
        
        // Add event listeners to date inputs
        fromDateInput.addEventListener('change', function() {
            toDateInput.min = this.value;
            calculateTotal();
        });
        
        toDateInput.addEventListener('change', calculateTotal);
        
        // Calculate on page load if dates are already set
        if (fromDateInput.value && toDateInput.value) {
            calculateTotal();
        }

        // JavaScript for Profile Dropdown (from index.php)
        const profileIconBtn = document.getElementById('profileIconBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profileIconBtn && profileDropdown) {
            profileIconBtn.onclick = function() {
                profileDropdown.classList.toggle("show");
            }

            // Close the dropdown if the user clicks outside of it
            window.onclick = function(event) {
                if (!event.target.matches('.profile-icon-btn')) {
                    if (profileDropdown.classList.contains('show')) {
                        profileDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>
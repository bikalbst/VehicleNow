<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Check if booking id is provided
if (!isset($_GET['id'])) {
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

// Get booking details
$booking_id = $_GET['id'];
$sql = "SELECT b.*, c.CAR_NAME, c.CAR_IMG, c.FUEL_TYPE, c.CAPACITY, c.TRANSMISSION, c.PRICE, 
        u.FNAME, u.LNAME, u.EMAIL, u.PHONE 
        FROM booking b 
        JOIN cars c ON b.CAR_ID = c.CAR_ID 
        JOIN users u ON b.EMAIL = u.EMAIL 
        WHERE b.BOOK_ID = $booking_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
} else {
    header("Location: index.php");
    exit();
}

// Calculate total days and amount
$from_date = new DateTime($booking['FROM_DT']);
$to_date = new DateTime($booking['TO_DT']);
$interval = $from_date->diff($to_date);
$total_days = $interval->days + 1; // Including both start and end day
$total_amount = $total_days * $booking['PRICE'];
$final_amount = $total_amount * 1.05; // Including 5% tax

// Process payment form submission
$payment_success = false;
$payment_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["pay_with_esewa"])) {
    // In a real implementation, you would redirect to eSewa's payment gateway
    // For this simulation, we'll just update the booking status
    
    // Update booking status to 'Confirmed'
    $sql = "UPDATE booking SET STATUS = 'Confirmed' WHERE BOOK_ID = $booking_id";
    
    if ($conn->query($sql) === TRUE) {
        $payment_success = true;
    } else {
        $payment_error = "Error updating booking status: " . $conn->error;
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
    <title>eSewa Payment - VehicleNow</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --esewa-color: #60BB46;
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
        
        .menu ul li a:hover {
            color: var(--primary-color);
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
        
        /* Page Container */
        .page-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 120px 20px 50px;
        }
        
        .page-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        /* Alert messages */
        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.2);
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.2);
        }
        
        /* Payment Container */
        .payment-container {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
        }
        
        /* Order Summary */
        .order-summary {
            flex: 1;
            min-width: 300px;
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 2rem;
        }
        
        .order-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
        }
        
        .order-info {
            margin-bottom: 1.5rem;
        }
        
        .order-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-row:last-child {
            border-bottom: none;
        }
        
        .order-row .label {
            color: #666;
        }
        
        .order-row .value {
            font-weight: 500;
        }
        
        .total-row {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent-color);
            padding-top: 1rem;
            margin-top: 1rem;
            border-top: 2px solid #eee;
        }
        
        /* eSewa Form */
        .esewa-form {
            flex: 1;
            min-width: 350px;
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 2rem;
        }
        
        .esewa-container {
            text-align: center;
        }
        
        .esewa-logo {
            width: 120px;
            margin-bottom: 1.5rem;
        }
        
        .esewa-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }
        
        .esewa-subtitle {
            color: #666;
            margin-bottom: 2rem;
        }
        
        .esewa-form-group {
            margin-bottom: 1.5rem;
        }
        
        .esewa-input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .esewa-input:focus {
            border-color: var(--esewa-color);
            outline: none;
        }
        
        .esewa-btn {
            width: 100%;
            padding: 1rem;
            background-color: var(--esewa-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .esewa-btn:hover {
            background-color: #52a33c;
        }
        
        .esewa-btn i {
            margin-right: 0.5rem;
            font-size: 1.2rem;
        }
        
        .secure-payment {
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 0.9rem;
        }
        
        .secure-payment i {
            color: var(--esewa-color);
            margin-right: 0.5rem;
        }
        
        .alternate-payment {
            margin-top: 2rem;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 1.5rem;
        }
        
        .alternate-payment p {
            margin-bottom: 1rem;
            color: #666;
        }
        
        .go-back-btn {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            background-color: transparent;
            border: 1px solid var(--dark-color);
            color: var(--dark-color);
            text-decoration: none;
            border-radius: 5px;
            transition: var(--transition);
            font-weight: 500;
        }
        
        .go-back-btn:hover {
            background-color: var(--dark-color);
            color: white;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .payment-container {
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
        <?php if(isset($_SESSION["user_name"])): ?>
            <div class="welcome-user">
                <div class="user-avatar">
                    <?php echo substr($_SESSION["user_name"], 0, 1); ?>
                </div>
                <span><?php echo $_SESSION["user_name"]; ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        <?php else: ?>
            <div class="auth-links">
                <a href="login.php" class="signin">Sign In</a>
                <a href="register.php" class="signup">Sign Up</a>
            </div>
        <?php endif; ?>
    </header>

    <div class="page-container">
        <h1 class="page-title">Pay with eSewa</h1>
        
        <?php if ($payment_success): ?>
            <div class="alert alert-success">
                <h3><i class="fas fa-check-circle"></i> Payment Successful!</h3>
                <p>Your booking has been confirmed. Redirecting to the confirmation page...</p>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = "booking-confirmation.php?id=<?php echo $booking_id; ?>";
                }, 3000);
            </script>
        <?php elseif (!empty($payment_error)): ?>
            <div class="alert alert-danger">
                <h3><i class="fas fa-exclamation-circle"></i> Payment Failed</h3>
                <p><?php echo $payment_error; ?></p>
            </div>
        <?php endif; ?>
        
        <div class="payment-container">
            <div class="order-summary">
                <h2 class="order-title">Order Summary</h2>
                <div class="order-info">
                    <div class="order-row">
                        <span class="label">Car</span>
                        <span class="value"><?php echo $booking['CAR_NAME']; ?></span>
                    </div>
                    <div class="order-row">
                        <span class="label">Duration</span>
                        <span class="value"><?php echo $total_days; ?> days</span>
                    </div>
                    <div class="order-row">
                        <span class="label">Rental Period</span>
                        <span class="value"><?php echo date('d M Y', strtotime($booking['FROM_DT'])); ?> - <?php echo date('d M Y', strtotime($booking['TO_DT'])); ?></span>
                    </div>
                    <div class="order-row">
                        <span class="label">Daily Rate</span>
                        <span class="value">रु <?php echo number_format($booking['PRICE']); ?></span>
                    </div>
                    <div class="order-row">
                        <span class="label">Subtotal</span>
                        <span class="value">रु <?php echo number_format($total_amount); ?></span>
                    </div>
                    <div class="order-row">
                        <span class="label">Tax (5%)</span>
                        <span class="value">रु <?php echo number_format($total_amount * 0.05); ?></span>
                    </div>
                    <div class="order-row total-row">
                        <span class="label">Total</span>
                        <span class="value">रु <?php echo number_format($final_amount); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="esewa-form">
                <div class="esewa-container">
                    <img src="https://blog.esewa.com.np/wp-content/uploads/2018/08/logo.png" alt="eSewa Logo" class="esewa-logo">
                    <h2 class="esewa-title">Pay using eSewa</h2>
                    <p class="esewa-subtitle">Fast, secure, and convenient way to pay</p>
                    
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $booking_id; ?>">
                        <div class="esewa-form-group">
                            <input type="text" class="esewa-input" placeholder="eSewa ID / Mobile Number" required>
                        </div>
                        
                        <div class="esewa-form-group">
                            <input type="password" class="esewa-input" placeholder="MPIN" required>
                        </div>
                        
                        <button type="submit" name="pay_with_esewa" class="esewa-btn">
                            <i class="fas fa-wallet"></i> Pay रु <?php echo number_format($final_amount); ?>
                        </button>
                    </form>
                    
                    <div class="secure-payment">
                        <i class="fas fa-lock"></i> Secured by eSewa Payment Gateway
                    </div>
                    
                    <div class="alternate-payment">
                        <p>Don't have an eSewa account?</p>
                        <a href="payment-bill.php?id=<?php echo $booking_id; ?>" class="go-back-btn">
                            Pay by Cash Instead
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 
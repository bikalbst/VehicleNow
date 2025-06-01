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
$sql = "SELECT b.*, c.CAR_NAME, c.PRICE, u.FNAME, u.LNAME, u.EMAIL 
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["process_payment"])) {
    // In a real application, you would process the payment with a payment gateway
    // For this example, we'll just simulate a successful payment
    
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
    <title>Card Payment - VehicleNow</title>
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
        
        /* Credit Card Form */
        .card-form {
            flex: 1;
            min-width: 350px;
        }
        
        .card-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            padding: 2rem;
            position: relative;
        }
        
        /* Credit Card Display */
        .card-display {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            height: 200px;
            border-radius: 16px;
            padding: 25px;
            position: relative;
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
            overflow: hidden;
        }
        
        .card-display::before {
            content: '';
            position: absolute;
            top: -100px;
            left: -100px;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .card-display::after {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        
        .card-chip {
            width: 50px;
            height: 40px;
            background: linear-gradient(135deg, #ddd, #999);
            border-radius: 8px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .card-chip::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 30px;
            height: 20px;
            background: linear-gradient(135deg, #888, #bbb);
            border-radius: 4px;
        }
        
        .card-number {
            color: white;
            font-size: 1.5rem;
            letter-spacing: 2px;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }
        
        .card-details {
            display: flex;
            justify-content: space-between;
            color: white;
            font-size: 0.9rem;
            position: relative;
            z-index: 1;
        }
        
        .card-details .name {
            text-transform: uppercase;
        }
        
        .card-details .expiry {
            display: flex;
            flex-direction: column;
        }
        
        .card-details .expiry span {
            font-size: 0.7rem;
            margin-bottom: 2px;
        }
        
        .card-brand {
            position: absolute;
            bottom: 20px;
            right: 30px;
            font-size: 2rem;
            color: white;
            z-index: 1;
        }
        
        /* Form */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }
        
        .form-row {
            display: flex;
            gap: 1rem;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .btn {
            width: 100%;
            padding: 1rem;
            background-color: var(--primary-color);
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
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .btn i {
            margin-right: 0.5rem;
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
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .card-number {
                font-size: 1.2rem;
            }
            
            .card-details {
                font-size: 0.8rem;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
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
        <h1 class="page-title">Complete Your Payment</h1>
        
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
            
            <div class="card-form">
                <div class="card-container">
                    <div class="card-display">
                        <div class="card-chip"></div>
                        <div class="card-number" id="displayCardNumber">•••• •••• •••• ••••</div>
                        <div class="card-details">
                            <div class="name" id="displayCardName">YOUR NAME</div>
                            <div class="expiry">
                                <span>VALID THRU</span>
                                <div id="displayExpiry">MM/YY</div>
                            </div>
                        </div>
                        <div class="card-brand">
                            <i class="fab fa-cc-visa"></i>
                        </div>
                    </div>
                    
                    <form id="payment-form" method="POST">
                        <div class="form-group">
                            <label for="card-number">Card Number</label>
                            <input type="text" class="form-control" id="card-number" placeholder="1234 5678 9012 3456" maxlength="19" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="card-name">Cardholder Name</label>
                            <input type="text" class="form-control" id="card-name" placeholder="John Doe" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiry-date">Expiry Date</label>
                                <input type="text" class="form-control" id="expiry-date" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" class="form-control" id="cvv" placeholder="123" maxlength="3" required>
                            </div>
                        </div>
                        
                        <button type="submit" name="process_payment" class="btn">
                            <i class="fas fa-lock"></i> Pay रु <?php echo number_format($final_amount); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Format card number with spaces
        document.getElementById('card-number').addEventListener('input', function(e) {
            let val = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let newVal = '';
            
            for(let i = 0; i < val.length; i++) {
                if(i % 4 === 0 && i > 0) newVal += ' ';
                newVal += val[i];
            }
            
            e.target.value = newVal;
            
            // Update display
            let displayNumber = newVal;
            if (displayNumber.length > 0) {
                // Show only last 4 digits, mask the rest
                displayNumber = displayNumber.replace(/\d(?=\d{4})/g, "•");
            } else {
                displayNumber = '•••• •••• •••• ••••';
            }
            document.getElementById('displayCardNumber').textContent = displayNumber;
        });
        
        // Format expiry date (MM/YY)
        document.getElementById('expiry-date').addEventListener('input', function(e) {
            let val = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let newVal = '';
            
            if (val.length > 0) {
                newVal = val.substring(0, 2);
                if (val.length > 2) {
                    newVal += '/' + val.substring(2);
                }
            }
            
            e.target.value = newVal;
            
            // Update display
            document.getElementById('displayExpiry').textContent = newVal || 'MM/YY';
        });
        
        // Update cardholder name display
        document.getElementById('card-name').addEventListener('input', function(e) {
            let val = e.target.value;
            document.getElementById('displayCardName').textContent = val || 'YOUR NAME';
        });
        
        // Allow only numbers for CVV
        document.getElementById('cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
        
        // Form validation
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            // In a real app, you would validate the card details and process the payment
            // For this demo, just let the form submit normally
        });
    </script>
</body>
</html> 
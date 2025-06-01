<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit();
}

// Check if booking ID is provided
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
$sql = "SELECT b.*, c.CAR_NAME, c.CAR_IMG, c.PRICE 
        FROM booking b 
        JOIN cars c ON b.CAR_ID = c.CAR_ID 
        WHERE b.BOOK_ID = $booking_id AND b.EMAIL = '".$_SESSION["email"]."'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
} else {
    header("Location: index.php");
    exit();
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - VehicleNow</title>
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
            --success-color: #2ecc71;
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
        
        /* Confirmation Container */
        .confirmation-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 120px 20px 50px;
        }
        
        .confirmation-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .confirmation-header i {
            font-size: 4rem;
            color: var(--success-color);
            margin-bottom: 1rem;
            display: block;
        }
        
        .confirmation-header h1 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
        
        .confirmation-header p {
            color: #777;
            font-size: 1.1rem;
        }
        
        .confirmation-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        
        .confirmation-car {
            display: flex;
            align-items: center;
            padding: 2rem;
            border-bottom: 1px solid #eee;
        }
        
        .car-img-sm {
            width: 120px;
            height: 80px;
            border-radius: 5px;
            overflow: hidden;
            margin-right: 1.5rem;
        }
        
        .car-img-sm img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .confirmation-car-info h2 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
        
        .price-tag {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--accent-color);
        }
        
        .booking-details {
            padding: 2rem;
            border-bottom: 1px solid #eee;
        }
        
        .booking-details h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--dark-color);
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        
        .detail-item {
            margin-bottom: 0.5rem;
        }
        
        .detail-label {
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 0.3rem;
        }
        
        .detail-value {
            font-weight: 500;
        }
        
        .total-amount {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-top: 1.5rem;
            text-align: right;
        }
        
        .confirmation-footer {
            padding: 2rem;
            text-align: center;
        }
        
        .confirmation-footer p {
            margin-bottom: 1.5rem;
            color: #777;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        
        .dashboard-btn, .home-btn {
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .dashboard-btn {
            background-color: var(--primary-color);
            color: white;
        }
        
        .home-btn {
            background-color: transparent;
            color: var(--dark-color);
            border: 1px solid #ddd;
        }
        
        .dashboard-btn:hover {
            background-color: #2980b9;
        }
        
        .home-btn:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        @media (max-width: 576px) {
            .confirmation-car {
                flex-direction: column;
                text-align: center;
            }
            
            .car-img-sm {
                margin-right: 0;
                margin-bottom: 1rem;
                width: 100%;
                height: 150px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .dashboard-btn, .home-btn {
                width: 100%;
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
        <div class="welcome-user">
            <div class="user-avatar">
                <?php echo substr($_SESSION["user_name"], 0, 1); ?>
            </div>
            <span><?php echo $_SESSION["user_name"]; ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="confirmation-container">
        <div class="confirmation-header">
            <i class="fas fa-check-circle"></i>
            <h1>Booking Confirmed!</h1>
            <p>Your car rental booking has been successfully placed.</p>
        </div>
        
        <div class="confirmation-card">
            <div class="confirmation-car">
                <div class="car-img-sm">
                    <img src="images/<?php echo $booking['CAR_IMG']; ?>" alt="<?php echo $booking['CAR_NAME']; ?>">
                </div>
                <div class="confirmation-car-info">
                    <h2><?php echo $booking['CAR_NAME']; ?></h2>
                    <p class="price-tag">रु <?php echo $booking['PRICE']; ?>/- per day</p>
                </div>
            </div>
            
            <div class="booking-details">
                <h3>Booking Details</h3>
                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">Booking ID</div>
                        <div class="detail-value">#<?php echo $booking['BOOK_ID']; ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Booking Date</div>
                        <div class="detail-value"><?php echo date('d M Y', strtotime($booking['BOOK_DATE'])); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">From Date</div>
                        <div class="detail-value"><?php echo date('d M Y', strtotime($booking['FROM_DT'])); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">To Date</div>
                        <div class="detail-value"><?php echo date('d M Y', strtotime($booking['TO_DT'])); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value"><?php echo $booking['STATUS']; ?></div>
                    </div>
                </div>
                
                <div class="total-amount">
                    Total Amount: रु <?php echo $booking['AMOUNT']; ?>/-
                </div>
            </div>
            
            <div class="confirmation-footer">
                <p>A confirmation email with your booking details has been sent to your registered email address.</p>
                <div class="action-buttons">
                    <a href="dashboard.php" class="dashboard-btn">Go to Dashboard</a>
                    <a href="index.php" class="home-btn">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 
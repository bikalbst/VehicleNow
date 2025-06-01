<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
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

// Get user details
$email = $_SESSION["email"];
$sql = "SELECT * FROM users WHERE EMAIL = '$email'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Get user's bookings
$sql = "SELECT b.*, c.CAR_NAME, c.CAR_IMG 
        FROM booking b 
        JOIN cars c ON b.CAR_ID = c.CAR_ID 
        WHERE b.EMAIL = '$email' 
        ORDER BY b.BOOK_ID DESC";
$bookingsResult = $conn->query($sql);

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - VehicleNow</title>
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
            --warning-color: #f39c12;
            --pending-color: #3498db;
            --cancelled-color: #e74c3c;
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
        
        .menu ul li a.active {
            color: var(--primary-color);
        }
        
        .menu ul li a.active:after {
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
        
        /* Dashboard Container */
        .dashboard-container {
            padding: 120px 5% 50px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .dashboard-header h1 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .dashboard-tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 2rem;
        }
        
        .dashboard-tab {
            padding: 1rem 1.5rem;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            border-bottom: 2px solid transparent;
        }
        
        .dashboard-tab.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
        }
        
        .dashboard-content {
            display: none;
        }
        
        .dashboard-content.active {
            display: block;
        }
        
        /* Profile Section */
        .profile-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .profile-item {
            margin-bottom: 1rem;
        }
        
        .profile-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 0.5rem;
        }
        
        .profile-value {
            font-size: 1.1rem;
        }
        
        .profile-actions {
            margin-top: 1.5rem;
            display: flex;
            justify-content: flex-end;
        }
        
        .profile-btn {
            padding: 0.6rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }
        
        .profile-btn:hover {
            background-color: #2980b9;
        }
        
        /* Bookings Section */
        .booking-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
            display: flex;
            flex-direction: column;
        }
        
        .booking-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        
        .booking-img {
            height: 200px;
            overflow: hidden;
        }
        
        .booking-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .booking-info {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .booking-car-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .booking-details {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .booking-detail {
            flex: 1 0 calc(50% - 1rem);
        }
        
        .booking-detail-label {
            font-size: 0.8rem;
            color: #777;
            margin-bottom: 0.2rem;
        }
        
        .booking-detail-value {
            font-weight: 500;
        }
        
        .booking-status {
            margin-top: auto;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            display: inline-block;
            font-weight: 500;
            font-size: 0.9rem;
            width: fit-content;
        }
        
        .status-confirmed {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        .status-pending {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--pending-color);
            border: 1px solid var(--pending-color);
        }
        
        .status-cancelled {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--cancelled-color);
            border: 1px solid var(--cancelled-color);
        }
        
        .status-completed {
            background-color: rgba(52, 73, 94, 0.1);
            color: var(--secondary-color);
            border: 1px solid var(--secondary-color);
        }
        
        .no-bookings {
            text-align: center;
            padding: 3rem;
            color: #777;
        }
        
        .no-bookings i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #ddd;
        }
        
        .no-bookings p {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }
        
        .book-now-btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background-color: var(--primary-color);
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .book-now-btn:hover {
            background-color: #2980b9;
        }
        
        @media (max-width: 768px) {
            .booking-card {
                flex-direction: column;
            }
            
            .booking-img {
                height: 180px;
            }
            
            .booking-info {
                padding: 1rem;
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
                <?php echo substr($user["FNAME"], 0, 1); ?>
            </div>
            <span><?php echo $_SESSION["user_name"]; ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>My Dashboard</h1>
        </div>
        
        <div class="dashboard-tabs">
            <div class="dashboard-tab active" data-tab="profile">My Profile</div>
            <div class="dashboard-tab" data-tab="bookings">My Bookings</div>
        </div>
        
        <div class="dashboard-content active" id="profile-content">
            <div class="profile-card">
                <div class="profile-info">
                    <div class="profile-item">
                        <div class="profile-label">First Name</div>
                        <div class="profile-value"><?php echo $user["FNAME"]; ?></div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">Last Name</div>
                        <div class="profile-value"><?php echo $user["LNAME"]; ?></div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">Email</div>
                        <div class="profile-value"><?php echo $user["EMAIL"]; ?></div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">Phone</div>
                        <div class="profile-value"><?php echo $user["PHONE"]; ?></div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">License Number</div>
                        <div class="profile-value"><?php echo $user["LICENSE_NO"]; ?></div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">Address</div>
                        <div class="profile-value"><?php echo $user["ADDRESS"]; ?></div>
                    </div>
                </div>
                <div class="profile-actions">
                    <a href="edit-profile.php" class="profile-btn">Edit Profile</a>
                </div>
            </div>
        </div>
        
        <div class="dashboard-content" id="bookings-content">
            <?php if ($bookingsResult->num_rows > 0): ?>
                <div class="booking-grid">
                    <?php while ($booking = $bookingsResult->fetch_assoc()): ?>
                        <div class="booking-card">
                            <div class="booking-img">
                                <img src="images/<?php echo $booking['CAR_IMG']; ?>" alt="<?php echo $booking['CAR_NAME']; ?>">
                            </div>
                            <div class="booking-info">
                                <div class="booking-car-name"><?php echo $booking['CAR_NAME']; ?></div>
                                <div class="booking-details">
                                    <div class="booking-detail">
                                        <div class="booking-detail-label">Booking ID</div>
                                        <div class="booking-detail-value">#<?php echo $booking['BOOK_ID']; ?></div>
                                    </div>
                                    <div class="booking-detail">
                                        <div class="booking-detail-label">Booking Date</div>
                                        <div class="booking-detail-value"><?php echo date('d M Y', strtotime($booking['BOOK_DATE'])); ?></div>
                                    </div>
                                    <div class="booking-detail">
                                        <div class="booking-detail-label">From Date</div>
                                        <div class="booking-detail-value"><?php echo date('d M Y', strtotime($booking['FROM_DT'])); ?></div>
                                    </div>
                                    <div class="booking-detail">
                                        <div class="booking-detail-label">To Date</div>
                                        <div class="booking-detail-value"><?php echo date('d M Y', strtotime($booking['TO_DT'])); ?></div>
                                    </div>
                                    <div class="booking-detail">
                                        <div class="booking-detail-label">Total Amount</div>
                                        <div class="booking-detail-value">रु <?php echo $booking['AMOUNT']; ?>/-</div>
                                    </div>
                                </div>
                                
                                <?php
                                $statusClass = '';
                                switch ($booking['STATUS']) {
                                    case 'Confirmed':
                                        $statusClass = 'status-confirmed';
                                        break;
                                    case 'Pending':
                                        $statusClass = 'status-pending';
                                        break;
                                    case 'Cancelled':
                                        $statusClass = 'status-cancelled';
                                        break;
                                    case 'Completed':
                                        $statusClass = 'status-completed';
                                        break;
                                    default:
                                        $statusClass = 'status-pending';
                                }
                                ?>
                                
                                <div class="booking-status <?php echo $statusClass; ?>">
                                    <?php echo $booking['STATUS']; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-bookings">
                    <i class="fas fa-calendar-xmark"></i>
                    <p>You haven't made any bookings yet.</p>
                    <a href="index.php#cars" class="book-now-btn">Book a Car Now</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Tab switching functionality
        const tabs = document.querySelectorAll('.dashboard-tab');
        const contents = document.querySelectorAll('.dashboard-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.getAttribute('data-tab');
                
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding content
                tab.classList.add('active');
                document.getElementById(`${target}-content`).classList.add('active');
            });
        });
    </script>
</body>
</html> 
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

// Get user details (for header)
$email = $_SESSION["email"];
$userSql = "SELECT FNAME FROM users WHERE EMAIL = '$email'";
$userResult = $conn->query($userSql);
$user = $userResult->fetch_assoc();


// Get user's bookings
$sql = "SELECT b.*, c.CAR_NAME, c.CAR_IMG 
        FROM booking b 
        JOIN cars c ON b.CAR_ID = c.CAR_ID 
        WHERE b.EMAIL = '$email' 
        ORDER BY b.BOOK_ID DESC";
$bookingsResult = $conn->query($sql);

// Close the database connection (will be closed after fetching all data)
// $conn->close(); // Close it at the end of the script
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - VehicleNow</title>
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
        
        .menu ul li a:hover,
        .menu ul li a.active {
            color: var(--primary-color);
        }
        
        .menu ul li a:hover:after,
        .menu ul li a.active:after {
            width: 100%;
        }

        /* Profile Dropdown (copied from index.php for consistency) */
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
            display: none; /* Hidden by default, shown by JS */
            position: absolute;
            right: 0;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
            padding: 0.5rem 0;
        }
        .dropdown-content.show { /* Added for JS to show */
            display: block;
        }

        .dropdown-content a {
            color: var(--dark-color);
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 0.9rem;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
            color: var(--primary-color);
        }
         .welcome-user-container { /* Copied from index.php */
            display: flex;
            align-items: center;
        }
         .welcome-user { /* Copied from index.php */
            font-weight: 500;
            margin-right: 10px;
        }
        
        /* Dashboard Container (general page styling) */
        .page-container { /* Renamed from dashboard-container for clarity */
            padding: 120px 5% 50px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .page-header h1 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 2rem;
        }
        
        /* Bookings Section */
        .booking-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
            display: flex;
            flex-direction: column; /* Default for larger screens if needed */
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
            flex: 1 0 calc(50% - 1rem); /* Two items per row */
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
            padding: 0.5rem 1rem;
            border-radius: 4px;
            display: inline-block;
            font-weight: 500;
            font-size: 0.9rem;
            width: fit-content;
        }
        
        .booking-footer-actions {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding-top: 1rem;
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
        
        .status-completed { /* Added from dashboard css */
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
        
        .booking-actions {
        }

        .booking-action-btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-cancel {
            background-color: var(--warning-color);
            color: white;
        }
        .btn-cancel:hover {
            background-color: #d48c0c; /* Darker warning */
        }

        .btn-delete-booking {
            background-color: var(--accent-color);
            color: white;
        }
        .btn-delete-booking:hover {
            background-color: #c0392b; /* Darker accent */
        }
        
        @media (max-width: 768px) {
            .booking-card {
                /* Already flex-direction: column, so this media query might not be needed for this property */
            }
            .booking-img {
                height: 180px;
            }
            .booking-info {
                padding: 1rem;
            }
            .booking-detail {
                 flex: 1 0 100%; /* Full width on smaller screens */
            }
        }

        .user-message-success {
            background-color: #d4edda; /* Light green */
            color: #155724; /* Dark green */
            border-color: #c3e6cb;
        }
        .user-message-error {
            background-color: #f8d7da; /* Light red */
            color: #721c24; /* Dark red */
            border-color: #f5c6cb;
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
        <div class="auth-links"> <!-- Copied from index.php -->
            <?php if(isset($_SESSION["user_name"])): ?>
                <div class="welcome-user-container">
                    <span class="welcome-user"><?php echo $_SESSION["user_name"]; ?></span>
                    <div class="profile-dropdown">
                        <button class="profile-icon-btn" onclick="toggleDropdown()">
                            <i class="fas fa-user"></i>
                        </button>
                        <div class="dropdown-content" id="profileDropdownContent">
                            <a href="mybookings.php" class="active">My Bookings</a> <!-- Set as active -->
                            <a href="editprofile.php">Edit Profile</a>
                            <a href="logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Should not happen on this page due to auth check, but as a fallback -->
                <a href="login.php" class="signin">Sign In</a>
                <a href="register.php" class="signup">Sign Up</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="page-container">
        <div class="page-header">
            <h1>My Bookings</h1>
        </div>

        <?php 
        if (isset($_SESSION['message'])): 
            $message = $_SESSION['message'];
            unset($_SESSION['message']); // Clear the message after displaying
        ?>
            <div class="user-message user-message-<?php echo htmlspecialchars($message['type']); ?>" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 5px; border: 1px solid transparent;">
                <?php echo htmlspecialchars($message['text']); ?>
            </div>
        <?php endif; ?>
        
        <div id="bookings-content"> <!-- Removed dashboard-content class -->
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
                                
                                <div class="booking-footer-actions">
                                    <div class="booking-status <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($booking['STATUS']); ?>
                                    </div>

                                    <div class="booking-actions">
                                        <?php if ($booking['STATUS'] === 'Pending'): ?>
                                            <a href="update_booking_status.php?book_id=<?php echo $booking['BOOK_ID']; ?>&action=cancel"
                                               class="booking-action-btn btn-cancel"
                                               onclick="return confirm('Are you sure you want to cancel this booking?');">
                                                <i class="fas fa-times-circle"></i> Cancel Booking
                                            </a>
                                        <?php elseif ($booking['STATUS'] === 'Returned' || $booking['STATUS'] === 'Completed' || $booking['STATUS'] === 'Cancelled'): ?>
                                            <a href="update_booking_status.php?book_id=<?php echo $booking['BOOK_ID']; ?>&action=delete"
                                               class="booking-action-btn btn-delete-booking"
                                               onclick="return confirm('Are you sure you want to delete this booking record? This action cannot be undone.');">
                                                <i class="fas fa-trash-alt"></i> Delete Record
                                            </a>
                                        <?php endif; ?>
                                    </div>
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
        // Profile Dropdown Toggle (copied from index.php)
        function toggleDropdown() {
            document.getElementById("profileDropdownContent").classList.toggle("show");
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.profile-icon-btn') && !event.target.closest('.profile-icon-btn')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?> 
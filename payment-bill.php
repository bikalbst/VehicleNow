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

// Handle email sending
$email_sent = false;
$email_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["send_email"])) {
    $to_email = $_POST["email"];
    
    // Email headers and content would be here
    // For this example, we're just setting a flag
    $email_sent = true;
    
    // In a real implementation, you would use PHP mail() or a library like PHPMailer
    // $success = mail($to_email, "Your Car Rental Bill", $email_body, $headers);
    // if ($success) {
    //     $email_sent = true;
    // } else {
    //     $email_error = "Failed to send email. Please try again.";
    // }
}

// Close the database connection
$conn->close();

// Calculate total days
$from_date = new DateTime($booking['FROM_DT']);
$to_date = new DateTime($booking['TO_DT']);
$interval = $from_date->diff($to_date);
$total_days = $interval->days + 1; // Including both start and end day

// Calculate total amount
$daily_rate = $booking['PRICE'];
$total_amount = $total_days * $daily_rate;

// Generate a unique invoice number
$invoice_number = 'INV-' . date('Ymd') . '-' . $booking_id;

// Format dates for display
$formatted_from_date = $from_date->format('d M Y');
$formatted_to_date = $to_date->format('d M Y');
$formatted_booking_date = date('d M Y', strtotime($booking['BOOK_DATE']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Rental Bill - VehicleNow</title>
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
        
        /* User profile - Existing styles, will be augmented by dropdown styles */
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
            z-index: 1001; /* Ensure it's above other elements if navbar is fixed */
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
        
        /* Page Container */
        .page-container {
            max-width: 850px;
            margin: 0 auto;
            padding: 120px 20px 50px;
        }
        
        /* Bill Container */
        .bill-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .bill-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .company-info h2 {
            color: var(--primary-color);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        
        .company-info p {
            color: #777;
            font-size: 0.9rem;
        }
        
        .bill-details {
            text-align: right;
        }
        
        .bill-details h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
        
        .bill-details .invoice-number {
            font-weight: 600;
            color: var(--accent-color);
        }
        
        .bill-details .date {
            color: #777;
            font-size: 0.9rem;
        }
        
        .customer-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        
        .billing-to, .car-info {
            flex: 1;
        }
        
        .billing-to h4, .car-info h4 {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
        
        .billing-to p, .car-info p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        
        .rental-details {
            margin-bottom: 2rem;
        }
        
        .rental-details h4 {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
        
        .rental-period {
            display: flex;
            justify-content: space-between;
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .rental-period div {
            text-align: center;
        }
        
        .rental-period div span {
            display: block;
        }
        
        .rental-period div .label {
            font-size: 0.8rem;
            color: #777;
            margin-bottom: 0.3rem;
        }
        
        .rental-period div .value {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .bill-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        
        .bill-table th {
            background-color: #f8f9fa;
            padding: 0.8rem;
            text-align: left;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .bill-table td {
            padding: 0.8rem;
            border-bottom: 1px solid #eee;
        }
        
        .bill-table .amount {
            text-align: right;
            font-weight: 500;
        }
        
        .bill-total {
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
        }
        
        .bill-total-table {
            width: 50%;
            border-collapse: collapse;
        }
        
        .bill-total-table td {
            padding: 0.5rem;
        }
        
        .bill-total-table .total-label {
            text-align: left;
            color: #666;
        }
        
        .bill-total-table .total-value {
            text-align: right;
            font-weight: 500;
        }
        
        .bill-total-table .grand-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent-color);
        }
        
        .bill-notes {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        
        .bill-notes h4 {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }
        
        .bill-notes p {
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Action Buttons */
        .bill-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            font-size: 0.9rem;
        }
        
        .btn i {
            margin-right: 0.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Email Form */
        .email-form {
            margin-top: 1.5rem;
            display: none;
        }
        
        .email-form.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        
        /* Alert messages */
        .alert {
            padding: 0.8rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.2);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .bill-header, .customer-info {
                flex-direction: column;
            }
            
            .bill-details, .car-info {
                text-align: left;
                margin-top: 1rem;
            }
            
            .bill-total-table {
                width: 100%;
            }
            
            .bill-actions {
                flex-direction: column;
                gap: 1rem;
            }
            
            .btn {
                width: 100%;
            }
            
            .rental-period {
                flex-direction: column;
                gap: 1rem;
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
                 <?php // This case should ideally not be reached due to session check at the top 
                       // but kept for robustness.
                 ?>
                <a href="login.php" class="signin">Sign In</a>
                <a href="register.php" class="signup">Sign Up</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="page-container">
        <div class="bill-container" id="bill-content">
            <?php if ($email_sent): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Bill has been sent to your email successfully!
                </div>
            <?php endif; ?>
            
            <div class="bill-header">
                <div class="company-info">
                    <h2>VehicleNow</h2>
                    <p>123 Car Street, Car City</p>
                    <p>Phone: +123 456 7890</p>
                    <p>Email: info@VehicleNow.com</p>
                </div>
                <div class="bill-details">
                    <h3>INVOICE</h3>
                    <p class="invoice-number"><?php echo $invoice_number; ?></p>
                    <p class="date">Date: <?php echo $formatted_booking_date; ?></p>
                </div>
            </div>
            
            <div class="customer-info">
                <div class="billing-to">
                    <h4>BILLED TO</h4>
                    <p><?php echo $booking['FNAME'] . ' ' . $booking['LNAME']; ?></p>
                    <p>Email: <?php echo $booking['EMAIL']; ?></p>
                    <p>Phone: <?php echo $booking['PHONE']; ?></p>
                </div>
                <div class="car-info">
                    <h4>CAR DETAILS</h4>
                    <p><strong><?php echo $booking['CAR_NAME']; ?></strong></p>
                    <p>Fuel: <?php echo $booking['FUEL_TYPE']; ?></p>
                    <p>Capacity: <?php echo $booking['CAPACITY']; ?> persons</p>
                    <p>Transmission: <?php echo $booking['TRANSMISSION']; ?></p>
                </div>
            </div>
            
            <div class="rental-details">
                <h4>RENTAL PERIOD</h4>
                <div class="rental-period">
                    <div>
                        <span class="label">Pick-up Date</span>
                        <span class="value"><?php echo $formatted_from_date; ?></span>
                    </div>
                    <div>
                        <span class="label">Drop-off Date</span>
                        <span class="value"><?php echo $formatted_to_date; ?></span>
                    </div>
                    <div>
                        <span class="label">Duration</span>
                        <span class="value"><?php echo $total_days; ?> days</span>
                    </div>
                </div>
            </div>
            
            <table class="bill-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Rate</th>
                        <th>Days</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Car Rental - <?php echo $booking['CAR_NAME']; ?></td>
                        <td>रु <?php echo number_format($daily_rate); ?> per day</td>
                        <td><?php echo $total_days; ?></td>
                        <td class="amount">रु <?php echo number_format($total_amount); ?></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="bill-total">
                <table class="bill-total-table">
                    <tr>
                        <td class="total-label">Subtotal</td>
                        <td class="total-value">रु <?php echo number_format($total_amount); ?></td>
                    </tr>
                    <tr>
                        <td class="total-label">Tax (5%)</td>
                        <td class="total-value">रु <?php echo number_format($total_amount * 0.05); ?></td>
                    </tr>
                    <tr>
                        <td class="total-label grand-total">TOTAL</td>
                        <td class="total-value grand-total">रु <?php echo number_format($total_amount * 1.05); ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="bill-notes">
                <h4>PAYMENT METHOD</h4>
                <p><strong>Cash Payment on Pickup</strong></p>
                <p>Please be prepared to pay the full amount in cash when you pick up the vehicle.</p>
                
                <h4 style="margin-top: 1rem;">NOTES</h4>
                <p>Thank you for choosing VehicleNow for your car rental needs. We hope you enjoy your journey!</p>
                <p>For any questions regarding this bill, please contact our customer service.</p>
            </div>
        </div>
        
        <div class="bill-actions">
            <button class="btn btn-outline" id="send-email-btn">
                <i class="fas fa-envelope"></i> Email Bill
            </button>
            <button class="btn btn-primary" id="download-bill-btn">
                <i class="fas fa-download"></i> Download Bill
            </button>
            <a href="booking-confirmation.php?id=<?php echo $booking_id; ?>" class="btn btn-primary">
                <i class="fas fa-check-circle"></i> Complete Booking
            </a>
        </div>
        
        <form class="email-form" id="email-form" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $booking['EMAIL']; ?>" required>
            </div>
            <button type="submit" name="send_email" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Send Email
            </button>
        </form>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        // Toggle email form
        document.getElementById('send-email-btn').addEventListener('click', function() {
            const emailForm = document.getElementById('email-form');
            emailForm.classList.toggle('active');
        });
        
        // Download PDF
        document.getElementById('download-bill-btn').addEventListener('click', function() {
            // Get the bill content
            const element = document.getElementById('bill-content');
            
            // PDF options
            const opt = {
                margin: [15, 15],
                filename: '<?php echo $invoice_number; ?>.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            // Generate and download PDF
            html2pdf().set(opt).from(element).save();
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
                if (!event.target.matches('.profile-icon-btn') && !profileIconBtn.contains(event.target)) { // Check if click is not on button or inside button (for icon case)
                    if (profileDropdown.classList.contains('show')) {
                        profileDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html> 
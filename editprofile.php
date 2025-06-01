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

// $conn->close(); // Close at the end of the script
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - VehicleNow</title>
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
        
        /* Header Styles (copied from mybookings.php) */
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

        /* Profile Dropdown (copied from mybookings.php) */
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
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
            padding: 0.5rem 0;
        }
        .dropdown-content.show {
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
        .welcome-user-container { 
            display: flex;
            align-items: center;
        }
        .welcome-user { 
            font-weight: 500;
            margin-right: 10px;
        }
        
        /* Page Container (general page styling) */
        .page-container { 
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
        
        /* Profile Section (copied from dashboard.php) */
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
        <div class="auth-links">
            <?php if(isset($_SESSION["user_name"])): ?>
                <div class="welcome-user-container">
                    <span class="welcome-user"><?php echo $_SESSION["user_name"]; ?></span>
                    <div class="profile-dropdown">
                        <button class="profile-icon-btn" onclick="toggleDropdown()">
                            <i class="fas fa-user"></i>
                        </button>
                        <div class="dropdown-content" id="profileDropdownContent">
                            <a href="mybookings.php">My Bookings</a>
                            <a href="editprofile.php" class="active">Edit Profile</a> <!-- Set as active -->
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

    <div class="page-container">
        <div class="page-header">
            <h1>My Profile</h1>
        </div>
        
        <div id="profile-content"> <!-- Removed dashboard-content and active classes -->
            <div class="profile-card">
                <div class="profile-info">
                    <div class="profile-item">
                        <div class="profile-label">First Name</div>
                        <div class="profile-value"><?php echo htmlspecialchars($user["FNAME"]); ?></div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">Last Name</div>
                        <div class="profile-value"><?php echo htmlspecialchars($user["LNAME"]); ?></div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">Email</div>
                        <div class="profile-value"><?php echo htmlspecialchars($user["EMAIL"]); ?></div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">Phone</div>
                        <div class="profile-value"><?php echo htmlspecialchars($user["PHONE"]); ?></div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">License Number</div>
                        <div class="profile-value"><?php echo htmlspecialchars($user["LICENSE_NO"]); ?></div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">Address</div>
                        <div class="profile-value"><?php echo htmlspecialchars($user["ADDRESS"]); ?></div>
                    </div>
                </div>
                <div class="profile-actions">
                    <!-- This button would ideally lead to a page with a form to edit details -->
                    <a href="edit-profile.php" class="profile-btn">Edit Profile Details</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Profile Dropdown Toggle (copied from mybookings.php)
        function toggleDropdown() {
            document.getElementById("profileDropdownContent").classList.toggle("show");
        }

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
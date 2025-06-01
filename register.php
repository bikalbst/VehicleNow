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

// Check if the registration form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $license = $_POST["license"];
    $address = $_POST["address"];
    $password = md5($_POST["password"]); // Store as MD5 hash
    
    // Check if the email already exists
    $checkEmail = "SELECT * FROM users WHERE EMAIL = '$email'";
    $result = $conn->query($checkEmail);
    
    if ($result->num_rows > 0) {
        // Email already exists
        $error_message = "Email already exists. Please use a different email.";
    } else {
        // Insert new user
        $sql = "INSERT INTO users (FNAME, LNAME, EMAIL, PHONE, LICENSE_NO, ADDRESS, PASSWORD) 
                VALUES ('$fname', '$lname', '$email', '$phone', '$license', '$address', '$password')";
        
        if ($conn->query($sql) === TRUE) {
            // Registration successful
            $_SESSION["user_name"] = $fname . " " . $lname;
            $_SESSION["email"] = $email;
            header("Location: index.php");
            exit();
        } else {
            // Error in registration
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
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
    <title>Sign Up - VehicleNow</title>
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
        
        /* Registration Form Container */
        .register-container {
            display: flex;
            justify-content: center;
            min-height: 100vh;
            padding: 120px 20px 40px;
        }
        
        .register-form {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 700px;
            padding: 2.5rem;
        }
        
        .register-form h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--dark-color);
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        .form-group {
            flex: 1 0 50%;
            padding: 0 10px;
            margin-bottom: 1.5rem;
        }
        
        .form-group.full-width {
            flex: 1 0 100%;
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
        
        .register-btn {
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
            margin-top: 1rem;
        }
        
        .register-btn:hover {
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
        
        .error-message {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            padding: 0.8rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .form-group {
                flex: 1 0 100%;
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
        <div class="auth-links">
            <a href="login.php" class="signin">Sign In</a>
        </div>
    </header>

    <div class="register-container">
        <div class="register-form">
            <h2>Create an Account</h2>
            <?php if(isset($error_message)): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="fname">First Name</label>
                        <input type="text" id="fname" name="fname" class="form-control" placeholder="Enter your first name" required>
                    </div>
                    <div class="form-group">
                        <label for="lname">Last Name</label>
                        <input type="text" id="lname" name="lname" class="form-control" placeholder="Enter your last name" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="license">License Number</label>
                        <input type="text" id="license" name="license" class="form-control" placeholder="Enter your license number" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group full-width">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" class="form-control" rows="3" placeholder="Enter your full address" required></textarea>
                    </div>
                </div>
                <button type="submit" name="register" class="register-btn">CREATE ACCOUNT</button>
                <div class="form-footer">
                    <p>Already have an account? <a href="login.php">Sign in</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
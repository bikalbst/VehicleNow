<?php
// Start the session at the beginning of the script
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

// Check if the login form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = md5($_POST["pass"]); // Stored as MD5 hashes in the database

    // Prepare and execute the SQL query
    $sql = "SELECT FNAME, LNAME, EMAIL FROM users WHERE EMAIL = '$email' AND PASSWORD = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Email and password match, retrieve the user's name
        $row = $result->fetch_assoc();
        $user_name = $row["FNAME"] . " " . $row["LNAME"];
        $user_email = $row["EMAIL"]; // Retrieve the user's email

        // Authentication successful
        $_SESSION["user_name"] = $user_name; // Store the user's name in the session
        $_SESSION["email"] = $user_email; // Store the user's email in the session
        
        // Redirect back to the page they came from or to the homepage
        $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
        header("Location: " . $redirect);
        exit();
    } else {
        // Email or password is incorrect
        $error_message = "Invalid email or password.";
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
    <title>Login - VehicleNow</title>
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
        
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding-top: 80px;
        }
        
        .login-form {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
        }
        
        .login-form h2 {
            text-align: center;
            margin-bottom: 2rem;
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
        
        .login-btn {
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
        }
        
        .login-btn:hover {
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
    </header>

    <div class="login-container">
        <div class="login-form">
            <h2>Login to Your Account</h2>
            <?php if(isset($error_message)): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . (isset($_GET['redirect']) ? '?redirect=' . $_GET['redirect'] : ''); ?>">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="pass" class="form-control" placeholder="Enter your password" required>
                </div>
                <button type="submit" name="login" class="login-btn">LOGIN</button>
                <div class="form-footer">
                    <p>Don't have an account? <a href="register.php">Sign up</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 
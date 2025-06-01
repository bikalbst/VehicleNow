<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN LOGIN</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap" rel="stylesheet">
    <script type="text/javascript">
        function preventBack() {
            window.history.forward(); 
        }          
        setTimeout("preventBack()", 0);         
        window.onunload = function () { null };
    </script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Poppins", sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
            position: relative;
        }

        .container {
            width: 340px;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .form h2 {
            text-align: center;
            color: #3498db;
            font-size: 22px;
            margin-bottom: 25px;
        }

        .input-field {
            width: 100%;
            height: 40px;
            padding: 5px 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .input-field:focus {
            outline: none;
            border-color: #3498db;
        }

        .login-btn {
            width: 100%;
            height: 40px;   
            background: #3498db;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            color: #fff;
            cursor: pointer;
            margin-top: 10px;
        }

        .home-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 8px 16px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>
<?php
    require_once('connection.php');
    if(isset($_POST['adlog'])){
        $id=$_POST['adid'];
        $pass=$_POST['adpass'];
        
        if(empty($id)|| empty($pass)) {
            echo '<script>alert("Please fill all fields")</script>';
        } else {
            // Use prepared statements to prevent SQL injection
            $stmt = $con->prepare("SELECT * FROM users WHERE EMAIL = ? AND IS_ADMIN = 'Y'");
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if($row = $result->fetch_assoc()){
                $db_password = $row['PASSWORD']; // Assuming password in users table is stored the same way
                // Verify the password. IMPORTANT: You are using unsalted md5, which is not secure.
                // Consider migrating to password_hash() and password_verify().
                if(md5($pass) == $db_password) { 
                    // Set session variables for admin if needed
                    $_SESSION['admin_id'] = $row['EMAIL']; // Or any other relevant admin identifier
                    $_SESSION['admin_name'] = $row['FNAME'] . ' ' . $row['LNAME'];
                    // Regenerate session ID for security after login
                    session_regenerate_id(true);

                    echo '<script>alert("Welcome ADMINISTRATOR!");</script>';
                    // Redirect to admin dashboard - ensure adminbook.php is the correct page
                    header("location: adminbook.php");
                    exit(); // Always exit after a header redirect
                } else {
                    echo '<script>alert("Incorrect password")</script>';
                }
            } else {
                echo '<script>alert("Admin ID not found or user is not an administrator")</script>';
            }
            $stmt->close();
        }
    }
?>

<a href="index.php" class="home-btn">Home</a>

<div class="container">
    <form class="form" method="POST">
        <h2>Admin Login</h2>
        <input class="input-field" type="text" name="adid" placeholder="Enter admin user id">
        <input class="input-field" type="password" name="adpass" placeholder="Enter admin password">
        <input type="submit" class="login-btn" value="LOGIN" name="adlog">
    </form>
</div>
 
</body>
</html>
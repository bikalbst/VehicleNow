<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status</title>
    <style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background-image: url("images/adminbg11.jpg");
    background-size: cover;
    background-repeat: no-repeat;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.navbar {
    list-style-type: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: rgba(255, 255, 255, 0.8);
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    height: 9vh;
}

.navbar li {
    margin-right: 20px;
}

.navbar li a.button {
    text-decoration: none;
    color: #fff;
    font-weight: bold;
    font-size: 18px;
    padding: 10px 20px;
    background-color: #ff7200;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.navbar li a.button:hover {
    background-color: #ff8c3b;
}

.greeting {
    font-size: 20px;
    font-weight: bold;
}

.box {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    padding: 20px;
    margin-top: 20px;
}

.box h1 {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 10px;
}

.box p {
    font-size: 18px;
    line-height: 1.6;
    margin-bottom: 10px;
}

.box .content {
    text-align: left;
}

@media (max-width: 600px) {
    .navbar {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .navbar li {
        margin-right: 0;
        margin-bottom: 10px;
    }
}



    </style>
</head>
<body>
<?php
session_start();
$_SESSION['isNotified'] = false;

require_once('connection.php');
$email = $_SESSION['email'];

$sql = "SELECT * FROM booking WHERE EMAIL='$email' ORDER BY BOOK_ID DESC LIMIT 1";
$result = mysqli_query($con, $sql);
$rows = mysqli_fetch_assoc($result);

if (!$rows) {
    echo '<script>alert("There are no booking details.")</script>';
    echo '<script>window.location.href = "cardetails.php";</script>';
} else {
    $sql2 = "SELECT * FROM users WHERE EMAIL='$email'";
    $result2 = mysqli_query($con, $sql2);
    $rows2 = mysqli_fetch_assoc($result2);

    $car_id = $rows['CAR_ID'];
    $sql3 = "SELECT * FROM cars WHERE CAR_ID='$car_id'";
    $result3 = mysqli_query($con, $sql3);
    $rows3 = mysqli_fetch_assoc($result3);
?>
    <div class="container">
        <ul class="navbar">
            <li><a class="button" href="cardetails.php">Go to Home</a></li>
            <li class="greeting">Hello, <?php echo $rows2['FNAME']." ".$rows2['LNAME']?></li>
        </ul>
        <div class="box">
            <div class="content">
                <h1>Car Name: <?php echo $rows3['CAR_NAME']?></h1>
                <p>No of Days: <?php echo $rows['DURATION']?></p>
                <p>Booking Status: <?php echo $rows['BOOK_STATUS']?></p>
            </div>
        </div>
    </div>
<?php } ?>
</body>
</html>

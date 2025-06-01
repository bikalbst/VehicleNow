<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMINISTRATOR</title>
</head>
<body>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: Arial, sans-serif;
        display: flex;
    }
    
    .sidebar {
        width: 250px;
        height: 100vh;
        /* background-color: skyblue; */
        position: fixed;
        left: 0;
        top: 0;
        overflow-y: auto;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }
    
    .logo {
        color: #ff7200;
        font-size: 35px;
        font-family: Arial;
        padding: 20px;
        text-align: center;
        border-bottom: 1px solid rgba(255,255,255,0.3);
    }
    
    .menu {
        margin-top: 30px;
    }
    
    .menu ul {
        display: block;
        width: 100%;
    }
    
    .menu ul li {
        list-style: none;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    
    .menu ul li a {
        text-decoration: none;
        color: #333;
        font-family: Arial;
        font-weight: bold;
        transition: 0.3s ease;
        display: block;
        padding: 15px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    
    .menu ul li a:hover {
        background-color: #87ceeb;
        color: #fff;
        padding-left: 25px;
    }
    
    .logout-btn {
        margin: 20px;
        text-align: center;
    }
    
    .nn {
        width: 100%;
        border: none;
        height: 40px;
        font-size: 18px;
        border-radius: 5px;
        cursor: pointer;
        color: white;
        transition: 0.4s ease;
        background-color: #333;
    }
    
    .nn a {
        text-decoration: none;
        color: #fff;
        font-weight: bold;
        display: block;
        width: 100%;
        height: 100%;
        line-height: 40px;
    }
    
    .main-content {
        margin-left: 250px;
        width: calc(100% - 250px);
        padding: 20px;
        height: 100vh;
        background: linear-gradient(to top, rgba(0,0,0,0)50%, rgba(0,0,0,0)50%),url("../images/carbg2.jpg");
        background-position: center;
        background-size: cover;
        animation: infiniteScrollBg 50s linear infinite;
    }
    
    .content-table {
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 0.9em;
        border-radius: 5px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        margin: 25px auto;
        width: 95%;
        background-color: #fff;
    }
    
    .content-table thead tr {
        background-color: #333;
        color: #fff;
        text-align: left;
    }
    
    .content-table th,
    .content-table td {
        padding: 12px 15px;
    }
    
    .content-table tbody tr {
        border-bottom: 1px solid #ddd;
    }
    
    .content-table tbody tr:nth-of-type(even) {
        background-color: #f3f3f3;
    }
    
    .content-table tbody tr:last-of-type {
        border-bottom: 2px solid #333;
    }
    
    .content-table tbody tr:hover {
        background-color: #e6e6e6;
    }
    
    .header {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }
    
    .but a {
        text-decoration: none;
        color: black;
    }
</style>

<?php
require_once('connection.php');
$query="select *from users";
$queryy=mysqli_query($con,$query);
$num=mysqli_num_rows($queryy);
?>

<div class="sidebar">
    <h2 class="logo">VehicleNow</h2>
    <div class="menu">
        <ul>
            <li><a href="adminvehicle.php">CAR MANAGEMENT</a></li>
            <li><a href="adminbook.php">DASHBOARD</a></li>
            <li><a href="admindash.php">FEEDBACKS</a></li>
            <li><a href="adminusers.php">USERS</a></li>
        </ul>
    </div>
    <div class="logout-btn">
        <button class="nn"><a href="index.php">LOGOUT</a></button>
    </div>
</div>

<div class="main-content">
    <h1 class="header">USERS</h1>
    <div>
        <table class="content-table">
            <thead>
                <tr>
                    <th>NAME</th> 
                    <th>EMAIL</th>
                    <th>LICENSE NUMBER</th>
                    <th>PHONE NUMBER</th> 
                    <th>GENDER</th> 
                    <th>DELETE USERS</th>
                </tr>
            </thead>
            <tbody>
            <?php
            while($res=mysqli_fetch_array($queryy)){
            ?>
            <tr class="active-row">
                <td><?php echo $res['FNAME']."  ".$res['LNAME'];?></td>
                <td><?php echo $res['EMAIL'];?></td>
                <td><?php echo $res['LICENSE_NO'];?></td>
                <td><?php echo $res['PHONE'];?></td>
                <td><?php echo $res['GENDER'];?></td>
                <td><button type="submit" class="but" name="approve"><a href="deleteuser.php?id=<?php echo $res['EMAIL']?>">DELETE USER</a></button></td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
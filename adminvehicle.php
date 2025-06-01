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
*{
    margin: 0;
    padding: 0;
}
.hai{
    width: 100%;
    background: linear-gradient(to top, rgba(0,0,0,0)50%, rgba(0,0,0,0)50%),url("../images/carbg2.jpg");
    background-position: center;
    background-size: cover;
    height: 109vh;
    animation: infiniteScrollBg 50s linear infinite;
}
.main{
    width: 100%;
    background: linear-gradient(to top, rgba(0,0,0,0)50%, rgba(0,0,0,0)50%);
    background-position: center;
    background-size: cover;
    height: 109vh;
    animation: infiniteScrollBg 50s linear infinite;
}
.navbar{
    width: 1200px;
    height: 75px;
    margin: auto;
}

.icon{
    width:200px;
    float: left;
    height : 70px;
}

.logo{
    color: #ff7200;
    font-size: 35px;
    font-family: Arial;
    padding-left: 20px;
    float:left;
    padding-top: 10px;

}
.menu{
    width: 400px;
    float: left;
    height: 70px;

}

ul{
    float: left;
    display: flex;
    justify-content: center;
    align-items: center;
}

ul li{
    list-style: none;
    margin-left: 62px;
    margin-top: 27px;
    font-size: 14px;

}

ul li a{
    text-decoration: none;
    color: black;
    font-family: Arial;
    font-weight: bold;
    transition: 0.4s ease-in-out;

}

.content-table{
    border-collapse: collapse;
    font-family: Arial, sans-serif;
    font-size: 0.9em;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    margin: 25px auto;
    width: 80%;
    background-color: #fff;
}

.content-table thead tr{
    background-color: #333;
    color: #fff;
    text-align: left;
}

.content-table th,
.content-table td{
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

.header{
    text-align: center;
    margin-top: -600px;
    color: #333;
}

.nn{
    width:100px;
    border:none;
    height: 40px;
    font-size: 18px;
    border-radius: 10px;
    cursor: pointer;
    color:white;
    transition: 0.4s ease;
    background-color: #333;
}

.nn a{
    text-decoration: none;
    color: #fff;
    font-weight: bold;
}

.add{
    width: 200px;
    height: 40px;
    background: #856348;
    border:none;
    font-size: 18px;
    border-radius: 10px;
    cursor: pointer;
    color:#fff;
    transition: 0.4s ease;
    margin: 20px auto;
    display: block;
}

.add a{
    text-decoration: none;
    color: #fff;
    font-weight: bold;
}

.but a{
    text-decoration: none;
    color: black;
}
</style>    
<?php

require_once('connection.php');
$query="SELECT *from cars";    
$queryy=mysqli_query($con,$query);
$num=mysqli_num_rows($queryy);

?>
<div class="hai">
    <div class="navbar">
        <div class="icon">
            <h2 class="logo">VehicleNow</h2>
        </div>
        <div class="menu">
            <ul>
                <li><a href="adminvehicle.php">VEHICLE MANAGEMENT</a></li>
                <li><a href="adminbook.php">DASHBOARD</a></li>
                <li><a href="admindash.php">FEEDBACKS</a></li>
                <li><a href="adminusers.php">USERS</a></li>
                <li><button class="nn"><a href="index.php">LOGOUT</a></button></li>
            </ul>
        </div>
    </div>
</div>
<div>
    <h1 class="header"></h1>
    <div>
        <div>
            <table class="content-table">
                <thead>
                    <tr>
                        <th>VEHICLE ID</th>
                        <th>VEHICLE NAME</th>
                        <th>FUEL TYPE</th>
                        <th>CAPACITY</th>
                        <th>PRICE</th>
                        <th>AVAILABLE</th>
                        <th>DELETE</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                while($res=mysqli_fetch_array($queryy)){
                ?>
                <tr>
                    <td><?php echo $res['CAR_ID'];?></td>
                    <td><?php echo $res['CAR_NAME'];?></td>
                    <td><?php echo $res['FUEL_TYPE'];?></td>
                    <td><?php echo $res['CAPACITY'];?></td>
                    <td><?php echo $res['PRICE'];?></td>
                    <td><?php  
                    if($res['AVAILABLE']=='Y')
                    {
                        echo 'YES';
                    }
                    else{
                        echo 'NO';
                    }
                    ?></td>
                    <td><button type="submit" class="but" name="approve"><a href="deletecar.php?id=<?php echo $res['CAR_ID']?>">DELETE CAR</a></button></td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <button class="add"><a href="addcar.php">+ ADD VEHICLES</a></button>
</div>
</body>
</html>
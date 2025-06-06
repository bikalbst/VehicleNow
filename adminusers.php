<?php
session_start();
require_once('connection.php');

// Fetch all non-admin users from the database
$query_users = "SELECT * FROM users WHERE IS_ADMIN != 'Y' ORDER BY USER_ID DESC";
$result_users = mysqli_query($con, $query_users);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - VehicleNow Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
            --error-color: #e74c3c;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        html {
            scroll-behavior: smooth;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
        body {
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            background-color: white;
            box-shadow: var(--shadow);
            position: sticky;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }
        .menu ul {
            display: flex;
            list-style: none;
        }
        .menu ul li { margin-left: 2rem; }
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
        .menu ul li a:hover { color: var(--primary-color); }
        .auth-links { display: flex; align-items: center; }
        .page-container {
            flex-grow: 1;
            width: 90%;
            max-width: 1400px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .page-header h1 {
            font-size: 2.2rem;
            color: var(--dark-color);
            font-weight: 600;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        .content-table thead tr {
            background-color: var(--secondary-color);
            color: white;
            text-align: left;
            font-weight: 600;
        }
        .content-table th,
        .content-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        .content-table tbody tr:nth-of-type(even) {
            background-color: #f9f9f9;
        }
        .content-table tbody tr:hover {
            background-color: #f1f1f1;
            color: var(--primary-color);
        }
        .action-btn {
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            margin-right: 5px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }
        .btn-delete {
            background-color: var(--error-color);
            color: white;
        }
        .btn-delete:hover { background-color: #c0392b; }
        .no-data-message {
            text-align: center;
            padding: 2rem;
            font-size: 1.1rem;
            color: #777;
        }
        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 3rem 5% 1.5rem;
            margin-top: auto;
        }
        .footer-content { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem; }
        .footer-col h3 { font-size: 1.2rem; margin-bottom: 1rem; }
        .footer-links a, .footer-col p { color: #bbb; text-decoration: none; }
    </style>
</head>
<body>
    <header class="navbar">
        <a href="admindash.php" class="logo">VehicleNow <span style="font-size: 0.8em; color: var(--accent-color);">Admin</span></a>
        <nav class="menu">
            <ul>
                <li><a href="admindash.php">Dashboard</a></li>
                <li><a href="adminvehicle.php">Vehicle Management</a></li>
                <li><a href="adminbook.php">Bookings</a></li>
                <li><a href="adminusers.php">Users</a></li>
                <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </nav>
    </header>

    <div class="page-container">
        <div class="page-header">
            <h1>Manage Users</h1>
        </div>

        <?php if(mysqli_num_rows($result_users) > 0): ?>
            <table class="content-table">
                <thead>
                    <tr>
                        <th>USER ID</th>
                        <th>NAME</th>
                        <th>EMAIL</th>
                        <th>PHONE</th>
                        <th>LICENSE NO.</th>
                        <th>ADDRESS</th>
                        <th>JOINED DATE</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = mysqli_fetch_assoc($result_users)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['USER_ID']); ?></td>
                        <td><?php echo htmlspecialchars($user['FNAME'] . ' ' . $user['LNAME']); ?></td>
                        <td><?php echo htmlspecialchars($user['EMAIL']); ?></td>
                        <td><?php echo htmlspecialchars($user['PHONE']); ?></td>
                        <td><?php echo htmlspecialchars($user['LICENSE_NO']); ?></td>
                        <td><?php echo htmlspecialchars($user['ADDRESS']); ?></td>
                        <td><?php echo date('d M Y', strtotime($user['CREATION_DATE'])); ?></td>
                        <td>
                            <a href="deleteuser.php?id=<?php echo urlencode($user['EMAIL']); ?>" 
                               class="action-btn btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                               <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data-message">
                <p>No users found in the database.</p>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="footer-content">
            <!-- Footer content can be copied from adminvehicle.php if needed -->
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> VehicleNow. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
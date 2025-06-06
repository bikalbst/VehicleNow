<?php
session_start();
require_once('connection.php');

// The main query to get booking details along with car and user information
$query_bookings = "SELECT 
                        b.BOOK_ID, b.FROM_DT, b.TO_DT, b.STATUS,
                        c.CAR_NAME, c.CAR_IMG,
                        u.FNAME, u.LNAME, u.EMAIL
                   FROM booking b
                   JOIN cars c ON b.CAR_ID = c.CAR_ID
                   JOIN users u ON b.EMAIL = u.EMAIL
                   ORDER BY b.BOOK_ID DESC";

$result_bookings = mysqli_query($con, $query_bookings);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - VehicleNow Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71; /* Confirmed */
            --warning-color: #f39c12; /* Pending */
            --info-color: #9b59b6;    /* Completed */
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }
        body {
            background-color: #f5f7fa; color: var(--dark-color);
            line-height: 1.6; display: flex; flex-direction: column; min-height: 100vh;
        }
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1.5rem 5%; background-color: white; box-shadow: var(--shadow);
            position: sticky; width: 100%; top: 0; z-index: 1000;
        }
        .logo {
            font-size: 1.8rem; font-weight: 700;
            color: var(--primary-color); text-decoration: none;
        }
        .menu ul { display: flex; list-style: none; }
        .menu ul li { margin-left: 2rem; }
        .menu ul li a {
            text-decoration: none; color: var(--dark-color); font-weight: 500;
        }
        .menu ul li a:hover { color: var(--primary-color); }
        .page-container {
            flex-grow: 1; width: 90%; max-width: 1600px;
            margin: 2rem auto; padding: 2rem;
            background-color: white; border-radius: 10px; box-shadow: var(--shadow);
        }
        .page-header { margin-bottom: 2rem; }
        .page-header h1 {
            font-size: 2.2rem; color: var(--dark-color); font-weight: 600;
        }
        .content-table {
            width: 100%; border-collapse: collapse; font-size: 0.9em;
            border-radius: 8px; overflow: hidden; box-shadow: var(--shadow);
        }
        .content-table thead tr {
            background-color: var(--secondary-color); color: white;
            text-align: left; font-weight: 600;
        }
        .content-table th, .content-table td {
            padding: 12px 15px; border-bottom: 1px solid #eee;
        }
        .content-table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .action-btn {
            padding: 0.4rem 0.8rem; border-radius: 4px; text-decoration: none;
            font-size: 0.85rem; font-weight: 500; margin-right: 5px;
            transition: var(--transition); border: none; cursor: pointer;
            color: white;
        }
        .btn-approve { background-color: var(--success-color); }
        .btn-approve:hover { background-color: #27ae60; }
        .btn-return { background-color: var(--info-color); }
        .btn-return:hover { background-color: #8e44ad; }
        .status-badge {
            padding: 0.3rem 0.8rem; border-radius: 15px; color: black;
            font-size: 0.8rem; font-weight: 500; text-transform: uppercase;
        }
        .status-Confirmed { background-color: var(--success-color); }
        .status-Pending { background-color: var(--warning-color); }
        .status-Cancelled { background-color: var(--accent-color); }
        .status-Completed { background-color: var(--secondary-color); }
        .no-data-message { text-align: center; padding: 2rem; font-size: 1.1rem; color: #777; }
        .footer {
            background-color: var(--dark-color); color: white;
            padding: 2rem 5%; text-align: center; margin-top: auto;
        }
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

    <main class="page-container">
        <div class="page-header">
            <h1>Manage Bookings</h1>
        </div>

        <?php if(mysqli_num_rows($result_bookings) > 0): ?>
            <table class="content-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Vehicle</th>
                        <th>Booking Dates</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($booking = mysqli_fetch_assoc($result_bookings)): ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($booking['BOOK_ID']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($booking['FNAME'] . ' ' . $booking['LNAME']); ?><br>
                            <small><?php echo htmlspecialchars($booking['EMAIL']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($booking['CAR_NAME']); ?></td>
                        <td>
                            <?php echo date('d M Y', strtotime($booking['FROM_DT'])); ?> to 
                            <?php echo date('d M Y', strtotime($booking['TO_DT'])); ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo htmlspecialchars($booking['STATUS']); ?>">
                                <?php echo htmlspecialchars($booking['STATUS']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if($booking['STATUS'] == 'Pending'): ?>
                                <a href="approve.php?id=<?php echo urlencode($booking['BOOK_ID']); ?>" class="action-btn btn-approve">
                                    <i class="fas fa-check"></i> Approve
                                </a>
                            <?php endif; ?>
                            <?php if($booking['STATUS'] == 'Confirmed'): ?>
                            <a href="adminreturn.php?id=<?php echo urlencode($booking['CAR_ID']); ?>&bookid=<?php echo urlencode($booking['BOOK_ID']); ?>" class="action-btn btn-return">
                                <i class="fas fa-undo-alt"></i> Mark Returned
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data-message">
                <p>No bookings found.</p>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> VehicleNow. All Rights Reserved.</p>
    </footer>
</body>
</html>
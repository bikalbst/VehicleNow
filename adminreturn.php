<?php

require_once('connection.php');

// Check if parameters are set
if (!isset($_GET['id']) || !isset($_GET['bookid'])) {
    echo '<script>alert("Error: Missing car ID or booking ID.")</script>';
    echo '<script> window.location.href = "adminbook.php";</script>';
    exit(); // Stop script execution
}

$carid = $_GET['id'];
$book_id = $_GET['bookid'];

// Validate if they are numeric to prevent issues, especially if not using prepared statements for all queries
if (!is_numeric($carid) || !is_numeric($book_id)) {
    echo '<script>alert("Error: Invalid car ID or booking ID.")</script>';
    echo '<script> window.location.href = "adminbook.php";</script>';
    exit();
}

// Fetch booking details (using prepared statement would be better)
// Assuming BOOK_ID is the correct column name in your 'booking' table
$sql_booking = "SELECT BOOK_ID, STATUS FROM booking WHERE BOOK_ID = ?";
$stmt_booking = $con->prepare($sql_booking);
$stmt_booking->bind_param("i", $book_id);
$stmt_booking->execute();
$result_booking = $stmt_booking->get_result();

if ($result_booking->num_rows === 0) {
    echo '<script>alert("Error: Booking not found.")</script>';
    echo '<script> window.location.href = "adminbook.php";</script>';
    exit();
}
$booking_details = $result_booking->fetch_assoc();
$stmt_booking->close();

// Fetch car details (using prepared statement would be better)
$sql_car = "SELECT CAR_ID, AVAILABLE FROM cars WHERE CAR_ID = ?";
$stmt_car = $con->prepare($sql_car);
$stmt_car->bind_param("i", $carid);
$stmt_car->execute();
$result_car = $stmt_car->get_result();

if ($result_car->num_rows === 0) {
    echo '<script>alert("Error: Car not found.")</script>';
    echo '<script> window.location.href = "adminbook.php";</script>';
    exit();
}
$car_details = $result_car->fetch_assoc();
$stmt_car->close();


if($car_details['AVAILABLE'] == 'Y' && $booking_details['STATUS'] == 'RETURNED') {
    echo '<script>alert("Car is already marked as available and booking as returned.")</script>';
    echo '<script> window.location.href = "adminbook.php";</script>';
} else {
    $con->begin_transaction(); // Start a transaction
    try {
        // Update car availability
        $stmt_update_car = $con->prepare("UPDATE cars SET AVAILABLE = 'Y' WHERE CAR_ID = ?");
        $stmt_update_car->bind_param("i", $car_details['CAR_ID']);
        $stmt_update_car->execute();
        $stmt_update_car->close();

        // Update booking status
        // Corrected to use $booking_details['BOOK_ID'] for clarity, though $book_id would also work here.
        $stmt_update_booking = $con->prepare("UPDATE booking SET STATUS = 'RETURNED' WHERE BOOK_ID = ?"); 
        $stmt_update_booking->bind_param("i", $booking_details['BOOK_ID']);
        $stmt_update_booking->execute();
        $stmt_update_booking->close();

        $con->commit(); // Commit transaction
        echo '<script>alert("Car returned successfully and booking updated.")</script>';
        echo '<script> window.location.href = "adminbook.php";</script>';

    } catch (mysqli_sql_exception $exception) {
        $con->rollback(); // Rollback transaction on error
        // Log error: error_log($exception->getMessage());
        echo '<script>alert("Error updating records. Please try again.")</script>';
        echo '<script> window.location.href = "adminbook.php";</script>';
    }
}

$con->close(); // Close connection at the end

?>
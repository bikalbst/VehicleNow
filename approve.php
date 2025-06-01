<?php

require_once('connection.php');

if (!isset($_GET['id'])) {
    echo '<script>alert("Error: Booking ID not provided.")</script>';
    echo '<script> window.location.href = "adminbook.php";</script>';
    exit();
}

$bookid = $_GET['id'];

if (!is_numeric($bookid)) {
    echo '<script>alert("Error: Invalid Booking ID.")</script>';
    echo '<script> window.location.href = "adminbook.php";</script>';
    exit();
}

// Fetch booking details along with car details using a JOIN
// Assuming STATUS is the column for booking status (e.g., PENDING, APPROVED, RETURNED)
// Assuming CAR_ID is present in the booking table to link to the cars table
$query = "SELECT 
            b.BOOK_ID, b.CAR_ID, b.EMAIL, b.STATUS AS BOOKING_STATUS, 
            b.FROM_DT, b.TO_DT,
            c.CAR_NAME, c.AVAILABLE AS CAR_AVAILABILITY
          FROM booking b
          JOIN cars c ON b.CAR_ID = c.CAR_ID
          WHERE b.BOOK_ID = ?";

$stmt = $con->prepare($query);
if (!$stmt) {
    // error_log("Prepare failed: (" . $con->errno . ") " . $con->error);
    echo '<script>alert("Database error preparing statement.")</script>';
    echo '<script> window.location.href = "adminbook.php";</script>';
    exit();
}

$stmt->bind_param("i", $bookid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<script>alert("Error: Booking not found.")</script>';
    echo '<script> window.location.href = "adminbook.php";</script>';
    $stmt->close();
    exit();
}

$details = $result->fetch_assoc();
$stmt->close();

$car_id = $details['CAR_ID'];
$car_availability = $details['CAR_AVAILABILITY'];
$booking_status = $details['BOOKING_STATUS'];
$from_dt = $details['FROM_DT'];
$to_dt = $details['TO_DT'];

// Check current booking status
if ($booking_status === 'APPROVED' || $booking_status === 'RETURNED') {
    echo '<script>alert("Booking ID: ' . $bookid . ' is already ' . htmlspecialchars($booking_status) . '. No action taken.")</script>';
    echo '<script> window.location.href = "adminbook.php";</script>';
    exit();
}

// New Conflict Check:
// Check for other 'APPROVED' bookings for the same car that overlap with this booking's dates
$conflict_query = "SELECT COUNT(*) AS conflict_count 
                   FROM booking 
                   WHERE CAR_ID = ? 
                     AND BOOK_ID != ? 
                     AND STATUS = 'APPROVED' 
                     AND (
                         (FROM_DT <= ? AND TO_DT >= ?) OR /* New booking engulfs existing */
                         (FROM_DT >= ? AND FROM_DT <= ?) OR /* Existing booking starts within new booking */
                         (TO_DT >= ? AND TO_DT <= ?)       /* Existing booking ends within new booking */
                     )";
$stmt_conflict = $con->prepare($conflict_query);
if (!$stmt_conflict) {
    echo '<script>alert("Database error preparing conflict check statement.")</script>';
    echo '<script> window.location.href = "adminbook.php";</script>';
    exit();
}
// Bind parameters: CAR_ID, current BOOK_ID, TO_DT (for new booking), FROM_DT (for new booking), 
// FROM_DT (for new booking), TO_DT (for new booking), FROM_DT (for new booking), TO_DT (for new booking)
$stmt_conflict->bind_param("iissssss", $car_id, $bookid, $to_dt, $from_dt, $from_dt, $to_dt, $from_dt, $to_dt);
$stmt_conflict->execute();
$conflict_result = $stmt_conflict->get_result();
$conflict_details = $conflict_result->fetch_assoc();
$stmt_conflict->close();

if ($conflict_details['conflict_count'] > 0) {
    echo '<script>alert("Error: This car is already booked (APPROVED) for the selected dates by another booking. Cannot approve.")</script>';
    echo '<script> window.location.href = "adminbook.php";</script>';
    exit();
}

// Proceed to approve
$con->begin_transaction();
try {
    // Update booking status to APPROVED
    // Assuming STATUS is the correct column name in the booking table
    $stmt_update_booking = $con->prepare("UPDATE booking SET STATUS = 'APPROVED' WHERE BOOK_ID = ?");
    if (!$stmt_update_booking) throw new Exception("Failed to prepare booking update: " . $con->error);
    $stmt_update_booking->bind_param("i", $bookid);
    $stmt_update_booking->execute();
    $stmt_update_booking->close();

    // Update car availability to 'N' (Not Available)
    $stmt_update_car = $con->prepare("UPDATE cars SET AVAILABLE = 'N' WHERE CAR_ID = ?");
    if (!$stmt_update_car) throw new Exception("Failed to prepare car update: " . $con->error);
    $stmt_update_car->bind_param("i", $car_id);
    $stmt_update_car->execute();
    $stmt_update_car->close();

    $con->commit();
    echo '<script>alert("Booking ID: ' . $bookid . ' APPROVED successfully. Car ID: ' . $car_id . ' marked as unavailable.")</script>';

    // Email sending logic (consider moving to a function or doing it asynchronously)
    // $email = $details['EMAIL'];
    // $carname = $details['CAR_NAME'];
    // $to_email = $email;
    // $subject = "DONOT-REPLY - Booking Approved";
    // $body = "YOUR BOOKING FOR THE CAR $carname IS BEEN APPROVED WITH BOOKING ID : $bookid";
    // $headers = "From: your-email@example.com"; // Replace with your actual sender email
    // if (mail($to_email, $subject, $body, $headers)) {
    //     // Optional: log success
    // } else {
    //     // Optional: log email sending failure
    // }

    echo '<script> window.location.href = "adminbook.php";</script>';

} catch (Exception $e) {
    $con->rollback();
    // error_log("Transaction failed: " . $e->getMessage());
    echo '<script>alert("An error occurred while approving the booking. Please try again. Details: ' . htmlspecialchars($e->getMessage()) . '.")</script>';
    echo '<script> window.location.href = "adminbook.php";</script>';
}

$con->close();
?>
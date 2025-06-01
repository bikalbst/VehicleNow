<?php

require_once('connection.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<script>alert("Error: Invalid or missing CAR ID.")</script>';
    echo '<script>window.location.href = "adminvehicle.php";</script>';
    exit();
}

$carid = $_GET['id'];

// Check for existing bookings associated with this car that are NOT 'RETURNED'
$stmt_check_bookings = $con->prepare("SELECT COUNT(*) AS active_booking_count FROM booking WHERE CAR_ID = ? AND STATUS != 'RETURNED'");
if (!$stmt_check_bookings) {
    echo '<script>alert("Database error preparing booking check: ' . htmlspecialchars($con->error) . '")</script>';
    echo '<script>window.location.href = "adminvehicle.php";</script>';
    exit();
}
$stmt_check_bookings->bind_param("i", $carid);
$stmt_check_bookings->execute();
$result_check = $stmt_check_bookings->get_result();
$row = $result_check->fetch_assoc();
$active_booking_count = $row['active_booking_count'];
$stmt_check_bookings->close();

if ($active_booking_count > 0) {
    echo '<script>alert("Error: This car cannot be deleted because it has ' . $active_booking_count . ' associated booking(s) that are not yet marked as \'RETURNED\'. Please ensure all bookings are completed and marked as \'RETURNED\' first.")</script>';
    echo '<script>window.location.href = "adminvehicle.php";</script>';
    exit();
}

// No active (non-RETURNED) bookings. 
// Now, we need to delete any 'RETURNED' bookings for this car before deleting the car itself.
$con->begin_transaction(); // Start a transaction for atomicity

try {
    $stmt_delete_returned_bookings = $con->prepare("DELETE FROM booking WHERE CAR_ID = ? AND STATUS = 'RETURNED'");
    if (!$stmt_delete_returned_bookings) {
        throw new Exception("Database error preparing to delete returned bookings: " . $con->error);
    }
    $stmt_delete_returned_bookings->bind_param("i", $carid);
    $stmt_delete_returned_bookings->execute();
    // We don't strictly need to check affected_rows here, as it's okay if there were no 'RETURNED' bookings to delete.
    $stmt_delete_returned_bookings->close();

    // Now, proceed with deleting the car
    $stmt_delete_car = $con->prepare("DELETE FROM cars WHERE CAR_ID = ?");
    if (!$stmt_delete_car) {
        throw new Exception("Database error preparing car delete statement: " . $con->error);
    }
    $stmt_delete_car->bind_param("i", $carid);

    if ($stmt_delete_car->execute()) {
        if ($stmt_delete_car->affected_rows > 0) {
            echo '<script>alert("CAR DELETED SUCCESSFULLY (Associated returned bookings, if any, were also removed.)")</script>';
        } else {
            echo '<script>alert("Error: Car not found or already deleted (after checking bookings).")</script>';
        }
    } else {
        throw new Exception("Error deleting car: " . $stmt_delete_car->error);
    }
    $stmt_delete_car->close();

    $con->commit(); // Commit all changes if successful

} catch (Exception $e) {
    $con->rollback(); // Rollback on any error
    echo '<script>alert("An error occurred: ' . htmlspecialchars($e->getMessage()) . '")</script>';
}

$con->close();

echo '<script>window.location.href = "adminvehicle.php";</script>';

?>
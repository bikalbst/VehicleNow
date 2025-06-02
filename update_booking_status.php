<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["email"])) {
    header("Location: login.php?error=nologin");
    exit();
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$database = "vehiclenow";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    // In a real app, log this error instead of dying
    die("Connection failed: " . $conn->connect_error);
}

$user_email = $_SESSION["email"];
$message = []; // For success/error messages

if (isset($_GET['book_id']) && isset($_GET['action'])) {
    $book_id = (int)$_GET['book_id'];
    $action = $_GET['action'];

    // Verify the booking belongs to the logged-in user before proceeding
    $stmt_check = $conn->prepare("SELECT CAR_ID, STATUS FROM booking WHERE BOOK_ID = ? AND EMAIL = ?");
    if (!$stmt_check) {
        $message = ['type' => 'error', 'text' => 'Error preparing statement: ' . $conn->error];
        $_SESSION['message'] = $message;
        header("Location: mybookings.php");
        exit();
    }
    $stmt_check->bind_param("is", $book_id, $user_email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows === 1) {
        $booking_data = $result_check->fetch_assoc();
        $car_id_to_update = $booking_data['CAR_ID'];
        $current_status = $booking_data['STATUS'];
        $stmt_check->close();

        $conn->begin_transaction();

        try {
            if ($action === 'cancel') {
                if ($current_status === 'Pending') {
                    // Update booking status to Cancelled
                    $stmt_cancel = $conn->prepare("UPDATE booking SET STATUS = 'Cancelled' WHERE BOOK_ID = ?");
                    if (!$stmt_cancel) throw new Exception('Error preparing cancel statement: ' . $conn->error);
                    $stmt_cancel->bind_param("i", $book_id);
                    $stmt_cancel->execute();
                    $stmt_cancel->close();

                    // Make the car available again
                    $stmt_update_car = $conn->prepare("UPDATE cars SET AVAILABLE = 'Y' WHERE CAR_ID = ?");
                    if (!$stmt_update_car) throw new Exception('Error preparing car update statement: ' . $conn->error);
                    $stmt_update_car->bind_param("i", $car_id_to_update);
                    $stmt_update_car->execute();
                    $stmt_update_car->close();
                    
                    $conn->commit();
                    $message = ['type' => 'success', 'text' => 'Booking #'.htmlspecialchars($book_id).' has been cancelled.'];
                } else {
                    $conn->rollback(); // Should not happen if UI is correct, but good to handle
                    $message = ['type' => 'error', 'text' => 'This booking cannot be cancelled.'];
                }
            } elseif ($action === 'delete') {
                // Allow deletion if booking is 'Returned', 'Completed', or 'Cancelled'
                if ($current_status === 'Returned' || $current_status === 'Completed' || $current_status === 'Cancelled') {
                    $stmt_delete = $conn->prepare("DELETE FROM booking WHERE BOOK_ID = ?");
                    if (!$stmt_delete) throw new Exception('Error preparing delete statement: ' . $conn->error);
                    $stmt_delete->bind_param("i", $book_id);
                    $stmt_delete->execute();
                    $stmt_delete->close();

                    $conn->commit();
                    $message = ['type' => 'success', 'text' => 'Booking record #'.htmlspecialchars($book_id).' has been deleted.'];
                } else {
                    $conn->rollback();
                    $message = ['type' => 'error', 'text' => 'This booking record cannot be deleted yet.'];
                }
            } else {
                $conn->rollback();
                $message = ['type' => 'error', 'text' => 'Invalid action specified.'];
            }
        } catch (Exception $e) {
            $conn->rollback();
            // In a real app, log $e->getMessage()
            $message = ['type' => 'error', 'text' => 'An error occurred. Please try again. Details: ' . htmlspecialchars($e->getMessage())];
        }
    } else {
        // Booking not found or does not belong to the user
        if($stmt_check) $stmt_check->close(); // Ensure closed if initialized
        $message = ['type' => 'error', 'text' => 'Booking not found or you do not have permission to modify it.'];
    }
} else {
    $message = ['type' => 'error', 'text' => 'Missing booking ID or action.'];
}

$_SESSION['message'] = $message;
$conn->close();
header("Location: mybookings.php");
exit();

?> 
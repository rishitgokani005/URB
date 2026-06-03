<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
try {
    include('../includes/db.php');
} catch (mysqli_sql_exception $e) {
    $_SESSION['message'] = "Database connection error: Please make sure MySQL is started in XAMPP.";
    header("Location: taxi-booking.php");
    exit;
}


if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = isset($_POST['booking_id']) ? mysqli_real_escape_string($conn, $_POST['booking_id']) : '';
    $user_id = $_SESSION['user_id'];

    if (!empty($booking_id)) {
        // Cancel the booking in the acabookings table
        $sql = "UPDATE acabookings SET booking_status = 'cancelled' WHERE booking_id = ? AND user_id = ? AND booking_status = 'active'";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("si", $booking_id, $user_id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $_SESSION['message'] = "Taxi booking successfully cancelled.";
                } else {
                    $_SESSION['message'] = "Booking could not be cancelled. It might be completed or already cancelled.";
                }
            } else {
                $_SESSION['message'] = "Error executing cancellation: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "Database error: " . $conn->error;
        }
    } else {
        $_SESSION['message'] = "Invalid booking reference.";
    }
}

// Redirect back to bookings page
header("Location: ../bookings.php");
exit;
?>

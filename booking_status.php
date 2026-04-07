<?php
session_start();
require 'includes/header.php';

// Connect to the database
$mysqli = new mysqli("localhost", "root", "", "dwk");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_GET['booking_id'])) {
    $bookingId = $mysqli->real_escape_string($_GET['booking_id']);
    $query = "SELECT paymentStatus FROM abookings WHERE id = '$bookingId' AND user_id = '{$_SESSION['user_id']}'";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        switch ($booking['paymentStatus']) {
            case 'SUCCESS':
                echo "Payment successful. Your booking is confirmed.";
                break;
            case 'PENDING':
                echo "Payment is pending. Please wait for confirmation.";
                break;
            case 'FAILURE':
                echo "Payment failed. Please try again.";
                break;
            default:
                echo "Unknown payment status.";
        }
    } else {
        echo "Booking not found.";
    }
} else {
    echo "Invalid request.";
}

$mysqli->close();
require 'includes/footer.php';
?>
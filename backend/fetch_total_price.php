<?php
session_start();
include('../includes/db.php');
$booking_id = $_SESSION['booking_id']; // Get booking ID from session

$query = "SELECT total_price FROM abookings WHERE booking_id = '$booking_id'";
$result = mysqli_query($connection, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $total_price = $row['total_price'];

    // Return JSON response
    echo json_encode(['status' => 'success', 'total_price' => $total_price]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Unable to fetch total price.']);
}
?>

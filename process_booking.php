<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Essential booking details
$user_id = $_SESSION['user_id'];
$bike_id = $_SESSION['selected_bike_id'];
$agency_id = $_SESSION['current_agency_id'] ?? '';
$booking_date = $_SESSION['booking_start'];
$return_date = $_SESSION['booking_end'];
$total_price = $_SESSION['final_amount'];

// Form Provided Details (The 5 things)
$name = $_SESSION['customer_name'];
$mobile = $_SESSION['customer_phone'];
$idProof = $_SESSION['id_proof'];
$pick_up_time = $_SESSION['pick_up_time'];
$drop_off_time = $_SESSION['drop_off_time'];

// Fetch bike details for pickup location
$bike_query = "SELECT address FROM abike WHERE id = '$bike_id'";
$bike_res = $conn->query($bike_query);
$bike_data = $bike_res->fetch_assoc();
$pickup_location = $bike_data['address'] ?? '';

$paymentMethod = "Cash";
$booking_status = "active";

// If we have a pending lock, update it. Otherwise insert (fallback).
if (isset($_SESSION['pending_booking_id'])) {
    $sr_no = $_SESSION['pending_booking_id'];
    $sql = "UPDATE abookings SET 
            user_id = ?, bike_id = ?, pickup_location = ?, agency_id = ?, 
            booking_date = ?, return_date = ?, total_price = ?, name = ?, 
            idProof = ?, mobile = ?, pick_up_time = ?, drop_off_time = ?, 
            paymentMethod = ?, booking_status = ? 
            WHERE sr_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssdsssssssi", $user_id, $bike_id, $pickup_location, $agency_id, $booking_date, $return_date, $total_price, $name, $idProof, $mobile, $pick_up_time, $drop_off_time, $paymentMethod, $booking_status, $sr_no);
} else {
    $sql = "INSERT INTO abookings (user_id, bike_id, pickup_location, agency_id, booking_date, return_date, total_price, name, idProof, mobile, pick_up_time, drop_off_time, paymentMethod, booking_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssdsssssss", $user_id, $bike_id, $pickup_location, $agency_id, $booking_date, $return_date, $total_price, $name, $idProof, $mobile, $pick_up_time, $drop_off_time, $paymentMethod, $booking_status);
}


if ($stmt->execute()) {
    unset($_SESSION['pending_booking_id']); // Clear the lock after success
    $_SESSION['message'] = "Booking successful!";
    header("Location: bookings.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
<?php
session_start();
require 'vendor/includes/autoload.php';

$mysqli = new mysqli("localhost", "root", "", "dwk");

// Validate CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die(json_encode(['error' => 'Invalid CSRF token']));
}

// Get and validate input data
$bike_id = $mysqli->real_escape_string($_POST['bike_id']);
$booking_date = $mysqli->real_escape_string($_POST['booking_date']);
$return_date = $mysqli->real_escape_string($_POST['return_date']);

// Fetch bike price and calculate total amount
$bike = $mysqli->query("SELECT price_per_day FROM abike WHERE id = '$bike_id'")->fetch_assoc();
$totalDays = ceil((strtotime($return_date) - strtotime($booking_date)) / 86400) + 1;
$totalAmount = $totalDays * $bike['price_per_day'] * 100;

// Create Razorpay order
try {
    $api = new Api('RAZORPAY_KEY_ID', 'RAZORPAY_KEY_SECRET');
    $order = $api->order->create([
        'amount' => $totalAmount,
        'currency' => 'INR',
        'payment_capture' => 1
    ]);
    
    header('Content-Type: application/json');
    echo json_encode($order);
} catch (Exception $e) {
    die(json_encode(['error' => $e->getMessage()]));
}
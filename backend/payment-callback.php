<?php
require_once 'includes/config.php';

// Get callback data
$response = file_get_contents('php://input');
$data = json_decode($response, true);

// Verify checksum
$checksum = $data['response']['checksum'];
$calculatedChecksum = hash('sha256', $data['response'] . '/pg/v1/pay' . PHONEPE_SALT_KEY) . '###' . PHONEPE_SALT_INDEX;

if ($checksum !== $calculatedChecksum) {
    die("Invalid checksum");
}

// Process payment status
$transactionId = $data['response']['merchantTransactionId'];
$paymentStatus = $data['response']['code'] === 'PAYMENT_SUCCESS' ? 'success' : 'failed';

// Update database
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$mysqli->query("UPDATE abookings SET payment_status = '$paymentStatus' WHERE transaction_id = '$transactionId'");

if ($paymentStatus === 'success') {
    // Bike availability is now handled by the booking records, not abike.status
    $booking = $mysqli->query("SELECT bike_id FROM abookings WHERE transaction_id = '$transactionId'")->fetch_assoc();
}

// Send response to PhonePe
echo json_encode(["message" => "Callback processed"]);
?>
<!-- live production   
error_log("Payment callback received: " . print_r($data, true));

// Add database transaction rollback on failure
$mysqli->begin_transaction();
try {
    // Update payment status
    // Update bike status
    
    $mysqli->commit();
} catch (Exception $e) {
    $mysqli->rollback();
    error_log("Transaction failed: " . $e->getMessage());
}-->
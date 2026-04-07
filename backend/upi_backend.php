<?php
// Set the content type to JSON
header('Content-Type: application/json');

// Get the raw POST data from the request
$inputData = json_decode(file_get_contents('php://input'), true);

// Check if UPI ID and amount are set
if (isset($inputData['upiId']) && isset($inputData['total_price'])) {
    $upiId = $inputData['upiId'];
    $amount = $inputData['amount'];

    // Construct the UPI payment link
    $merchantName = 'MerchantName'; // Set the merchant name
    $merchantCode = '0000'; // Set your merchant code (if any)
    $transactionId = uniqid('txn_'); // Generate a unique transaction ID
    $merchantUrl = 'http://example.com'; // Set your merchant website URL

    // UPI deep link format
    $upiLink = "upi://pay?pa=$upiId&pn=$merchantName&mc=$merchantCode&tid=$transactionId&url=$merchantUrl&am=$amount&cu=INR";

    // Send the payment link as a JSON response
    echo json_encode([
        'status' => 'success',
        'paymentLink' => $upiLink
    ]);
} else {
    // Return an error if required data is missing
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid data'
    ]);
}
?>

<?php
session_start();
require_once 'includes/config.php';

// Verify CSRF token
// if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
//     die("Invalid CSRF token");
// }

// Create database connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Get bike price
$id = $mysqli->real_escape_string($_GET['id']);
$bikeResult = $mysqli->query("SELECT price_per_day FROM abike WHERE id = '$id'");
$bike = $bikeResult->fetch_assoc();
$pricePerDay = $bike['price_per_day'];

// Calculate total price
$booking_date = new DateTime($_POST['booking_date']);
$return_date = new DateTime($_POST['return_date']);
$interval = $return_date->diff($booking_date);
$totalDays = $interval->days + 1;
$totalPrice = $totalDays * $pricePerDay;

// Generate transaction ID
$transactionId = 'TXN' . uniqid();

// Insert booking with pending status
$query = "INSERT INTO abookings (
    user_id, bike_id, booking_date, return_date, total_price, 
    name, age, idproof, mobile, email, paymentMethod,
    pick_up_time, drop_off_time, transaction_id, payment_status
) VALUES (
    '{$_SESSION['user_id']}', '$id', '{$_POST['booking_date']}', 
    '{$_POST['return_date']}', '$totalPrice', '{$_POST['name']}', 
    '{$_POST['age']}', '{$_POST['idProof']}', '{$_POST['mobile']}', 
    '{$_POST['email']}', '{$_POST['paymentMethod']}', 
    '{$_POST['pick_up_time']}', '{$_POST['drop_off_time']}',
    '$transactionId', 'pending'
)";

if (!$mysqli->query($query)) {
    die("Error creating booking: " . $mysqli->error);
}

// Prepare PhonePe payload
$payload = [
    "merchantId" => "PGTESTPAYUAT86",
    "merchantTransactionId" => $transactionId,
    "merchantUserId" => $_SESSION['user_id'],
    "amount" => $totalPrice * 100, // Amount in paise
    "redirectUrl" => "localhost/dwk/payment-callback.php",
    "redirectMode" => "POST",
    "callbackUrl" => "localhost/dwk/payment-callback.php",
    "paymentInstrument" => [
        "type" => "PAY_PAGE"
    ]
];

// Generate checksum
$base64Payload = base64_encode(json_encode($payload));
$checksum = hash('sha256', $base64Payload . '/pg/v1/pay' . PHONEPE_SALT_KEY) . '###' . PHONEPE_SALT_INDEX;

// PhonePe API URL
$apiUrl = (PHONEPE_ENV === 'SANDBOX') 
    ? 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay'
    : 'https://api.phonepe.com/apis/pg/v1/pay';

// Initiate payment request
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-VERIFY: ' . $checksum
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['request' => $base64Payload]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $responseData = json_decode($response, true);
    $paymentUrl = $responseData['data']['instrumentResponse']['redirectInfo']['url'];
    header("Location: $paymentUrl");
    exit();
} else {
    die("Error initiating payment: " . $response);
}
?>
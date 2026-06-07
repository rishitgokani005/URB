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


// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    
    // Read and sanitize input variables
    $cab_id = isset($_POST['cab_id']) ? mysqli_real_escape_string($conn, $_POST['cab_id']) : '';
    $agency_id = isset($_POST['agency_id']) ? mysqli_real_escape_string($conn, $_POST['agency_id']) : '';
    $pickup_location = isset($_POST['pickup_location']) ? mysqli_real_escape_string($conn, $_POST['pickup_location']) : '';
    $drop_location = isset($_POST['drop_location']) ? mysqli_real_escape_string($conn, $_POST['drop_location']) : '';
    $trip_type = isset($_POST['trip_type']) && $_POST['trip_type'] === 'roundtrip' ? 'roundtrip' : 'oneway';
    $booking_date = isset($_POST['booking_date']) ? mysqli_real_escape_string($conn, $_POST['booking_date']) : '';
    $return_date = isset($_POST['return_date']) && !empty($_POST['return_date']) ? mysqli_real_escape_string($conn, $_POST['return_date']) : null;
    $pick_up_time = isset($_POST['pick_up_time']) ? mysqli_real_escape_string($conn, $_POST['pick_up_time']) : '';
    $total_price = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0.00;
    
    // Passenger inputs
    $passenger_name = isset($_POST['passenger_name']) ? mysqli_real_escape_string($conn, $_POST['passenger_name']) : '';
    $passenger_phone = isset($_POST['passenger_phone']) ? mysqli_real_escape_string($conn, $_POST['passenger_phone']) : '';
    $passenger_email = isset($_POST['passenger_email']) ? mysqli_real_escape_string($conn, $_POST['passenger_email']) : '';
    $id_proof = isset($_POST['id_proof']) ? mysqli_real_escape_string($conn, $_POST['id_proof']) : '';
    
    // Basic verification
    if (empty($cab_id) || empty($pickup_location) || empty($drop_location) || empty($booking_date) || empty($pick_up_time) || empty($passenger_name) || empty($passenger_phone)) {
        $_SESSION['message'] = "Please fill all required passenger and route details.";
        header("Location: taxi-booking.php");
        exit;
    }

    // Generate unique booking id
    $booking_id = 'CAB' . time();
    $est_distance = isset($_POST['est_distance']) ? intval($_POST['est_distance']) : 100;

    // Prepare SQL Insert statement
    $sql = "INSERT INTO acabookings (
                booking_id, user_id, cab_id, agency_id, 
                pickup_location, drop_location, trip_type, est_distance,
                booking_date, return_date, pick_up_time, 
                total_price, name, idProof, mobile, email, 
                paymentMethod, booking_status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Cash', 'active')";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(
            "sissssissssdssss", 
            $booking_id, $user_id, $cab_id, $agency_id,
            $pickup_location, $drop_location, $trip_type, $est_distance,
            $booking_date, $return_date, $pick_up_time,
            $total_price, $passenger_name, $id_proof, $passenger_phone, $passenger_email
        );

        if ($stmt->execute()) {
            $_SESSION['message'] = "Cab booking successful! Ride ID: $booking_id";
            // Display alert popup and redirect to homepage
            echo "<script>
                alert('booked taxi');
                window.location.href = '../index.php';
            </script>";
            exit;
        } else {
            $_SESSION['message'] = "Booking execution error: " . $stmt->error;
            header("Location: taxi-booking.php");
            exit;
        }
    } else {
        $_SESSION['message'] = "Database statement preparation failed: " . $conn->error;
        header("Location: taxi-booking.php");
        exit;
    }
} else {
    header("Location: taxi-booking.php");
    exit;
}
?>

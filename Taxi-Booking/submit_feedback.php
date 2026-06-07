<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = isset($_POST['booking_id']) ? mysqli_real_escape_string($conn, $_POST['booking_id']) : '';
    $cab_id = isset($_POST['cab_id']) ? mysqli_real_escape_string($conn, $_POST['cab_id']) : '';
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
    $comments = isset($_POST['comments']) ? mysqli_real_escape_string($conn, $_POST['comments']) : '';
    $user_id = $_SESSION['user_id'];

    if (empty($booking_id) || empty($cab_id) || $rating < 1 || $rating > 5) {
        $_SESSION['message'] = "Invalid feedback details. Please try again.";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../bookings.php'));
        exit;
    }

    // Insert feedback into cab_feedback table
    $sql = "INSERT INTO cab_feedback (booking_id, user_id, cab_id, rating, comments) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sisiss", $booking_id, $user_id, $cab_id, $rating, $comments);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Thank you for your feedback! Your rating of {$rating} stars has been recorded.";
        } else {
            // Check if unique constraint failed (already rated)
            if ($conn->errno == 1062) {
                $_SESSION['message'] = "Feedback has already been submitted for this booking.";
            } else {
                $_SESSION['message'] = "Failed to submit feedback. Please try again later. Error: " . $conn->error;
            }
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = "Database error: " . $conn->error;
    }
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../bookings.php'));
exit;
?>

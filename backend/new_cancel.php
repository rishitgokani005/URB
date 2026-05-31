<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['booking_id']) && !empty($_POST['booking_id'])) {
        $booking_id = trim($_POST['booking_id']); // Get the booking ID
        $reason = isset($_POST['reason']) ? $_POST['reason'] : 'Not specified';
        if ($reason === 'Other' && isset($_POST['other_reason'])) {
            $reason = trim($_POST['other_reason']);
        }

        // For debugging: Check what booking_id is being passed
        error_log("Booking ID received: " . $booking_id . " Reason: " . $reason);

        // Proceed with the cancellation query
        $check_query = "SELECT * FROM abookings WHERE booking_id = ? AND booking_status = 'active'";
        $check_stmt = $conn->prepare($check_query);

        if ($check_stmt) {
            $check_stmt->bind_param("s", $booking_id); // Match specific booking_id
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows > 0) {
                // Fetch the bike ID associated with the booking
                $booking_data = $result->fetch_assoc();
                $bike_id = $booking_data['bike_id']; // Assuming `bike_id` column exists in `abookings` table

                // Proceed to cancel booking with reason
                $update_booking_query = "UPDATE abookings SET booking_status = 'cancelled', cancellation_reason = ? WHERE booking_id = ? AND booking_status = 'active'";
                $update_booking_stmt = $conn->prepare($update_booking_query);

                if ($update_booking_stmt) {
                    $update_booking_stmt->bind_param("ss", $reason, $booking_id);

                    if ($update_booking_stmt->execute()) {
                        echo "<script>
                            alert('Booking successfully cancelled.');
                            window.location.href = '../bookings.php';
                        </script>";
                    } else {
                        echo "<script>
                            alert('Error updating booking status.');
                            window.history.back();
                        </script>";
                    }
                    $update_booking_stmt->close();
                } else {
                    echo "<script>
                        alert('Error preparing update statement.');
                        window.history.back();
                    </script>";
                }
            } else {
                echo "<script>
                    alert('No active bookings found for this booking ID.');
                    window.history.back();
                </script>";
            }

            $check_stmt->close();
        } else {
            echo "<script>
                alert('Error preparing check statement.');
                window.history.back();
            </script>";
        }
    } else {
        echo "<script>
            alert('Invalid booking ID.');
            window.history.back();
        </script>";
    }
} else {
    echo "<script>
        alert('Invalid request method.');
        window.history.back();
    </script>";
}

$conn->close();
?>

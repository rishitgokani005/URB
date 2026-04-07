<?php
session_start();
require 'includes/db.php'; // Your database connection file

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Prepare the statement with parameter binding
    $stmt = $conn->prepare("SELECT status FROM abike WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $bike = $result->fetch_assoc();

    // Toggle status based on current value
    $new_status = $bike['status'] ? 0 : 1;

    // Update the new status in the database
    $update_stmt = $conn->prepare("UPDATE abike SET status = ? WHERE id = ?");
    $update_stmt->bind_param("is", $new_status, $id);
    $update_stmt->execute();

    // Redirect after updating status
    header('Location: admin_dashboard.php');
    exit();
} else {
    echo "Invalid request.";
}
?>

<?php
include('includes/db.php');

$sql1 = "ALTER TABLE abookings ADD COLUMN IF NOT EXISTS pickup_location VARCHAR(255) AFTER bike_id";
$sql2 = "ALTER TABLE abike ADD COLUMN IF NOT EXISTS address VARCHAR(255) AFTER id";

if ($conn->query($sql1) && $conn->query($sql2)) {
    echo "Database updated successfully: Added fields to abookings and abike tables.";
} else {
    echo "Error updating database: " . $conn->error;
}
?>

<?php
include('includes/db.php');

// List of columns to ensure existence in abookings
$columns = [
    'agency_id' => "VARCHAR(20) NOT NULL AFTER bike_id",
    'age' => "INT(11) NOT NULL DEFAULT 18",
    'booking_id' => "VARCHAR(20) DEFAULT NULL",
    'pick_up_time' => "TIME DEFAULT '09:00:00'",
    'drop_off_time' => "TIME DEFAULT '09:00:00'"
];

foreach ($columns as $col => $definition) {
    $check = $conn->query("SHOW COLUMNS FROM abookings LIKE '$col'");
    if ($check->num_rows == 0) {
        $conn->query("ALTER TABLE abookings ADD COLUMN $col $definition");
        echo "Added column $col<br>";
    }
}

// Ensure agency_id in abike
$check_bike = $conn->query("SHOW COLUMNS FROM abike LIKE 'agency_id'");
if ($check_bike->num_rows == 0) {
    $conn->query("ALTER TABLE abike ADD COLUMN agency_id VARCHAR(20) NOT NULL");
    echo "Added agency_id to abike<br>";
}

// Ensure users table exists (some use register)
$conn->query("CREATE TABLE IF NOT EXISTS users LIKE register"); 
// If register exists and users doesn't, this copies it. 
// Otherwise handled by the full SQL I gave.

echo "Database auto-repair complete.";
?>

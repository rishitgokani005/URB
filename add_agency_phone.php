<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dwk";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add phone column to agencies table if it doesn't exist
$sql = "ALTER TABLE agencies ADD COLUMN IF NOT EXISTS phone VARCHAR(15) AFTER email";
if ($conn->query($sql)) {
    echo "Column 'phone' added to 'agencies' table successfully or already exists.\n";
} else {
    echo "Error adding column: " . $conn->error . "\n";
}

// Optional: Add phone column to users table if it doesn't exist (already exists per schema)

$conn->close();
?>

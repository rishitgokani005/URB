<?php
include(__DIR__ . '/../includes/db.php');

$sql = "CREATE TABLE IF NOT EXISTS cab_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    cab_id VARCHAR(20) NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'cab_feedback' created successfully.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}
?>

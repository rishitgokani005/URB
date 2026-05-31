<?php
$conn = new mysqli('localhost', 'root', '', 'dwk');
if ($conn->query("ALTER TABLE abookings ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP")) {
    echo "Column added successfully";
} else {
    echo "Error: " . $conn->error;
}
?>

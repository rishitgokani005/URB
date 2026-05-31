<?php
include('includes/db.php');

// 1. Create Agencies Table
$sql1 = "CREATE TABLE IF NOT EXISTS `agencies` (
  `id` varchar(20) PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `city` varchar(50) NOT NULL,
  `email` varchar(100) UNIQUE NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// 2. Add agency_id to abike
$sql2 = "ALTER TABLE `abike` ADD COLUMN IF NOT EXISTS `agency_id` varchar(20)";

// 3. Add agency_id to abookings
$sql3 = "ALTER TABLE `abookings` ADD COLUMN IF NOT EXISTS `agency_id` varchar(20)";

if ($conn->query($sql1) && $conn->query($sql2) && $conn->query($sql3)) {
    echo "Database structure updated successfully.<br>";
} else {
    echo "Error updating database: " . $conn->error . "<br>";
}

// 4. Cleanup old files (I'll do this via script in next step)
?>

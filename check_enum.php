<?php
$conn = new mysqli('localhost', 'root', '', 'dwk');
$res = $conn->query("SHOW COLUMNS FROM abookings LIKE 'booking_status'");
$row = $res->fetch_assoc();
echo $row['Type'] . "\n";
?>

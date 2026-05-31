<?php
$conn = new mysqli('localhost', 'root', '', 'dwk');
$res = $conn->query('DESCRIBE abookings');
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
?>

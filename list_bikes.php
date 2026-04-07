<?php
$mysqli = new mysqli('localhost', 'root', '', 'dwk');
if ($mysqli->connect_error) die("Conn Failed");
$result = $mysqli->query('SELECT id, model FROM abike');
while ($row = $result->fetch_assoc()) {
    echo $row['id'] . ' - ' . $row['model'] . "\n";
}
$mysqli->close();
?>

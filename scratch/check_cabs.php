<?php
$conn = new mysqli('localhost', 'root', '', 'dwk');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== CABS ===\n";
$res = $conn->query("SELECT * FROM acab");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "No acab data or query error.\n";
}

echo "=== AGENCIES ===\n";
$res2 = $conn->query("SELECT * FROM agencies");
if ($res2) {
    while ($row = $res2->fetch_assoc()) {
        print_r($row);
    }
}
?>

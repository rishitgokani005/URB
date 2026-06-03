<?php
$conn = new mysqli('localhost', 'root', '', 'dwk');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== TABLES ===\n";
$res = $conn->query("SHOW TABLES");
while ($row = $res->fetch_row()) {
    $table = $row[0];
    echo "- $table\n";
    $fields = $conn->query("DESCRIBE `$table`");
    while ($f = $fields->fetch_assoc()) {
        echo "  * {$f['Field']} ({$f['Type']})\n";
    }
}
?>

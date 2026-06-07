<?php
include('includes/db.php');

function check_table($conn, $table) {
    echo "<h3>Columns in $table:</h3>";
    $res = $conn->query("SHOW COLUMNS FROM $table");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            echo $row['Field'] . " - " . $row['Type'] . "<br>";
        }
    } else {
        echo "Table $table not found.<br>";
    }
}

check_table($conn, 'agencies');
check_table($conn, 'abookings');
?>

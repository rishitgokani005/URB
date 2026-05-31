<?php
include 'includes/db.php';

$queries = [
    "ALTER TABLE abookings MODIFY age int(100) DEFAULT NULL",
    "ALTER TABLE abookings MODIFY email varchar(255) DEFAULT NULL",
    "ALTER TABLE abookings MODIFY paymentMethod varchar(255) DEFAULT 'Cash'",
    "ALTER TABLE abookings MODIFY id varchar(255) DEFAULT NULL"
];

foreach ($queries as $query) {
    if ($conn->query($query) === TRUE) {
        echo "Query successful: $query<br>";
    } else {
        echo "Error: " . $conn->error . "<br>";
    }
}

echo "Migration for simplified booking complete.";
?>

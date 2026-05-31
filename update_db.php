<?php
include 'includes/db.php';

$queries = [
    "ALTER TABLE abike ADD COLUMN city VARCHAR(100) AFTER address",
    "ALTER TABLE abike ADD COLUMN agency_name VARCHAR(150) AFTER city",
    "ALTER TABLE abike ADD COLUMN image2 VARCHAR(255) AFTER image",
    "ALTER TABLE abike ADD COLUMN image3 VARCHAR(255) AFTER image2",
    "ALTER TABLE abike ADD COLUMN image4 VARCHAR(255) AFTER image3"
];

foreach ($queries as $query) {
    if ($conn->query($query) === TRUE) {
        echo "Query successful: $query<br>";
    } else {
        echo "Error executing query ($query): " . $conn->error . "<br>";
    }
}

// Add some sample data updates based on existing addresses
$updates = [
    "UPDATE abike SET city='Somnath', agency_name='Shreeji Bike Rentals' WHERE address LIKE '%shreeji%'",
    "UPDATE abike SET city='Dwarka', agency_name='Krishna Bike Rental' WHERE address LIKE '%Krishna%'",
    "UPDATE abike SET city='Dwarka', agency_name='Bharat Bikes' WHERE address LIKE '%Bharat%'"
];

foreach ($updates as $update) {
    $conn->query($update);
}

echo "Database update complete.";
?>

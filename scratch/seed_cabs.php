<?php
$conn = new mysqli('localhost', 'root', '', 'dwk');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Clear existing cabs in acab table first to prevent duplicate errors if re-run
$conn->query("DELETE FROM acab");

// Cabs data definition
$cabs = [
    // Dwarka Agency: AGY92503 (Krishna Bikes)
    [
        'id' => 'CAB-DWK-001',
        'agency_id' => 'AGY92503',
        'agency_name' => 'Krishna Bikes',
        'cab_name' => 'Maruti Suzuki Swift (AC)',
        'cab_type' => 'Hatchback',
        'seats' => 4,
        'price_per_km' => 11,
        'address' => 'Krishna Bike Rental, 1st Floor ABC Building, Dwarka',
        'city' => 'Dwarka',
        'image' => '4-seater-car.webp',
        'image2' => 'AC' // Let's store AC status here for easy querying or filtering if needed, though we can check if string contains AC or add field if database permits. We will query cab_name or build filter logically!
    ],
    [
        'id' => 'CAB-DWK-002',
        'agency_id' => 'AGY92503',
        'agency_name' => 'Krishna Bikes',
        'cab_name' => 'Honda City Premium (AC)',
        'cab_type' => 'Sedan',
        'seats' => 4,
        'price_per_km' => 14,
        'address' => 'Krishna Bike Rental, 1st Floor ABC Building, Dwarka',
        'city' => 'Dwarka',
        'image' => '4-seater-car.webp',
        'image2' => 'AC'
    ],
    [
        'id' => 'CAB-DWK-003',
        'agency_id' => 'AGY92503',
        'agency_name' => 'Krishna Bikes',
        'cab_name' => 'Toyota Innova Crysta (AC)',
        'cab_type' => 'SUV',
        'seats' => 7,
        'price_per_km' => 18,
        'address' => 'Krishna Bike Rental, 1st Floor ABC Building, Dwarka',
        'city' => 'Dwarka',
        'image' => '7-Seater-cab.jpg',
        'image2' => 'AC'
    ],
    [
        'id' => 'CAB-DWK-004',
        'agency_id' => 'AGY92503',
        'agency_name' => 'Krishna Bikes',
        'cab_name' => 'Tempo Traveler Cruiser (AC)',
        'cab_type' => 'Van',
        'seats' => 11,
        'price_per_km' => 22,
        'address' => 'Krishna Bike Rental, 1st Floor ABC Building, Dwarka',
        'city' => 'Dwarka',
        'image' => '11-seater-cab.avif',
        'image2' => 'AC'
    ],
    [
        'id' => 'CAB-DWK-005',
        'agency_id' => 'AGY92503',
        'agency_name' => 'Krishna Bikes',
        'cab_name' => 'Mahindra Bolero (Non-AC)',
        'cab_type' => 'SUV',
        'seats' => 7,
        'price_per_km' => 14,
        'address' => 'Krishna Bike Rental, 1st Floor ABC Building, Dwarka',
        'city' => 'Dwarka',
        'image' => '7-Seater-cab.jpg',
        'image2' => 'Non-AC'
    ],
    
    // Somnath Agency: AGY6F5A3 (Happy Bikes)
    [
        'id' => 'CAB-SOM-001',
        'agency_id' => 'AGY6F5A3',
        'agency_name' => 'Happy Bikes',
        'cab_name' => 'Hyundai i20 Magna (AC)',
        'cab_type' => 'Hatchback',
        'seats' => 4,
        'price_per_km' => 11,
        'address' => 'Shreeji Bike Rentals, XYZ Road, Somnath',
        'city' => 'Somnath',
        'image' => '4-seater-car.webp',
        'image2' => 'AC'
    ],
    [
        'id' => 'CAB-SOM-002',
        'agency_id' => 'AGY6F5A3',
        'agency_name' => 'Happy Bikes',
        'cab_name' => 'Maruti Ertiga (AC)',
        'cab_type' => 'SUV',
        'seats' => 7,
        'price_per_km' => 15,
        'address' => 'Shreeji Bike Rentals, XYZ Road, Somnath',
        'city' => 'Somnath',
        'image' => '7-Seater-cab.jpg',
        'image2' => 'AC'
    ],
    [
        'id' => 'CAB-SOM-003',
        'agency_id' => 'AGY6F5A3',
        'agency_name' => 'Happy Bikes',
        'cab_name' => 'Tata Winger Tourist Cruiser (Non-AC)',
        'cab_type' => 'Van',
        'seats' => 11,
        'price_per_km' => 19,
        'address' => 'Shreeji Bike Rentals, XYZ Road, Somnath',
        'city' => 'Somnath',
        'image' => '11-seater-cab.avif',
        'image2' => 'Non-AC'
    ]
];

// Insert cabs into db
$stmt = $conn->prepare("INSERT INTO acab (id, agency_id, agency_name, cab_name, cab_type, seats, price_per_km, address, city, image, image2) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$inserted = 0;
foreach ($cabs as $c) {
    $stmt->bind_param("sssssiissss", $c['id'], $c['agency_id'], $c['agency_name'], $c['cab_name'], $c['cab_type'], $c['seats'], $c['price_per_km'], $c['address'], $c['city'], $c['image'], $c['image2']);
    if ($stmt->execute()) {
        $inserted++;
    } else {
        echo "Error inserting {$c['cab_name']}: " . $stmt->error . "\n";
    }
}

echo "Successfully seeded $inserted cabs into acab table!\n";
$stmt->close();
$conn->close();
?>

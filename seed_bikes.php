<?php
$mysqli = new mysqli('localhost', 'root', '', 'dwk');
if ($mysqli->connect_error) die("Conn Failed");

$bikes = [
    // Yatra
    "INSERT IGNORE INTO abike (id, image, model, color, price_per_day, deposite, status) VALUES ('YB100', 'hotel.jpg', 'Honda Activa', 'Black', 500, 1000, 1)",
    "INSERT IGNORE INTO abike (id, image, model, color, price_per_day, deposite, status) VALUES ('YB101', 'hotel.jpg', 'Suzuki Access', 'White', 550, 1000, 1)",
    // Safar
    "INSERT IGNORE INTO abike (id, image, model, color, price_per_day, deposite, status) VALUES ('SB200', 'hotel.jpg', 'Bajaj Pulsar', 'Red', 700, 1500, 1)",
    // Rapid Ride
    "INSERT IGNORE INTO abike (id, image, model, color, price_per_day, deposite, status) VALUES ('RR300', 'hotel.jpg', 'Royal Enfield', 'Blue', 1200, 2000, 1)",
    // Diu
    "INSERT IGNORE INTO abike (id, image, model, color, price_per_day, deposite, status) VALUES ('DIU400', 'hotel.jpg', 'TVS Jupiter', 'Yellow', 450, 800, 1)",
    // Goa
    "INSERT IGNORE INTO abike (id, image, model, color, price_per_day, deposite, status) VALUES ('GOA500', 'hotel.jpg', 'Yamaha R15', 'Black', 1500, 2500, 1)"
];

foreach ($bikes as $sql) {
    if (!$mysqli->query($sql)) {
        echo "Error: " . $mysqli->error . "\n";
    }
}
echo "Done\n";
?>

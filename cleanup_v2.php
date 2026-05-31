<?php
$files_to_delete = [
    'Dwarka.php', 'Somnath.php', 'diu.php', 'goa.php', 'rapid.php', 'safar.php', 'samay.php', 
    'shreejibike.php', 'somnathbike.php', 'testbike.php', 'yatra.php', 'a.php', 'scaffold.php',
    'diu-bikes.php', 'goa-bikes.php'
];

foreach ($files_to_delete as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "Deleted: $file<br>";
    }
}

// Clean old admin panels
$admin_files = [
    'admin/1admin.php', 'admin/2admin.php', 'admin/aadmin.php', 
    'admin/abookings.php', 'admin/shreejiadmin.php', 
    'admin/shreejibookings.php', 'admin/somnathbookings.php'
];

foreach ($admin_files as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "Deleted admin file: $file<br>";
    }
}

echo "Cleanup complete.";
?>

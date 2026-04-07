<?php
$files = [
    'admin/abookings.php',
    'admin/pbookings.php',
    'admin/shreejibookings.php',
    'admin/somnathbookings.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace("'includes/header.php'", "'../includes/header.php'", $content);
        $content = str_replace("'includes/footer.php'", "'../includes/footer.php'", $content);
        
        // Also check double quotes just in case
        $content = str_replace("\"includes/header.php\"", "\"../includes/header.php\"", $content);
        $content = str_replace("\"includes/footer.php\"", "\"../includes/footer.php\"", $content);
        
        file_put_contents($file, $content);
        echo "Fixed $file\n";
    }
}
?>

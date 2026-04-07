<?php
$template = file_get_contents('a.php');

$files = [
    'yatra.php' => "id like 'YB%'",
    'safar.php' => "id like 'SB%'",
    'rapid.php' => "id like 'RR%'",
    'goa-bikes.php' => "id like 'GOA%'",
    'diu-bikes.php' => "id like 'DIU%'"
];

foreach ($files as $file => $query) {
    $content = str_replace("id like 'GJ%'", $query, $template);
    file_put_contents($file, $content);
}
echo "Created files";
?>

<?php
$files = [
    'admin/abookings.php',
    'admin/pbookings.php',
    'admin/shreejibookings.php',
    'admin/somnathbookings.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);

    // FIX 1: Redirect to ../index.php
    $content = str_replace("window.location.href='index.php'", "window.location.href='../index.php'", $content);

    // FIX 2: Terms.html path
    $content = str_replace('href="terms.html"', 'href="../terms.html"', $content);

    // FIX 3: Extraneous closing div from previous bad replace that pushed elements outside
    // Look for:
    //      </div>
    //      </div>
    //  </div>
    // </div>
    // AND the nested form issue. Wait, using regex is safer.
    
    // Instead of Regex, let's fix the exact block at the bottom
    $block_target = <<<HTML
<div class="form-group">
        <form action="" method="POST">
    <!-- Pass the booking_id -->
    <input type="hidden" name="booking_id" value="<?php echo \$booking_id; ?>">
    <button type="submit">Book</button>

</form>
</div>
</div>
HTML;
    // Actually, looking at abookings.php lines 428-446:
    // We only need to fix the bad `</div>` which was right after `paymentMethod`.
    // Let's fix that directly by targeting payment-method-group closure.
    
    $payment_target = <<<HTML
        <div class="radio-item">
            <input type="radio" id="cash" name="paymentMethod" value="Cash" required>
            <label for="cash">Cash</label>
        </div>
        </div>
    </div>
</div>
HTML;

    $payment_replacement = <<<HTML
        <div class="radio-item">
            <input type="radio" id="cash" name="paymentMethod" value="Cash" required>
            <label for="cash">Cash</label>
        </div>
    </div>
</div>
HTML;
    $content = str_replace($payment_target, $payment_replacement, $content);
    
    // FIX 4: Remove nested <form action="" method="POST"> which breaks HTML
    $nested_form_target = <<<HTML
<div class="form-group">
        <form action="" method="POST">
    <!-- Pass the booking_id -->
HTML;
    $nested_form_replacement = <<<HTML
<div class="form-group">
    <!-- Pass the booking_id -->
HTML;
    $content = str_replace($nested_form_target, $nested_form_replacement, $content);

    // Also, there might be other permutations like `name="booking_id" value="">`
    // Let's just remove `<form action="" method="POST">` wherever it appears after `<form id="bookingForm"`
    $content = str_replace('<form action="" method="POST">', '', $content);
    
    file_put_contents($file, $content);
    echo "Fixed \$file\n";
}
?>

<?php

// Manually autoload Razorpay SDK classes
function razorpay_autoloader($class) {
    // Check if the class belongs to Razorpay
    $prefix = 'Razorpay\\';  // The namespace Razorpay uses

    // If the class uses the Razorpay namespace, proceed
    if (strpos($class, $prefix) === 0) {
        // Replace the namespace separator with directory separator
        $relativeClass = str_replace($prefix, '', $class);
        $relativeClass = str_replace('\\', '/', $relativeClass);

        // Define the path to the Razorpay SDK classes
        $file = __DIR__ . '/razorpay-php/' . $relativeClass . '.php';

        // If the file exists, require it
        if (file_exists($file)) {
            require_once $file;
        }
    }
}

// Register the autoloader
spl_autoload_register('razorpay_autoloader');

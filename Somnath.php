<?php 
session_start();
require 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo/urbanride1.ico"  sizes="1080x1080" type="image/x-icon">
    <link rel="stylesheet" href="css/Dwarka1.css">
    <title>Somnath Bike Rentals</title>
    <!-- <style>
/* Make the body take full height and remove any default margin */
body, html {
    margin: 0;
    padding: 0;
    height: 100%;
}

.fullscreen-image {
    width: 100%;
    height: 100%;
   ; /* This will ensure the image covers the screen */
    display: block;
    position: absolute; /* To make sure it fits the viewport */
    top: 0;
    left: 0;
}

    </style> -->
    
</head>
<body>

  <section class="agency-container" id="agency-container">
    <!-- Travel agency cards will be dynamically inserted here -->
  </section>
    <script src="assets/js/somnath.js"></script>

<!-- 
    <div class="image-container">
        <img src="images/cs.png" alt="Coming Soon" class="fullscreen-image">
    </div> -->
    <?php require 'includes/footer.php';?>
</body>
</html>

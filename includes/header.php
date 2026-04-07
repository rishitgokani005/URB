<?php $base_url = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : ''; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;300;400;500;700;900&family=Inter:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
</head>
<body>
    <header class="glass-effect">
        <div id="menu-bar" class="fas fa-bars"></div>
        <div class="logo">
            <span class="white" style="color: var(--dark-color); font-family: 'Bebas Neue', sans-serif;">u</span>
            <span class="white" style="color: var(--dark-color); font-family: 'Bebas Neue', sans-serif;">r</span>
            <span class="white" style="color: var(--dark-color); font-family: 'Bebas Neue', sans-serif;">b</span>
            <span class="white" style="color: var(--dark-color); font-family: 'Bebas Neue', sans-serif;">a</span>
            <span class="white" style="color: var(--dark-color); font-family: 'Bebas Neue', sans-serif;">n</span>
            <span class="orange-italic" style="color: var(--primary); font-family: 'Bebas Neue', sans-serif; text-decoration: underline;">R</span>
            <span class="white" style="color: var(--dark-color); font-family: 'Bebas Neue', sans-serif;">i</span>
            <span class="white" style="color: var(--dark-color); font-family: 'Bebas Neue', sans-serif;">d</span>
            <span class="white" style="color: var(--dark-color); font-family: 'Bebas Neue', sans-serif;">e</span>
        </div>
        <nav class="navbar">
            <a href="<?php echo $base_url; ?>index.php">Home</a>
            <a href="<?php echo $base_url; ?>my_bookings.php">My Bookings</a>
            <a href="<?php echo $base_url; ?>index.php#offer-section">What we offer?</a>
            <a href="<?php echo $base_url; ?>index.php#how-it-works">How it works</a>
            <a href="<?php echo $base_url; ?>logout.php">Logout</a>
            <a href="#footer">About us</a>
        </nav>
    </header>
    <main>
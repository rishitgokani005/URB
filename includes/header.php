<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '';

// Auto-Unlock Mechanism
if (isset($_SESSION['pending_booking_id'])) {
    $current_page = basename($_SERVER['PHP_SELF']);
    $booking_pages = ['booking-details.php', 'checkout.php', 'process_booking.php', 'booking_status.php'];
    
    if (!in_array($current_page, $booking_pages)) {
        include_once __DIR__ . '/db.php';
        $pending_id = $_SESSION['pending_booking_id'];
        $conn->query("DELETE FROM abookings WHERE sr_no = '$pending_id' AND booking_status = 'pending'");
        unset($_SESSION['pending_booking_id']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/style.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;300;400;500;700;900&family=Inter:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <?php
    if (!isset($_SESSION['loggedin'])) {
        include_once __DIR__ . '/login_modal.php';
    }
    ?>
    <header id="main-header">
        <div id="menu-bar" class="fas fa-bars"></div>
        <a href="<?php echo $base_url; ?>index.php" class="logo">
            Urban<span>Ride</span>
        </a>

        <nav class="nav-links">
            <a href="<?php echo $base_url; ?>index.php">Home</a>
            <a href="<?php echo isset($_SESSION['loggedin']) ? $base_url . 'bookings.php' : 'javascript:void(0)'; ?>"
                onclick="<?php echo !isset($_SESSION['loggedin']) ? 'showLoginModal();' : ''; ?>">My Bookings</a>
            <a href="<?php echo $base_url; ?>index.php#fleet">Our Fleet</a>
            <a href="<?php echo $base_url; ?>index.php#how-it-works">How It Works</a>
        </nav>

        <div class="header-btns">
            <?php if (isset($_SESSION['loggedin'])): ?>
                <a href="<?php echo $base_url; ?>logout.php" class="btn-login">Logout</a>
                <!-- <a href="<?php echo $base_url; ?>bookings.php" class="btn-signup">My Account</a> -->
            <?php else: ?>
                <a href="javascript:void(0)" onclick="showLoginModal()" class="btn-login">Login</a>
                <!-- <a href="register.php" class="btn-signup">Sign Up</a> -->
            <?php endif; ?>
        </div>
    </header>

    <main>
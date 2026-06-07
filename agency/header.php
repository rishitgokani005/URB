<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_in_agency = (basename(dirname($_SERVER['PHP_SELF'])) === 'agency');
$agency_path = $is_in_agency ? '' : '../agency/';

if (!isset($_SESSION['agency_logged_in'])) {
    header('Location: ' . $agency_path . 'index.php');
    exit;
}
include('../includes/db.php');
$agency_id = $_SESSION['agency_id'];
$agency_name = $_SESSION['agency_name'];
$base_url = '../';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $agency_name; ?> | Agency Dashboard</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* Admin Specific Overrides */
        body {
            background: var(--bg-sub);
        }

        .admin-nav {
            background: var(--accent);
            padding: 1.2rem 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1100;
        }

        .admin-links {
            display: flex;
            gap: 2rem;
        }

        .admin-links a {
            color: #94A3B8;
            font-weight: 500;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .admin-links a:hover,
        .admin-links a.active {
            color: white;
        }

        .admin-content {
            padding: 100px 7% 40px;
            min-height: 80vh;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            background: var(--primary-light);
            color: var(--primary);
        }

        .stat-info h4 {
            font-size: 0.8rem;
            color: var(--text-sub);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-info b {
            font-size: 1.8rem;
            color: var(--text-main);
            font-family: var(--font-heading);
        }

        .table-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        th {
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid var(--bg-sub);
            color: var(--text-sub);
            font-size: 0.85rem;
            text-transform: uppercase;
            font-weight: 700;
        }

        td {
            padding: 20px 15px;
            border-bottom: 1px solid var(--bg-sub);
            color: var(--text-main);
            font-size: 0.95rem;
        }

        .btn-update {
            background: var(--primary);
            color: white !important;
            padding: 8px 15px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        /* Modal Styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            padding: 40px;
            border-radius: 30px;
            width: 100%;
            max-width: 550px;
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--border);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-content h2 {
            margin-bottom: 25px;
            font-family: var(--font-heading);
        }

        .modal-content input,
        .modal-content textarea {
            width: 100%;
            padding: 12px 15px;
            border-radius: 12px;
            border: 1.5px solid var(--border);
            margin-bottom: 15px;
            font-weight: 600;
            font-family: inherit;
        }

        /* Status Badges */
        .badge-active { background: #E0F2FE; color: #0369A1; }
        .badge-pending { background: #FEF3C7; color: #92400E; }
        .badge-completed { background: #F3F4F6; color: #4B5563; }
        .badge-cancelled { background: #FEE2E2; color: #991B1B; }

        .badge-active, .badge-pending, .badge-completed, .badge-cancelled {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 800;
            display: inline-block;
        }

        @media (max-width: 768px) {
            .admin-nav {
                padding: 1rem 5%;
            }

            #admin-menu-bar {
                display: block !important;
            }

            .admin-links {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 100%;
                background: var(--accent);
                flex-direction: column;
                gap: 0;
                transition: 0.3s;
                border-top: 1px solid rgba(255,255,255,0.1);
                display: flex;
                padding: 20px 0;
                box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            }

            .admin-links.mobile-active {
                left: 0;
            }

            .admin-links a {
                padding: 15px 5%;
                width: 100%;
                border-bottom: 1px solid rgba(255,255,255,0.05);
                font-size: 1.1rem;
            }

            .admin-content {
                padding: 20px 5%;
            }
        }
    </style>
</head>

<body>
    <nav class="admin-nav">
        <a href="<?php echo $agency_path; ?>dashboard.php" class="logo" style="color: white;">Urban<span style="color: var(--primary);">Ride</span> <span style="font-size: 0.6rem; color: #94A3B8; margin-left: 5px;">AGENCY</span></a>
        
        <div id="admin-menu-bar" class="fas fa-bars" style="display: none; cursor: pointer; font-size: 1.5rem; color: white;"></div>

        <div class="admin-links" id="admin-nav-links">
            <a href="<?php echo $agency_path; ?>dashboard.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">Home</a>
            <a href="<?php echo $agency_path; ?>bikes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'bikes.php' ? 'active' : ''; ?>">Our
                Bikes</a>
            <a href="<?php echo $agency_path; ?>bookings.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>">Bookings</a>
            <a href="<?php echo $agency_path; ?>completed_bookings.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'completed_bookings.php' ? 'active' : ''; ?>">Completed Bookings</a>
            <a href="../Taxi-Booking/manage-cabs.php"
                class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage-cabs.php' ? 'active' : ''; ?>"><i class="fas fa-taxi" style="margin-right:5px;"></i>Manage Cabs</a>
            <a href="profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>"><i class="fas fa-user-circle" style="margin-right:5px;"></i>Profile</a>
            <a href="../logout.php" style="color: #F87171;"><i class="fas fa-power-off"></i></a>
        </div>
    </nav>

    <script>
        const menuBtn = document.getElementById('admin-menu-bar');
        const navLinks = document.getElementById('admin-nav-links');

        if (menuBtn) {
            menuBtn.onclick = () => {
                navLinks.classList.toggle('mobile-active');
                menuBtn.classList.toggle('fa-times');
            };
        }
    </script>
    <div class="admin-content">
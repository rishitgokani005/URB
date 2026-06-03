<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_in_admin = (basename(dirname($_SERVER['PHP_SELF'])) === 'admin');
$admin_path = $is_in_admin ? '' : '../admin/';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ' . $admin_path . 'index.php');
    exit;
}
include('../includes/db.php');
$base_url = '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Admin | UrbanRide</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* Admin Specific Overrides */
        body { background: var(--bg-sub); }
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
        .admin-nav .logo span { color: var(--primary); }
        .admin-links { display: flex; gap: 2rem; }
        .admin-links a { 
            color: #94A3B8; 
            font-weight: 500; 
            font-size: 0.9rem;
            transition: 0.3s;
        }
        .admin-links a:hover, .admin-links a.active { color: white; }
        
        .admin-content { padding: 40px 7%; min-height: 80vh; }
        
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
            width: 60px; height: 60px;
            border-radius: 15px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            background: var(--primary-light);
            color: var(--primary);
        }
        .stat-info h4 { font-size: 0.8rem; color: var(--text-sub); text-transform: uppercase; letter-spacing: 1px; }
        .stat-info b { font-size: 1.8rem; color: var(--text-main); font-family: var(--font-heading); }

        .table-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            overflow-x: auto;
        }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th { text-align: left; padding: 15px; border-bottom: 2px solid var(--bg-sub); color: var(--text-sub); font-size: 0.85rem; text-transform: uppercase; font-weight: 700; }
        td { padding: 20px 15px; border-bottom: 1px solid var(--bg-sub); color: var(--text-main); font-size: 0.95rem; }
        
        .badge {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .badge-pending { background: #FEF3C7; color: #92400E; }
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
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); display: none; justify-content: center; align-items: center;
            z-index: 2000; backdrop-filter: blur(5px);
        }
        .modal-content {
            background: white; padding: 40px; border-radius: 30px; width: 100%; max-width: 500px;
            box-shadow: var(--shadow-xl); border: 1px solid var(--border);
        }
        .modal-content h2 { margin-bottom: 25px; font-family: var(--font-heading); }
        .modal-content input {
            width: 100%; padding: 12px 15px; border-radius: 12px; border: 1.5px solid var(--border);
            margin-bottom: 15px; font-weight: 600;
        }

        @media (max-width: 768px) {
            .admin-links { display: none; }
            .admin-nav { padding: 1rem 5%; }
            .admin-content { padding: 20px 5%; }
        }
    </style>
</head>
<body>
    <nav class="admin-nav">
        <a href="<?php echo $admin_path; ?>dashboard.php" class="logo" style="color: white;">Urban<span>Ride</span> Admin</a>
        <div class="admin-links">
            <a href="<?php echo $admin_path; ?>dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
            <a href="<?php echo $admin_path; ?>manage-agencies.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage-agencies.php' ? 'active' : ''; ?>">Partners</a>
            <a href="<?php echo $admin_path; ?>view-bookings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'view-bookings.php' ? 'active' : ''; ?>">All Bookings</a>
            <a href="../Taxi-Booking/manage-cabs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage-cabs.php' ? 'active' : ''; ?>"><i class="fas fa-taxi" style="margin-right:5px;"></i>Manage Cabs</a>
            <a href="../logout.php" style="color: #F87171;"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </nav>
    <div class="admin-content">

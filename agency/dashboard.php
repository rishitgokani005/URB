<?php
include('header.php');

// Fetch cab fleet size & bookings for this agency
$total_cabs     = $conn->query("SELECT COUNT(*) FROM acab WHERE agency_id = '$agency_id'")->fetch_row()[0];
$total_cab_books= $conn->query("SELECT COUNT(*) FROM acabookings WHERE agency_id = '$agency_id'")->fetch_row()[0];
?>

<div class="section-title reveal">
    <span class="sub-heading">Welcome back</span>
    <h2><?php echo $agency_name; ?> Overview</h2>
</div>

<div class="dashboard-grid reveal">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-motorcycle"></i></div>
        <div class="stat-info">
            <h4>Bike Fleet</h4>
            <b><?php echo $conn->query("SELECT COUNT(*) FROM abike WHERE agency_id = '$agency_id'")->fetch_row()[0]; ?></b>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-info">
            <h4>Bike Bookings</h4>
            <b><?php echo $conn->query("SELECT COUNT(*) FROM abookings WHERE agency_id = '$agency_id'")->fetch_row()[0]; ?></b>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#EFF6FF; color:#2563EB;"><i class="fas fa-taxi"></i></div>
        <div class="stat-info">
            <h4>Cab Fleet</h4>
            <b><?php echo $total_cabs; ?></b>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#F0FDF4; color:#16A34A;"><i class="fas fa-route"></i></div>
        <div class="stat-info">
            <h4>Taxi Bookings</h4>
            <b><?php echo $total_cab_books; ?></b>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <h4>Active Rides</h4>
            <b><?php echo $conn->query("SELECT COUNT(*) FROM abookings WHERE agency_id = '$agency_id' AND booking_status = 'active'")->fetch_row()[0]; ?></b>
        </div>
    <?php
    $agency_info = $conn->query("SELECT has_pickup FROM agencies WHERE id = '$agency_id'")->fetch_assoc();
    $pickup_status = ($agency_info['has_pickup'] ?? 0) ? 'Available' : 'Disabled';
    $pickup_color = ($agency_info['has_pickup'] ?? 0) ? '#16A34A' : '#EF4444';
    $pickup_bg = ($agency_info['has_pickup'] ?? 0) ? '#F0FDF4' : '#FEF2F2';
    ?>
    <div class="stat-card" style="background: <?php echo $pickup_bg; ?>; border-color: <?php echo $pickup_color; ?>33;">
        <div class="stat-icon" style="background: white; color: <?php echo $pickup_color; ?>;"><i class="fas fa-truck-fast"></i></div>
        <div class="stat-info">
            <h4>Pick-up Service</h4>
            <b style="color: <?php echo $pickup_color; ?>;"><?php echo $pickup_status; ?></b>
        </div>
    </div>
</div>

<div class="table-card reveal">
    <div class="header-row" style="margin-bottom: 20px;">
        <h3 style="font-family: var(--font-heading); font-size: 1.5rem;">Recent Activity</h3>
        <a href="bookings.php" class="btn-signup" style="font-size: 0.8rem; padding: 8px 20px;">All Bookings</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Vehicle</th>
                <th>Customer</th>
                <th>Duration</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT b.*, a.model 
                    FROM abookings b
                    JOIN abike a ON b.bike_id = a.id
                    WHERE b.agency_id = '$agency_id'
                    ORDER BY b.booking_id DESC LIMIT 5";
            $res = $conn->query($sql);
            while($row = $res->fetch_assoc()):
            ?>
            <tr>
                <td><b>#<?php echo $row['booking_id']; ?></b></td>
                <td><?php echo $row['model']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo date('d M', strtotime($row['booking_date'])); ?> - <?php echo date('d M', strtotime($row['return_date'])); ?></td>
                <td><span class="badge badge-active">Confirmed</span></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('revealed'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php include('footer.php'); ?>

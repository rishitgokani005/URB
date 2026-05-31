<?php
include('header.php');

// Specific Agency Stats
$total_bikes = $conn->query("SELECT COUNT(*) FROM abike WHERE agency_id = '$agency_id'")->fetch_row()[0];
$total_bookings = $conn->query("SELECT COUNT(*) FROM abookings WHERE agency_id = '$agency_id'")->fetch_row()[0];
$active_bookings = $conn->query("SELECT COUNT(*) FROM abookings WHERE agency_id = '$agency_id' AND (booking_status = 'active' OR booking_status = 'confirmed')")->fetch_row()[0];
?>

<div class="section-title reveal">
    <span class="sub-heading">Welcome back, Super Admin</span>
    <h2><?php echo $agency_name; ?> Overview</h2>
</div>

<div class="dashboard-grid reveal">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-motorcycle"></i></div>
        <div class="stat-info">
            <h4>Fleet Size</h4>
            <b><?php echo $total_bikes; ?></b>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-info">
            <h4>All Bookings</h4>
            <b><?php echo $total_bookings; ?></b>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <h4>Current Rides</h4>
            <b><?php echo $active_bookings; ?></b>
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
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('revealed');
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php include('footer.php'); ?>

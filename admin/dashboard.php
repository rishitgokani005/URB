<?php
include('header.php');

// Fetch Stats
$total_agencies = $conn->query("SELECT COUNT(*) FROM agencies")->fetch_row()[0];
$total_bookings = $conn->query("SELECT COUNT(*) FROM abookings")->fetch_row()[0];
$total_cities = $conn->query("SELECT COUNT(DISTINCT city) FROM agencies")->fetch_row()[0];
?>

<div class="section-title reveal">
    <span class="sub-heading">Live Statistics</span>
    <h2>System Overview</h2>
</div>

<div class="dashboard-grid reveal">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-store"></i></div>
        <div class="stat-info">
            <h4>Total Partners</h4>
            <b><?php echo $total_agencies; ?></b>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-info">
            <h4>Total Bookings</h4>
            <b><?php echo $total_bookings; ?></b>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-city"></i></div>
        <div class="stat-info">
            <h4>Cities Active</h4>
            <b><?php echo $total_cities; ?></b>
        </div>
    </div>
</div>

<div class="table-card reveal">
    <div class="header-row" style="margin-bottom: 20px;">
        <h3 style="font-family: var(--font-heading); font-size: 1.5rem;">Recent Partnership Requests</h3>
        <a href="manage-agencies.php" class="btn-signup" style="font-size: 0.8rem; padding: 8px 20px;">Manage All</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Partner Name</th>
                <th>City Location</th>
                <th>Registration Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = $conn->query("SELECT * FROM agencies ORDER BY created_at DESC LIMIT 5");
            while($row = $res->fetch_assoc()) {
                echo "<tr>
                    <td><b>#{$row['id']}</b></td>
                    <td>{$row['name']}</td>
                    <td>{$row['city']}</td>
                    <td>" . date('d M Y', strtotime($row['created_at'])) . "</td>
                </tr>";
            }
            ?>
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

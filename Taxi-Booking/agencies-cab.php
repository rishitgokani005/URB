<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

$pickup_city = isset($_GET['pickup_city']) ? mysqli_real_escape_string($conn, $_GET['pickup_city']) : '';
$pickup_location = isset($_GET['pickup_location']) ? $_GET['pickup_location'] : '';
$drop_location = isset($_GET['drop_location']) ? $_GET['drop_location'] : '';
$trip_type = isset($_GET['trip_type']) ? $_GET['trip_type'] : 'oneway';
$pickup_date = isset($_GET['pickup_date']) ? $_GET['pickup_date'] : '';
$return_date = isset($_GET['return_date']) ? $_GET['return_date'] : '';
$pickup_time = isset($_GET['pickup_time']) ? $_GET['pickup_time'] : '';
$est_distance = isset($_GET['est_distance']) ? intval($_GET['est_distance']) : 100;

// Query agencies that have cabs in the chosen city
$query = "SELECT a.id, a.name, a.city, 
          MIN(c.price_per_km) as min_price, 
          GROUP_CONCAT(DISTINCT c.cab_name SEPARATOR ', ') as cab_variety 
          FROM agencies a
          JOIN acab c ON a.id = c.agency_id
          WHERE c.city = '$pickup_city'
          GROUP BY a.id";
$result = $conn->query($query);

// Keep all parameters to forward to taxi-booking.php
$query_params = http_build_query([
    'pickup_city' => $pickup_city,
    'pickup_location' => $pickup_location,
    'drop_location' => $drop_location,
    'trip_type' => $trip_type,
    'pickup_date' => $pickup_date,
    'return_date' => $return_date,
    'pickup_time' => $pickup_time,
    'est_distance' => $est_distance,
    'search_submitted' => 1
]);

require '../includes/header.php';
?>

<link rel="stylesheet" href="../css/style.css">

<section class="agency-section" style="background: var(--bg-sub); min-height: 90vh; padding: 120px 7% 80px;">
    <div class="section-title reveal">
        <span class="sub-heading">Available in <?php echo htmlspecialchars($pickup_city); ?></span>
        <h2>Choose Your Cab Partner</h2>
        <?php if ($pickup_date): ?>
            <p style="margin-top: 10px; color: var(--text-sub);">
                <i class="fas fa-calendar-days" style="margin-right:8px;color:var(--primary);"></i>
                Schedule: <?php echo date('D, j M', strtotime($pickup_date)); ?> at <?php echo htmlspecialchars($pickup_time); ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="fleet-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="agency-card reveal">
                    <div>
                        <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                        <div class="price-range">
                            <?php echo $row['min_price'] ? 'Starting from ₹' . htmlspecialchars($row['min_price']) . ' / km' : 'No cabs available'; ?>
                        </div>
                        <div class="variety">
                            <i class="fas fa-taxi" style="margin-right: 8px; color: var(--primary);"></i>
                            <?php 
                                if ($row['cab_variety']) {
                                    $variety = explode(',', $row['cab_variety']);
                                    echo count($variety) . ' Cabs Available: ' . htmlspecialchars($row['cab_variety']);
                                } else {
                                    echo 'Check back later for availability';
                                }
                            ?>
                        </div>
                    </div>
                    <a href="taxi-booking.php?agency_id=<?php echo urlencode($row['id']); ?>&<?php echo $query_params; ?>" class="btn-signup" style="text-align: center; border-radius: 15px; font-size: 0.95rem;">Select This Agency</a>
                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <div class="register-card reveal" style="grid-column: 1/-1; margin: 0 auto; text-align: center;">
                <div style="font-size: 4rem; color: var(--primary); margin-bottom: 20px;"><i class="fas fa-search-location"></i></div>
                <h2 style="font-size: 1.5rem;">No Agencies Found</h2>
                <p style="color: var(--text-sub); margin-bottom: 30px;">We couldn't find any partners in <b><?php echo htmlspecialchars($pickup_city); ?></b> with cabs available.</p>
                <a href="../index.php" class="btn-signup" style="display: inline-block;">Try Another Location</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('revealed');
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php require '../includes/footer.php'; ?>

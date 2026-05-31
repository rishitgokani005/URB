<?php
session_start();
include('includes/db.php');
require 'includes/header.php';

$city = isset($_GET['city']) ? mysqli_real_escape_string($conn, $_GET['city']) : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

if ($start_date && $end_date && strtotime($start_date) > strtotime($end_date)) {
    echo "<div style='padding: 150px 7%; text-align: center; background: var(--bg-sub); min-height: 80vh;'>
            <div class='register-card reveal' style='margin: 0 auto;'>
                <h2 style='color: #DC2626;'>Invalid Date Range</h2>
                <p style='color: var(--text-sub); margin-bottom: 30px;'>Return date cannot be before the pick-up date.</p>
                <a href='index.php' class='btn-signup' style='display: inline-block;'>Go Back to Search</a>
            </div>
          </div>";
    require 'includes/footer.php';
    exit;
}

$_SESSION['booking_start'] = $start_date;
$_SESSION['booking_end'] = $end_date;
$_SESSION['booking_city'] = $city;

$query = "SELECT a.id, a.name, a.city, 
          MIN(b.price_per_day) as min_price, 
          GROUP_CONCAT(DISTINCT b.model SEPARATOR ', ') as bike_variety 
          FROM agencies a
          LEFT JOIN abike b ON a.id = b.agency_id
          WHERE a.city = '$city'
          GROUP BY a.id";
$result = $conn->query($query);
?>

<link rel="stylesheet" href="css/style.css">

<section class="agency-section" style="background: var(--bg-sub); min-height: 90vh; padding: 120px 7% 80px;">
    <div class="section-title reveal">
        <span class="sub-heading">Available in <?php echo htmlspecialchars($city); ?></span>
        <h2>Choose Your Rental Partner</h2>
        <?php if ($start_date && $end_date): ?>
            <p style="margin-top: 10px; color: var(--text-sub);">
    <i class="fas fa-calendar-days"
    style="margin-right:8px;color:var(--primary);"></i>Book from <?php echo date('D, j M', strtotime($start_date)); ?> to <?php echo date('D, j M', strtotime($end_date)); ?></p>
        <?php endif; ?>
    </div>

    <div class="fleet-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="agency-card reveal">
                    <div>
                        <h2><?php echo htmlspecialchars($row['name']); ?></h2>
                        <div class="price-range">
                            <?php echo $row['min_price'] ? 'Starting from ₹' . htmlspecialchars($row['min_price']) . ' / day' : 'No bikes available'; ?>
                        </div>
                        <div class="variety">
                            <i class="fas fa-motorcycle" style="margin-right: 8px; color: var(--primary);"></i>
                            <?php 
                                if ($row['bike_variety']) {
                                    $variety = explode(',', $row['bike_variety']);
                                    echo count($variety) . ' Models Available: ' . htmlspecialchars($row['bike_variety']);
                                } else {
                                    echo 'Check back later for availability';
                                }
                            ?>
                        </div>
                    </div>
                    <a href="agency-bikes.php?agency_id=<?php echo urlencode($row['id']); ?>" class="btn-signup" style="text-align: center; border-radius: 15px; font-size: 0.95rem;">Select This Agency</a>
                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <div class="register-card reveal" style="grid-column: 1/-1; margin: 0 auto; text-align: center;">
                <div style="font-size: 4rem; color: var(--primary); margin-bottom: 20px;"><i class="fas fa-search-location"></i></div>
                <h2 style="font-size: 1.5rem;">No Agencies Found</h2>
                <p style="color: var(--text-sub); margin-bottom: 30px;">We couldn't find any partners in <b><?php echo htmlspecialchars($city); ?></b> for your selected criteria.</p>
                <a href="index.php" class="btn-signup" style="display: inline-block;">Try Another Location</a>
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

<?php require 'includes/footer.php'; ?>
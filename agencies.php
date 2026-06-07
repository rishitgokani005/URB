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

$query = "SELECT a.id, a.name, a.city, a.has_pickup, 
          MIN(b.price_per_day) as min_price, 
          GROUP_CONCAT(DISTINCT b.model SEPARATOR ', ') as bike_variety 
          FROM agencies a
          LEFT JOIN abike b ON a.id = b.agency_id
          WHERE a.city = '$city'
          GROUP BY a.id";
$result = $conn->query($query);
?>

<link rel="stylesheet" href="css/style.css">
<style>
/* Agency card: stretch to fill, push badge to bottom */
.agency-card {
    background: white;
    border-radius: 24px;
    padding: 30px;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border);
    transition: 0.4s;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
    padding-bottom: 0 !important;
    --card-padding: 30px;
}

.agency-card h2 {
    font-size: 1.6rem;
    color: var(--accent);
    margin-bottom: 10px;
}

.agency-card .price-range {
    color: var(--primary);
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 15px;
}

/* Remove bottom margin/padding from the inner wrapper div */
.agency-card > div:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
}

/* Pick-up badge flush to bottom, full width */
.pickup-badge {
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-align: center;
    color: var(--primary);
    font-size: 0.75rem;
    font-weight: 700;
    background: var(--primary-light);
    padding: 6px 10px;
    border-radius: 0;
    margin-top: 24px;
    margin-bottom: 0;
    width: calc(100% + var(--card-padding) * 2);
    margin-left: calc(-1 * var(--card-padding));

    /* Glass effect */
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-bottom: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
}

/* Shine sweep */
.pickup-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -60%;
    width: 50%;
    height: 100%;
    background: linear-gradient(
        120deg,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0.45) 50%,
        rgba(255, 255, 255, 0) 100%
    );
    border-radius: inherit;
    pointer-events: none;
    animation: badge-shine 3s ease-in-out infinite;
}

@keyframes badge-shine {
    0%   { left: -60%; }
    40%  { left: 110%; }
    100% { left: 110%; }
}

@media (max-width: 768px) {
    .agency-card {
        --card-padding: 30px; /* keep same unless you reduce padding on mobile */
    }

    .pickup-badge {
        font-size: 0.7rem;
        padding: 5px 8px;
        margin-top: 16px;
    }
}

@media (max-width: 480px) {
    .agency-card {
        --card-padding: 30px; /* keep same unless you reduce padding on mobile */
    }

    .pickup-badge {
        font-size: 0.68rem;
        padding: 5px 6px;
        gap: 4px;
    }
}
</style>

<section class="agency-section" style="background: var(--bg-sub); min-height: 90vh; padding: 120px 7% 80px;">
    <div class="section-title reveal">
        <span class="sub-heading">Available in <?php echo htmlspecialchars($city); ?></span>
        <h2>Choose Your Rental Partner</h2>
        <?php if ($start_date && $end_date): ?>
            <p style="margin-top: 10px; color: var(--text-sub);">
                <i class="fas fa-calendar-days" style="margin-right:8px;color:var(--primary);"></i>
                Book from <?php echo date('D, j M', strtotime($start_date)); ?> to <?php echo date('D, j M', strtotime($end_date)); ?>
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
                    <div>
                        <a href="agency-bikes.php?agency_id=<?php echo urlencode($row['id']); ?>" class="btn-signup" style="text-align: center; border-radius: 15px; font-size: 0.95rem; width: 100%; display: block; margin-bottom: 8px;">Select This Agency</a>
                        <?php if ($row['has_pickup']): ?>
                            <div class="pickup-badge">
                                <i class="fas fa-location-dot"></i> Pick-up Service Available
                            </div>
                        <?php endif; ?>
                    </div>
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
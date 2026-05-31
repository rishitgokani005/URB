<?php
session_start();
include('includes/db.php');
require 'includes/header.php';

$agency_id = isset($_GET['agency_id']) ? mysqli_real_escape_string($conn, $_GET['agency_id']) : '';

// Fetch agency details
$agency_query = "SELECT * FROM agencies WHERE id = '$agency_id'";
$agency_res = $conn->query($agency_query);
$agency = $agency_res->fetch_assoc();

if (!$agency) {
    header("Location: agencies.php");
    exit;
}

// Store agency info in session for booking
$_SESSION['current_agency_id'] = $agency_id;

// Fetch bikes for this agency
$start_date = $_SESSION['booking_start'] ?? '';
$end_date = $_SESSION['booking_end'] ?? '';

$query = "SELECT * FROM abike 
          WHERE agency_id = '$agency_id' 
          AND id NOT IN (
              SELECT bike_id FROM abookings 
              WHERE (booking_status = 'active' OR (booking_status = 'pending' AND created_at > NOW() - INTERVAL 15 MINUTE))
              AND NOT (return_date < '$start_date' OR booking_date > '$end_date')
          )";
$result = $conn->query($query);
$is_logged_in = isset($_SESSION['loggedin']);
$compulsory_login = true;

// Fetch total bookings for "happy customers"
$booking_count_query = "SELECT COUNT(*) as total_bookings FROM abookings WHERE agency_id = '$agency_id'";
$booking_count_res = $conn->query($booking_count_query);
$happy_customers = $booking_count_res->fetch_assoc()['total_bookings'] ?? 0;
?>

<link rel="stylesheet" href="css/style.css">
<style>
    .bikes-section {
        padding: 10rem 5% 5rem;
        background: #fdfdfd;
        min-height: 80vh;
    }

    .bike-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2.5rem;
        margin-top: 3rem;
        justify-content: center;
    }

    .bike-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        transition: all 0.4s;
        border: 1px solid rgba(0, 0, 0, 0.03);
        display: flex;
        flex-direction: column;
    }

    .image-slider {
        position: relative;
        height: 200px;
        overflow: hidden;
        background: #eee;
        cursor: pointer;
    }

    .slider-container {
        display: flex;
        transition: transform 0.5s ease-in-out;
        height: 100%;
    }

    .slider-item {
        min-width: 100%;
        height: 100%;
    }

    .slider-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .slider-dots {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 6px;
        z-index: 5;
    }

    .dot {
        width: 8px;
        height: 8px;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
    }

    .dot.active {
        background: var(--primary);
        width: 20px;
        border-radius: 4px;
    }

    .bike-content {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .bike-name {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .bike-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .rent-tag {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--primary);
    }

    .book-btn {
        width: 100%;
        padding: 1rem;
        background: var(--dark);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        text-align: center;
        text-decoration: none;
        margin-top: auto;
    }

    .bike-card:hover .book-btn {
        background: var(--primary);
    }

    .deposit-tag {
        font-size: 0.85rem;
        color: #888;
        margin-top: 4px;
    }
    
    .agency-profile-banner {
    width: 80%;
    max-width: 1000px;
    aspect-ratio: 8 / 1; /* Width = 2x Height */
    margin: -3rem auto 3rem;
    position: relative;

    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 0 60px;

    border-radius: 32px;

    background: rgba(255, 255, 255, 0.18);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);

    border: 1px solid rgba(255,255,255,0.35);

    box-shadow:
        0 10px 40px rgba(0,0,0,0.08),
        inset 0 1px 1px rgba(255,255,255,0.4);

    overflow: hidden;
}

/* Glass reflection animation */
.agency-profile-banner::before {
    content: "";
    position: absolute;
    top: -150%;
    left: -50%;
    width: 70%;
    height: 400%;

    background: linear-gradient(
        90deg,
        transparent,
        rgba(36, 34, 34, 0.15),
        transparent
    );

    transform: rotate(25deg);
    animation: glassShine 6s linear infinite;
}

@keyframes glassShine {
    from {
        left: -120%;
    }
    to {
        left: 220%;
    }
}

.agency-info-main {
    position: relative;
    z-index: 2;
    text-align: left;
}

.agency-info-main h1 {
    margin: 0;
    font-size: 3rem;
    font-weight: 800;
    color: var(--accent);
    line-height: 1.1;
}

.agency-meta {
    margin-top: 14px;
    font-size: 1.1rem;
    color: #555;
    font-weight: 500;
    display: block;
}

.agency-meta span {
    background: none;
    border: none;
    padding: 0;
    display: inline;
}

/* Tablet */
@media (max-width: 768px) {
    .agency-profile-banner {
        width: 90%;
        padding: 0 30px;
        aspect-ratio: auto;
        min-height: 220px;
    }

    .agency-info-main h1 {
        font-size: 2.2rem;
    }

    .agency-meta {
        font-size: 0.95rem;
        line-height: 1.8;
    }
}

/* Mobile */
@media (max-width: 576px) {
    .agency-profile-banner {
        width: 100%;
        padding: 15px;
        min-height: 120px;
    }

    .agency-info-main h1 {
        font-size: 1.8rem;
    }

    .agency-meta {
        font-size: 0.9rem;
    }
}


.btn-rent {
    background: var(--accent);
    color: white !important;
    padding: 10px 20px;
    border-radius: 50px;
    font-weight: 600;
    box-shadow: var(--shadow-md);
    font-size: 1rem;
    transition: 0.3s;
    border: none;
    cursor: pointer;
    display: inline-block;
    
}

.btn-rent:hover {
    transform: scale(1.05);
    box-shadow: var(--shadow-lg);
}
</style>

<?php if (!$is_logged_in): ?>
    <?php require 'includes/login_modal.php'; ?>
    <script>
        window.onload = function() {
            showLoginModal();
        }
    </script>
    <div class="compulsory-login-overlay" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; background: #fdfdfd;">
        <div style="text-align: center;">
            <i class="fas fa-lock" style="font-size: 4rem; color: var(--primary); margin-bottom: 2rem; opacity: 0.3;"></i>
            <h2 style="font-family: var(--font-heading); color: #333;">Please login to view available bikes</h2>
            <p style="color: #666; margin-top: 1rem;">Login or create an account to start booking your ride.</p>
            <button onclick="showLoginModal()" class="btn-primary" style="margin-top: 2rem; width: auto; padding: 1rem 3rem;">Login Now</button>
        </div>
    </div>
<?php else: ?>
<section class="bikes-section">
<div class="agency-profile-banner reveal">

    <div class="agency-info-main">

        <h1><?php echo htmlspecialchars($agency['name']); ?></h1>

  <div class="agency-meta">
            <?php echo htmlspecialchars($agency['city']); ?>
            |
            <?php echo $result->num_rows; ?> Vehicles Available
            |
            <?php echo $happy_customers; ?> Happy Customers
            |
            Verified Partner
        </div>
    </div>
</div>

    <div class="bike-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()):
                $images = array_filter([$row['image'], $row['image2'], $row['image3'], $row['image4']]);
                $bike_id = htmlspecialchars($row['id']);
                ?>
                <div class="bike-card no-hover-card" id="bike-<?php echo $bike_id; ?>">
                    <div class="image-slider" onclick="nextSlide('<?php echo $bike_id; ?>')">
                        <div class="slider-container" id="container-<?php echo $bike_id; ?>">
                            <?php foreach ($images as $img): ?>
                                <div class="slider-item"><img src="uploads/<?php echo htmlspecialchars($img); ?>"></div>
                            <?php endforeach; ?>
                        </div>
                        <div class="slider-dots" id="dots-<?php echo $bike_id; ?>">
                            <?php foreach ($images as $i => $img): ?>
                                <div class="dot <?php echo $i === 0 ? 'active' : ''; ?>"></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="bike-content">
                        <h3 class="bike-name"><?php echo htmlspecialchars($row['model']); ?></h3>
                        <div class="bike-meta">
                            <div>
                                <span>Color: <?php echo ucfirst($row['color']); ?></span>
                                <div class="deposit-tag">Deposit: ₹<?php echo $row['deposite']; ?></div>
                            </div>
                            <div class="rent-tag">₹<?php echo $row['price_per_day']; ?> <span>/ day</span></div>
                        </div>
                        <a href="booking-details.php?bike_id=<?php echo $bike_id; ?>" class="btn-rent" style="width: 100%; text-align: center; border-radius: 12px;">Rent This Bike</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="grid-column: 1/-1; text-align: center; padding: 3rem;">No bikes currently available from this agency.
            </p>
        <?php endif; ?>
    </div>
</section>

<script>
    const sliders = {};
    function nextSlide(id) {
        if (!sliders[id]) sliders[id] = 0;
        const container = document.getElementById('container-' + id);
        const dots = document.getElementById('dots-' + id).children;
        const count = container.children.length;
        sliders[id] = (sliders[id] + 1) % count;
        container.style.transform = `translateX(-${sliders[id] * 100}%)`;
        Array.from(dots).forEach((dot, i) => dot.classList.toggle('active', i === sliders[id]));
    }
</script>

<?php endif; ?>
<?php require 'includes/footer.php'; ?>
<?php
session_start();
require 'includes/header.php';
include 'includes/db.php';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$cards = $conn->query("SELECT * FROM abike WHERE status = 1 AND `id` like 'GJ%'");
?>

<link rel="stylesheet" href="css/bike.css">

<div class="bike-card-container">
    <?php while ($row = $cards->fetch_assoc()): ?>
        <div class="bike-card">
            <div class="image-container">
                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Bike Image">
            </div>
            <div class="content">
                <div class="bike-id"><?php echo htmlspecialchars($row['id']); ?></div>
                <h2><?php echo htmlspecialchars($row['model']); ?></h2>
                <div class="details">
                    <div class="detail-item">
                        <span>Color</span>
                        <span><?php echo htmlspecialchars($row['color']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span>Deposit</span>
                        <span>₹<?php echo htmlspecialchars($row['deposite']); ?></span>
                    </div>
                </div>
                <div class="price-tag">₹<?php echo htmlspecialchars($row['price_per_day']); ?> <span>/ day</span></div>
                <?php echo '<a href="admin/abookings.php?id=' . htmlspecialchars($row["id"]) . '&csrf_token=' . htmlspecialchars($_SESSION['csrf_token']) . '" class="book-btn">Book Now</a>';?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script>
    document.querySelectorAll('.bike-card img').forEach(img => {
        img.addEventListener('click', function(e) {
            const card = this.closest('.bike-card');
            const allCards = document.querySelectorAll('.bike-card');
            allCards.forEach(c => { if(c !== card) c.classList.remove('active'); });
            card.classList.toggle('active');
            e.stopPropagation();
        });
    });

    document.addEventListener('click', function(e) {
        if(!e.target.closest('.bike-card')) {
            document.querySelectorAll('.bike-card').forEach(c => { c.classList.remove('active'); });
        }
    });
</script>

<?php require 'includes/footer.php'?>
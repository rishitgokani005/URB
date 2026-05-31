<?php
include('header.php');
?>

<div class="header-row reveal" style="margin-bottom: 30px;">
    <h2 style="font-family: var(--font-heading); font-size: 2rem;">Customer Bookings</h2>
    <p style="color: var(--text-sub);">Manage and track your recent vehicle rentals</p>
</div>

<div class="booking-grid reveal">
    <?php
    $sql = "SELECT b.*, a.model, a.image, a.color 
            FROM abookings b
            JOIN abike a ON b.bike_id = a.id
            WHERE b.agency_id = '$agency_id' AND b.booking_status != 'cancelled'
            ORDER BY b.booking_date ASC, b.pick_up_time ASC";
    $res = $conn->query($sql);
    if($res->num_rows > 0):
        while($row = $res->fetch_assoc()):
            $is_offline = ($row['name'] === 'OFFLINE BOOKING');
            
            // Precise Status Logic using DateTime
            $current_time = new DateTime();
            $start_time = new DateTime($row['booking_date'] . ' ' . $row['pick_up_time']);
            $end_time = new DateTime($row['return_date'] . ' ' . $row['drop_off_time']);
            
            $status = 'Completed';
            $status_class = 'badge-completed';
            
            if ($row['booking_status'] === 'cancelled') {
                $status = 'Cancelled';
                $status_class = 'badge-cancelled';
            } else {
                if ($current_time < $start_time) {
                    $status = 'Pending';
                    $status_class = 'badge-pending';
                } elseif ($current_time >= $start_time && $current_time <= $end_time) {
                    $status = 'Ongoing';
                    $status_class = 'badge-active';
                } else {
                    $status = 'Completed';
                    $status_class = 'badge-completed';
                }
            }

            // FILTER: Only show Pending and Ongoing in this page
            if ($status === 'Completed' || $status === 'Cancelled') continue;
    ?>
    <div class="booking-card">
        <div class="booking-bike-info">
            <h4 style="font-size: 1.1rem;"><?php echo htmlspecialchars($row['model']); ?></h4>
            <span style="font-size: 0.75rem; color: var(--text-sub);">Color: <?php echo ucfirst($row['color']); ?></span>
        </div>
        <div class="booking-main-details">
            <h3 style="<?php echo $is_offline ? 'color: var(--primary);' : ''; ?>">
                <?php echo htmlspecialchars($row['name']); ?>
                <span class="<?php echo $status_class; ?>" style="font-size: 0.6rem; vertical-align: middle; margin-left: 10px;"><?php echo $status; ?></span>
            </h3>
            <p><i class="fas fa-hashtag"></i> ID: <?php echo $row['booking_id']; ?></p>
            <p><i class="fas fa-phone"></i> <?php echo $row['mobile']; ?></p>
            <p><i class="fas fa-clock"></i> Pickup: <?php echo date('h:i A', strtotime($row['pick_up_time'])); ?></p>
            <p><i class="fas fa-receipt"></i> Amount: <b>₹<?php echo number_format($row['total_price'], 2); ?></b></p>
        </div>
        <div class="booking-date-highlight">
            <span>Rental Period</span>
            <b><?php echo date('d M', strtotime($row['booking_date'])); ?> - <?php echo date('d M', strtotime($row['return_date'])); ?></b>
        </div>
    </div>
    <?php 
        endwhile;
    else:
    ?>
        <div style="text-align: center; padding: 100px 0;">
            <i class="fas fa-calendar-xmark" style="font-size: 4rem; color: var(--border); margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-sub);">No bookings found yet.</h3>
        </div>
    <?php endif; ?>
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

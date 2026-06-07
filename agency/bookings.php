<?php
include('header.php');
?>

<style>
    .tab-bar { display:flex; gap:0; margin-bottom:30px; border-bottom:2px solid var(--border); }
    .tab-btn {
        padding:12px 28px; border:none; background:none; font-family:'Outfit',sans-serif;
        font-size:0.95rem; font-weight:700; color:var(--text-sub);
        border-bottom:3px solid transparent; margin-bottom:-2px;
        cursor:pointer; transition:0.2s; display:flex; align-items:center; gap:8px;
    }
    .tab-btn.active { color:var(--primary); border-bottom-color:var(--primary); }
    .tab-section { display:none; }
    .tab-section.active { display:block; }
    
    .booking-card.is-cancelled {
        background: #FEE2E2 !important; /* Light red background */
        border-color: #FECACA !important;
    }
    .booking-card.is-cancelled .booking-main-details h3,
    .booking-card.is-cancelled .booking-main-details p,
    .booking-card.is-cancelled .booking-date-highlight b {
        color: #991B1B !important;
    }
</style>

<div class="header-row reveal" style="margin-bottom: 30px;">
    <h2 style="font-family: var(--font-heading); font-size: 2rem;">Customer Bookings</h2>
    <p style="color: var(--text-sub);">Manage and track all your bike and taxi bookings</p>
</div>

<!-- Tab Bar -->
<div class="tab-bar">
    <button class="tab-btn active" onclick="showTab('bikes', this)">
        <i class="fas fa-motorcycle"></i> Bike Bookings
    </button>
    <button class="tab-btn" onclick="showTab('cabs', this)">
        <i class="fas fa-taxi"></i> Taxi Bookings
    </button>
</div>

<!-- ===== BIKE BOOKINGS ===== -->
<div id="tab-bikes" class="tab-section active">
    <div class="booking-grid reveal">
        <?php
        $sql = "SELECT b.*, a.model, a.image, a.color 
                FROM abookings b
                JOIN abike a ON b.bike_id = a.id
                WHERE b.agency_id = '$agency_id'
                ORDER BY b.booking_id DESC";
        $res = $conn->query($sql);
        $found = false;
        if($res->num_rows > 0):
            while($row = $res->fetch_assoc()):
                $is_offline = ($row['name'] === 'OFFLINE BOOKING');
                $is_cancelled = ($row['booking_status'] === 'cancelled');
                
                $current_time = new DateTime();
                $start_time   = new DateTime($row['booking_date'] . ' ' . $row['pick_up_time']);
                $end_time     = new DateTime($row['return_date']  . ' ' . $row['drop_off_time']);
                
                $status       = 'Completed';
                $status_class = 'badge-completed';
                
                if ($is_cancelled) {
                    $status = 'Cancelled'; $status_class = 'badge-cancelled';
                } elseif ($current_time < $start_time) {
                    $status = 'Pending';   $status_class = 'badge-pending';
                } elseif ($current_time <= $end_time) {
                    $status = 'Ongoing';   $status_class = 'badge-active';
                }
                
                // If completed, only show in history page (optional but keeps standard)
                if ($status === 'Completed' && !$is_cancelled) continue;
                
                $found = true;
        ?>
        <div class="booking-card <?php echo $is_cancelled ? 'is-cancelled' : ''; ?>">
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
                <?php if (!empty($row['is_pickup'])): ?>
                    <p style="color: var(--primary);"><i class="fas fa-truck"></i> Pick-up: <?php echo htmlspecialchars($row['pickup_address']); ?></p>
                <?php endif; ?>
                <p><i class="fas fa-clock"></i> Pickup: <?php echo date('h:i A', strtotime($row['pick_up_time'])); ?></p>
                <p><i class="fas fa-receipt"></i> Amount: <b>₹<?php echo number_format($row['total_price'], 2); ?></b></p>
            </div>
            <div class="booking-date-highlight">
                <span>Rental Period</span>
                <b><?php echo date('d M', strtotime($row['booking_date'])); ?> - <?php echo date('d M', strtotime($row['return_date'])); ?></b>
            </div>
        </div>
        <?php endwhile; endif; ?>
        <?php if (!$found): ?>
        <div style="text-align:center; padding:80px 0; background:white; border-radius:24px;">
            <i class="fas fa-motorcycle" style="font-size:4rem; color:var(--border); margin-bottom:20px;"></i>
            <h3 style="color:var(--text-sub);">No active bike bookings.</h3>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===== CAB / TAXI BOOKINGS ===== -->
<div id="tab-cabs" class="tab-section">
    <div class="booking-grid reveal">
        <?php
        $cab_sql = "SELECT cb.*, c.cab_name, c.seats, c.image2 as ac_status
                    FROM acabookings cb
                    JOIN acab c ON cb.cab_id = c.id
                    WHERE cb.agency_id = '$agency_id'
                    ORDER BY cb.booking_id DESC";
        $cres = $conn->query($cab_sql);
        $cab_found = false;
        if($cres && $cres->num_rows > 0):
            while($crow = $cres->fetch_assoc()):
                $is_c_cancelled = ($crow['booking_status'] === 'cancelled');
                $current_time = new DateTime();
                $cstart = new DateTime($crow['booking_date'] . ' ' . $crow['pick_up_time']);
                $cstatus = 'Pending';
                $cstatus_class = 'badge-pending';
                if ($is_c_cancelled) {
                    $cstatus = 'Cancelled'; $cstatus_class = 'badge-cancelled';
                } elseif ($current_time >= $cstart) {
                    $cstatus = 'Ongoing'; $cstatus_class = 'badge-active';
                }
                $cab_found = true;
        ?>
        <div class="booking-card <?php echo $is_c_cancelled ? 'is-cancelled' : ''; ?>">
            <div class="booking-bike-info">
                <h4 style="font-size:1.1rem;"><?php echo htmlspecialchars($crow['cab_name']); ?></h4>
                <span style="font-size:0.75rem; color:var(--text-sub);"><?php echo $crow['seats']; ?> Seater · <?php echo htmlspecialchars($crow['ac_status'] ?? 'AC'); ?></span>
            </div>
            <div class="booking-main-details">
                <h3>
                    <?php echo htmlspecialchars($crow['name']); ?>
                    <span class="<?php echo $cstatus_class; ?>" style="font-size:0.6rem; vertical-align:middle; margin-left:10px;"><?php echo $cstatus; ?></span>
                </h3>
                <p><i class="fas fa-hashtag"></i> ID: <?php echo $crow['booking_id']; ?></p>
                <p><i class="fas fa-phone"></i> <?php echo $crow['mobile']; ?></p>
                <p><i class="fas fa-location-dot"></i> <?php echo htmlspecialchars($crow['pickup_location']); ?> → <?php echo htmlspecialchars($crow['drop_location']); ?></p>
                <p><i class="fas fa-clock"></i> Pickup: <?php echo date('h:i A', strtotime($crow['pick_up_time'])); ?></p>
                <p><i class="fas fa-receipt"></i> Amount: <b>₹<?php echo number_format($crow['total_price'], 2); ?></b></p>
            </div>
            <div class="booking-date-highlight">
                <span>Trip Date</span>
                <b><?php echo date('d M Y', strtotime($crow['booking_date'])); ?></b>
            </div>
        </div>
        <?php endwhile; endif; ?>
        <?php if (!$cab_found): ?>
        <div style="text-align:center; padding:80px 0; background:white; border-radius:24px;">
            <i class="fas fa-taxi" style="font-size:4rem; color:var(--border); margin-bottom:20px;"></i>
            <h3 style="color:var(--text-sub);">No active taxi bookings.</h3>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function showTab(name, btn) {
        document.querySelectorAll('.tab-section').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('revealed'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php include('footer.php'); ?>

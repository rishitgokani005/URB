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
    .tab-btn:hover { color:var(--text-main); }
    .tab-section { display:none; }
    .tab-section.active { display:block; }
    .badge-active   { background:#DCFCE7; color:#166534; padding:5px 12px; border-radius:50px; font-size:0.75rem; font-weight:700; }
    .badge-cancelled{ background:#FEE2E2; color:#991B1B; padding:5px 12px; border-radius:50px; font-size:0.75rem; font-weight:700; }
    .badge-pending  { background:#FEF3C7; color:#92400E; padding:5px 12px; border-radius:50px; font-size:0.75rem; font-weight:700; }
</style>

<div class="header-row reveal" style="margin-bottom:30px;">
    <div>
        <h2 style="font-family:var(--font-heading); font-size:2rem;">All Bookings</h2>
        <p style="color:var(--text-sub);">Monitor all bike and taxi ride bookings across the platform</p>
    </div>
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

<!-- ===== BIKE BOOKINGS TAB ===== -->
<div id="tab-bikes" class="tab-section active">
    <div class="table-card reveal">
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User</th>
                    <th>Bike</th>
                    <th>Agency</th>
                    <th>Dates</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT b.*, u.name as user_name, a.model as bike_name, ag.name as agency_name 
                        FROM abookings b
                        LEFT JOIN users u ON b.user_id = u.user_id
                        LEFT JOIN abike a ON b.bike_id = a.id
                        LEFT JOIN agencies ag ON b.agency_id = ag.id
                        ORDER BY b.booking_date DESC";
                $res = $conn->query($sql);
                if ($res && $res->num_rows > 0):
                    while ($row = $res->fetch_assoc()):
                        $status_class = match($row['booking_status']) {
                            'active'    => 'badge-active',
                            'cancelled' => 'badge-cancelled',
                            default     => 'badge-pending'
                        };
                ?>
                <tr>
                    <td><code style="background:#F1F5F9; padding:3px 8px; border-radius:6px; font-size:0.8rem;"><?php echo $row['booking_id']; ?></code></td>
                    <td><b><?php echo htmlspecialchars($row['user_name'] ?? '—'); ?></b></td>
                    <td><?php echo htmlspecialchars($row['bike_name'] ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($row['agency_name'] ?? '—'); ?></td>
                    <td style="font-size:0.85rem;">
                        <div><?php echo date('d M Y', strtotime($row['booking_date'])); ?></div>
                        <div style="color:var(--text-sub);">→ <?php echo date('d M Y', strtotime($row['return_date'])); ?></div>
                    </td>
                    <td><b>₹<?php echo number_format($row['total_price'], 2); ?></b></td>
                    <td><span class="<?php echo $status_class; ?>"><?php echo ucfirst($row['booking_status']); ?></span></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="7" style="text-align:center; padding:40px; color:var(--text-sub);">No bike bookings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ===== CAB / TAXI BOOKINGS TAB ===== -->
<div id="tab-cabs" class="tab-section">
    <div class="table-card reveal">
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User</th>
                    <th>Cab</th>
                    <th>Route</th>
                    <th>Date & Time</th>
                    <th>Total</th>
                    <th>Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql2 = "SELECT cb.*, u.name as user_name, c.cab_name, c.seats
                         FROM acabookings cb
                         LEFT JOIN users u ON cb.user_id = u.user_id
                         LEFT JOIN acab c ON cb.cab_id = c.id
                         ORDER BY cb.booking_date DESC";
                $res2 = $conn->query($sql2);
                if ($res2 && $res2->num_rows > 0):
                    while ($crow = $res2->fetch_assoc()):
                        $cstatus_class = match($crow['booking_status']) {
                            'active'    => 'badge-active',
                            'cancelled' => 'badge-cancelled',
                            default     => 'badge-pending'
                        };
                ?>
                <tr>
                    <td><code style="background:#F1F5F9; padding:3px 8px; border-radius:6px; font-size:0.8rem;"><?php echo $crow['booking_id']; ?></code></td>
                    <td><b><?php echo htmlspecialchars($crow['user_name'] ?? '—'); ?></b></td>
                    <td>
                        <div><b><?php echo htmlspecialchars($crow['cab_name'] ?? '—'); ?></b></div>
                        <div style="font-size:0.8rem; color:var(--text-sub);"><?php echo $crow['seats']; ?> Seater</div>
                    </td>
                    <td style="font-size:0.85rem; max-width:180px;">
                        <div style="font-weight:600;"><i class="fas fa-location-dot" style="color:var(--primary); margin-right:4px;"></i><?php echo htmlspecialchars($crow['pickup_location']); ?></div>
                        <div style="color:var(--text-sub); margin-top:4px;"><i class="fas fa-flag-checkered" style="margin-right:4px;"></i><?php echo htmlspecialchars($crow['drop_location']); ?></div>
                    </td>
                    <td style="font-size:0.85rem;">
                        <div><?php echo date('d M Y', strtotime($crow['booking_date'])); ?></div>
                        <div style="color:var(--text-sub);"><?php echo date('h:i A', strtotime($crow['pick_up_time'])); ?></div>
                    </td>
                    <td><b>₹<?php echo number_format($crow['total_price'], 2); ?></b></td>
                    <td>
                        <span style="background:#EFF6FF; color:#1D4ED8; padding:4px 10px; border-radius:50px; font-size:0.75rem; font-weight:700; text-transform:capitalize;">
                            <?php echo ucfirst($crow['trip_type'] ?? 'oneway'); ?>
                        </span>
                    </td>
                    <td><span class="<?php echo $cstatus_class; ?>"><?php echo ucfirst($crow['booking_status']); ?></span></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="8" style="text-align:center; padding:40px; color:var(--text-sub);">No taxi bookings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
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
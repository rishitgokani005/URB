<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_admin = isset($_SESSION['admin_logged_in']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
$is_agency = isset($_SESSION['agency_logged_in']);

// Restrict access to logged-in admins or agencies only
if (!$is_admin && !$is_agency) {
    header("Location: ../index.php");
    exit;
}

$db_connection_error = false;
try {
    include('../includes/db.php');
} catch (mysqli_sql_exception $e) {
    $db_connection_error = true;
}

// Load corresponding header or offline view
if ($db_connection_error) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Manage Cabs | Offline</title>
        <link rel='stylesheet' href='../css/style.css'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css'>
    </head>
    <body style='padding: 60px 7%; background: #F8FAFC; font-family: sans-serif; color: #0F172A;'>
        <div style='max-width: 600px; margin: 100px auto; text-align: center; background: white; padding: 40px; border-radius: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #E2E8F0;'>
            <i class='fas fa-exclamation-triangle' style='font-size: 3.5rem; color: #EF4444; margin-bottom: 20px;'></i>
            <h2 style='font-size: 1.8rem; font-weight: 800; margin-bottom: 10px;'>Database Connection Offline</h2>
            <p style='color: #64748B; line-height: 1.6;'>Please start your MySQL database server in XAMPP to manage your cab inventory.</p>
            <a href='../index.php' style='display:inline-block; margin-top:20px; padding: 12px 24px; background:#0F172A; color:white; text-decoration:none; border-radius:50px; font-weight:700;'>Back to Home</a>
        </div>
    </body>
    </html>";
    exit;
}

// If connection works, proceed with layout
if ($is_admin) {
    include('../admin/header.php');
} else {
    include('../agency/header.php');
}

$agency_id = $is_agency ? $_SESSION['agency_id'] : '';
$agency_name = $is_agency ? $_SESSION['agency_name'] : '';

// 1. Handle Add Cab
if (isset($_POST['add_cab'])) {
    $cab_name = mysqli_real_escape_string($conn, $_POST['cab_name']);
    $cab_type = mysqli_real_escape_string($conn, $_POST['cab_type']);
    $seats = intval($_POST['seats']);
    $price_per_km = intval($_POST['price_per_km']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $ac_status = mysqli_real_escape_string($conn, $_POST['ac_status']);
    
    $target_agency_id = $agency_id;
    $target_agency_name = $agency_name;

    if ($is_admin) {
        $target_agency_id = mysqli_real_escape_string($conn, $_POST['agency_id']);
        $agency_info = $conn->query("SELECT name FROM agencies WHERE id='$target_agency_id'")->fetch_assoc();
        $target_agency_name = $agency_info['name'] ?? 'Central Fleet';
    }

    // Photo uploads & fallback
    $img = '';
    if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
        $img = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "Cabs Photo/" . $img);
    } else {
        // Auto-fallback depending on seats count
        if ($seats == 4) {
            $img = '4-seater-car.webp';
        } elseif ($seats == 7) {
            $img = '7-Seater-cab.jpg';
        } else {
            $img = '11-seater-cab.avif';
        }
    }

    $id = 'CAB' . strtoupper(substr($cab_type, 0, 2)) . rand(100, 999);

    $sql = "INSERT INTO acab (id, agency_id, agency_name, cab_name, cab_type, seats, price_per_km, address, city, image, image2) 
            VALUES ('$id', '$target_agency_id', '$target_agency_name', '$cab_name', '$cab_type', '$seats', '$price_per_km', '$address', '$city', '$img', '$ac_status')";
    
    if ($conn->query($sql)) {
        echo "<script>alert('Cab vehicle successfully added!'); window.location.href='manage-cabs.php';</script>";
    } else {
        echo "<script>alert('Error inserting cab: " . mysqli_real_escape_string($conn, $conn->error) . "');</script>";
    }
}

// 2. Handle Delete Cab
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $delete_query = $is_admin ? "DELETE FROM acab WHERE id='$id'" : "DELETE FROM acab WHERE id='$id' AND agency_id='$agency_id'";
    
    if ($conn->query($delete_query)) {
        echo "<script>alert('Cab vehicle successfully deleted!'); window.location.href='manage-cabs.php';</script>";
    } else {
        echo "<script>alert('Error deleting cab: " . mysqli_real_escape_string($conn, $conn->error) . "');</script>";
    }
}

// 3. Handle Update Rate/Price
if (isset($_POST['update_price'])) {
    $cab_id = mysqli_real_escape_string($conn, $_POST['cab_id']);
    $new_price = intval($_POST['new_price']);
    $update_query = $is_admin ? "UPDATE acab SET price_per_km = '$new_price' WHERE id = '$cab_id'" : "UPDATE acab SET price_per_km = '$new_price' WHERE id = '$cab_id' AND agency_id = '$agency_id'";
    
    if ($conn->query($update_query)) {
        echo "<script>alert('Cab rate successfully updated!'); window.location.href='manage-cabs.php';</script>";
    } else {
        echo "<script>alert('Error updating rate: " . mysqli_real_escape_string($conn, $conn->error) . "');</script>";
    }
}

// 4. Handle Edit Cab
if (isset($_POST['edit_cab'])) {
    $cab_id       = mysqli_real_escape_string($conn, $_POST['edit_cab_id']);
    $cab_name     = mysqli_real_escape_string($conn, $_POST['edit_cab_name']);
    $cab_type     = mysqli_real_escape_string($conn, $_POST['edit_cab_type']);
    $seats        = intval($_POST['edit_seats']);
    $price_per_km = intval($_POST['edit_price_per_km']);
    $address      = mysqli_real_escape_string($conn, $_POST['edit_address']);
    $city         = mysqli_real_escape_string($conn, $_POST['edit_city']);
    $ac_status    = mysqli_real_escape_string($conn, $_POST['edit_ac_status']);

    // Handle optional new image upload
    $img_sql = '';
    if (isset($_FILES['edit_image']) && $_FILES['edit_image']['name'] != '') {
        $img = $_FILES['edit_image']['name'];
        move_uploaded_file($_FILES['edit_image']['tmp_name'], "Cabs Photo/" . $img);
        $img_sql = ", image = '$img'";
    }

    $where = $is_admin ? "WHERE id='$cab_id'" : "WHERE id='$cab_id' AND agency_id='$agency_id'";
    $sql = "UPDATE acab SET 
                cab_name='$cab_name', cab_type='$cab_type', seats='$seats',
                price_per_km='$price_per_km', address='$address', city='$city',
                image2='$ac_status' $img_sql
            $where";

    if ($conn->query($sql)) {
        echo "<script>alert('Cab details updated successfully!'); window.location.href='manage-cabs.php';</script>";
    } else {
        echo "<script>alert('Error updating cab: " . mysqli_real_escape_string($conn, $conn->error) . "');</script>";
    }
}

// 5. Handle Lock Cab (Offline Booking)
if (isset($_POST['offline_book'])) {
    $cab_id = mysqli_real_escape_string($conn, $_POST['cab_id']);
    $start = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end = mysqli_real_escape_string($conn, $_POST['end_date']);
    $user_id = 1; // Locked/Offline bookings standard user
    $booking_id = 'CAB' . time();
    
    $target_agency_id = $agency_id;
    if ($is_admin) {
        $cab_info = $conn->query("SELECT agency_id FROM acab WHERE id='$cab_id'")->fetch_assoc();
        $target_agency_id = $cab_info['agency_id'] ?? '';
    }

    $sql = "INSERT INTO acabookings (
                booking_id, user_id, cab_id, agency_id, 
                booking_date, return_date, booking_status, 
                name, mobile, idProof, total_price, pick_up_time
            ) VALUES ('$booking_id', '$user_id', '$cab_id', '$target_agency_id', '$start', '$end', 'active', 'OFFLINE BOOKING', 'N/A', 'N/A', 0, '09:00:00')";
    
    if ($conn->query($sql)) {
        echo "<script>alert('Cab vehicle locked offline successfully!'); window.location.href='manage-cabs.php';</script>";
    } else {
        echo "<script>alert('Error locking vehicle: " . mysqli_real_escape_string($conn, $conn->error) . "');</script>";
    }
}
?>

<!-- Custom Premium Dashboard Override styles -->
<style>
    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 35px;
    }

    .btn-manage-cabs {
        font-family: 'Outfit', sans-serif;
        font-size: 1.8rem;
        font-weight: 800;
        color: #0F172A;
    }

    .inventory-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }

    .cab-inventory-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -2px rgba(0,0,0,0.05);
        border: 1px solid var(--border);
        transition: 0.3s;
        display: flex;
        flex-direction: column;
    }

    .cab-inventory-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);
    }

    .card-img {
        height: 180px;
        position: relative;
    }

    .card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .card-details {
        padding: 25px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex-grow: 1;
    }

    .cab-model-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.3rem;
        font-weight: 800;
        color: #0F172A;
        margin-bottom: 4px;
    }

    .detail-line {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.9rem;
        color: var(--text-sub);
    }

    .detail-line i {
        width: 16px;
        color: var(--primary);
    }

    .pricing-control {
        margin-top: 10px;
        padding-top: 15px;
        border-top: 1px dashed var(--border);
    }

    .pricing-control label {
        display: block;
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-sub);
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .price-input {
        flex: 1;
        padding: 8px 12px;
        border-radius: 10px;
        border: 1.5px solid var(--border);
        font-weight: 700;
        font-size: 0.95rem;
        outline: none;
    }

    .price-input:focus {
        border-color: var(--primary);
    }

    .btn-update-inline {
        width: 40px;
        height: 38px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        transition: 0.2s;
    }

    .btn-update-inline:hover {
        background: #e04400;
        transform: scale(1.05);
    }

    .card-actions {
        margin-top: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding-top: 15px;
    }

    .btn-lock-offline {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: #0F172A;
        color: white !important;
        padding: 12px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.85rem;
        border: none;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-lock-offline:hover {
        background: var(--primary);
    }

    .btn-delete-vehicle {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px;
        background: #FFF1F1;
        color: #EF4444;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-delete-vehicle:hover {
        background: #EF4444;
        color: white;
    }

    /* Modal Close Cross button UI (Circular & Elegant) */
    .modal-close-cross {
        position: absolute;
        top: 25px;
        right: 25px;
        background: #F1F5F9;
        border: none;
        color: #64748B;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: 0.2s;
        z-index: 10;
    }

    .modal-close-cross:hover {
        background: #FEE2E2;
        color: #EF4444;
        transform: scale(1.05);
    }

    /* Modal dialog overrides */
    .modal-dialog-content {
        background: white;
        padding: 35px;
        border-radius: 30px;
        width: 90%;
        max-width: 550px;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--border);
        position: relative;
        overflow-y: auto;
        max-height: 90vh;
        animation: fadeInUp 0.4s ease;
    }

    .modal-dialog-content h2 {
        font-family: 'Outfit', sans-serif;
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 25px;
    }

    /* Slick Modal Fields */
    .modal-dialog-content select,
    .modal-dialog-content input[type="text"],
    .modal-dialog-content input[type="number"],
    .modal-dialog-content input[type="date"],
    .modal-dialog-content textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        outline: none;
        margin-bottom: 15px;
        transition: 0.2s;
        background-color: white;
    }

    .modal-dialog-content select:focus,
    .modal-dialog-content input:focus,
    .modal-dialog-content textarea:focus {
        border-color: var(--primary);
    }

    .modal-btn-row {
        display: flex;
        gap: 15px;
        margin-top: 15px;
    }

    .btn-submit-fleet {
        flex: 2;
        background: var(--primary);
        color: white;
        border: none;
        padding: 14px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-submit-fleet:hover {
        background: #e04400;
        box-shadow: 0 8px 16px rgba(255, 77, 1, 0.25);
    }

    /* Secondary Cancel Button - Elegant, clean Outline style */
    .btn-cancel-fleet {
        flex: 1;
        background: #F8FAFC;
        color: #475569;
        border: 1.5px solid var(--border);
        padding: 14px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: 0.2s;
        text-align: center;
    }

    .btn-cancel-fleet:hover {
        background: #F1F5F9;
        color: #0F172A;
        border-color: #94A3B8;
    }
</style>

<div class="header-row reveal">
    <div>
        <h2 class="btn-manage">Manage Cab Fleet</h2>
    </div>
    <button class="btn-add" onclick="document.getElementById('addCabModal').style.display='flex'">Add Taxi Cab</button>
</div>

<!-- Cabs Inventory Grid -->
<div class="inventory-grid reveal">
    <?php
    $cabs_sql = $is_admin ? "SELECT * FROM acab ORDER BY id DESC" : "SELECT * FROM acab WHERE agency_id = '$agency_id' ORDER BY id DESC";
    $res = $conn->query($cabs_sql);
    
    if($res && $res->num_rows > 0):
        while($row = $res->fetch_assoc()):
            $ac_status = (strpos(strtolower($row['cab_name']), 'non-ac') !== false || $row['image2'] === 'Non-AC') ? 'Non-AC' : 'AC';
    ?>
    <div class="cab-inventory-card">
        <div class="card-img">
            <img src="Cabs Photo/<?php echo htmlspecialchars($row['image']); ?>" alt="Cab Image">
            <span class="badge badge-active" style="position:absolute; top:15px; right:15px; background:rgba(15,23,42,0.85); color:white; backdrop-filter:blur(5px);"><?php echo $ac_status; ?></span>
        </div>
        <div class="card-details">
            <h3 class="cab-model-title"><?php echo htmlspecialchars($row['cab_name']); ?></h3>
            <div class="detail-line"><i class="fas fa-couch"></i> <span>Capacity: <b><?php echo $row['seats']; ?> Seater</b></span></div>
            <div class="detail-line"><i class="fas fa-location-dot"></i> <span>City: <b><?php echo htmlspecialchars($row['city']); ?></b></span></div>
            <div class="detail-line"><i class="fas fa-building"></i> <span>Partner: <b><?php echo htmlspecialchars($row['agency_name']); ?></b></span></div>
            <div class="detail-line"><i class="fas fa-road"></i> <span>Address: <b><?php echo htmlspecialchars($row['address']); ?></b></span></div>
            
            <div class="pricing-control">
                <label>Price / Km (₹)</label>
                <form method="POST" style="display: flex; gap: 8px;">
                    <input type="hidden" name="cab_id" value="<?php echo $row['id']; ?>">
                    <input type="number" name="new_price" value="<?php echo $row['price_per_km']; ?>" class="price-input" min="1" required>
                    <button type="submit" name="update_price" class="btn-update-inline"><i class="fas fa-check"></i></button>
                </form>
            </div>

            <div class="card-actions">
                <button onclick="openEditModal(
                    '<?php echo $row['id']; ?>',
                    '<?php echo htmlspecialchars(addslashes($row['cab_name'])); ?>',
                    '<?php echo htmlspecialchars($row['cab_type']); ?>',
                    '<?php echo $row['seats']; ?>',
                    '<?php echo $row['price_per_km']; ?>',
                    '<?php echo htmlspecialchars(addslashes($row['address'])); ?>',
                    '<?php echo htmlspecialchars($row['city']); ?>',
                    '<?php echo $ac_status; ?>'
                )" class="btn-lock-offline" style="background: #1E40AF;">
                    <i class="fas fa-pen-to-square"></i> Edit Cab Details
                </button>
                <button onclick="openLockModal('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['cab_name']); ?>')" class="btn-lock-offline">
                    <i class="fas fa-calendar-times"></i> Lock Dates Offline
                </button>
                <a href="?delete=<?php echo $row['id']; ?>" class="btn-delete-vehicle" onclick="return confirm('Remove this taxi cab vehicle from fleet?')">
                    <i class="fas fa-trash-alt"></i> Delete Cab
                </a>
            </div>
        </div>
    </div>
    <?php 
        endwhile;
    else:
    ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 80px 0; background:white; border-radius:24px; border: 1px solid var(--border);">
            <i class="fas fa-taxi" style="font-size: 4rem; color: var(--border); margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-sub);">No cabs found in the fleet catalog. Add one to get started!</h3>
        </div>
    <?php endif; ?>
</div>

<!-- Add Cab Modal (Featuring Perfect Close/Cancel UI) -->
<div id="addCabModal" class="modal">
    <div class="modal-dialog-content">
        <!-- Elegant circular close button in top right -->
        <button type="button" class="modal-close-cross" onclick="document.getElementById('addCabModal').style.display='none'">&times;</button>
        
        <h2>Add New Vehicle</h2>
        <form method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Cab Vehicle Model Name</label>
                    <input type="text" name="cab_name" placeholder="e.g. Maruti Swift Dzire" required>
                </div>
                <div>
                    <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Cab Classification</label>
                    <select name="cab_type" required>
                        <option value="Hatchback">Hatchback</option>
                        <option value="Sedan">Sedan</option>
                        <option value="SUV">SUV</option>
                        <option value="Van">Van/Cruiser</option>
                    </select>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Seating Capacity</label>
                    <select name="seats" required>
                        <option value="4">4 Seater</option>
                        <option value="7">7 Seater</option>
                        <option value="11">11 Seater</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Air Conditioning</label>
                    <select name="ac_status" required>
                        <option value="AC">AC Cab</option>
                        <option value="Non-AC">Non-AC Cab</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Rate / Km (₹)</label>
                    <input type="number" name="price_per_km" placeholder="e.g. 12" min="1" required>
                </div>
                <div>
                    <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">City Location</label>
                    <select name="city" required>
                        <option value="Dwarka">Dwarka</option>
                        <option value="Somnath">Somnath</option>
                    </select>
                </div>
            </div>

            <?php if ($is_admin): ?>
                <div style="margin-bottom: 15px;">
                    <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Assign to Agency Partner</label>
                    <select name="agency_id" required>
                        <?php 
                        $agencies_res = $conn->query("SELECT id, name FROM agencies");
                        while($agy = $agencies_res->fetch_assoc()):
                        ?>
                            <option value="<?php echo $agy['id']; ?>"><?php echo htmlspecialchars($agy['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div>
                <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Pick-up Address</label>
                <textarea name="address" placeholder="Enter pickup station details" required style="height: 70px; width: 100%; padding: 12px; border-radius: 12px; border: 1.5px solid var(--border); margin-bottom: 15px; font-family: inherit; font-weight:600; resize:none;"></textarea>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Upload Cab Photo (Optional - Defaults to fallback template if left blank)</label>
                <input type="file" name="image" style="font-size: 0.85rem; border:none; padding:0;">
            </div>
            
            <div class="modal-btn-row">
                <button type="submit" name="add_cab" class="btn-submit-fleet">Add Taxi to Fleet</button>
                <button type="button" class="btn-cancel-fleet" onclick="document.getElementById('addCabModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Lock Dates Modal (Featuring Perfect Close/Cancel UI) -->
<div id="lockCabModal" class="modal">
    <div class="modal-dialog-content" style="max-width: 450px;">
        <!-- Elegant circular close button in top right -->
        <button type="button" class="modal-close-cross" onclick="document.getElementById('lockCabModal').style.display='none'">&times;</button>
        
        <h2>Lock Dates <span id="lock-cab-name" style="color: var(--primary);"></span></h2>
        <p style="margin-bottom: 20px; color: var(--text-sub); font-size:0.9rem;">Mark this cab offline by selecting the date range block.</p>
        <form method="POST">
            <input type="hidden" name="cab_id" id="lock-cab-id">
            <div class="form-group-taxi">
                <label>Lock Start Date</label>
                <input type="date" name="start_date" min="<?php echo date('Y-m-d'); ?>" required style="width:100%; padding:12px; border:1.5px solid var(--border); border-radius:12px; font-weight:600;">
            </div>
            <div class="form-group-taxi" style="margin-top:15px;">
                <label>Lock End Date</label>
                <input type="date" name="end_date" min="<?php echo date('Y-m-d'); ?>" required style="width:100%; padding:12px; border:1.5px solid var(--border); border-radius:12px; font-weight:600;">
            </div>
            <div class="modal-btn-row" style="margin-top: 25px;">
                <button type="submit" name="offline_book" class="btn-submit-fleet">Lock Vehicle</button>
                <button type="button" class="btn-cancel-fleet" onclick="document.getElementById('lockCabModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Cab Modal -->
<div id="editCabModal" class="modal">
    <div class="modal-dialog-content">
        <button type="button" class="modal-close-cross" onclick="document.getElementById('editCabModal').style.display='none'">&times;</button>
        <h2>Edit Cab Details</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="edit_cab_id" id="edit_cab_id">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Cab Vehicle Model Name</label>
                    <input type="text" name="edit_cab_name" id="edit_cab_name" placeholder="e.g. Maruti Swift Dzire" required>
                </div>
                <div>
                    <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Cab Classification</label>
                    <select name="edit_cab_type" id="edit_cab_type" required>
                        <option value="Hatchback">Hatchback</option>
                        <option value="Sedan">Sedan</option>
                        <option value="SUV">SUV</option>
                        <option value="Van">Van/Cruiser</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Seating Capacity</label>
                    <select name="edit_seats" id="edit_seats" required>
                        <option value="4">4 Seater</option>
                        <option value="7">7 Seater</option>
                        <option value="11">11 Seater</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Air Conditioning</label>
                    <select name="edit_ac_status" id="edit_ac_status" required>
                        <option value="AC">AC Cab</option>
                        <option value="Non-AC">Non-AC Cab</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Rate / Km (₹)</label>
                    <input type="number" name="edit_price_per_km" id="edit_price_per_km" placeholder="e.g. 12" min="1" required>
                </div>
                <div>
                    <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">City Location</label>
                    <select name="edit_city" id="edit_city" required>
                        <option value="Dwarka">Dwarka</option>
                        <option value="Somnath">Somnath</option>
                    </select>
                </div>
            </div>

            <div>
                <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Pick-up Address</label>
                <textarea name="edit_address" id="edit_address" placeholder="Enter pickup station details" required style="height: 70px; width: 100%; padding: 12px; border-radius: 12px; border: 1.5px solid var(--border); margin-bottom: 15px; font-family: inherit; font-weight:600; resize:none;"></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="font-size:0.75rem; font-weight:700; color:var(--text-sub); display:block; margin-bottom:5px;">Update Cab Photo (Optional – Leave blank to keep current)</label>
                <input type="file" name="edit_image" style="font-size: 0.85rem; border:none; padding:0;">
            </div>

            <div class="modal-btn-row">
                <button type="submit" name="edit_cab" class="btn-submit-fleet">Save Changes</button>
                <button type="button" class="btn-cancel-fleet" onclick="document.getElementById('editCabModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, name, type, seats, price, address, city, ac) {
        document.getElementById('edit_cab_id').value        = id;
        document.getElementById('edit_cab_name').value      = name;
        document.getElementById('edit_price_per_km').value  = price;
        document.getElementById('edit_address').value       = address;

        // Set dropdowns
        const typeEl = document.getElementById('edit_cab_type');
        for (let o of typeEl.options) o.selected = (o.value === type);

        const seatsEl = document.getElementById('edit_seats');
        for (let o of seatsEl.options) o.selected = (o.value == seats);

        const cityEl = document.getElementById('edit_city');
        for (let o of cityEl.options) o.selected = (o.value === city);

        const acEl = document.getElementById('edit_ac_status');
        for (let o of acEl.options) o.selected = (o.value === ac);

        document.getElementById('editCabModal').style.display = 'flex';
    }

    function openLockModal(id, name) {
        document.getElementById('lock-cab-id').value = id;
        document.getElementById('lock-cab-name').innerText = name;
        document.getElementById('lockCabModal').style.display = 'flex';
    }

    // Scroll reveal observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('revealed');
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php
if ($is_admin) {
    include('../admin/footer.php');
} else {
    include('../agency/footer.php');
}
?>

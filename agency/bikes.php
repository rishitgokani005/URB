<?php
include('header.php');

// Handle Add Bike
if (isset($_POST['add_bike'])) {
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $color = mysqli_real_escape_string($conn, $_POST['color']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $deposit = mysqli_real_escape_string($conn, $_POST['deposit']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Get city from agency record
    $agency_info = $conn->query("SELECT city, name FROM agencies WHERE id='$agency_id'")->fetch_assoc();
    $city = $agency_info['city'];
    $agency_name = $agency_info['name'];

    // Handle Images
    $img1 = $_FILES['image']['name'];
    $img2 = $_FILES['image2']['name'];
    $img3 = $_FILES['image3']['name'];
    $img4 = $_FILES['image4']['name'];

    move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $img1);
    move_uploaded_file($_FILES['image2']['tmp_name'], "../uploads/" . $img2);
    move_uploaded_file($_FILES['image3']['tmp_name'], "../uploads/" . $img3);
    move_uploaded_file($_FILES['image4']['tmp_name'], "../uploads/" . $img4);

    $id = strtoupper(substr($model, 0, 2)) . rand(100, 999);

    $sql = "INSERT INTO abike (id, model, color, price_per_day, deposite, address, image, image2, image3, image4, city, agency_name, agency_id) 
            VALUES ('$id', '$model', '$color', '$price', '$deposit', '$address', '$img1', '$img2', '$img3', '$img4', '$city', '$agency_name', '$agency_id')";
    
    if ($conn->query($sql)) {
        echo "<script>alert('Bike added successfully!'); window.location.href='bikes.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM abike WHERE id='$id' AND agency_id='$agency_id'");
    header('Location: bikes.php');
}

// Handle Price Update
if (isset($_POST['update_price'])) {
    $bike_id = $_POST['bike_id'];
    $new_price = $_POST['new_price'];
    $conn->query("UPDATE abike SET price_per_day = '$new_price' WHERE id = '$bike_id' AND agency_id = '$agency_id'");
    echo "<script>alert('Price updated successfully!'); window.location.href='bikes.php';</script>";
}

// Handle Offline Booking (Locking)
if (isset($_POST['offline_book'])) {
    $bike_id = $_POST['bike_id'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $user_id = 1; // System/Admin user
    
    $sql = "INSERT INTO abookings (user_id, bike_id, agency_id, booking_date, return_date, booking_status, name, mobile, idProof, total_price, pick_up_time, drop_off_time) 
            VALUES ('$user_id', '$bike_id', '$agency_id', '$start', '$end', 'active', 'OFFLINE BOOKING', 'N/A', 'N/A', 0, '09:00:00', '20:00:00')";
    
    if ($conn->query($sql)) {
        echo "<script>alert('Bike locked successfully for selected dates!'); window.location.href='bikes.php';</script>";
    } else {
        echo "<script>alert('Error locking bike: " . $conn->error . "');</script>";
    }
}
?>

<div class="header-row reveal" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h2 class="btn-manage">Manage Bikes</h2>
    </div>
    <button class="btn-add" onclick="document.getElementById('addModal').style.display='flex'">Add Vehicle</button>
</div>

<!-- Grid of Cards -->
<div class="inventory-grid reveal">
    <?php
    $res = $conn->query("SELECT * FROM abike WHERE agency_id = '$agency_id' ORDER BY id DESC");
    if($res->num_rows > 0):
        while($row = $res->fetch_assoc()):
    ?>
    <div class="bike-inventory-card">
        <div class="card-img">
            <img src="../uploads/<?php echo $row['image']; ?>" alt="Bike Image">
        </div>
        <div class="card-details">
            <h3 class="bike-model"><?php echo $row['model']; ?></h3>
            <div class="detail-line"><i class="fas fa-palette"></i> <span>Color: <?php echo ucfirst($row['color']); ?></span></div>
            <div class="detail-line"><i class="fas fa-location-dot"></i> <span>City: <?php echo $row['city']; ?></span></div>
            <div class="detail-line"><i class="fas fa-money-bill-transfer"></i> <span>Deposit: ₹<?php echo $row['deposite']; ?></span></div>
            
            <div class="pricing-control">
                <label>Daily Price (₹)</label>
                <form method="POST" style="display: flex; gap: 8px;">
                    <input type="hidden" name="bike_id" value="<?php echo $row['id']; ?>">
                    <input type="number" name="new_price" value="<?php echo $row['price_per_day']; ?>" class="price-input">
                    <button type="submit" name="update_price" class="btn-update"><i class="fas fa-check"></i></button>
                </form>
            </div>

            <div class="card-actions">
                <button onclick="openBookModal('<?php echo $row['id']; ?>', '<?php echo $row['model']; ?>')" class="btn-signup" style="width: 100%; border-radius: 12px; margin-bottom: 10px; margin-top: 0;">
                    <i class="fas fa-calendar-check"></i> Book Offline
                </button>
                <a href="?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Remove this vehicle from fleet?')">
                    <i class="fas fa-trash-alt"></i> Delete Vehicle
                </a>
            </div>
        </div>
    </div>
    <?php 
        endwhile;
    else:
    ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 100px 0;">
            <i class="fas fa-motorcycle" style="font-size: 4rem; color: var(--border); margin-bottom: 20px;"></i>
            <h3 style="color: var(--text-sub);">No bikes found in your inventory. Add one to get started!</h3>
        </div>
    <?php endif; ?>
</div>


<style>
    .inventory-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }
    .bike-inventory-card {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        transition: 0.3s;
    }
    .bike-inventory-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    .card-img {
        height: 200px;
        position: relative;
    }
    .card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .card-status {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 0.7rem;
        padding: 5px 12px;
        border-radius: 50px;
        font-weight: 800;
        backdrop-filter: blur(5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .card-details {
        padding: 25px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .bike-model {
        font-family: var(--font-heading);
        font-size: 1.4rem;
        color: var(--accent);
        margin-bottom: 5px;
    }
    .detail-line {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.9rem;
        color: var(--text-sub);
    }
    .detail-line i {
        width: 20px;
        color: var(--primary);
    }
    .pricing-control {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px dashed var(--border);
    }
    .pricing-control label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-sub);
        margin-bottom: 8px;
        text-transform: uppercase;
    }
    .price-input {
        flex: 1;
        padding: 8px 6px;
        border-radius: 10px;
        border: 1.5px solid var(--border);
        font-weight: 700;
        font-size: 1rem;
        outline: none;
        max-width: 80%;
    }
    .price-input:focus {
        border-color: var(--primary);
    }
    .btn-update {
        width: 40px;
        flex-shrink: 0;

    }
    .btn-delete {
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px;
        background: #FFF1F1;
        color: #F87171;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: 0.3s;
    }
    .btn-delete:hover {
        background: #F87171;
        color: white;
    }
</style>

<div id="addModal" class="modal">
    <div class="modal-content">
        <h2>Add New Vehicle</h2>
        <form method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <input type="text" name="model" placeholder="Model (e.g. Activa 6G)" required>
                <input type="text" name="color" placeholder="Color" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <input type="number" name="price" placeholder="Rate / Day (₹)" required>
                <input type="number" name="deposit" placeholder="Deposit (₹)" required>
            </div>
            <textarea name="address" placeholder="Pick-up Full Address" required style="height: 100px; width: 100%; padding: 12px; border-radius: 12px; border: 1.5px solid var(--border); margin-bottom: 15px; font-family: inherit;"></textarea>
            
            <p style="margin-bottom: 10px; font-weight: 700; font-size: 0.85rem; color: var(--text-sub);">Upload Fleet Photos (4 Required):</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
                <input type="file" name="image" required style="font-size: 0.7rem;">
                <input type="file" name="image2" required style="font-size: 0.7rem;">
                <input type="file" name="image3" required style="font-size: 0.7rem;">
                <input type="file" name="image4" required style="font-size: 0.7rem;">
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <button type="submit" name="add_bike" class="btn-signup" style="flex: 2; margin-top: 0;">Add to Fleet</button>
                <button type="button" onclick="document.getElementById('addModal').style.display='none'" style="flex: 1; background: #F8FAFC; color: #475569; border: 1.5px solid var(--border); padding: 10px 24px; border-radius: 50px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: 0.2s; text-align: center;" onmouseover="this.style.background='#F1F5F9'; this.style.color='#0F172A'; this.style.borderColor='#94A3B8';" onmouseout="this.style.background='#F8FAFC'; this.style.color='#475569'; this.style.borderColor='var(--border)';">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Offline Book Modal -->
<div id="bookModal" class="modal">
    <div class="modal-content">
        <h2>Lock Bike <span id="lock-bike-name" style="color: var(--primary);"></span></h2>
        <p style="margin-bottom: 20px; color: var(--text-sub);">Set dates to mark this bike as booked/offline.</p>
        <form method="POST">
            <input type="hidden" name="bike_id" id="lock-bike-id">
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" name="start_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label>End Date</label>
                <input type="date" name="end_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div style="display: flex; gap: 15px; margin-top: 20px;">
                <button type="submit" name="offline_book" class="btn-signup" style="flex: 2; margin-top: 0;">Lock Vehicle</button>
                <button type="button" onclick="document.getElementById('bookModal').style.display='none'" style="flex: 1; background: #F8FAFC; color: #475569; border: 1.5px solid var(--border); padding: 10px 24px; border-radius: 50px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: 0.2s; text-align: center;" onmouseover="this.style.background='#F1F5F9'; this.style.color='#0F172A'; this.style.borderColor='#94A3B8';" onmouseout="this.style.background='#F8FAFC'; this.style.color='#475569'; this.style.borderColor='var(--border)';">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBookModal(id, model) {
        document.getElementById('lock-bike-id').value = id;
        document.getElementById('lock-bike-name').innerText = model;
        document.getElementById('bookModal').style.display = 'flex';
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('revealed');
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php include('footer.php'); ?>

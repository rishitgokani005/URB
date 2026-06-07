<?php
include('header.php');

$success_msg = '';
$error_msg = '';

// Fetch current agency details
$stmt = $conn->prepare("SELECT mobile, address, has_pickup FROM agencies WHERE id = ?");
$stmt->bind_param("s", $agency_id);
$stmt->execute();
$res = $stmt->get_result();
$agency_data = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $has_pickup = isset($_POST['has_pickup']) ? 1 : 0;

    $update_stmt = $conn->prepare("UPDATE agencies SET mobile = ?, address = ?, has_pickup = ? WHERE id = ?");
    $update_stmt->bind_param("ssis", $mobile, $address, $has_pickup, $agency_id);
    
    if ($update_stmt->execute()) {
        $success_msg = "Profile updated successfully!";
        $agency_data['mobile'] = $mobile;
        $agency_data['address'] = $address;
        $agency_data['has_pickup'] = $has_pickup;
    } else {
        $error_msg = "Error updating profile: " . $conn->error;
    }
}
?>

<style>
    .profile-card {
        background: white;
        border-radius: 30px;
        padding: 40px;
        max-width: 700px;
        margin: 0 auto;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        font-weight: 700;
        margin-bottom: 10px;
        color: var(--text-main);
        font-size: 0.95rem;
    }

    .form-input {
        width: 100%;
        padding: 15px 20px;
        border-radius: 15px;
        border: 1.5px solid var(--border);
        font-family: inherit;
        font-size: 1rem;
        transition: 0.3s;
        background: #fdfdfd;
    }

    .form-input:focus {
        border-color: var(--primary);
        background: white;
        outline: none;
        box-shadow: 0 0 15px rgba(255, 77, 1, 0.08);
    }

    .toggle-container {
        display: flex;
        align-items: center;
        gap: 15px;
        background: var(--bg-sub);
        padding: 20px;
        border-radius: 20px;
        margin-top: 10px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: var(--primary);
    }

    input:focus + .slider {
        box-shadow: 0 0 1px var(--primary);
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }

    .btn-save {
        background: var(--primary);
        color: white;
        border: none;
        padding: 15px 40px;
        border-radius: 15px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: 0.3s;
        width: 100%;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255, 77, 1, 0.2);
    }

    .alert {
        padding: 15px 20px;
        border-radius: 15px;
        margin-bottom: 30px;
        font-weight: 600;
    }

    .alert-success { background: #DCFCE7; color: #166534; }
    .alert-error { background: #FEE2E2; color: #991B1B; }
</style>

<div class="section-title reveal">
    <span class="sub-heading">Agency Settings</span>
    <h2>Company Profile</h2>
    <p style="color: var(--text-sub); margin-top: 10px;">Manage your contact details and service preferences.</p>
</div>

<div class="profile-card reveal">
    <?php if ($success_msg): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle" style="margin-right: 10px;"></i> <?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle" style="margin-right: 10px;"></i> <?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Mobile Number</label>
            <input type="text" name="mobile" class="form-input" placeholder="e.g. +91 98765 43210" value="<?php echo htmlspecialchars($agency_data['mobile'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Office Address</label>
            <textarea name="address" class="form-input" rows="4" placeholder="Enter your full agency address" required><?php echo htmlspecialchars($agency_data['address'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Pick-up Service</label>
            <div class="toggle-container">
                <label class="switch">
                    <input type="checkbox" name="has_pickup" <?php echo ($agency_data['has_pickup'] ?? 0) ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
                <div>
                    <b style="display: block; color: var(--text-main);">Enable Pick-up Service</b>
                    <span style="font-size: 0.8rem; color: var(--text-sub);">Allow users to request bike delivery to their location.</span>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-save">Save Changes</button>
    </form>
</div>

<script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('revealed'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php include('footer.php'); ?>

<?php
session_start();
include('includes/db.php');
require 'includes/header.php';

$bike_id = isset($_GET['bike_id']) ? mysqli_real_escape_string($conn, $_GET['bike_id']) : '';

// Fetch bike details for confirmation
$query = "SELECT * FROM abike WHERE id = '$bike_id'";
$result = $conn->query($query);
$bike = $result->fetch_assoc();

if (!$bike) {
    header("Location: index.php");
    exit;
}

$_SESSION['selected_bike_id'] = $bike_id;

// Immediate Bike Locking Mechanism
if (isset($_SESSION['user_id']) && $bike_id && isset($_SESSION['booking_start'])) {
    $start = $_SESSION['booking_start'];
    $end = $_SESSION['booking_end'];
    
    // Ensure we don't have a double lock for the same trip in this session
    if (!isset($_SESSION['pending_booking_id'])) {
        // 1. Check if bike is already booked/locked by someone else
        $check_query = "SELECT * FROM abookings 
                        WHERE bike_id = '$bike_id' 
                        AND (booking_status = 'active' OR (booking_status = 'pending' AND created_at > NOW() - INTERVAL 15 MINUTE))
                        AND NOT (return_date < ? OR booking_date > ?)";
        $stmt = $conn->prepare($check_query);
        $stmt->bind_param("ss", $start, $end);
        $stmt->execute();
        $check_res = $stmt->get_result();
        
        if ($check_res->num_rows > 0) {
            header("Location: agencies.php?error=recently_booked");
            exit;
        }

        // 2. Insert pending record to lock it
        $user_id = $_SESSION['user_id'];
        $agency_id = $bike['agency_id'] ?? '';
        $pickup = $bike['address'] ?? '';
        
        $lock_sql = "INSERT INTO abookings (user_id, bike_id, pickup_location, agency_id, booking_date, return_date, booking_status, name, idProof, mobile, total_price, pick_up_time, drop_off_time) 
                     VALUES (?, ?, ?, ?, ?, ?, 'pending', '', '', '', 0, '00:00:00', '00:00:00')";
        $lock_stmt = $conn->prepare($lock_sql);
        $lock_stmt->bind_param("isssss", $user_id, $bike_id, $pickup, $agency_id, $start, $end);
        if ($lock_stmt->execute()) {
            $_SESSION['pending_booking_id'] = $conn->insert_id;
        }
    }
}
?>

<link rel="stylesheet" href="css/style.css">
<style>
    /* .booking-info-section {
        padding: 5rem 5%;
        background: #f8f9fa;
        min-height: 80vh;
        display: flex;
        justify-content: center;
        align-items: center;
    } */

    /* .info-card {
        background: white;
        padding: 3rem;
        border-radius: 30px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 600px;
    }

    .info-card h2 {
        font-family: var(--font-heading);
        margin-bottom: 2rem;
        text-align: center;
        font-size: 2.2rem;
    }
*/
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.8rem;
        font-weight: 600;
        color: #444;
    }

    .form-group input[type="text"],
    .form-group input[type="tel"] {
        width: 100%;
        padding: 1.2rem;
        border: 1.5px solid #eee;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-group input:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 10px rgba(255, 77, 1, 0.1);
    }

    .proof-options {
        display: flex;
        gap: 2rem;
        margin-top: 1rem;
    }

    .radio-group {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .radio-group input {
        width: 20px;
        height: 20px;
        accent-color: var(--primary);
    }

    .summary-mini {
        background: rgba(255, 77, 1, 0.05);
        padding: 1.5rem;
        border-radius: 16px;
        margin-bottom: 2.5rem;
        border: 1px dashed var(--primary);
    }

    .summary-mini p {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .proceed-btn {
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
    margin-left: 28px;


    
    }
    .proceed-btn:hover {
    transform: scale(1.05);
    box-shadow: var(--shadow-lg);
  }

    .custom-select {
        width: 100%;
        padding: 1.2rem;
        border: 1.5px solid #eee;
        border-radius: 12px;
        font-size: 1rem;
        appearance: none;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23ff4d01' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat right 1rem center;
        background-color: white;
        cursor: pointer;
    }

    .time-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .info-card {
            padding: 1.5rem;
            margin: 10px;
            margin-left: 1px;
        }

        .proof-options {
            flex-direction: column;
            gap: 1rem;
        }

        .time-grid {
            grid-template-columns: 1fr;
        }
    }
</style>


<section class="booking-info-section">
    <div class="info-card">
        <h2>Driver Information</h2>

        <div class="summary-mini">
            <p><span>Bike:</span> <b><?php echo htmlspecialchars($bike['model']); ?></b></p>
            <p><span>Dates:</span> <b><?php echo htmlspecialchars($_SESSION['booking_start'] ?? ''); ?> to
                    <?php echo htmlspecialchars($_SESSION['booking_end'] ?? ''); ?></b></p>
            <p><span>Pick-up Location:</span> <b><?php echo htmlspecialchars($bike['address']); ?></b></p>
        </div>

        <form action="checkout.php" method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="customer_name" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="customer_phone" placeholder="Enter mobile number" required pattern="[0-9]{10}">
            </div>

            <div class="form-group">
                <label>Identity Proof</label>
                <div class="proof-options">
                    <label class="radio-group">
                        <input type="radio" name="id_proof" value="Driving License" checked>
                        Driving License
                    </label>
                    <label class="radio-group">
                        <input type="radio" name="id_proof" value="Aadhar Card">
                        Aadhar Card
                    </label>
                    <label class="radio-group">
                        <input type="radio" name="id_proof" value="Voter ID">
                        Voter ID
                    </label>
                </div>
            </div>

            <div class="time-grid">
                <div class="form-group">
                    <label>Pick-up Time</label>
                    <select name="pick_up_time" class="custom-select" required>
                        <?php for ($i = 9; $i <= 20; $i++):
                            $time = sprintf("%02d:00", $i);
                            $label = $i <= 12 ? ($i == 12 ? "12:00 PM" : "$i:00 AM") : ($i - 12) . ":00 PM";
                            ?>
                            <option value="<?php echo $time; ?>"><?php echo $label; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Drop-off Time</label>
                    <select name="drop_off_time" class="custom-select" required>
                        <?php for ($i = 9; $i <= 20; $i++):
                            $time = sprintf("%02d:00", $i);
                            $label = $i <= 12 ? ($i == 12 ? "12:00 PM" : "$i:00 AM") : ($i - 12) . ":00 PM";
                            ?>
                            <option value="<?php echo $time; ?>" <?php echo $i == 20 ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="proceed-btn">Proceed to Checkout</button>
        </form>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
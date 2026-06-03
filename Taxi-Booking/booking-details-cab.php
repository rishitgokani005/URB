<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$cab_id = isset($_GET['cab_id']) ? mysqli_real_escape_string($conn, $_GET['cab_id']) : '';
$rate = isset($_GET['rate']) ? floatval($_GET['rate']) : 0.00;
$base_rent = isset($_GET['base_rent']) ? floatval($_GET['base_rent']) : 0.00;
$pickup_location = isset($_GET['pickup_location']) ? htmlspecialchars($_GET['pickup_location']) : '';
$drop_location = isset($_GET['drop_location']) ? htmlspecialchars($_GET['drop_location']) : '';
$trip_type = isset($_GET['trip_type']) ? htmlspecialchars($_GET['trip_type']) : 'oneway';
$pickup_date = isset($_GET['pickup_date']) ? htmlspecialchars($_GET['pickup_date']) : '';
$pickup_time = isset($_GET['pickup_time']) ? htmlspecialchars($_GET['pickup_time']) : '';
$return_date = isset($_GET['return_date']) ? htmlspecialchars($_GET['return_date']) : '';
$est_distance = isset($_GET['est_distance']) ? intval($_GET['est_distance']) : 100;

// Fetch cab and agency details
$query = "SELECT * FROM acab WHERE id = '$cab_id'";
$result = $conn->query($query);
$cab = $result ? $result->fetch_assoc() : null;

if (!$cab) {
    header("Location: taxi-booking.php");
    exit;
}

// Prefill values
$prefill_name = isset($_SESSION['customer_name']) ? htmlspecialchars($_SESSION['customer_name']) : '';
$prefill_phone = isset($_SESSION['customer_phone']) ? htmlspecialchars($_SESSION['customer_phone']) : '';
$prefill_email = isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '';

// Setup header (path variables resolved relative to subdirectory)
require '../includes/header.php';
?>

<link rel="stylesheet" href="../css/style.css">
<style>
    .booking-details-section {
        padding: 80px 5%;
        background: #F8FAFC;
        min-height: 85vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Inter', sans-serif;
    }

    .booking-details-card {
        background: white;
        padding: 40px;
        border-radius: 30px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.05);
        width: 100%;
        max-width: 650px;
        border: 1px solid #E2E8F0;
        box-sizing: border-box;
    }

    .booking-details-card h2 {
        font-family: 'Outfit', sans-serif;
        font-size: 2.2rem;
        font-weight: 900;
        color: #0F172A;
        margin-bottom: 25px;
        text-align: left;
    }

    .summary-box-cab {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 30px;
    }

    .summary-grid-cab {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .summary-grid-cab div {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .summary-grid-cab div.full-width {
        grid-column: span 2;
    }

    .summary-label-cab {
        font-size: 0.8rem;
        font-weight: 700;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-val-cab {
        font-size: 1rem;
        font-weight: 800;
        color: #0F172A;
        font-family: 'Outfit', sans-serif;
    }

    .form-group-cab {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 20px;
    }

    .form-group-cab label {
        font-size: 0.8rem;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-group-cab input,
    .form-group-cab select {
        height: 50px;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        width: 100%;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
        background-color: white;
    }

    .form-group-cab input:focus,
    .form-group-cab select:focus {
        border-color: #FF4D01;
        box-shadow: 0 0 0 3px rgba(255, 77, 1, 0.1);
        outline: none;
    }

    .form-group-cab select {
        -webkit-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230f172a' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 15px center !important;
        background-size: 14px !important;
        padding-right: 40px !important;
        cursor: pointer;
    }

    /* Checkout Calculations Styles */
    .bill-section-cab {
        border-top: 1px dashed #CBD5E1;
        padding-top: 20px;
        margin-top: 25px;
        margin-bottom: 25px;
    }

    .bill-title-cab {
        font-weight: 800;
        font-size: 0.85rem;
        color: #0F172A;
        text-transform: uppercase;
        display: block;
        margin-bottom: 15px;
        letter-spacing: 0.5px;
    }

    .bill-row-cab {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        font-weight: 600;
        color: #64748B;
        margin-bottom: 10px;
    }

    .bill-row-cab.total-cab {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0F172A;
        border-top: 1.5px solid #E2E8F0;
        padding-top: 12px;
        margin-top: 12px;
        font-family: 'Outfit', sans-serif;
    }

    .coupon-section-cab {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .coupon-section-cab input {
        height: 42px;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 0.85rem;
        font-weight: 700;
        flex: 1;
        box-sizing: border-box;
    }

    .coupon-section-cab button {
        background: #0F172A;
        color: white;
        border: none;
        padding: 0 20px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: 0.2s;
    }

    .coupon-section-cab button:hover {
        background: #1E293B;
    }

    .btn-submit-booking-cab {
        background: #FF4D01 !important;
        color: white !important;
        height: 55px;
        border-radius: 14px;
        border: none;
        cursor: pointer;
        font-size: 1.05rem;
        font-weight: 800;
        transition: transform 0.2s, box-shadow 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
        width: 100%;
        font-family: 'Outfit', sans-serif;
        box-shadow: 0 6px 15px rgba(255, 77, 1, 0.25);
    }

    .btn-submit-booking-cab:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255, 77, 1, 0.35);
    }

    @media (max-width: 600px) {
        .summary-grid-cab {
            grid-template-columns: 1fr;
        }
        .summary-grid-cab div.full-width {
            grid-column: span 1;
        }
        .booking-details-card {
            padding: 25px;
        }
    }
</style>

<section class="booking-details-section">
    <div class="booking-details-card">
        <h2>Passenger Details</h2>

        <div class="summary-box-cab">
            <div class="summary-grid-cab">
                <div>
                    <span class="summary-label-cab">Selected Cab:</span>
                    <span class="summary-val-cab"><?php echo htmlspecialchars($cab['cab_name']); ?></span>
                </div>
                <div>
                    <span class="summary-label-cab">Agency:</span>
                    <span class="summary-val-cab"><?php echo htmlspecialchars($cab['agency_name']); ?></span>
                </div>
                <div class="full-width">
                    <span class="summary-label-cab">Route:</span>
                    <span class="summary-val-cab"><?php echo $pickup_location . ' &rarr; ' . $drop_location; ?></span>
                </div>
                <div>
                    <span class="summary-label-cab">Trip Type:</span>
                    <span class="summary-val-cab"><?php echo $trip_type === 'roundtrip' ? 'Round Trip' : 'One Way'; ?></span>
                </div>
                <div class="full-width">
                    <span class="summary-label-cab">Pickup Schedule:</span>
                    <span class="summary-val-cab">
                        <?php 
                        $sched = $pickup_date . ' at ' . $pickup_time;
                        if ($trip_type === 'roundtrip' && $return_date) {
                            $sched .= ' (Return: ' . $return_date . ')';
                        }
                        echo htmlspecialchars($sched);
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <form action="process_booking.php" method="POST" id="cab_booking_form">
            <!-- Hidden backend inputs -->
            <input type="hidden" name="cab_id" value="<?php echo htmlspecialchars($cab_id); ?>">
            <input type="hidden" name="agency_id" value="<?php echo htmlspecialchars($cab['agency_id']); ?>">
            <input type="hidden" name="pickup_city" value="<?php echo htmlspecialchars($cab['city']); ?>">
            <input type="hidden" name="pickup_location" value="<?php echo htmlspecialchars($pickup_location); ?>">
            <input type="hidden" name="drop_location" value="<?php echo htmlspecialchars($drop_location); ?>">
            <input type="hidden" name="trip_type" value="<?php echo htmlspecialchars($trip_type); ?>">
            <input type="hidden" name="booking_date" value="<?php echo htmlspecialchars($pickup_date); ?>">
            <input type="hidden" name="return_date" value="<?php echo htmlspecialchars($return_date); ?>">
            <input type="hidden" name="pick_up_time" value="<?php echo htmlspecialchars($pickup_time); ?>">
            <input type="hidden" name="est_distance" value="<?php echo $est_distance; ?>">
            <input type="hidden" name="total_price" id="form_final_price" value="">

            <!-- Passenger details -->
            <div class="form-group-cab">
                <label>Passenger Full Name</label>
                <input type="text" name="passenger_name" placeholder="Enter passenger's name" required value="<?php echo $prefill_name; ?>">
            </div>

            <div class="form-group-cab">
                <label>Passenger Mobile</label>
                <input type="tel" name="passenger_phone" placeholder="10-digit mobile number" required pattern="[0-9]{10}" value="<?php echo $prefill_phone; ?>">
            </div>

            <div class="form-group-cab">
                <label>Passenger Email</label>
                <input type="email" name="passenger_email" placeholder="Enter email address" required value="<?php echo $prefill_email; ?>">
            </div>

            <div class="form-group-cab">
                <label>Identity Proof</label>
                <select name="id_proof" required>
                    <option value="Aadhar Card" selected>Aadhar Card</option>
                    <option value="Driving License">Driving License</option>
                    <option value="Voter ID">Voter ID</option>
                </select>
            </div>

            <!-- Billing Section -->
            <div class="bill-section-cab">
                <span class="bill-title-cab">Fare Breakdown</span>
                <div class="bill-row-cab">
                    <span>Base Rent (₹<?php echo $rate; ?>/km x <?php echo $est_distance; ?> km)</span>
                    <span>₹<?php echo number_format($base_rent, 2); ?></span>
                </div>
                <div class="bill-row-cab">
                    <span>GST & Booking Fee (5%)</span>
                    <span>₹<?php echo number_format($base_rent * 0.05, 2); ?></span>
                </div>
                <div class="bill-row-cab" id="coupon_row" style="display:none; color: #2c7a7b;">
                    <span>Coupon Discount (APP50)</span>
                    <span>- ₹50.00</span>
                </div>
                <div class="bill-row-cab total-cab">
                    <span>Total Bill (Cash Payment)</span>
                    <span id="bill_total_val">₹<?php echo number_format($base_rent * 1.05, 2); ?></span>
                </div>
            </div>

            <!-- Coupon Input -->
            <div class="coupon-section-cab">
                <input type="text" id="coupon_code" placeholder="Coupon Code (e.g. APP50)">
                <button type="button" onclick="applyCoupon();">Apply</button>
            </div>
            <p id="coupon_msg" style="font-size:0.8rem; margin-top: 8px; display:none;"></p>

            <div style="margin-top: 25px;">
                <button type="submit" class="btn-submit-booking-cab">Confirm Booking & Ride</button>
            </div>
        </form>
    </div>
</section>

<script>
    const baseRent = <?php echo $base_rent; ?>;
    let isCouponApplied = false;

    function applyCoupon() {
        const code = document.getElementById('coupon_code').value.trim();
        const msgEl = document.getElementById('coupon_msg');
        const couponRow = document.getElementById('coupon_row');
        
        if (code === 'APP50') {
            isCouponApplied = true;
            couponRow.style.display = 'flex';
            msgEl.style.display = 'block';
            msgEl.style.color = '#2c7a7b';
            msgEl.innerHTML = '<i class="fas fa-check-circle"></i> Coupon applied: ₹50 discount!';
        } else if (code === '') {
            isCouponApplied = false;
            couponRow.style.display = 'none';
            msgEl.style.display = 'none';
        } else {
            isCouponApplied = false;
            couponRow.style.display = 'none';
            msgEl.style.display = 'block';
            msgEl.style.color = '#ff3c00';
            msgEl.innerHTML = '<i class="fas fa-times-circle"></i> Invalid coupon code.';
        }
        recalcBill();
    }

    function recalcBill() {
        const gst = baseRent * 0.05;
        let total = baseRent + gst;
        if (isCouponApplied) {
            total = Math.max(0, total - 50);
        }
        
        document.getElementById('bill_total_val').innerText = '₹' + total.toFixed(2);
        document.getElementById('form_final_price').value = total.toFixed(2);
    }

    // Initialize form total price
    recalcBill();
</script>

<?php require '../includes/footer.php'; ?>

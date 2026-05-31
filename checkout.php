<?php
session_start();
include('includes/db.php');
require 'includes/header.php';

// POST data from booking-details.php (only update if fields are present)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['customer_name'])) {
    $_SESSION['customer_name'] = $_POST['customer_name'];
    $_SESSION['customer_phone'] = $_POST['customer_phone'];
    $_SESSION['id_proof'] = $_POST['id_proof'];
    $_SESSION['pick_up_time'] = $_POST['pick_up_time'];
    $_SESSION['drop_off_time'] = $_POST['drop_off_time'];
}

$bike_id = $_SESSION['selected_bike_id'] ?? '';
$start_date = $_SESSION['booking_start'] ?? '';
$end_date = $_SESSION['booking_end'] ?? '';

// Calculate days
$d1 = new DateTime($start_date);
$d2 = new DateTime($end_date);
$interval = $d1->diff($d2);
$days = $interval->days + 1; // Include both dates

// Fetch bike details
$query = "SELECT * FROM abike WHERE id = '$bike_id'";
$result = $conn->query($query);
$bike = $result->fetch_assoc();

if (!$bike) {
    header("Location: index.php");
    exit;
}

$base_price = $bike['price_per_day'] * $days;

// GST Logic: 18% gst on 20% amount of final amount
// Final Amount (Before tax) = $base_price
$commissionable_amount = $base_price * 0.20;
$gst = $commissionable_amount * 0.18;
$total_before_discount = $base_price + $gst;

// Coupon logic
$discount = 0;
$applied_coupon = "";
if (isset($_POST['coupon']) && $_POST['coupon'] === 'APP50') {
    $discount = 50;
    $applied_coupon = "APP50";
}

$final_amount = $total_before_discount - $discount;
?>

<link rel="stylesheet" href="css/style.css">
<style>
    .checkout-section {
        padding: 8rem 5% 5rem;
        background: #fdfdfd;
        min-height: 90vh;
    }
    .checkout-container {
        max-width: 1000px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 3rem;
        margin-left: -17px;
    }
    .checkout-card {
        background: white;
        padding: 2.5rem;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
    }
    .checkout-card h3 {
        font-family: var(--font-heading);
        font-size: 1.6rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #f9f9f9;
        padding-bottom: 1rem;
    }
    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        color: #555;
    }
    .summary-item.total {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px solid #f9f9f9;
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--dark-color);
    }
    .summary-item b {
        color: var(--dark-color);
    }
    
    .coupon-box {
        margin-top: 2rem;
        display: flex;
        gap: 10px;
    }
    .coupon-box input {
        flex: 1;
        padding: 0.8rem;
        border: 1.5px solid #eee;
        border-radius: 8px;
    }
    .coupon-box button {
        padding: 0.8rem 1.5rem;
        background: #f0f0f0;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .coupon-box button:hover {
        background: #ddd;
    }
    
    .payment-placeholder {
        margin-top: 3rem;
        padding: 2rem;
        background: #f9f9f9;
        border-radius: 16px;
        text-align: center;
        border: 2px solid #eee;
    }
    .payment-placeholder i {
        font-size: 2.5rem;
        color: #ccc;
        margin-bottom: 1rem;
    }
    
    .final-pay-btn {
        width: 100%;
        padding: 1.2rem;
        background: var(--accent);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 800;
        font-size: 1.2rem;
        margin-top: 1.5rem;
        cursor: pointer;
    }
    
    .badge-success {
        background: #e6fffa;
        color: #2c7a7b;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    @media (max-width: 900px) {
        .checkout-container {
            grid-template-columns: 1fr;
        }
        .checkout-card {
            padding: 1.5rem;
        }
    }
</style>


<section class="checkout-section">
    <div class="section-header centered">
        <h2 class="main-heading">Checkout</h2>
        <p style="color: #666;">Confirm your booking and proceed to payment</p>
    </div>

    <div class="checkout-container">
        <!-- Left Side: Summary -->
        <div class="checkout-card">
            <h3>Booking Details</h3>
            <div class="summary-item">
                <span>Rider Name</span>
                <b><?php echo htmlspecialchars($_SESSION['customer_name'] ?? ''); ?></b>
            </div>
            <div class="summary-item">
                <span>Phone</span>
                <b><?php echo htmlspecialchars($_SESSION['customer_phone'] ?? ''); ?></b>
            </div>
            <div class="summary-item">
                <span>ID Proof</span>
                <b><?php echo htmlspecialchars($_SESSION['id_proof'] ?? ''); ?></b>
            </div>
            <div class="summary-item">
                <span>Bike</span>
                <b><?php echo htmlspecialchars($bike['model']); ?></b>
            </div>
            <div class="summary-item">
                <span>Duration</span>
                <b><?php echo $days; ?> Days</b>
            </div>
            <div class="summary-item">
                <span>Dates</span>
                <b><?php echo htmlspecialchars($start_date); ?> to <?php echo htmlspecialchars($end_date); ?></b>
            </div>

            <div class="payment-placeholder">
                <i class="fas fa-credit-card"></i>
                <p>Payment Gateway Integration Ready</p>
                <span style="font-size: 0.8rem; color: #999;">Powered by Razorpay (Future Integration)</span>
            </div>
        </div>

        <!-- Right Side: Pricing -->
        <div class="checkout-card">
            <h3>Price Summary</h3>
            <div class="summary-item">
                <span>Base Rent (₹<?php echo $bike['price_per_day']; ?> x <?php echo $days; ?>)</span>
                <span>₹<?php echo number_format($base_price, 2); ?></span>
            </div>
            <div class="summary-item">
                <span>GST (18% of 20% fee)</span>
                <span>₹<?php echo number_format($gst, 2); ?></span>
            </div>
            
            <?php if ($discount > 0): ?>
                <div class="summary-item" style="color: #2c7a7b;">
                    <span>Discount (<?php echo $applied_coupon; ?>)</span>
                    <span>- ₹<?php echo number_format($discount, 2); ?></span>
                </div>
            <?php endif; ?>

            <div class="summary-item total">
                <span>Final Total</span>
                <span>₹<?php echo number_format($final_amount, 2); ?></span>
            </div>

            <?php 
                $invalid_coupon = false;
                if (isset($_POST['coupon']) && $_POST['coupon'] !== '' && $_POST['coupon'] !== 'APP50') {
                    $invalid_coupon = true;
                }
            ?>
            <form method="POST" class="coupon-box">
                <input type="text" name="coupon" placeholder="Coupon Code (e.g. APP50)" value="<?php echo htmlspecialchars($applied_coupon); ?>">
                <button type="submit">Apply</button>
            </form>
            
            <?php if ($applied_coupon === "APP50"): ?>
                <p style="font-size: 0.8rem; color: #2c7a7b; margin-top: 5px;">
                    <i class="fas fa-check-circle"></i> Coupon applied successfully!
                </p>
            <?php elseif ($invalid_coupon): ?>
                <p style="font-size: 0.8rem; color: #ff3c00; margin-top: 5px;">
                    <i class="fas fa-times-circle"></i> Invalid coupon code.
                </p>
            <?php endif; ?>

            <?php
            // Store final calculation in session for insertion
            $_SESSION['final_amount'] = $final_amount;
            $_SESSION['days'] = $days;
            ?>
            <form action="process_booking.php" method="POST">
                <button type="submit" class="final-pay-btn">Proceed with Booking</button>
            </form>

        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>

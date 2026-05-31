<?php
session_start();
require '../includes/header.php';

// Connect to the database
$mysqli = new mysqli("localhost", "root", "", "dwk");

// Check for connection errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token");
}

// Check if ID is set
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (!is_string($id) || empty($id)) {
        die("Invalid ID");
    }
    
    // Fetch bike details (including price per day)
    $bikeQuery = "SELECT model, price_per_day FROM abike WHERE id = '$id'";
    $bikeResult = $mysqli->query($bikeQuery);
    if ($bikeResult->num_rows === 0) {
        die("Bike not found.");
    }
    $bike = $bikeResult->fetch_assoc();
} else {
    die("ID is required.");
}

if (!isset($_SESSION['user_id'])) {
    die("Please login first.");
}

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $mysqli->real_escape_string($_POST['name']);
    $age = $mysqli->real_escape_string($_POST['age']);
    $idProof = $mysqli->real_escape_string($_POST['idProof']);
    $booking_date = $mysqli->real_escape_string($_POST['booking_date']);
    $return_date = $mysqli->real_escape_string($_POST['return_date']);
    $pick_up_time = $mysqli->real_escape_string($_POST['pick_up_time']);
    $drop_off_time = $mysqli->real_escape_string($_POST['drop_off_time']);
    $mobile = $mysqli->real_escape_string($_POST['mobile']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $paymentMethod = $mysqli->real_escape_string($_POST['paymentMethod']);
    $totalDays = (strtotime($return_date) - strtotime($booking_date)) / (60 * 60 * 24) + 1;
    $totalPrice = $totalDays * $bike['price_per_day'];

    // Insert into bookings table
    $query = "INSERT INTO abookings (user_id, bike_id, booking_date, return_date, total_price, name, age, idproof, mobile, email, paymentMethod,pick_up_time, drop_off_time) 
              VALUES ('{$_SESSION['user_id']}', '$id', '$booking_date', '$return_date', '$totalPrice', '$name', '$age', '$idProof', '$mobile', '$email', '$paymentMethod', '$pick_up_time', '$drop_off_time')";

    if ($mysqli->query($query)) {
        echo "<script>alert('Bike is successfully booked for you'); window.location.href='../index.php';</script>";
    } else {
        echo "Error: " . $query . "<br>" . $mysqli->error;
    }
}

$mysqli->close();
?>



<!-- HTML structure remains the same -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>
    <link rel="icon" href="logo/urbanride1.ico"  sizes="1080x1080" type="image/x-icon">
    <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

:root {
    --primary: #FF6B6B;
    --secondary: #4ECDC4;
    --dark: #2D3436;
    --light: #F9F9F9;
    --gradient: linear-gradient(135deg, 
        rgba(255, 107, 0, 0.9) 0%, 
        rgba(178, 66, 5, 0.85) 70%, 
        rgba(0, 0, 0, 0.95) 100%
    );
    --glass-bg: rgba(255, 255, 255, 0.95);
    --glass-border: rgba(255, 255, 255, 0.15);
    --text-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
    display: flex;
    flex-direction: column; /* Stack header, form, footer */
    align-items: center;
    padding: 0;
    margin: 0;
    animation: gradientShift 15s ease infinite;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.booking-form {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
    margin: 120px 20px 60px; /* More top margin for header, bottom for footer */
    animation: formEntrance 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards;
}

@keyframes formEntrance {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.booking-form h2 {
    color:   rgba(255, 107, 0, 0.9);
    text-align: center;
    margin-bottom: 30px;
    font-size: 2.2em;
    text-shadow: var(--text-shadow);
}

.form-group {
    margin-bottom: 25px;
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: var(--dark);
    font-weight: 600;
    text-shadow: var(--text-shadow);
}

input, select {
    width: 100%;
    padding: 12px 20px;
    border: 1px outset  rgba(178, 66, 5, 0.85)  ;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.8);
    transition: all 0.3s ease;
    font-size: 16px;
}

input:focus, select:focus {
    outline: none;
    border:  3px inset  rgba(178, 66, 5, 0.85) !important ;

}
/*
input[type="radio"] {
  accent-color: var(--primary);
  transform: scale(1.2)!important;
  margin-top:-25px!important; 
  margin-left: -10px !important;
}*/
.payment-method-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.radio-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 200px;
}

input[type="radio"] {
    accent-color: var(--primary);
    transform: scale(1.4);
    margin-left:-10px;
    margin-top:-5px;
    order: 2; /* Move radio button to right side */
}

.date-time-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

button[type="submit"] {
    background: var(--gradient);
    color: white;
    border: none;
    padding: 16px 40px;
    border-radius: 50px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: block;
    margin: 25px auto 0;
    transform: scale(1);
}

button[type="submit"]:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
}

button[type="submit"]::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        transparent,
        rgba(255,255,255,0.2),
        transparent
    );
    transform: rotate(45deg);
    animation: shine 1.5s infinite;
}

@keyframes shine {
    0% { left: -50%; }
    100% { left: 150%; }
}

#total_price {
    font-weight: 700;
    color: var(--primary);
    font-size: 1.2em;
    text-align: center;
}

.compliance-notice {
    text-align: center;
    margin-top: 20px;
    font-size: 0.9em;
    color: var(--dark);
}

.compliance-notice a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
}

.compliance-notice a:hover {
    text-decoration: underline;
}

/* Stagger animations for form groups */
.form-group:nth-child(1) { animation-delay: 0.2s; }
.form-group:nth-child(2) { animation-delay: 0.5s; }
.form-group:nth-child(3) { animation-delay: 0.6s; }
.form-group:nth-child(4) { animation-delay: 0.8s; }
.form-group:nth-child(5) { animation-delay: 1.0s; }
.form-group:nth-child(6) { animation-delay: 1.1s; }
.form-group:nth-child(7) { animation-delay: 1.2s; }
.form-group:nth-child(8) { animation-delay: 1.3s; }
.form-group:nth-child(9) { animation-delay: 1.4s; }

@media (max-width: 768px) {
    .booking-form {
        padding: 25px;
    }
    
    .date-time-group {
        grid-template-columns: 1fr;
    }
    
    button[type="submit"] {
        width: 100%;
        padding: 14px 20px;
    }
}
</style> 
</head>
<!-- Spinner Start --
<div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">

        </div>
    </div>
    Spinner End -->

<div class="booking-form">
    <h2>Booking Form</h2>
    <form id="bookingForm" method="POST" action="">        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="age">Age:</label>
            <input type="number" id="age" name="age"  min="18" placeholder="above 18 years" required >
        </div>
        <div class="form-group">
            <label for="idProof">ID Proof:</label>
            <select id="idProof" name="idProof" required>
                <option value="Driving License">Driving License</option>
                <option value="Aadhar Card">Aadhar Card</option>
                <option value="Voter ID">Voter ID</option>
                <option value="Passport">Passport</option>
            </select>
        </div>
        <div class="form-group">
            <label for="date">Start Date:</label>
            <input type="date" id="booking_date" name="booking_date" required>
        </div>
        <div class="form-group">
        <label for="pick_up_time">Pick-Up Time:</label>
    <select id="pick_up_time" name="pick_up_time" required>
        <option value="09:00">9:00 AM</option>
        <option value="10:00">10:00 AM</option>
        <option value="11:00">11:00 AM</option>
        <option value="12:00">12:00 PM</option>
        <option value="13:00">1:00 PM</option>
        <option value="14:00">2:00 PM</option>
        <option value="15:00">3:00 PM</option>
        <option value="16:00">4:00 PM</option>
        <option value="17:00">5:00 PM</option>
        <option value="18:00">6:00 PM</option>
        <option value="19:00">7:00 PM</option>
        <option value="20:00">8:00 PM</option>
        <option value="21:00">9:00 PM</option>
    </select><br>
    </div>
        <div class="form-group">
            <label for="date">Return Date:</label>
            <input type="date" id="return_date" name="return_date" required>
        </div>
        <div class="form-group">
        <label for="drop_off_time">Drop-Off Time:</label>
    <select id="drop_off_time" name="drop_off_time" required>
        <option value="09:00">9:00 AM</option>
        <option value="10:00">10:00 AM</option>
        <option value="11:00">11:00 AM</option>
        <option value="12:00">12:00 PM</option>
        <option value="13:00">1:00 PM</option>
        <option value="14:00">2:00 PM</option>
        <option value="15:00">3:00 PM</option>
        <option value="16:00">4:00 PM</option>
        <option value="17:00">5:00 PM</option>
        <option value="18:00">6:00 PM</option>
        <option value="19:00">7:00 PM</option>
        <option value="20:00">8:00 PM</option>
        <option value="21:00">9:00 PM</option>
    </select><br>

    </div>
        <div class="form-group">
    <label for="mobile">Mobile:</label>
    <input type="text" id="mobile" name="mobile" required 
           pattern="\d{10}" 
           maxlength="10" 
           title="Please enter exactly 10 digits">
</div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
    <label>Payment Method:</label>
    <div class="payment-method-group">
        <div class="radio-item">
            <input type="radio" id="cash" name="paymentMethod" value="Cash" required>
            <label for="cash">Cash</label>
        </div>
    </div>
</div>


        <!--        <div class="Tform-group">
    <input type="radio" id="T&C" name="Terms&Condition" value="T&C" required>
    <span class="radio-label">
        <a href="../terms.html" target="_blank">Terms & Condition</a>
    </span>
</div>-->
<div class="form-group">
            <label for="total_price">Total Price:</label>
            <input type="text" id="total_price" name="total_price" readonly>
        </div>
       <!-- <div class="form-group">
        <form action="payment_page.php" method="POST">
   Pass the booking_id -->
    <div class="compliance-notice">
    <p>By proceeding, you agree to our <a href="../terms.html">Terms of Service</a> </p>
</div>
<div class="form-group">
        
    <!-- Pass the booking_id -->
    <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
    <button type="submit">Book</button>

</form>
</div>
</div>
<?php require '../includes/footer.php'?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dateInput = document.getElementById("booking_date");

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    const today = new Date();
    const todayStr = formatDate(today);

    const maxDate = new Date(today);
    maxDate.setDate(today.getDate() + 3);
    const maxDateStr = formatDate(maxDate);

    dateInput.setAttribute("min", todayStr);
    dateInput.setAttribute("max", maxDateStr);
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dateInput = document.getElementById("return_date");

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    const today = new Date();
    const todayStr = formatDate(today);

    const maxDate = new Date(today);
    maxDate.setDate(today.getDate() + 3);
    const maxDateStr = formatDate(maxDate);

    dateInput.setAttribute("min", todayStr);
    dateInput.setAttribute("max", maxDateStr);
});
</script>

<script>
    const bookingDateInput = document.getElementById("booking_date");
    const returnDateInput = document.getElementById("return_date");
    const totalPriceInput = document.getElementById("total_price");

    const pricePerDay = <?php echo $bike['price_per_day']; ?>;

    function calculatePrice() {
        const bookingDate = new Date(bookingDateInput.value);
        const returnDate = new Date(returnDateInput.value);

        if (bookingDate && returnDate && bookingDate <= returnDate) {
            const diffTime = Math.abs(returnDate - bookingDate);
            const totalDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Include today
            const totalPrice = totalDays * pricePerDay;
            totalPriceInput.value = totalPrice;
        } else {
            totalPriceInput.value = "";
        }
    }

    bookingDateInput.addEventListener("change", calculatePrice);
    returnDateInput.addEventListener("change", calculatePrice);
</script>


</body>

</html>


<?php
session_start();

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
    $bikeQuery = "SELECT model, price_per_day, status FROM abike WHERE id = '$id'";
    $bikeResult = $mysqli->query($bikeQuery);
    if ($bikeResult->num_rows === 0) {
        die("Bike not found.");
    }
    $bike = $bikeResult->fetch_assoc();

    // Check if bike is already booked
    if ($bike['status'] === 'booked') {
        die("This bike is already booked.");
    }
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


      // Mock payment process
      require_once 'payment_p.php'; // Include a mock payment gateway
      $payment_gateway = new PaymentGateway();
      $payment_status = $payment_gateway->processPayment($payment_method, $name, $mobile, $email);
    // Insert into bookings table
    if ($payment_status) {
    $query = "INSERT INTO abookings (user_id, bike_id, booking_date, return_date, total_price, name, age, idproof, mobile, email, paymentMethod,pick_up_time, drop_off_time) 
              VALUES ('{$_SESSION['user_id']}', '$id', '$booking_date', '$return_date', '$totalPrice', '$name', '$age', '$idProof', '$mobile', '$email', '$paymentMethod', '$pick_up_time', '$drop_off_time')";

    if ($mysqli->query($query)) {
        // Update bike status to 'booked'
        $updateStatusQuery = "UPDATE abike SET status = '' WHERE id = '$id'";
        if ($mysqli->query($updateStatusQuery)) {
            echo "<script>alert('Pay  to confirm booking.');</script>";
        } else {
            echo "Error updating bike status: " . $mysqli->error;
        }
    } }else {
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
    <script src="https://pay.google.com/gp/p/js/pay.js" type="text/javascript"></script>
    <style>
        /* Custom styles for the page */
        .google-pay-button {
            width: 300px;
            height: 50px;
        }
    </style>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 120vh;
            margin: 0;
            background-color: #f2f2f2;
        }
        .booking-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            height: 100%;
          
        }
        .booking-form h2 {
            text-align: center;
            color: #ff3c00;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group input[type="email"],
        .form-group select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .form-group input:focus {
             border: 2px solid #ff3c00;
             outline: none;
        }
        .form-group input[type="radio"] {
            margin-right: 5px;
        }
        .form-group .radio-label {
            color: #222;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #ff3c00;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        .Tform-group {
            text-decoration: underline;
            color:#fb6031; 
            cursor:pointer;
            font-size: 11px;
            padding-bottom:10px;
            padding-top:-20px;
        }
        .form-group button:hover {
            background-color: #e63600;
        }
    </style>
</head>
<body>

<div class="booking-form">
    <h2>Booking Form</h2>
    <form id="bookingForm" method="POST" action="">
        <div class="form-group">
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
            <input type="email" id="email" name="email">
        </div>
        <div class="form-group">
            <label>Payment Method:</label>
            <input type="radio" id="cash" name="paymentMethod" value="Cash" unchecked>
            <span class="radio-label">Cash</span>
        </div>
        <div class="Tform-group">
    <input type="radio" id="T&C" name="Terms&Condition" value="T&C">
    <span class="radio-label">
        <a href="terms.html" target="_blank">Terms & Condition</a>
    </span>
</div>
<div class="form-group">
            <label for="total_price">Total Price:</label>
            <input type="text" id="total_price" name="total_price" readonly>
        </div>
        <div id="googlePayButton"></div>

        <div class="form-group">
  <!-- Pass booking_id as a hidden input -->
  <input type="hidden" name="name" value="<?php echo $name; ?>">
    <button type="submit" formaction="payment_p.php">Book</button>
        </div>
    </form>
</div>

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
<!--                                          PAYMENT                            -->
<script type="text/javascript">
    // Initialize Google Pay Client
    const paymentsClient = new google.payments.api.PaymentsClient({ environment: 'SANDBOX' });

    // Create Google Pay button
    const button = paymentsClient.createButton({
        onClick: onGooglePayClicked,
        allowedPaymentMethods: ['CARD', 'PAYPAL'],
        buttonColor: 'black',
        buttonType: 'short',
    });

    document.getElementById('googlePayButton').appendChild(button);

    function onGooglePayClicked() {
        // Fetch the total price from the form
        const totalPrice = document.getElementById('total_price').value;

        // Ensure the price is properly formatted (2 decimal places)
        const formattedTotalPrice = parseFloat(totalPrice).toFixed(2);

        // Prepare the Google Pay payment request
        const paymentDataRequest = {
            apiVersion: 2,
            apiVersionMinor: 0,
            allowedPaymentMethods: [
                {
                    type: 'CARD',
                    parameters: {
                        allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                        allowedCardNetworks: ['MASTERCARD', 'VISA']
                    },
                    tokenizationSpecification: {
                        type: 'PAYMENT_GATEWAY',
                        parameters: {
                            gateway: 'stripe',  // Your gateway name (example)
                            gatewayMerchantId: 'YOUR_STRIPE_GATEWAY_MERCHANT_ID'  // Placeholder; replace with your actual gateway merchant ID
                        }
                    }
                }
            ],
            merchantInfo: {
                merchantName: 'rbyk',  // Your merchant name
                merchantId: ' BCR2DN4T26SOJK3X'  // Your Google Merchant ID
            },
            transactionInfo: {
                totalPriceStatus: 'FINAL',
                totalPrice: 'formattedTotalPrice',  // Dynamic total price from form
                currencyCode: 'INR'  // Your currency code
            }
        };

        // Call Google Pay API
        paymentsClient.loadPaymentData(paymentDataRequest)
            .then(function(paymentData) {
                processPayment(paymentData);
            })
            .catch(function(err) {
                console.error("Payment failed:", err);  // Log detailed error information
                alert('Payment failed! Please try again.');
            });
    }

    // Process the payment
    function processPayment(paymentData) {
        const formData = new FormData();
        formData.append('paymentData', JSON.stringify(paymentData));

        fetch('process_payment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert("Payment successful!");
        })
        .catch(error => {
            console.error("Error processing payment", error);
        });
    }
</script>

</body>
</html>


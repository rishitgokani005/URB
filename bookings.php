<?php
session_start();
require 'includes/header.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dwk";

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("An error occurred. Please try again later.");
}

// Fetch the current user's ID from the session
$user_id = $_SESSION['user_id'];

// Logic for dynamic status will be handled in the loop for better precision
// We will still keep the basic update for DB integrity if needed, 
// but the display will be calculated on-the-fly.

// Fetch user bookings with bike details
$sql = "SELECT a.booking_id, b.model, b.color, b.address, b.image,
               a.booking_date, a.return_date, a.booking_status,
               a.pick_up_time, a.drop_off_time
        FROM abookings a
        JOIN abike b ON a.bike_id = b.id
        WHERE a.user_id = ?
        ORDER BY a.booking_date DESC, a.pick_up_time DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Preparation failed: " . $conn->error);
    die("An error occurred. Please try again later.");
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="logo/urbanride1.ico" sizes="1080x1080" type="image/x-icon">
    <title>My Bookings</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100;300;400;500;600;700;800;900&display=swap');

        :root {
            --primary: #FF4D01;
            --primary-light: #FFEDE5;
            --text-main: #0F172A;
            --text-sub: #64748B;
            --bg-body: #F8FAFC;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Outfit', sans-serif;
            color: var(--text-main);
        }

        .bookings-wrapper {
            max-width: 1000px;
            margin: 120px auto 40px;
            padding: 0 20px;
        }

        .page-header {
            margin-bottom: 40px;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            color: var(--text-main);
        }

        .bookings-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
        }

        .premium-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: var(--card-shadow);
            border: 1px solid #E2E8F0;
            display: flex;
            gap: 30px;
            position: relative;
            overflow: hidden;
            transition: var(--transition);
        }

        .premium-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .card-bike-icon {
            width: 140px;
            height: 100px;
            background: var(--primary-light);
            border-radius: 16px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .card-bike-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-content {
            flex-grow: 1;
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .card-top h3 {
            font-size: 1.4rem;
            font-weight: 700;
        }

        .status-pill {
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pill[data-status="pending"] { background: #FEF3C7; color: #92400E; }
        .status-pill[data-status="ongoing"] { background: #DCFCE7; color: #166534; }
        .status-pill[data-status="completed"] { background: #F1F5F9; color: #475569; }
        .status-pill[data-status="cancelled"] { background: #FEE2E2; color: #991B1B; }

        .card-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
            color: var(--text-sub);
        }

        .detail-item i {
            color: var(--primary);
            width: 16px;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px dashed #E2E8F0;
        }

        .booking-id-text {
            font-family: monospace;
            background: #F1F5F9;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            color: #475569;
        }

        .btn-cancel-trigger {
            background: #FEE2E2;
            color: #EF4444;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-cancel-trigger:hover {
            background: #EF4444;
            color: white;
        }

        /* Slide-up Container Styles */
        #cancellation-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 3000;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #cancellation-container {
            position: fixed;
            bottom: -100%;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 600px;
            height: 90vh; /* Little space at top */
            background: white;
            border-radius: 40px 40px 0 0;
            z-index: 3001;
            padding: 40px 30px;
            box-shadow: 0 -10px 40px rgba(0,0,0,0.2);
            transition: bottom 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        #cancellation-container.active {
            bottom: 0;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .modal-header h2 {
            font-size: 1.8rem;
            font-weight: 800;
        }

        .close-modal {
            background: #F1F5F9;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-sub);
        }

        .reason-list {
            list-style: none;
            flex-grow: 1;
            overflow-y: auto;
        }

        .reason-item {
            margin-bottom: 12px;
        }

        .reason-item input[type="radio"] {
            display: none;
        }

        .reason-label {
            display: block;
            padding: 16px 20px;
            background: #F8FAFC;
            border: 2px solid #F1F5F9;
            border-radius: 16px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
        }

        .reason-item input[type="radio"]:checked + .reason-label {
            border-color: var(--primary);
            background: var(--primary-light);
            color: var(--primary);
        }

        #other-reason-text {
            width: 100%;
            margin-top: 15px;
            padding: 15px;
            border-radius: 15px;
            border: 2px solid #E2E8F0;
            display: none;
            resize: none;
            font-family: inherit;
        }

        .btn-confirm-cancel {
            background: var(--primary);
            color: white;
            border: none;
            width: 100%;
            padding: 18px;
            border-radius: 18px;
            font-weight: 700;
            font-size: 1.1rem;
            margin-top: 20px;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(255, 77, 1, 0.2);
        }

        .message {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 4000;
        }

        @media (max-width: 768px) {
            .premium-card {
                flex-direction: column;
                gap: 20px;
                padding: 20px;
            }

            .card-bike-icon {
                width: 100%;
                height: 150px;
            }

            .card-details {
                grid-template-columns: 1fr;
            }

            .card-top h3 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="bookings-wrapper">
        <div class="page-header">
            <p style="color: red;">While collecting the bike please bring your orginal pan card/driving license</p>
        </div>

        <div class="bookings-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        // Dynamic Status Calculation
                        $current_time = new DateTime();
                        $start_time = new DateTime($row['booking_date'] . ' ' . $row['pick_up_time']);
                        $end_time = new DateTime($row['return_date'] . ' ' . $row['drop_off_time']);
                        
                        $display_status = $row['booking_status'];
                        if ($row['booking_status'] === 'active') {
                            if ($current_time < $start_time) $display_status = 'pending';
                            elseif ($current_time <= $end_time) $display_status = 'ongoing';
                            else $display_status = 'completed';
                        }
                    ?>
                    <div class="premium-card">
                        <div class="card-bike-icon">
                            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['model']) ?>">
                        </div>
                        <div class="card-content">
                            <div class="card-top">
                                <h3><?= htmlspecialchars($row['model']) ?></h3>
                                <span class="status-pill" data-status="<?= $display_status ?>">
                                    <?= ucfirst($display_status) ?>
                                </span>
                            </div>

                            <div class="card-details">
                                <div class="detail-item">
                                    <i class="fas fa-palette"></i>
                                    <span>Color: <b><?= ucfirst(htmlspecialchars($row['color'])) ?></b></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Pick Up: <b><?= htmlspecialchars($row['address']) ?></b></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Booking: <b><?= date('d M Y, h:i A', strtotime($row['booking_date'] . ' ' . $row['pick_up_time'])) ?></b></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>Return: <b><?= date('d M Y, h:i A', strtotime($row['return_date'] . ' ' . $row['drop_off_time'])) ?></b></span>
                                </div>
                            </div>

                            <div class="card-footer">
                                <span class="booking-id-text">ID: <?= htmlspecialchars($row['booking_id']) ?></span>
                                <?php if ($display_status === 'pending'): ?>
                                    <button class="btn-cancel-trigger" onclick="openCancelModal('<?= $row['booking_id'] ?>')">
                                        Cancel Bike
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state" style="padding: 100px; text-align: center; background: white; border-radius: 24px;">
                    <i class="fas fa-calendar-xmark" style="font-size: 4rem; color: #E2E8F0; margin-bottom: 20px; display: block;"></i>
                    <p style="font-size: 1.2rem; color: var(--text-sub);">You haven't made any bookings yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cancellation Modal Overlay -->
    <div id="cancellation-overlay" onclick="closeCancelModal()"></div>

    <!-- Cancellation Container (Slide-up) -->
    <div id="cancellation-container">
        <div class="modal-header">
            <h2>Cancel Booking</h2>
            <div class="close-modal" onclick="closeCancelModal()">
                <i class="fas fa-times"></i>
            </div>
        </div>
        
        <p style="color: var(--text-sub); margin-bottom: 20px;">Please tell us why you are cancelling your booking.</p>
        
        <form id="cancelForm" action="backend/new_cancel.php" method="POST">
            <input type="hidden" id="cancel_booking_id" name="booking_id" value="">
            
            <div class="reason-list">
                <div class="reason-item">
                    <input type="radio" name="reason" id="r1" value="Change of plans" required>
                    <label for="r1" class="reason-label">Change of plans</label>
                </div>
                <div class="reason-item">
                    <input type="radio" name="reason" id="r2" value="Found a better option">
                    <label for="r2" class="reason-label">Found a better option</label>
                </div>
                <div class="reason-item">
                    <input type="radio" name="reason" id="r3" value="Too expensive">
                    <label for="r3" class="reason-label">Too expensive</label>
                </div>
                <div class="reason-item">
                    <input type="radio" name="reason" id="r4" value="Booked by mistake">
                    <label for="r4" class="reason-label">Booked by mistake</label>
                </div>
                <div class="reason-item">
                    <input type="radio" name="reason" id="r5" value="Travel cancelled">
                    <label for="r5" class="reason-label">Travel cancelled</label>
                </div>
                <div class="reason-item">
                    <input type="radio" name="reason" id="other" value="Other">
                    <label for="other" class="reason-label">Other</label>
                </div>
                <textarea id="other-reason-text" name="other_reason" placeholder="Please specify your reason..." rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn-confirm-cancel">Confirm Cancellation</button>
        </form>
    </div>

    <script>
        function openCancelModal(bookingId) {
            document.getElementById('cancel_booking_id').value = bookingId;
            const overlay = document.getElementById('cancellation-overlay');
            const container = document.getElementById('cancellation-container');
            
            overlay.style.display = 'block';
            setTimeout(() => {
                overlay.style.opacity = '1';
                container.classList.add('active');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeCancelModal() {
            const overlay = document.getElementById('cancellation-overlay');
            const container = document.getElementById('cancellation-container');
            
            overlay.style.opacity = '0';
            container.classList.remove('active');
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 500);
            document.body.style.overflow = 'auto';
        }

        // Show/hide other reason text field
        const reasonRadios = document.querySelectorAll('input[name="reason"]');
        const otherText = document.getElementById('other-reason-text');
        
        reasonRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                if (radio.id === 'other') {
                    otherText.style.display = 'block';
                    otherText.setAttribute('required', 'required');
                } else {
                    otherText.style.display = 'none';
                    otherText.removeAttribute('required');
                }
            });
        });
    </script>
    <?php
    $stmt->close();
    $conn->close();
    ?>
    <?php require 'includes/footer.php'; ?>
</body>

</html>
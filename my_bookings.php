<?php
require 'includes/header.php';
session_start();

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

// Update booking_status for expired bookings
$update_query = "UPDATE abookings
                 SET booking_status = 'Completed'
                 WHERE booking_status = 'Active' AND return_date <= CURDATE()";

if (!$conn->query($update_query)) {
    error_log("Update query failed: " . $conn->error);
}

// Fetch user bookings with bike details
$sql = "SELECT a.booking_id, b.model, b.color, b.address, 
               a.booking_date, a.return_date, a.booking_status
        FROM abookings a
        JOIN abike b ON a.bike_id = b.id
        WHERE a.user_id = ?
        ORDER BY a.booking_date DESC";

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
   <!-- <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4ff;
            color: #333;
        }

        h2 {
            text-align: center;
            margin: 20px 0;
            color: #555;
        }

        /*.message {
            background-color: #ffeb3b;
            color: #333;
            padding: 10px;
            text-align: center;
            margin: 10px auto;
            border-radius: 8px;
            max-width: 600px;
        }
*/  .intro-message {
    text-align: center;
            margin: 15px auto;
            color: #333;
            font-size: 1.5rem;
            max-width: 450px;
            height: 100px;
            padding: 10px ;
            background-color: #f7b42c;
            background-image: linear-gradient(315deg, #f7b42c 0%, #fc575e 74%);

           /* background: linear-gradient(135deg, #ff7eb3, #ff758c);*/
            color: white;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            font-weight: bold;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .intro-message:before {
            content: '🛵';
            font-size: 100px;
            position: absolute;
            top: -3px;
            right: -18px;
            z-index: -1;
            opacity: 0.4;
        }
        .intro-message:after {
        content: '🌍';
        font-size: 100px;
        position: absolute;
        top: -50px;
        left: -30px;
        z-index: -1;
        opacity: 0.4;
    }

        .bookings-container {
            display: flex;
            flex-direction: column;
            gap: 30px;
            justify-content: center;
            padding: 20px;
        }

        .booking-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 350px;
            padding: 20px;
            transition: transform 0.3s ease;
            border-left: 10px solid #ff3c00;
        }

        .booking-card:hover {
            transform: scale(1.05);
        }

        .booking-card h3 {
            margin: 0 0 10px;
        }

        .booking-card p {
            margin: 5px 0;
        }

        .btn-cancel {
            background-color: #f44336;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }

        .btn-cancel:hover {
            background-color: #d32f2f;
        }

        @media (max-width: 768px) {
            .bookings-container {
                flex-direction: column; /* Cards are displayed vertically */
                align-items: center;
                gap: 10px;
            }

            .booking-card {
                max-width: 90%;
                margin-bottom: 10px;
            }
        }
    </style>-->
    <style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 40px 20px;
}


.bookings-container {
    max-width: 1200px;
    margin: 80px auto 40px; /* Increased top margin */
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    padding: 20px;
}

.booking-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    padding: 35px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
}

.booking-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.booking-card h3 {
    color: #2c3e50;
    margin-bottom: 15px;
    font-size: 0.5em;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
}

.booking-card p {
    margin: 10px 0;
    color: #555;
    font-size: 0.95em;
}

.booking-card p strong {
    color: #2c3e50;
    font-weight: 600;
}

.btn-cancel {
    background: #ff4757;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 15px;
    font-size: 0.9em;
}

.btn-cancel:hover {
    background: #ff6b81;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 107, 129, 0.3);
}

.message {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #4CAF50;
    color: white;
    padding: 15px 30px;
    border-radius: 30px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideInTop 0.5s ease-out, fadeOut 1s ease 3s forwards;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

.message::before {
    content: '✓';
    font-size: 1.2em;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInTop {
    from {
        opacity: 0;
        transform: translate(-50%, -100%);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0);
    }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}

/* Staggered animations for booking cards */
.booking-card:nth-child(1) { animation-delay: 0.3s; }
.booking-card:nth-child(2) { animation-delay: 0.5s; }
.booking-card:nth-child(3) { animation-delay: 0.7s; }
.booking-card:nth-child(n+4) { animation-delay: 0.8s; }

/* Status badges */
.booking-card p:last-child {
    margin-top: 15px;
    padding: 6px 12px;
    background: #4CAF50;
    color: white;
    border-radius: 20px;
    display: inline-block;
    font-size: 0.85em;
}

.booking-card p:last-child[data-status="completed"] {
    background: #95a5a6;
}

@media (max-width: 768px) {
    .bookings-container {
        grid-template-columns: 1fr;
    }
    
    .booking-card {
        margin: 0 10px;
    }
    
    .intro-message {
        margin: 10px;
        font-size: 1em;
    }
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 50px 20px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    color: #666;
    font-size: 1.2em;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}
</style>
</head>
<body>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="message"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="bookings-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="booking-card">
                    <h3>Booking ID: <?= htmlspecialchars($row['booking_id']) ?></h3>
                    <p><strong>Pick Up:</strong> <?= htmlspecialchars($row['address']) ?></p>
                    <p><strong>Model:</strong> <?= htmlspecialchars($row['model']) ?></p>
                    <p><strong>Color:</strong> <?= htmlspecialchars($row['color']) ?></p>
                    <p><strong>Booking Date:</strong> <?= htmlspecialchars($row['booking_date']) ?></p>
                    <p><strong>Return Date:</strong> <?= htmlspecialchars($row['return_date']) ?></p>
                    <p><strong>Status:</strong> <span data-status="<?= strtolower(htmlspecialchars($row['booking_status'])) ?>"><?= ucfirst(htmlspecialchars($row['booking_status'])) ?></span></p>

                    <?php if (strtolower($row['booking_status']) === 'active'): ?>
                        <form action="new_cancel.php" method="POST" style="display: inline;">
                            <input type="hidden" name="booking_id" value="<?= htmlspecialchars($row['booking_id']) ?>">
                            <button type="submit" class="btn-cancel" onclick="return confirm('Are you sure you want to cancel this booking?');">Cancel Booking</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-state">You have no bookings yet.</p>
        <?php endif; ?>
    </div>
    <?php
    $stmt->close();
    $conn->close();
    ?>
    <?php require 'includes/footer.php';?>
</body>
</html>

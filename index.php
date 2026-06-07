<?php
session_start();
include('includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $redirect_url = isset($_POST['redirect_url']) ? $_POST['redirect_url'] : 'index.php';

    if (isset($_POST['register'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $password = $_POST['password'];

        $check_email_query = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $check_email_query);

        if (mysqli_num_rows($result) > 0) {
            $separator = (strpos($redirect_url, '?') !== false) ? '&' : '?';
            header("Location: " . $redirect_url . $separator . "error=email_exists");
            exit();
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO users (name, email, phone, password) VALUES ('$name', '$email', '$phone', '$hashed_password')";

            if (mysqli_query($conn, $insert_query)) {
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = $email;
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                $_SESSION['user_role'] = 'user';
                session_write_close();
                header('Location: ' . $redirect_url);
                exit();
            } else {
                $separator = (strpos($redirect_url, '?') !== false) ? '&' : '?';
                header("Location: " . $redirect_url . $separator . "error=register_failed");
                exit();
            }
        }
    } else {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];

        $query = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['email'] = $email;
                $_SESSION['user_role'] = $row['role'];
                $_SESSION['user_id'] = $row['user_id'];

                session_write_close();
                if ($row['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: $redirect_url");
                }
                exit;
            } else {
                $separator = (strpos($redirect_url, '?') !== false) ? '&' : '?';
                header("Location: " . $redirect_url . $separator . "error=incorrect_password");
                exit();
            }
        } else {
            $separator = (strpos($redirect_url, '?') !== false) ? '&' : '?';
            header("Location: " . $redirect_url . $separator . "error=invalid_email");
            exit();
        }
    }
}

require 'includes/header.php';
?>
<link rel="stylesheet" href="css/style.css">
<style>
/* Dynamic Sliding Search Switcher & Transitions */
.search-float, .lite-search-compact {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    padding: 0 !important;
    transition: none !important;
}

/* Form cards inside switcher containers */
.search-float .search-form, .lite-search-compact .search-form {
    padding: 25px;
    border-radius: 30px;
    box-shadow: var(--shadow-xl);
    border: 1px solid transparent;
    transition: background-color 0.4s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.4s ease, box-shadow 0.4s ease;
}

.lite-search-compact .search-form {
    padding: 25px 30px;
    border-radius: 20px;
    box-shadow: var(--shadow-lg);
}

/* White background when Bike is active */
.search-float.mode-bike .search-form, .lite-search-compact.mode-bike .search-form {
    background: white !important;
    color: var(--text-main) !important;
    border-color: var(--border) !important;
}

/* White background when Cab is active */
.search-float.mode-cab .search-form, .lite-search-compact.mode-cab .search-form {
    background: white !important;
    color: var(--text-main) !important;
    border-color: var(--border) !important;
}

/* Tab Switcher Wrapper */
.search-tabs-container {
    display: flex;
    justify-content: flex-start;
    margin-bottom: 0 !important;
    width: 100%;
}

.search-float .search-tabs-container {
    padding-left: 44px;
}

.lite-search-compact .search-tabs-container {
    padding-left: 49px;
}

.search-tabs {
    display: flex;
    background: #E2E8F0;
    padding: 4px;
    border-radius: 50px;
    position: relative;
    width: 260px;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
    border: 1px solid rgba(0,0,0,0.05);
    transition: background-color 0.4s ease, border-color 0.4s ease;
}

.tab-btn {
    flex: 1;
    border: none;
    background: transparent;
    padding: 10px 0;
    font-weight: 700;
    font-size: 0.9rem;
    color: #475569;
    cursor: pointer;
    z-index: 2;
    transition: color 0.3s ease;
    font-family: var(--font-heading);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.tab-btn.active {
    color: white;
}

.tab-slider {
    position: absolute;
    top: 4px;
    bottom: 4px;
    left: 4px;
    width: calc(50% - 4px);
    background: var(--primary); /* Orange active tab */
    border-radius: 50px;
    z-index: 1;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.3s ease;
    box-shadow: 0 2px 6px rgba(255, 77, 1, 0.3);
}

/* Cab selection state on switcher */
.search-tabs.cab-active .tab-slider {
    transform: translateX(100%);
    background: #0F172A; /* Dark slate active slider */
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.3);
}

.search-tabs.cab-active .tab-btn:first-child {
    color: #475569;
}

.search-tabs.cab-active .tab-btn:nth-child(2) {
    color: white;
}

/* Styling for forms when container background is White (mode-bike and mode-cab) */
.search-float.mode-bike select,
.search-float.mode-bike input,
.lite-search-compact.mode-bike select,
.lite-search-compact.mode-bike input,
.search-float.mode-cab select,
.search-float.mode-cab input,
.lite-search-compact.mode-cab select,
.lite-search-compact.mode-cab input {
    background-color: white !important;
    border-color: var(--border) !important;
    color: var(--text-main) !important;
}

.search-float.mode-bike .search-item label,
.lite-search-compact.mode-bike .search-item label,
.search-float.mode-cab .search-item label,
.lite-search-compact.mode-cab .search-item label {
    color: var(--text-sub) !important;
}

.search-float.mode-bike select option,
.lite-search-compact.mode-bike select option,
.search-float.mode-cab select option,
.lite-search-compact.mode-cab select option {
    background-color: white;
    color: var(--text-main);
}

.search-float.mode-bike select:focus,
.search-float.mode-bike input:focus,
.lite-search-compact.mode-bike select:focus,
.lite-search-compact.mode-bike input:focus,
.search-float.mode-cab select:focus,
.search-float.mode-cab input:focus,
.lite-search-compact.mode-cab select:focus,
.lite-search-compact.mode-cab input:focus {
    border-color: var(--primary) !important;
    background-color: white !important;
}

.search-float.mode-bike .search-go,
.lite-search-compact.mode-bike .search-go {
    background: var(--primary) !important;
    color: white !important;
}

.search-float.mode-bike .search-go:hover,
.lite-search-compact.mode-bike .search-go:hover {
    background: #e04400 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(255, 77, 1, 0.3) !important;
}

/* Blending Switcher when container is Orange (mode-bike and mode-cab) */
.search-float.mode-bike .search-tabs,
.lite-search-compact.mode-bike .search-tabs,
.search-float.mode-cab .search-tabs,
.lite-search-compact.mode-cab .search-tabs {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1) !important;
}

.search-float.mode-bike .tab-btn,
.lite-search-compact.mode-bike .tab-btn,
.search-float.mode-cab .tab-btn,
.lite-search-compact.mode-cab .tab-btn {
    color: white !important;
}

.search-float.mode-bike .tab-btn.active,
.lite-search-compact.mode-bike .tab-btn.active,
.search-float.mode-cab .tab-btn.active,
.lite-search-compact.mode-cab .tab-btn.active {
    color: var(--primary) !important;
}

.search-float.mode-bike .tab-slider,
.lite-search-compact.mode-bike .tab-slider,
.search-float.mode-cab .tab-slider,
.lite-search-compact.mode-cab .tab-slider {
    background: white !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1) !important;
}

/* Cab Form Row 1 & 2 styles (matches taxi-booking.php exactly) */
.search-row-1 {
    display: grid !important;
    grid-template-columns: 1.2fr 2fr 1.5fr 1.5fr; /* 4 columns for One Way */
    gap: 15px !important;
    margin-bottom: 20px !important;
    align-items: center !important;
    width: 100% !important;
}

.search-row-1.round-trip-active {
    grid-template-columns: 1fr 1.5fr 1.2fr 1.2fr 1.2fr !important; /* 5 columns for Round Trip */
}

.search-row-2 {
    display: grid !important;
    grid-template-columns: 2.2fr 2.2fr 1.4fr 1.4fr;
    gap: 15px !important;
    align-items: center !important;
    width: 100% !important;
}

.distance-container {
    display: flex !important;
    flex-direction: column !important;
    gap: 6px !important;
}

.distance-header {
    display: flex !important;
    justify-content: space-between !important;
}

.distance-header span {
    font-size: 0.72rem !important;
    font-weight: 800 !important;
    color: #475569 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

.distance-slider-wrap {
    display: flex !important;
    align-items: center !important;
    gap: 15px !important;
    border: 1px solid #E2E8F0 !important;
    padding: 0 16px !important;
    border-radius: 12px !important;
    background: white !important;
    height: 50px !important;
    box-sizing: border-box !important;
}

.distance-slider-wrap input[type="range"] {
    -webkit-appearance: none;
    appearance: none;
    flex: 1 !important;
    width: 100% !important;
    background: transparent !important; /* Make parent input transparent */
    outline: none !important;
    cursor: pointer !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* Track Styling */
.distance-slider-wrap input[type="range"]::-webkit-slider-runnable-track {
    width: 100% !important;
    height: 6px !important;
    background: #334155 !important; /* Dark gray track */
    border-radius: 10px !important;
    border: none !important;
}

.distance-slider-wrap input[type="range"]::-moz-range-track {
    width: 100% !important;
    height: 6px !important;
    background: #334155 !important;
    border-radius: 10px !important;
    border: none !important;
}

/* Chrome, Safari, Opera, Edge Thumb Styling */
.distance-slider-wrap input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px !important;
    height: 20px !important;
    border-radius: 50% !important;
    background: #FF4D01 !important; /* Orange thumb */
    cursor: pointer !important;
    box-shadow: 0 1px 4px rgba(0,0,0,0.3) !important;
    border: none !important;
    margin-top: -7px !important; /* Center on 6px track: (6/2) - (20/2) = -7 */
    transition: transform 0.1s ease !important;
}

.distance-slider-wrap input[type="range"]::-webkit-slider-thumb:hover {
    transform: scale(1.15) !important;
}

/* Firefox Thumb Styling */
.distance-slider-wrap input[type="range"]::-moz-range-thumb {
    width: 20px !important;
    height: 20px !important;
    border-radius: 50% !important;
    background: #FF4D01 !important; /* Orange thumb */
    cursor: pointer !important;
    box-shadow: 0 1px 4px rgba(0,0,0,0.3) !important;
    border: none !important;
    transition: transform 0.1s ease !important;
}

.distance-slider-wrap input[type="range"]::-moz-range-thumb:hover {
    transform: scale(1.15) !important;
}

.distance-val-stack {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    line-height: 1.1 !important;
    min-width: 45px !important;
}

.distance-num {
    font-size: 1.15rem !important;
    font-weight: 800 !important;
    color: #FF4D01 !important;
    font-family: 'Outfit', sans-serif !important;
}

.distance-unit {
    font-size: 0.75rem !important;
    font-weight: 800 !important;
    color: #FF4D01 !important;
    text-transform: uppercase !important;
    font-family: 'Outfit', sans-serif !important;
}

.search-go-taxi {
    background: #FF4D01 !important;
    color: white !important;
    height: 50px !important;
    border-radius: 12px !important;
    border: none !important;
    cursor: pointer !important;
    font-size: 0.95rem !important;
    font-weight: 800 !important;
    transition: transform 0.2s, box-shadow 0.2s !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    justify-content: center !important;
    width: 100% !important;
    font-family: 'Outfit', sans-serif !important;
}

.search-go-taxi:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 16px rgba(255, 77, 1, 0.3) !important;
}

/* Custom dropdown and pickers in Bike & Cab modes */
.search-float.mode-bike select,
.lite-search-compact.mode-bike select,
.search-float.mode-cab select,
.lite-search-compact.mode-cab select {
    -webkit-appearance: none !important;
    appearance: none !important;
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230f172a' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 15px center !important;
    background-size: 14px !important;
    padding-right: 40px !important;
}

.search-float.mode-bike input[type="date"],
.lite-search-compact.mode-bike input[type="date"],
.search-float.mode-cab input[type="date"],
.lite-search-compact.mode-cab input[type="date"] {
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230f172a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 15px center !important;
    background-size: 16px !important;
    padding-right: 40px !important;
}

.search-float.mode-bike input[type="time"],
.lite-search-compact.mode-bike input[type="time"],
.search-float.mode-cab input[type="time"],
.lite-search-compact.mode-cab input[type="time"] {
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230f172a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'/%3E%3Cpolyline points='12 6 12 12 16 14'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 15px center !important;
    background-size: 16px !important;
    padding-right: 40px !important;
}

/* Make calendar and time pickers trigger on clicking anywhere in the input */
input[type="date"], input[type="time"] {
    position: relative !important;
}

input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    opacity: 0 !important;
    cursor: pointer !important;
}

.search-float.mode-cab select,
.search-float.mode-cab input,
.lite-search-compact.mode-cab select,
.lite-search-compact.mode-cab input {
    background-color: white !important;
    border: 1px solid #E2E8F0 !important;
    border-radius: 12px !important;
    color: #0f172a !important;
    font-weight: 700 !important;
    font-size: 0.95rem !important;
    height: 50px !important;
    padding: 12px 16px !important;
    box-sizing: border-box !important;
    transition: border-color 0.2s, box-shadow 0.2s !important;
}

.search-float.mode-cab select:focus,
.search-float.mode-cab input:focus,
.lite-search-compact.mode-cab select:focus,
.lite-search-compact.mode-cab input:focus {
    border-color: #FF4D01 !important;
    box-shadow: 0 0 0 3px rgba(255, 77, 1, 0.1) !important;
}

.search-float.mode-cab .search-item label,
.lite-search-compact.mode-cab .search-item label {
    color: #475569 !important;
    font-size: 0.72rem !important;
    font-weight: 800 !important;
    letter-spacing: 0.5px !important;
    text-transform: uppercase !important;
    margin-bottom: 6px !important;
}

@media (max-width: 992px) {
    .search-row-1, .search-row-1.round-trip-active {
        grid-template-columns: 1fr 1fr !important;
    }
    .search-row-2 {
        grid-template-columns: 1fr 1fr !important;
    }
}

@media (max-width: 768px) {
    .search-grid,
    .search-row-1,
    .search-row-1.round-trip-active,
    .search-row-2 {
        grid-template-columns: 1fr !important;
        gap: 15px !important;
    }
}
</style>


<!-- Premium Hero Section -->
<section class="hero">
    <img src="images/home_3.png" alt="Hero Background" class="hero-bg-img">
    <div class="hero-overlay"></div>
    <div class="hero-container">
        <div class="hero-content reveal">
            <h1>Explore India <span>On Two Wheels.</span></h1>
            <p>Premium bike rentals for the modern explorer. From city commutes to mountain trails, we have the perfect
                ride for every journey.</p>
        </div>
    </div>
</section>


<!-- Redesigned Search Section (Featuring Sliding Switcher & Dynamic Background Transitions) -->
<div id="search-section" class="search-float reveal mode-bike">
    <?php
    $city_query = "SELECT DISTINCT city FROM abike WHERE city IS NOT NULL AND city != ''";
    $city_result = $conn->query($city_query);
    ?>

    <!-- Sliding Tab Switcher -->
    <div class="search-tabs-container">
        <div class="search-tabs" id="top-search-tabs">
            <button type="button" class="tab-btn active" onclick="switchSearchMode('bike', 'top')"><i class="fas fa-motorcycle"></i> Bikes</button>
            <button type="button" class="tab-btn" onclick="switchSearchMode('cab', 'top')"><i class="fas fa-taxi"></i> Cabs</button>
            <div class="tab-slider"></div>
        </div>
    </div>

    <!-- Bike Form -->
    <form action="agencies.php" method="GET" onsubmit="return validateDates('top')" id="top-bike-form" class="search-form bike-form">
        <div class="search-grid">
            <div class="search-item">
                <label>City Location</label>
                <select name="city" required>
                    <option value="" disabled selected>Choose Your City</option>
                    <?php 
                    $city_result->data_seek(0);
                    while ($row = $city_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['city']); ?>">
                            <?php echo htmlspecialchars($row['city']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="search-item">
                <label>Pick-up Date</label>
                <input type="date" name="start_date" id="top_start_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="search-item">
                <label>Return Date</label>
                <input type="date" name="end_date" id="top_end_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <button type="submit" class="search-go">Search Bikes <i class="fas fa-arrow-right"></i></button>
        </div>
    </form>

    <!-- Cab Form -->
    <form action="Taxi-Booking/agencies-cab.php" method="GET" onsubmit="return validateCabDates('top')" id="top-cab-form" class="search-form cab-form" style="display: none;">
        <input type="hidden" name="search_submitted" value="1">
        
        <!-- Row 1: Trip configuration -->
        <div class="search-row-1">
            <div class="search-item">
                <label>Trip Type</label>
                <select name="trip_type" id="top_trip_type" onchange="toggleCabReturnDate('top');">
                    <option value="oneway" selected>One Way</option>
                    <option value="roundtrip">Round Trip</option>
                </select>
            </div>
            
            <div class="search-item">
                <label>City Location</label>
                <select name="pickup_city" required>
                    <option value="" disabled selected>Choose City</option>
                    <?php 
                    $city_result->data_seek(0);
                    while ($row = $city_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($row['city']); ?>">
                            <?php echo htmlspecialchars($row['city']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="search-item">
                <label>Pick-up Date</label>
                <input type="date" name="pickup_date" id="top_cab_pickup_date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="search-item">
                <label>Pick-up Time</label>
                <input type="time" name="pickup_time" required>
            </div>

            <div class="search-item" id="top_return_date_wrapper" style="display: none;">
                <label>Return Date</label>
                <input type="date" name="return_date" id="top_cab_return_date" min="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>

        <!-- Row 2: Locations & Distance Estimation -->
        <div class="search-row-2">
            <div class="search-item">
                <label>Pick-up Address</label>
                <input type="text" name="pickup_location" placeholder="Enter pickup address" required>
            </div>

            <div class="search-item">
                <label>Drop-off Address</label>
                <input type="text" name="drop_location" placeholder="Enter destination address" required>
            </div>

            <div class="distance-container">
                <div class="distance-header">
                    <span>Est. Distance</span>
                </div>
                <div class="distance-slider-wrap">
                    <input type="range" name="est_distance" id="top_est_distance" min="10" max="600" step="5" value="100" oninput="updateTopDistanceVal(this.value);">
                    <div class="distance-val-stack">
                        <span class="distance-num" id="top_distance_display_num">100</span>
                        <span class="distance-unit">km</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="search-go-taxi">Search Cabs <i class="fas fa-arrow-right"></i></button>
        </div>
    </form>
</div>

<!-- Updated Timeline -->
<section id="how-it-works" class="how-it-works reveal">
    <div class="section-title">
        <span class="sub-heading">Guidelines</span>
        <h2>How to Start Your Ride</h2>
    </div>

    <div class="process-timeline">
        <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <h3>01. Select City & Dates</h3>
                <p>Choose your destination and the duration of your trip to see available partners.</p>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <h3>02. Pick Your Machine</h3>
                <p>Browse our extensive fleet and choose the bike that fits your style and budget.</p>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <h3>03. Quick KYC</h3>
                <p>Choose a valid driving license and ID proof for verification.</p>
            </div>
        </div>
        <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <h3>04. Ride Away</h3>
                <p>Collect your keys from the designated center and begin your adventure.</p>
            </div>
        </div>
    </div>
</section>

<!-- Hotel Advertisement Section -->
<section id="hotels" class="featured-fleet reveal">
    <div class="section-title">
        <span class="sub-heading">Partner Stays</span>
        <h2>Premium Hotel Partners</h2>
    </div>
    <div class="fleet-grid">
        <div class="hotel-card">
            <div class="hotel-images">
                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                    class="hotel-img-main">
                <img
                    src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80">
                <img
                    src="https://images.unsplash.com/photo-1540518614846-7eded433c457?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80">
            </div>
            <div class="hotel-info">
                <h3>The Grand Dwarka</h3>
                <p>Luxury suites with breathtaking sea views. 15% OFF for UrbanRide explorers.</p>
            </div>
        </div>
        <div class="hotel-card">
            <div class="hotel-images">
                <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                    class="hotel-img-main">
                <img
                    src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80">
                <img
                    src="https://images.unsplash.com/photo-1564501049412-61c2a3083791?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80">
            </div>
            <div class="hotel-info">
                <h3>Heritage Inn</h3>
                <p>A blend of tradition and comfort in the heart of the city.</p>
            </div>
        </div>
        <div class="hotel-card">
            <div class="hotel-images">
                <img src="https://images.unsplash.com/photo-1517840901100-8179e982ad4e?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                    class="hotel-img-main">
                <img
                    src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80">
                <img
                    src="https://images.unsplash.com/photo-1551882547-ff43c638f614?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80">
            </div>
            <div class="hotel-info">
                <h3>Oceanic Breeze</h3>
                <p>Coastal tranquility meets modern design. Exclusive rider lounge available.</p>
            </div>
        </div>
</section>

<!-- Taxi Booking CTA Section -->
<section class="reveal" style="padding: 80px 7%; background: white;">
    <div style="
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 60%, #0F172A 100%);
        border-radius: 32px;
        padding: 60px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 40px;
        position: relative;
        overflow: hidden;
    ">
        <!-- Glow accent -->
        <div style="position:absolute; top:-60px; right:120px; width:300px; height:300px; background:radial-gradient(circle, rgba(255,77,1,0.25) 0%, transparent 70%); pointer-events:none;"></div>

        <!-- Left: Text & CTA -->
        <div style="flex:1; position:relative; z-index:2;">
            <span style="background:rgba(255,77,1,0.15); color:#FF4D01; padding:6px 16px; border-radius:50px; font-size:0.8rem; font-weight:700; letter-spacing:1px; text-transform:uppercase;">New Service</span>
            <h2 style="font-family:'Outfit',sans-serif; font-size:2.8rem; font-weight:900; color:white; margin:18px 0 14px; line-height:1.2;">
                Need a Cab? <br><span style="color:#FF4D01;">We've Got You Covered.</span>
            </h2>
            <p style="color:#94A3B8; font-size:1rem; line-height:1.7; max-width:480px; margin-bottom:30px;">
                Book a comfortable ride for your entire group. Choose from 4, 7, or 11-seater cabs with AC or non-AC options — starting from just ₹10/km.
            </p>

            <!-- Feature Badges -->
            <div style="display:flex; flex-wrap:wrap; gap:12px; margin-bottom:35px;">
                <span style="background:rgba(255,255,255,0.08); color:#CBD5E1; padding:8px 18px; border-radius:50px; font-size:0.85rem; font-weight:600; display:flex; align-items:center; gap:6px;">
                    <i class="fas fa-snowflake" style="color:#60A5FA;"></i> AC &amp; Non-AC
                </span>
                <span style="background:rgba(255,255,255,0.08); color:#CBD5E1; padding:8px 18px; border-radius:50px; font-size:0.85rem; font-weight:600; display:flex; align-items:center; gap:6px;">
                    <i class="fas fa-users" style="color:#34D399;"></i> 4 / 7 / 11 Seater
                </span>
                <span style="background:rgba(255,255,255,0.08); color:#CBD5E1; padding:8px 18px; border-radius:50px; font-size:0.85rem; font-weight:600; display:flex; align-items:center; gap:6px;">
                    <i class="fas fa-location-dot" style="color:#F87171;"></i> Doorstep Pickup
                </span>
                <span style="background:rgba(255,255,255,0.08); color:#CBD5E1; padding:8px 18px; border-radius:50px; font-size:0.85rem; font-weight:600; display:flex; align-items:center; gap:6px;">
                    <i class="fas fa-road" style="color:#FBBF24;"></i> One-way &amp; Round Trip
                </span>
            </div>

            <a href="Taxi-Booking/taxi-booking.php" style="
                display: inline-flex; align-items: center; gap: 10px;
                background: #FF4D01; color: white;
                padding: 16px 36px; border-radius: 50px;
                font-family: 'Outfit', sans-serif; font-size: 1rem; font-weight: 700;
                text-decoration: none;
                box-shadow: 0 10px 30px rgba(255,77,1,0.35);
                transition: transform 0.2s, box-shadow 0.2s;
            " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 35px rgba(255,77,1,0.45)'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(255,77,1,0.35)'">
                Book a Cab Now <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <!-- Right: Animated Cab Icon -->
        <div style="flex-shrink:0; position:relative; z-index:2; text-align:center; display:flex; flex-direction:column; align-items:center; gap:20px;">
            <div style="
                width: 160px; height: 160px;
                background: rgba(255,77,1,0.12);
                border-radius: 50%;
                display: flex; align-items:center; justify-content:center;
                animation: floatCab 3s ease-in-out infinite;
                border: 1px solid rgba(255,77,1,0.2);
            ">
                <i class="fas fa-taxi" style="font-size:5rem; color:#FF4D01;"></i>
            </div>
            <div style="display:flex; gap:15px;">
                <div style="text-align:center;">
                    <div style="font-family:'Outfit',sans-serif; font-size:1.6rem; font-weight:900; color:white;">₹10</div>
                    <div style="color:#64748B; font-size:0.75rem;">Per KM</div>
                </div>
                <div style="width:1px; background:rgba(255,255,255,0.1);"></div>
                <div style="text-align:center;">
                    <div style="font-family:'Outfit',sans-serif; font-size:1.6rem; font-weight:900; color:white;">24/7</div>
                    <div style="color:#64748B; font-size:0.75rem;">Available</div>
                </div>
                <div style="width:1px; background:rgba(255,255,255,0.1);"></div>
                <div style="text-align:center;">
                    <div style="font-family:'Outfit',sans-serif; font-size:1.6rem; font-weight:900; color:white;">11</div>
                    <div style="color:#64748B; font-size:0.75rem;">Max Seats</div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
@keyframes floatCab {
    0%, 100% { transform: translateY(0px); }
    50%       { transform: translateY(-12px); }
}
</style>

<!-- Lite Search Section (GOOO!! Section with Sliding Switcher) -->
<section class="lite-search reveal" style="padding: 100px 7% 60px; background: var(--bg-sub);">
    <div class="section-title">
        <span class="sub-heading">Quick Find</span>
        <h2 style="font-family: var(--font-heading); font-size: 2rem; margin-bottom: 30px;">GOOO!!</h2>
    </div>
    
    <div id="lite-search-container-box" class="lite-search-compact reveal mode-bike" style="max-width: 950px; margin: 0 auto; padding: 25px 30px;">
        <!-- Sliding Tab Switcher -->
        <div class="search-tabs-container">
            <div class="search-tabs" id="bottom-search-tabs">
                <button type="button" class="tab-btn active" onclick="switchSearchMode('bike', 'bottom')"><i class="fas fa-motorcycle"></i> Bikes</button>
                <button type="button" class="tab-btn" onclick="switchSearchMode('cab', 'bottom')"><i class="fas fa-taxi"></i> Cabs</button>
                <div class="tab-slider"></div>
            </div>
        </div>

        <!-- Bike Form -->
        <form action="agencies.php" method="GET" onsubmit="return validateDates('bottom')" id="bottom-bike-form" class="search-form bike-form">
            <div class="search-grid">
                <div class="search-item">
                    <label>Location</label>
                    <select name="city" required>
                        <option value="" disabled selected>Select City</option>
                        <?php 
                        $city_result->data_seek(0);
                        while ($row = $city_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['city']); ?>">
                                <?php echo htmlspecialchars($row['city']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="search-item">
                    <label>Pickup Date</label>
                    <input type="date" name="start_date" id="bottom_start_date" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="search-item">
                    <label>Return Date</label>
                    <input type="date" name="end_date" id="bottom_end_date" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <button type="submit" class="search-go">Search Bikes</button>
            </div>
        </form>

        <!-- Cab Form -->
        <form action="Taxi-Booking/agencies-cab.php" method="GET" onsubmit="return validateCabDates('bottom')" id="bottom-cab-form" class="search-form cab-form" style="display: none;">
            <input type="hidden" name="search_submitted" value="1">
            
            <!-- Row 1: Trip configuration -->
            <div class="search-row-1">
                <div class="search-item">
                    <label>Trip Type</label>
                    <select name="trip_type" id="bottom_trip_type" onchange="toggleCabReturnDate('bottom');">
                        <option value="oneway" selected>One Way</option>
                        <option value="roundtrip">Round Trip</option>
                    </select>
                </div>
                
                <div class="search-item">
                    <label>City Location</label>
                    <select name="pickup_city" required>
                        <option value="" disabled selected>Choose City</option>
                        <?php 
                        $city_result->data_seek(0);
                        while ($row = $city_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['city']); ?>">
                                <?php echo htmlspecialchars($row['city']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="search-item">
                    <label>Pick-up Date</label>
                    <input type="date" name="pickup_date" id="bottom_cab_pickup_date" min="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="search-item">
                    <label>Pick-up Time</label>
                    <input type="time" name="pickup_time" required>
                </div>

                <div class="search-item" id="bottom_return_date_wrapper" style="display: none;">
                    <label>Return Date</label>
                    <input type="date" name="return_date" id="bottom_cab_return_date" min="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>

            <!-- Row 2: Locations & Distance Estimation -->
            <div class="search-row-2">
                <div class="search-item">
                    <label>Pick-up Address</label>
                    <input type="text" name="pickup_location" placeholder="Enter pickup address" required>
                </div>

                <div class="search-item">
                    <label>Drop-off Address</label>
                    <input type="text" name="drop_location" placeholder="Enter destination address" required>
                </div>

                <div class="distance-container">
                    <div class="distance-header">
                        <span>Est. Distance</span>
                    </div>
                    <div class="distance-slider-wrap">
                        <input type="range" name="est_distance" id="bottom_est_distance" min="10" max="600" step="5" value="100" oninput="updateBottomDistanceVal(this.value);">
                        <div class="distance-val-stack">
                            <span class="distance-num" id="bottom_distance_display_num">100</span>
                            <span class="distance-unit">km</span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="search-go-taxi">Search Cabs <i class="fas fa-arrow-right"></i></button>
            </div>
        </form>
    </div>
</section>

<script>
    // Sliding Tab Switcher Logic
    function switchSearchMode(mode, section) {
        const isTop = (section === 'top');
        const containerId = isTop ? 'search-section' : 'lite-search-container-box';
        const container = document.getElementById(containerId);
        
        const tabsId = isTop ? 'top-search-tabs' : 'bottom-search-tabs';
        const tabs = document.getElementById(tabsId);
        
        const bikeFormId = isTop ? 'top-bike-form' : 'bottom-bike-form';
        const cabFormId = isTop ? 'top-cab-form' : 'bottom-cab-form';
        
        const bikeForm = document.getElementById(bikeFormId);
        const cabForm = document.getElementById(cabFormId);
        
        const buttons = tabs.querySelectorAll('.tab-btn');
        
        if (mode === 'bike') {
            container.classList.remove('mode-cab');
            container.classList.add('mode-bike');
            
            tabs.classList.remove('cab-active');
            buttons[0].classList.add('active');
            buttons[1].classList.remove('active');
            
            bikeForm.style.display = 'block';
            cabForm.style.display = 'none';
        } else {
            container.classList.remove('mode-bike');
            container.classList.add('mode-cab');
            
            tabs.classList.add('cab-active');
            buttons[0].classList.remove('active');
            buttons[1].classList.add('active');
            
            bikeForm.style.display = 'none';
            cabForm.style.display = 'block';
        }
    }

    function validateDates(section) {
        const start = document.getElementById(section + '_start_date').value;
        const end = document.getElementById(section + '_end_date').value;
        if (new Date(start) > new Date(end)) {
            alert('Return date cannot be before pick-up date.');
            return false;
        }
        return true;
    }

    function validateCabDates(section) {
        const tripType = document.getElementById(section === 'top' ? 'top_trip_type' : 'bottom_trip_type').value;
        const pDate = document.getElementById(section === 'top' ? 'top_cab_pickup_date' : 'bottom_cab_pickup_date').value;
        
        if (tripType === 'roundtrip') {
            const rDate = document.getElementById(section === 'top' ? 'top_cab_return_date' : 'bottom_cab_return_date').value;
            if (new Date(pDate) > new Date(rDate)) {
                alert('Return date cannot be before pickup date!');
                return false;
            }
        }
        return true;
    }

    function toggleCabReturnDate(section) {
        const tripType = document.getElementById(section === 'top' ? 'top_trip_type' : 'bottom_trip_type').value;
        const returnWrapper = document.getElementById(section === 'top' ? 'top_return_date_wrapper' : 'bottom_return_date_wrapper');
        const returnInput = document.getElementById(section === 'top' ? 'top_cab_return_date' : 'bottom_cab_return_date');
        const row1 = returnWrapper.parentElement; // search-row-1

        if (tripType === 'roundtrip') {
            returnWrapper.style.display = 'block';
            returnInput.setAttribute('required', 'required');
            row1.classList.add('round-trip-active');
        } else {
            returnWrapper.style.display = 'none';
            returnInput.removeAttribute('required');
            returnInput.value = '';
            row1.classList.remove('round-trip-active');
        }
    }

    function updateTopDistanceVal(val) {
        const numEl = document.getElementById('top_distance_display_num');
        if (numEl) numEl.innerText = val;
    }

    function updateBottomDistanceVal(val) {
        const numEl = document.getElementById('bottom_distance_display_num');
        if (numEl) numEl.innerText = val;
    }

    // Programmatic picker popup trigger on clicking the input
    document.querySelectorAll('input[type="date"], input[type="time"]').forEach(input => {
        input.addEventListener('click', () => {
            try {
                input.showPicker();
            } catch (e) {
                console.log('showPicker not supported or failed:', e);
            }
        });
        input.addEventListener('focus', () => {
            try {
                input.showPicker();
            } catch (e) {
                console.log('showPicker not supported or failed:', e);
            }
        });
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php require 'includes/footer.php' ?>
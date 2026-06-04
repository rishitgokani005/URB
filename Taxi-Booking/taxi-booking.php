<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_connection_error = false;
try {
    include('../includes/db.php');
} catch (mysqli_sql_exception $e) {
    $db_connection_error = true;
}

require '../includes/header.php';

// Fetch distinct cities for the search dropdown
$city_result = null;
$cabs_result = null;
$search_city = '';
$pickup_location = '';
$drop_location = '';
$trip_type = 'oneway';
$pickup_date = '';
$return_date = '';
$pickup_time = '';
$est_distance = 100;

if (!$db_connection_error) {
    $city_query = "SELECT DISTINCT city FROM acab WHERE city IS NOT NULL AND city != ''";
    $city_result = $conn->query($city_query);

    // Get search params if submitted
    $search_city = isset($_GET['pickup_city']) ? mysqli_real_escape_string($conn, $_GET['pickup_city']) : '';
    $pickup_location = isset($_GET['pickup_location']) ? $_GET['pickup_location'] : '';
    $drop_location = isset($_GET['drop_location']) ? $_GET['drop_location'] : '';
    $trip_type = isset($_GET['trip_type']) ? $_GET['trip_type'] : 'oneway';
    $pickup_date = isset($_GET['pickup_date']) ? $_GET['pickup_date'] : '';
    $return_date = isset($_GET['return_date']) ? $_GET['return_date'] : '';
    $pickup_time = isset($_GET['pickup_time']) ? $_GET['pickup_time'] : '';
    $est_distance = isset($_GET['est_distance']) ? intval($_GET['est_distance']) : 100;

    $selected_agency_id = isset($_GET['agency_id']) ? mysqli_real_escape_string($conn, $_GET['agency_id']) : '';
    $agency = null;
    $happy_customers = 0;
    if ($selected_agency_id) {
        $agency_info_query = "SELECT * FROM agencies WHERE id = '$selected_agency_id'";
        $agency_res = $conn->query($agency_info_query);
        if ($agency_res && $agency_res->num_rows > 0) {
            $agency = $agency_res->fetch_assoc();
            
            // Query total bookings for happy customers count of this agency
            $booking_count_query = "SELECT COUNT(*) as total_bookings FROM acabookings WHERE agency_id = '$selected_agency_id'";
            $booking_count_res = $conn->query($booking_count_query);
            if ($booking_count_res) {
                $happy_customers = $booking_count_res->fetch_assoc()['total_bookings'] ?? 0;
            }
        }
    }

    if ($search_city) {
        $cabs_query = "SELECT * FROM acab WHERE city = '$search_city'";
        if ($selected_agency_id) {
            $cabs_query .= " AND agency_id = '$selected_agency_id'";
        }
        $cabs_result = $conn->query($cabs_query);
    }
}
?>

<!-- Custom CSS for self-contained rich styling matching index.php -->
<style>
    :root {
        --primary: #FF4D01;
        --primary-light: #FFEDE5;
        --text-main: #0F172A;
        --text-sub: #64748B;
        --bg-main: #FFFFFF;
        --bg-sub: #F8FAFC;
        --border: #E2E8F0;
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    /* Taxi booking Hero adjustments */
    .taxi-hero {
        min-height: 70vh; /* Increased height */
        display: flex;
        align-items: center;
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); /* Clean dark gradient instead of bg image */
        position: relative;
        overflow: hidden;
        color: white;
        padding-top: 120px;
        padding-bottom: 120px;
    }

    .taxi-hero .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at 80% 20%, rgba(255, 77, 1, 0.15) 0%, transparent 60%); /* Subtle glowing accent sphere */
        z-index: 2;
    }

    .taxi-hero .hero-container {
        position: relative;
        z-index: 3;
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 5%;
        text-align: center;
    }

    .taxi-hero h1 {
        font-family: 'Outfit', sans-serif;
        font-size: 3.8rem;
        font-weight: 900;
        margin-bottom: 15px;
    }

    .taxi-hero h1 span {
        color: var(--primary);
        text-shadow: 0 0 30px rgba(255, 77, 1, 0.4);
    }

    .taxi-hero p {
        color: #E2E8F0;
        font-size: 1.15rem;
        max-width: 600px;
        margin: 0 auto 30px;
    }

    /* Floating Search Section */
    .search-float-taxi {
        background: white;
        padding: 30px;
        border-radius: 30px;
        box-shadow: var(--shadow-xl);
        margin: -80px auto 40px;
        position: relative;
        z-index: 100;
        max-width: 1140px;
        border: 1px solid var(--border);
        color: var(--text-main);
    }

    /* Search Row 1 & 2 styles matching index.php (second reference image layout) */
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

    .search-item {
        display: flex !important;
        flex-direction: column !important;
        gap: 6px !important;
    }

    .search-item label {
        color: #475569 !important;
        font-size: 0.72rem !important;
        font-weight: 800 !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
        margin-bottom: 6px !important;
    }

    .search-item select,
    .search-item input {
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
        width: 100% !important;
    }

    .search-item select:focus,
    .search-item input:focus {
        border-color: #FF4D01 !important;
        box-shadow: 0 0 0 3px rgba(255, 77, 1, 0.1) !important;
        outline: none !important;
    }

    /* Custom dropdown arrow and native picker indicator cover-click overlays */
    .search-float-taxi select {
        -webkit-appearance: none !important;
        appearance: none !important;
        background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230f172a' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 15px center !important;
        background-size: 14px !important;
        padding-right: 40px !important;
        cursor: pointer !important;
    }

    .search-float-taxi input[type="date"],
    .search-float-taxi input[type="time"] {
        position: relative !important;
    }

    .search-float-taxi input[type="date"] {
        background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230f172a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'/%3E%3Cline x1='16' y1='2' x2='16' y2='6'/%3E%3Cline x1='8' y1='2' x2='8' y2='6'/%3E%3Cline x1='3' y1='10' x2='21' y2='10'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 15px center !important;
        background-size: 16px !important;
        padding-right: 40px !important;
    }

    .search-float-taxi input[type="time"] {
        background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230f172a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='10'/%3E%3Cpolyline points='12 6 12 12 16 14'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 15px center !important;
        background-size: 16px !important;
        padding-right: 40px !important;
    }

    .search-float-taxi input[type="date"]::-webkit-calendar-picker-indicator,
    .search-float-taxi input[type="time"]::-webkit-calendar-picker-indicator {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        opacity: 0 !important;
        cursor: pointer !important;
    }

    /* Range slider styles */
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
        background: transparent !important;
        outline: none !important;
        cursor: pointer !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .distance-slider-wrap input[type="range"]::-webkit-slider-runnable-track {
        width: 100% !important;
        height: 6px !important;
        background: #334155 !important;
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

    .distance-slider-wrap input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px !important;
        height: 20px !important;
        border-radius: 50% !important;
        background: #FF4D01 !important;
        cursor: pointer !important;
        box-shadow: 0 1px 4px rgba(0,0,0,0.3) !important;
        border: none !important;
        margin-top: -7px !important;
        transition: transform 0.1s ease !important;
    }

    .distance-slider-wrap input[type="range"]::-webkit-slider-thumb:hover {
        transform: scale(1.15) !important;
    }

    .distance-slider-wrap input[type="range"]::-moz-range-thumb {
        width: 20px !important;
        height: 20px !important;
        border-radius: 50% !important;
        background: #FF4D01 !important;
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
        align-self: flex-end !important;
    }

    .search-go-taxi:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 16px rgba(255, 77, 1, 0.3) !important;
    }

    /* Results Section & Filters */
    .results-section {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px 5% 80px;
    }

    .filter-bar {
        background: #F1F5F9;
        border-radius: 20px;
        padding: 15px 25px;
        margin-bottom: 35px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        border: 1px solid var(--border);
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .filter-group span {
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--text-sub);
        text-transform: uppercase;
    }

    .filter-btn {
        background: white;
        border: 1px solid var(--border);
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }

    .filter-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .filter-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .sort-select {
        padding: 8px 12px;
        border-radius: 10px;
        border: 1px solid var(--border);
        font-size: 0.85rem;
        font-weight: 600;
        outline: none;
        cursor: pointer;
    }

    /* Cars Grid */
    .cabs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 30px;
    }

    .cab-card {
        background: white;
        border-radius: 24px;
        border: 1px solid var(--border);
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .cab-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .cab-img-container {
        height: 200px;
        background: var(--bg-sub);
        position: relative;
        overflow: hidden;
    }

    .cab-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .cab-card:hover .cab-img-container img {
        transform: scale(1.06);
    }

    .cab-badge-ac {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(15, 23, 42, 0.85);
        color: white;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        backdrop-filter: blur(5px);
    }

    .cab-badge-seats {
        position: absolute;
        bottom: 15px;
        left: 15px;
        background: var(--primary);
        color: white;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .cab-info {
        padding: 25px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .cab-header-info {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .cab-header-info h3 {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--text-main);
        font-family: 'Outfit', sans-serif;
    }

    .cab-agency {
        font-size: 0.8rem;
        color: var(--text-sub);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .cab-specs {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 20px;
        padding: 12px 0;
        border-top: 1px dashed var(--border);
        border-bottom: 1px dashed var(--border);
    }

    .spec-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: var(--text-sub);
    }

    .spec-item i {
        color: var(--primary);
        width: 14px;
    }

    .cab-price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }

    .price-rate {
        display: flex;
        flex-direction: column;
    }

    .price-rate .rate-num {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--primary);
        font-family: 'Outfit', sans-serif;
    }

    .price-rate .rate-lbl {
        font-size: 0.75rem;
        color: var(--text-sub);
        font-weight: 600;
    }

    .estimate-fare-lbl {
        text-align: right;
        display: flex;
        flex-direction: column;
    }

    .estimate-fare-lbl .fare-num {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text-main);
        font-family: 'Outfit', sans-serif;
    }

    .estimate-fare-lbl .fare-lbl {
        font-size: 0.75rem;
        color: var(--text-sub);
        font-weight: 600;
    }

    .btn-book-cab {
        background: var(--text-main);
        color: white !important;
        border: none;
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: 0.2s;
        text-align: center;
        width: 100%;
        margin-top: 20px;
        display: block;
    }

    .btn-book-cab:hover {
        background: var(--primary);
        transform: scale(1.02);
        box-shadow: 0 6px 12px rgba(255, 77, 1, 0.2);
    }

    /* Empty search results */
    .empty-search {
        grid-column: 1 / -1;
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 30px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-md);
        max-width: 600px;
        margin: 0 auto;
    }

    .empty-search i {
        font-size: 3.5rem;
        color: var(--border);
        margin-bottom: 20px;
    }

    .empty-search h2 {
        font-size: 1.5rem;
        margin-bottom: 8px;
    }

    .empty-search p {
        color: var(--text-sub);
        font-size: 0.95rem;
    }

    /* Modal Overlay */
    .booking-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(8px);
        z-index: 3000;
        display: none;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .booking-modal {
        background: white;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        border-radius: 30px;
        padding: 35px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border: 1px solid var(--border);
        position: relative;
        overflow-y: auto;
        transform: scale(0.9);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .booking-modal-overlay.active {
        display: flex;
        opacity: 1;
    }

    .booking-modal-overlay.active .booking-modal {
        transform: scale(1);
    }

    .modal-close {
        position: absolute;
        top: 25px;
        right: 25px;
        background: #F1F5F9;
        border: none;
        color: #64748B;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        transition: 0.2s;
    }

    .modal-close:hover {
        background: var(--primary-light);
        color: var(--primary);
    }

    .modal-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 25px;
        color: var(--text-main);
    }

    .summary-box {
        background: var(--bg-sub);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        font-size: 0.9rem;
    }

    .summary-grid-full {
        grid-column: span 2;
        border-top: 1px dashed var(--border);
        padding-top: 10px;
        margin-top: 5px;
    }

    .summary-label {
        color: var(--text-sub);
        font-weight: 600;
    }

    .summary-val {
        font-weight: 700;
        color: var(--text-main);
        text-align: right;
    }

    .form-group-taxi {
        margin-bottom: 18px;
    }

    .form-group-taxi label {
        display: block;
        margin-bottom: 8px;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--text-main);
    }

    .form-group-taxi input,
    .form-group-taxi select {
        width: 100%;
        padding: 12px 14px;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        outline: none;
        transition: 0.3s;
    }

    .form-group-taxi input:focus,
    .form-group-taxi select:focus {
        border-color: var(--primary);
    }

    .radio-group-taxi {
        display: flex;
        gap: 20px;
        margin-top: 8px;
    }

    .radio-option {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .radio-option input {
        width: 18px;
        height: 18px;
        accent-color: var(--primary);
    }

    .coupon-section {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .coupon-section input {
        flex: 1;
        padding: 10px 14px;
        border: 1.5px solid var(--border);
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .coupon-section button {
        background: #F1F5F9;
        border: 1px solid var(--border);
        padding: 0 15px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        font-size: 0.85rem;
    }

    .coupon-section button:hover {
        background: #E2E8F0;
    }

    .bill-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 0.9rem;
        color: var(--text-sub);
    }

    .bill-row.total {
        border-top: 1.5px solid var(--border);
        padding-top: 12px;
        margin-top: 12px;
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--text-main);
    }

    .btn-submit-booking {
        background: var(--primary);
        color: white;
        border: none;
        width: 100%;
        padding: 15px;
        border-radius: 14px;
        font-size: 1.05rem;
        font-weight: 800;
        cursor: pointer;
        margin-top: 25px;
        transition: 0.2s;
    }

    .btn-submit-booking:hover {
        background: #e04400;
        box-shadow: 0 8px 20px rgba(255, 77, 1, 0.35);
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
        .search-row-1,
        .search-row-1.round-trip-active,
        .search-row-2 {
            grid-template-columns: 1fr !important;
            gap: 15px !important;
        }
        .search-go-taxi {
            width: 100% !important;
        }
        .taxi-hero h1 {
            font-size: 2.5rem !important;
        }
        .cabs-grid {
            grid-template-columns: 1fr !important;
        }
    }

    /* Agency Profile Banner Styles */
    .agency-profile-banner {
        width: 80%;
        max-width: 1000px;
        aspect-ratio: 8 / 1;
        margin: -3rem auto 3rem;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 0 60px;
        border-radius: 32px;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        border: 1px solid rgba(255, 255, 255, 0.35);
        box-shadow: 0 10px 40px rgba(0,0,0,0.08), inset 0 1px 1px rgba(255,255,255,0.4);
        overflow: hidden;
        font-family: 'Outfit', sans-serif;
    }

    .agency-profile-banner::before {
        content: "";
        position: absolute;
        top: -150%;
        left: -50%;
        width: 70%;
        height: 400%;
        background: linear-gradient(90deg, transparent, rgba(36, 34, 34, 0.15), transparent);
        transform: rotate(25deg);
        animation: glassShine 6s linear infinite;
    }

    @keyframes glassShine {
        from { left: -120%; }
        to { left: 220%; }
    }

    .agency-info-main {
        position: relative;
        z-index: 2;
        text-align: left;
    }

    .agency-info-main h1 {
        margin: 0;
        font-size: 3rem;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.1;
    }

    .agency-meta {
        margin-top: 14px;
        font-size: 1.1rem;
        color: #555;
        font-weight: 500;
        display: block;
    }

    /* Tablet */
    @media (max-width: 768px) {
        .agency-profile-banner {
            width: 90%;
            padding: 0 30px;
            aspect-ratio: auto;
            min-height: 220px;
            margin: 0 auto 2rem;
        }

        .agency-info-main h1 {
            font-size: 2.2rem;
        }

        .agency-meta {
            font-size: 0.95rem;
            line-height: 1.8;
        }
    }

    /* Mobile */
    @media (max-width: 576px) {
        .agency-profile-banner {
            width: 100%;
            padding: 15px;
            min-height: 120px;
            margin: 0 auto 1.5rem;
        }

        .agency-info-main h1 {
            font-size: 1.8rem;
        }

        .agency-meta {
            font-size: 0.9rem;
        }
    }
</style>

<!-- DB Connection Error Alert Banner -->
<?php if ($db_connection_error): ?>
    <div class="reveal" style="max-width: 1140px; margin: 120px auto 0; padding: 25px; background: #FEF2F2; border: 1.5px solid #FCA5A5; border-radius: 20px; text-align: center; color: #991B1B; font-family: 'Outfit', sans-serif;">
        <i class="fas fa-exclamation-triangle" style="font-size: 2.2rem; margin-bottom: 12px; color: #EF4444;"></i>
        <h3 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 8px;">Database Connection Error</h3>
        <p style="font-size: 0.95rem; line-height: 1.6; max-width: 600px; margin: 0 auto;">
            We are currently unable to connect to the database. Please ensure that your MySQL database server is started and running in the XAMPP Control Panel.
        </p>
    </div>
<?php endif; ?>

<?php if ($agency): ?>
    <!-- Hidden search criteria to prevent JS error when search form is hidden -->
    <input type="hidden" id="search_pickup_location" value="<?php echo htmlspecialchars($pickup_location); ?>">
    <input type="hidden" id="search_drop_location" value="<?php echo htmlspecialchars($drop_location); ?>">
    <input type="hidden" id="trip_type" value="<?php echo htmlspecialchars($trip_type); ?>">
    <input type="hidden" id="pickup_date" value="<?php echo htmlspecialchars($pickup_date); ?>">
    <input type="hidden" id="pickup_time" value="<?php echo htmlspecialchars($pickup_time); ?>">
    <input type="hidden" id="return_date" value="<?php echo htmlspecialchars($return_date); ?>">
    <input type="hidden" id="est_distance" value="<?php echo htmlspecialchars($est_distance); ?>">
<?php endif; ?>

<?php if (!$agency): ?>
<!-- Hero Section mirroring index.php layout -->
<section class="taxi-hero">
    <div class="hero-overlay"></div>
    <div class="hero-container">
        <h1 class="reveal">Book Premium <span>Taxi Service.</span></h1>
        <p class="reveal">Comfortable intercity and local cab travels. Select your vehicle option and get real-time transparent distance pricing instantly.</p>
    </div>
</section>

<!-- Search form section mirroring index.php floating bar -->
<div id="search-section" class="search-float-taxi reveal">
    <form action="agencies-cab.php" method="GET" onsubmit="return validateSearchForm();">
        <input type="hidden" name="search_submitted" value="1">
        
        <!-- Row 1: Trip configuration -->
        <div class="search-row-1 <?php echo $trip_type === 'roundtrip' ? 'round-trip-active' : ''; ?>">
            <div class="search-item">
                <label>Trip Type</label>
                <select name="trip_type" id="trip_type" onchange="toggleReturnDate();">
                    <option value="oneway" <?php echo $trip_type === 'oneway' ? 'selected' : ''; ?>>One Way</option>
                    <option value="roundtrip" <?php echo $trip_type === 'roundtrip' ? 'selected' : ''; ?>>Round Trip</option>
                </select>
            </div>
            
            <div class="search-item">
                <label>City Location</label>
                <select name="pickup_city" required id="pickup_city">
                    <option value="" disabled <?php echo empty($search_city) ? 'selected' : ''; ?>>Choose City</option>
                    <?php if ($city_result && $city_result->num_rows > 0): ?>
                        <?php while ($row = $city_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['city']); ?>" <?php echo $search_city === $row['city'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['city']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="Dwarka">Dwarka</option>
                        <option value="Somnath">Somnath</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="search-item">
                <label>Pick-up Date</label>
                <input type="date" name="pickup_date" id="pickup_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($pickup_date); ?>" required>
            </div>

            <div class="search-item">
                <label>Pick-up Time</label>
                <input type="time" name="pickup_time" id="pickup_time" value="<?php echo htmlspecialchars($pickup_time); ?>" required>
            </div>

            <div class="search-item" id="return_date_wrapper" style="<?php echo $trip_type === 'oneway' ? 'display:none;' : ''; ?>">
                <label>Return Date</label>
                <input type="date" name="return_date" id="return_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars($return_date); ?>">
            </div>
        </div>

        <!-- Row 2: Locations & Distance Estimation -->
        <div class="search-row-2">
            <div class="search-item">
                <label>Pick-up Address</label>
                <input type="text" name="pickup_location" id="search_pickup_location" placeholder="Enter pickup address" value="<?php echo htmlspecialchars($pickup_location); ?>" required>
            </div>

            <div class="search-item">
                <label>Drop-off Address</label>
                <input type="text" name="drop_location" id="search_drop_location" placeholder="Enter destination address" value="<?php echo htmlspecialchars($drop_location); ?>" required>
            </div>

            <div class="distance-container">
                <div class="distance-header">
                    <span>Est. Distance</span>
                </div>
                <div class="distance-slider-wrap">
                    <input type="range" name="est_distance" id="est_distance" min="10" max="600" step="5" value="<?php echo $est_distance; ?>" oninput="updateDistanceVal(this.value);">
                    <div class="distance-val-stack">
                        <span class="distance-num" id="distance_display_num"><?php echo $est_distance; ?></span>
                        <span class="distance-unit">km</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="search-go-taxi">Search Cabs <i class="fas fa-arrow-right"></i></button>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Main Results & Cabs Grid -->
<section class="results-section" id="results-section" style="<?php echo $agency ? 'padding-top: 140px;' : ''; ?>">
    <?php if ($search_city): ?>
        <?php if ($agency): ?>
            <div class="agency-profile-banner reveal">
                <div class="agency-info-main">
                    <h1><?php echo htmlspecialchars($agency['name']); ?></h1>
                    <div class="agency-meta">
                        <?php echo htmlspecialchars($agency['city']); ?>
                        |
                        <?php echo $cabs_result ? $cabs_result->num_rows : 0; ?> Vehicles Available
                        |
                        <?php echo $happy_customers; ?> Happy Customers
                        |
                        Verified Partner
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="section-title reveal" style="margin-bottom: 25px; text-align: left;">
                <span class="sub-heading">Available Taxis in <?php echo htmlspecialchars($search_city); ?></span>
                <h2>Select From Our Fleet</h2>
                <p style="color: var(--text-sub); margin-top: 5px;">Estimated Distance: <span style="color: var(--primary); font-weight:700;"><?php echo $est_distance; ?> km</span> (Change slider above to adjust fare calculations)</p>
            </div>
        <?php endif; ?>

        <!-- Dynamic Filter Toolbar -->
        <div class="filter-bar reveal">
            <div class="filter-group">
                <span>Seats:</span>
                <button class="filter-btn active" onclick="filterCabs('seats', 'all', this)">All</button>
                <button class="filter-btn" onclick="filterCabs('seats', '4', this)">4 Seater</button>
                <button class="filter-btn" onclick="filterCabs('seats', '7', this)">7 Seater</button>
                <button class="filter-btn" onclick="filterCabs('seats', '11', this)">11 Seater</button>
            </div>

            <div class="filter-group">
                <span>AC Type:</span>
                <button class="filter-btn active" onclick="filterCabs('ac', 'all', this)">All Cabs</button>
                <button class="filter-btn" onclick="filterCabs('ac', 'AC', this)">AC Only</button>
                <button class="filter-btn" onclick="filterCabs('ac', 'Non-AC', this)">Non-AC</button>
            </div>

            <div>
                <select class="sort-select" onchange="sortCabs(this.value)">
                    <option value="default">Sort: Default</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                </select>
            </div>
        </div>

        <!-- Cabs Card Grid -->
        <div class="cabs-grid reveal" id="cabs_container">
            <?php if ($cabs_result && $cabs_result->num_rows > 0): ?>
                <?php while ($cab = $cabs_result->fetch_assoc()): 
                    $price_per_km = $cab['price_per_km'];
                    $base_rent = $price_per_km * $est_distance;
                    $ac_status = (strpos(strtolower($cab['cab_name']), 'non-ac') !== false || $cab['image2'] === 'Non-AC') ? 'Non-AC' : 'AC';
                ?>
                    <div class="cab-card" data-seats="<?php echo $cab['seats']; ?>" data-ac="<?php echo $ac_status; ?>" data-price="<?php echo $base_rent; ?>">
                        <div class="cab-img-container">
                            <img src="Cabs Photo/<?php echo htmlspecialchars($cab['image']); ?>" alt="<?php echo htmlspecialchars($cab['cab_name']); ?>">
                            <span class="cab-badge-ac"><?php echo $ac_status; ?></span>
                            <span class="cab-badge-seats"><?php echo $cab['seats']; ?> Seats</span>
                        </div>
                        <div class="cab-info">
                            <div class="cab-header-info">
                                <h3><?php echo htmlspecialchars($cab['cab_name']); ?></h3>
                            </div>
                            <div class="cab-agency">
                                <i class="fas fa-taxi"></i>
                                <span>Provided by <?php echo htmlspecialchars($cab['agency_name']); ?></span>
                            </div>
                            <div class="cab-specs">
                                <div class="spec-item">
                                    <i class="fas fa-briefcase"></i>
                                    <span><?php echo $cab['seats'] <= 4 ? '2 Bags' : '4+ Bags'; ?> Luggage</span>
                                </div>
                                <div class="spec-item">
                                    <i class="fas fa-snowflake"></i>
                                    <span><?php echo $ac_status === 'AC' ? 'Climate Control' : 'Standard Ventilation'; ?></span>
                                </div>
                                <div class="spec-item">
                                    <i class="fas fa-gears"></i>
                                    <span>Manual Transmission</span>
                                </div>
                                <div class="spec-item">
                                    <i class="fas fa-location-dot"></i>
                                    <span>City: <?php echo htmlspecialchars($cab['city']); ?></span>
                                </div>
                            </div>
                            
                            <div class="cab-price-row">
                                <div class="price-rate">
                                    <span class="rate-num">₹<?php echo $price_per_km; ?></span>
                                    <span class="rate-lbl">per kilometer</span>
                                </div>
                                <div class="estimate-fare-lbl">
                                    <span class="fare-num">₹<?php echo number_format($base_rent); ?></span>
                                    <span class="fare-lbl">est. fare (<?php echo $est_distance; ?> km)</span>
                                </div>
                            </div>
                            
                            <button type="button" class="btn-book-cab" onclick="initiateCabBooking(<?php echo htmlspecialchars(json_encode($cab)); ?>, <?php echo $price_per_km; ?>, <?php echo $base_rent; ?>);">
                                Book Cab Now
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-search">
                    <i class="fas fa-car-burst"></i>
                    <h2>No Cabs Listed</h2>
                    <p>We couldn't find any cabs in <b><?php echo htmlspecialchars($search_city); ?></b> currently. Check back later.</p>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div class="empty-search reveal">
            <i class="fas fa-search-location"></i>
            <h2>Find a Taxi</h2>
            <p>Please enter your pickup location, drop-off location, select date/time, and click search to list available taxi options.</p>
        </div>
    <?php endif; ?>
</section>

<!-- My Taxi Bookings Section -->
<?php if (isset($_SESSION['user_id']) && !$db_connection_error): 
    // Fetch user cab bookings
    $uid = $_SESSION['user_id'];
    $my_bookings_query = "SELECT cb.*, c.cab_name, c.image, c.price_per_km, c.agency_name 
                          FROM acabookings cb 
                          LEFT JOIN acab c ON cb.cab_id = c.id 
                          WHERE cb.user_id = '$uid' 
                          ORDER BY cb.created_at DESC";
    $my_bookings_res = $conn->query($my_bookings_query);
?>
    <section class="results-section reveal" style="border-top: 1px solid var(--border); padding-top: 60px;">
        <div class="section-title" style="margin-bottom: 35px; text-align: left;">
            <span class="sub-heading">Your Rides</span>
            <h2>My Taxi Bookings</h2>
        </div>
        
        <div class="bookings-grid" style="display: flex; flex-direction: column; gap: 20px;">
            <?php if ($my_bookings_res && $my_bookings_res->num_rows > 0): ?>
                <?php while ($b = $my_bookings_res->fetch_assoc()): 
                    $b_status = $b['booking_status'];
                ?>
                    <div class="cab-card" style="flex-direction: row; align-items: center; padding: 20px; gap: 20px; flex-wrap: wrap;">
                        <div style="width: 120px; height: 90px; border-radius: 12px; overflow: hidden; background: var(--bg-sub); flex-shrink: 0;">
                            <img src="Cabs Photo/<?php echo htmlspecialchars($b['image'] ?? '4-seater-car.webp'); ?>" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <div style="flex-grow: 1; min-width: 250px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <h3 style="font-family:'Outfit', sans-serif; font-size:1.15rem; font-weight:800;"><?php echo htmlspecialchars($b['cab_name'] ?? 'Taxi Ride'); ?></h3>
                                <span class="status-pill" data-status="<?php echo htmlspecialchars($b_status); ?>" style="padding: 4px 12px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
                                background: <?php echo $b_status === 'active' ? '#DCFCE7' : ($b_status === 'cancelled' ? '#FEE2E2' : '#F1F5F9'); ?>;
                                color: <?php echo $b_status === 'active' ? '#166534' : ($b_status === 'cancelled' ? '#991B1B' : '#475569'); ?>;">
                                    <?php echo htmlspecialchars($b_status); ?>
                                </span>
                            </div>
                            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px; font-size:0.85rem; color:var(--text-sub);">
                                <div><i class="fas fa-map-marker-alt" style="color:var(--primary); margin-right:6px;"></i>Pickup: <b><?php echo htmlspecialchars($b['pickup_location']); ?></b></div>
                                <div><i class="fas fa-location-arrow" style="color:var(--primary); margin-right:6px;"></i>Dropoff: <b><?php echo htmlspecialchars($b['drop_location']); ?></b></div>
                                <div><i class="fas fa-calendar" style="color:var(--primary); margin-right:6px;"></i>Pickup Time: <b><?php echo date('d M Y', strtotime($b['booking_date'])) . ' at ' . date('h:i A', strtotime($b['pick_up_time'])); ?></b></div>
                                <div><i class="fas fa-receipt" style="color:var(--primary); margin-right:6px;"></i>Price: <b>₹<?php echo number_format($b['total_price']); ?></b> (Cash)</div>
                            </div>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:10px; align-items:flex-end; justify-content:center; flex-shrink:0;">
                            <span style="font-family:monospace; font-size:0.75rem; background:#F1F5F9; padding:4px 8px; border-radius:6px; color:#475569;">
                                ID: <?php echo htmlspecialchars($b['booking_id']); ?>
                            </span>
                            <?php if ($b_status === 'active'): ?>
                                <form action="cancel_booking.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this cab booking?');">
                                    <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($b['booking_id']); ?>">
                                    <button type="submit" style="background:#FEE2E2; color:#EF4444; border:none; padding:8px 16px; border-radius:8px; font-weight:600; font-size:0.8rem; cursor:pointer; transition:0.2s;">
                                        Cancel Ride
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; background: white; border-radius: 20px; border: 1px solid var(--border);">
                    <p style="color: var(--text-sub); font-size:0.95rem;">You have no taxi bookings yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<!-- Sleek Passenger Booking Details Overlay Modal -->
<div class="booking-modal-overlay" id="booking_modal_overlay">
    <div class="booking-modal">
        <button class="modal-close" onclick="closeCabBookingModal();">&times;</button>
        <h2 class="modal-title">Passenger Details</h2>

        <div class="summary-box">
            <div class="summary-grid">
                <div>
                    <span class="summary-label">Selected Cab:</span>
                    <div class="summary-val" id="summary_cab_name">-</div>
                </div>
                <div>
                    <span class="summary-label">Agency:</span>
                    <div class="summary-val" id="summary_agency">-</div>
                </div>
                <div>
                    <span class="summary-label">Route:</span>
                    <div class="summary-val" id="summary_route" style="font-size:0.8rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">-</div>
                </div>
                <div>
                    <span class="summary-label">Trip Type:</span>
                    <div class="summary-val" id="summary_triptype" style="text-transform: capitalize;">-</div>
                </div>
                <div class="summary-grid-full">
                    <span class="summary-label">Pickup Schedule:</span>
                    <div class="summary-val" id="summary_schedule" style="text-align:left; margin-top:2px;">-</div>
                </div>
            </div>
        </div>

        <form action="process_booking.php" method="POST" id="cab_booking_form">
            <!-- Hidden backend transport data -->
            <input type="hidden" name="cab_id" id="form_cab_id">
            <input type="hidden" name="agency_id" id="form_agency_id">
            <input type="hidden" name="pickup_city" id="form_pickup_city" value="<?php echo htmlspecialchars($search_city); ?>">
            <input type="hidden" name="pickup_location" id="form_pickup_location">
            <input type="hidden" name="drop_location" id="form_drop_location">
            <input type="hidden" name="trip_type" id="form_trip_type" value="<?php echo htmlspecialchars($trip_type); ?>">
            <input type="hidden" name="booking_date" id="form_pickup_date" value="<?php echo htmlspecialchars($pickup_date); ?>">
            <input type="hidden" name="return_date" id="form_return_date" value="<?php echo htmlspecialchars($return_date); ?>">
            <input type="hidden" name="pick_up_time" id="form_pickup_time" value="<?php echo htmlspecialchars($pickup_time); ?>">
            <input type="hidden" name="est_distance" id="form_est_distance" value="<?php echo $est_distance; ?>">
            <input type="hidden" name="total_price" id="form_final_price">

            <!-- Passenger inputs -->
            <div class="form-group-taxi">
                <label>Passenger Full Name</label>
                <input type="text" name="passenger_name" placeholder="Enter passenger's name" required 
                       value="<?php echo isset($_SESSION['loggedin']) && isset($_SESSION['email']) ? htmlspecialchars($_SESSION['customer_name'] ?? '') : ''; ?>">
            </div>

            <div class="form-group-taxi">
                <label>Passenger Mobile</label>
                <input type="tel" name="passenger_phone" placeholder="10-digit mobile number" required pattern="[0-9]{10}"
                       value="<?php echo isset($_SESSION['loggedin']) && isset($_SESSION['email']) ? htmlspecialchars($_SESSION['customer_phone'] ?? '') : ''; ?>">
            </div>

            <div class="form-group-taxi">
                <label>Passenger Email</label>
                <input type="email" name="passenger_email" placeholder="Enter email address" required
                       value="<?php echo isset($_SESSION['loggedin']) && isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>">
            </div>

            <div class="form-group-taxi">
                <label>Identity Proof</label>
                <select name="id_proof" required>
                    <option value="Aadhar Card">Aadhar Card</option>
                    <option value="Driving License">Driving License</option>
                    <option value="Voter ID">Voter ID</option>
                </select>
            </div>

            <!-- Checkout calculations -->
            <div style="border-top:1px dashed var(--border); padding-top:15px; margin-top:20px;">
                <label style="font-weight:700; font-size:0.85rem; color:var(--text-main); display:block; margin-bottom:12px;">Fare Breakdown</label>
                <div class="bill-row">
                    <span>Base Rent (<span id="bill_rate">₹0</span>/km x <span id="bill_dist">0</span> km)</span>
                    <span id="bill_base">₹0.00</span>
                </div>
                <div class="bill-row">
                    <span>GST & Booking Fee (5%)</span>
                    <span id="bill_tax">₹0.00</span>
                </div>
                <div class="bill-row" id="coupon_row" style="display:none; color: #2c7a7b;">
                    <span>Coupon Discount (APP50)</span>
                    <span>- ₹50.00</span>
                </div>
                <div class="bill-row total">
                    <span>Total Bill (Cash Payment)</span>
                    <span id="bill_total">₹0.00</span>
                </div>
            </div>

            <!-- Coupon application -->
            <div class="coupon-section" style="margin-top:15px;">
                <input type="text" id="coupon_code" placeholder="Coupon Code (e.g. APP50)">
                <button type="button" onclick="applyCoupon();">Apply</button>
            </div>
            <p id="coupon_msg" style="font-size:0.8rem; margin-top:-15px; margin-bottom:15px; display:none;"></p>

            <button type="submit" class="btn-submit-booking">Confirm Booking & Ride</button>
        </form>
    </div>
</div>

<!-- Scripts for dynamic functionality -->
<script>
    // Constants & State
    const isLoggedIn = <?php echo isset($_SESSION['loggedin']) ? 'true' : 'false'; ?>;
    let originalBaseRent = 0;
    let pricePerKm = 0;
    let isCouponApplied = false;

    // Toggle return date based on trip type
    function toggleReturnDate() {
        const tripType = document.getElementById('trip_type').value;
        const returnWrapper = document.getElementById('return_date_wrapper');
        const returnInput = document.getElementById('return_date');
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

    // Dynamic distance slider feedback
    function updateDistanceVal(val) {
        const numEl = document.getElementById('distance_display_num');
        if (numEl) numEl.innerText = val;
    }

    // Validate dates before submitting search
    function validateSearchForm() {
        const tripType = document.getElementById('trip_type').value;
        const pDate = document.getElementById('pickup_date').value;
        
        if (tripType === 'roundtrip') {
            const rDate = document.getElementById('return_date').value;
            if (new Date(pDate) > new Date(rDate)) {
                alert('Return date cannot be before pickup date!');
                return false;
            }
        }
        return true;
    }

    // Client-side filtering logic
    let filterSeats = 'all';
    let filterAc = 'all';

    function filterCabs(category, value, btnEl) {
        // Toggle active button state
        const siblings = btnEl.parentNode.querySelectorAll('.filter-btn');
        siblings.forEach(sib => sib.classList.remove('active'));
        btnEl.classList.add('active');

        if (category === 'seats') filterSeats = value;
        if (category === 'ac') filterAc = value;

        // Apply filters
        const cards = document.querySelectorAll('.cab-card');
        cards.forEach(card => {
            const seats = card.getAttribute('data-seats');
            const ac = card.getAttribute('data-ac');

            const matchesSeats = (filterSeats === 'all') || (seats === filterSeats);
            const matchesAc = (filterAc === 'all') || (ac === filterAc);

            if (matchesSeats && matchesAc) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Client-side sorting logic
    function sortCabs(criteria) {
        const container = document.getElementById('cabs_container');
        const cards = Array.from(container.getElementsByClassName('cab-card'));

        if (criteria === 'default') {
            // Restore default order (by array index)
            location.reload();
            return;
        }

        cards.sort((a, b) => {
            const priceA = parseFloat(a.getAttribute('data-price'));
            const priceB = parseFloat(b.getAttribute('data-price'));

            if (criteria === 'price_asc') return priceA - priceB;
            if (criteria === 'price_desc') return priceB - priceA;
            return 0;
        });

        // Re-append sorted elements
        cards.forEach(card => container.appendChild(card));
    }

    // Open booking checkout page
    function initiateCabBooking(cab, rate, baseRent) {
        if (!isLoggedIn) {
            // Show standard login modal
            showLoginModal();
            return;
        }

        const pickupLoc = encodeURIComponent(document.getElementById('search_pickup_location').value);
        const dropLoc = encodeURIComponent(document.getElementById('search_drop_location').value);
        const tripType = encodeURIComponent(document.getElementById('trip_type').value);
        const pickupDate = encodeURIComponent(document.getElementById('pickup_date').value);
        const pickupTime = encodeURIComponent(document.getElementById('pickup_time').value);
        const returnDate = encodeURIComponent(document.getElementById('return_date').value);
        const estDist = encodeURIComponent(document.getElementById('est_distance').value);

        // Redirect to booking-details-cab.php with all parameters
        window.location.href = `booking-details-cab.php?cab_id=${cab.id}&rate=${rate}&base_rent=${baseRent}&pickup_location=${pickupLoc}&drop_location=${dropLoc}&trip_type=${tripType}&pickup_date=${pickupDate}&pickup_time=${pickupTime}&return_date=${returnDate}&est_distance=${estDist}`;
    }

    function closeCabBookingModal() {
        const modalOverlay = document.getElementById('booking_modal_overlay');
        modalOverlay.classList.remove('active');
        setTimeout(() => {
            modalOverlay.style.display = 'none';
        }, 300);
        document.body.style.overflow = 'auto';
    }

    // Bill recalculation
    function recalcBill() {
        const estDist = parseInt(document.getElementById('est_distance').value);
        const base = pricePerKm * estDist;
        const tax = base * 0.05; // 5% GST
        let total = base + tax;

        if (isCouponApplied) {
            total -= 50; // APP50 coupon gives flat 50 discount
            if (total < 0) total = 0;
        }

        document.getElementById('bill_rate').innerText = '₹' + pricePerKm;
        document.getElementById('bill_dist').innerText = estDist;
        document.getElementById('bill_base').innerText = '₹' + base.toFixed(2);
        document.getElementById('bill_tax').innerText = '₹' + tax.toFixed(2);
        document.getElementById('bill_total').innerText = '₹' + total.toFixed(2);
        
        // Update hidden form inputs
        document.getElementById('form_final_price').value = total.toFixed(2);
    }

    // Coupon logic
    function applyCoupon() {
        const code = document.getElementById('coupon_code').value.trim();
        const msgEl = document.getElementById('coupon_msg');
        
        if (code === 'APP50') {
            isCouponApplied = true;
            document.getElementById('coupon_row').style.display = 'flex';
            msgEl.style.display = 'block';
            msgEl.style.color = '#2c7a7b';
            msgEl.innerHTML = '<i class="fas fa-check-circle"></i> Coupon applied: ₹50 discount!';
            recalcBill();
        } else if (code === '') {
            isCouponApplied = false;
            document.getElementById('coupon_row').style.display = 'none';
            msgEl.style.display = 'none';
            recalcBill();
        } else {
            isCouponApplied = false;
            document.getElementById('coupon_row').style.display = 'none';
            msgEl.style.display = 'block';
            msgEl.style.color = '#ff3c00';
            msgEl.innerHTML = '<i class="fas fa-times-circle"></i> Invalid coupon code.';
            recalcBill();
        }
    }

    // Smooth scroll to results on query search
    <?php if (!empty($search_city)): ?>
    document.addEventListener("DOMContentLoaded", function() {
        const resultsSec = document.getElementById("results-section");
        if (resultsSec) {
            resultsSec.scrollIntoView({ behavior: "smooth", block: "start" });
        }
    });
    <?php endif; ?>

    // Scroll reveal observer mirroring index.php animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<?php require '../includes/footer.php'; ?>

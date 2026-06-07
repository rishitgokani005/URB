<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$booking_id = isset($_GET['booking_id']) ? mysqli_real_escape_string($conn, $_GET['booking_id']) : '';
$user_id = $_SESSION['user_id'];

if (empty($booking_id)) {
    die("Invalid booking reference.");
}

// Fetch booking details
$sql = "SELECT cb.*, c.cab_name, c.image, c.price_per_km, c.agency_name, u.name as user_name, u.email as user_email, u.phone as user_phone
        FROM acabookings cb
        LEFT JOIN acab c ON cb.cab_id = c.id
        LEFT JOIN users u ON cb.user_id = u.user_id
        WHERE cb.booking_id = '$booking_id' AND cb.user_id = '$user_id'";
$res = $conn->query($sql);
if (!$res || $res->num_rows === 0) {
    die("Booking not found or access denied.");
}

$b = $res->fetch_assoc();
$est_distance = isset($b['est_distance']) && intval($b['est_distance']) > 0 ? intval($b['est_distance']) : 100;

// Check if status is completed
$b_status = $b['booking_status'];
if ($b_status === 'active') {
    $current_time = new DateTime();
    $cstart = new DateTime($b['booking_date'] . ' ' . $b['pick_up_time']);
    if ($current_time >= $cstart) {
        $cend = clone $cstart;
        $cend->modify('+3 hours');
        if ($current_time > $cend) {
            $b_status = 'completed';
        }
    }
}

if ($b_status !== 'completed') {
    die("Invoices can only be generated for completed rides.");
}

// Generate PDF using FPDF
require('../includes/fpdf.php');

class PDF extends FPDF {
    // Page header
    function Header() {
        // Arial bold 20
        $this->SetFont('Arial', 'B', 20);
        $this->SetTextColor(255, 77, 1); // #FF4D01 (Primary)
        $this->Cell(0, 10, 'UrbanRide', 0, 1, 'L');
        
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(100, 116, 139); // Slate-500
        $this->Cell(0, 5, 'Your Premium Mobility Partner', 0, 1, 'L');
        
        $this->Ln(5);
        $this->SetDrawColor(226, 232, 240); // Border-Slate-200
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
    }

    // Page footer
    function Footer() {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(148, 163, 184); // Slate-400
        $this->Cell(0, 10, 'Thank you for riding with UrbanRide!', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 11);

// Title Box
$pdf->SetFillColor(248, 250, 252); // Slate-50
$pdf->SetTextColor(15, 23, 42); // Slate-900
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 12, '  CAB RIDE INVOICE', 0, 1, 'L', true);
$pdf->Ln(5);

// Metadata Block
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(100, 116, 139); // Slate-500
$pdf->Cell(50, 6, 'Invoice No:', 0, 0);
$pdf->SetTextColor(15, 23, 42); // Slate-900
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(50, 6, $b['booking_id'], 0, 0);

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(100, 116, 139); // Slate-500
$pdf->Cell(50, 6, 'Invoice Date:', 0, 0);
$pdf->SetTextColor(15, 23, 42); // Slate-900
$pdf->Cell(0, 6, date('d M Y'), 0, 1);

$pdf->SetTextColor(100, 116, 139); // Slate-500
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 6, 'Booking Date:', 0, 0);
$pdf->SetTextColor(15, 23, 42); // Slate-900
$pdf->Cell(50, 6, date('d M Y', strtotime($b['booking_date'])), 0, 0);

$pdf->SetTextColor(100, 116, 139); // Slate-500
$pdf->Cell(50, 6, 'Pickup Time:', 0, 0);
$pdf->SetTextColor(15, 23, 42); // Slate-900
$pdf->Cell(0, 6, date('h:i A', strtotime($b['pick_up_time'])), 0, 1);

$pdf->Ln(8);

// Two columns for Customer & Agency info
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(95, 6, 'PASSENGER DETAILS', 0, 0);
$pdf->Cell(0, 6, 'AGENCY DETAILS', 0, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(71, 85, 105); // Slate-600
$pdf->Cell(95, 5, 'Name: ' . $b['name'], 0, 0);
$pdf->Cell(0, 5, 'Agency Name: ' . $b['agency_name'], 0, 1);

$pdf->Cell(95, 5, 'Phone: ' . $b['mobile'], 0, 0);
$pdf->Cell(0, 5, 'Cab Model: ' . ($b['cab_name'] ?? 'Premium Cab'), 0, 1);

$pdf->Cell(95, 5, 'Email: ' . ($b['email'] ?: $b['user_email']), 0, 0);
$pdf->Cell(0, 5, 'Vehicle ID: ' . $b['cab_id'], 0, 1);

$pdf->Ln(10);

// Trip Details Section
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(0, 6, 'TRIP DETAILS', 0, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(71, 85, 105); // Slate-600
$pdf->Cell(45, 6, 'Pickup Location:', 0, 0);
$pdf->SetTextColor(15, 23, 42);
$pdf->MultiCell(0, 6, $b['pickup_location'], 0, 'L');

$pdf->SetTextColor(71, 85, 105);
$pdf->Cell(45, 6, 'Dropoff Location:', 0, 0);
$pdf->SetTextColor(15, 23, 42);
$pdf->MultiCell(0, 6, $b['drop_location'], 0, 'L');

$pdf->SetTextColor(71, 85, 105);
$pdf->Cell(45, 6, 'Trip Type:', 0, 0);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(0, 6, ucfirst($b['trip_type']), 0, 1);

$pdf->SetTextColor(71, 85, 105);
$pdf->Cell(45, 6, 'Estimated Distance:', 0, 0);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(0, 6, $est_distance . ' km', 0, 1);

$pdf->Ln(10);

// Billing Section (Table)
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(0, 6, 'BILLING SUMMARY', 0, 1);
$pdf->Ln(2);

// Table Header
$pdf->SetFillColor(241, 245, 249); // Slate-100
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(110, 8, '  Description', 0, 0, 'L', true);
$pdf->Cell(40, 8, 'Rate / Details  ', 0, 0, 'R', true);
$pdf->Cell(40, 8, 'Amount  ', 0, 1, 'R', true);

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(71, 85, 105);

// Base Fare Calculation (Rate per km * Est Distance)
$rate_per_km = floatval($b['price_per_km'] ?? 15);
$distance = floatval($est_distance);
$base_fare = $rate_per_km * $distance;

$pdf->Cell(110, 8, '  Base Ride Fare', 0, 0, 'L');
$pdf->Cell(40, 8, 'Rs ' . number_format($rate_per_km, 2) . '/km  ', 0, 0, 'R');
$pdf->Cell(40, 8, 'Rs ' . number_format($base_fare, 2) . '  ', 0, 1, 'R');

// GST / Taxes (5%)
$gst = $base_fare * 0.05;
$pdf->Cell(110, 8, '  Taxes & Service Fees (5% GST)', 0, 0, 'L');
$pdf->Cell(40, 8, '5%  ', 0, 0, 'R');
$pdf->Cell(40, 8, 'Rs ' . number_format($gst, 2) . '  ', 0, 1, 'R');

// Coupon Discount if applicable
$total_due = $base_fare + $gst;
$discount = 0.00;
if (abs($total_due - floatval($b['total_price'])) > 1.0) {
    // If stored total_price is less than total_due, a discount was applied
    $discount = $total_due - floatval($b['total_price']);
    $pdf->SetTextColor(220, 38, 38); // Red-600
    $pdf->Cell(110, 8, '  Coupon Discount (APP50)', 0, 0, 'L');
    $pdf->Cell(40, 8, 'Flat  ', 0, 0, 'R');
    $pdf->Cell(40, 8, '- Rs ' . number_format($discount, 2) . '  ', 0, 1, 'R');
}

$pdf->SetDrawColor(226, 232, 240);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());

// Total
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(110, 10, '  Total Paid (Cash)', 0, 0, 'L');
$pdf->Cell(40, 10, '', 0, 0, 'R');
$pdf->Cell(40, 10, 'Rs ' . number_format(floatval($b['total_price']), 2) . '  ', 0, 1, 'R');

$pdf->Output('I', 'Invoice_' . $b['booking_id'] . '.pdf');
exit;
?>

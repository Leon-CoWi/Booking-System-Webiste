<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit(); }
require_once '../connection.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
    $roomID   = mysqli_real_escape_string($conn, $_POST['room']);
    $checkin  = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $guests   = (int)$_POST['guests'];
    $nights   = (int)((strtotime($checkout) - strtotime($checkin)) / 86400);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $adminID  = isset($_SESSION['adminID']) ? $_SESSION['adminID'] : 'NULL';

    $payMethod      = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $today          = date('Y-m-d');
    $roomInfo       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rooms WHERE RoomID = '$roomID'"));
    $baseRate       = $roomInfo['BaseRate'] ?? 0;
    $extraGuestRate = $roomInfo['ExtraGuestRate'] ?? 0;
    $maxOccupancies = $roomInfo['MaxOccupancies'] ?? 0;
    $extraGuests    = max(0, $guests - $maxOccupancies);
    $extraGuestFee  = $extraGuests * $extraGuestRate;

    $discountedWeeks = floor($nights / 7);
    $remainingDays   = $nights % 7;
    $discountAmount  = ($discountedWeeks * 7 * $baseRate) * 0.10;
    $totalAmount     = ($discountedWeeks * 7 * $baseRate * 0.90) + ($remainingDays * $baseRate) + ($extraGuestFee * $nights);

    // INSERT CUSTOMER
    mysqli_query($conn, "INSERT INTO customers (CustomerName, Email, PhoneNumber)
        VALUES ('$name', '$email', '$phone')");
    $customerID = mysqli_insert_id($conn);

    // INSERT PAYMENT
    mysqli_query($conn, "INSERT INTO payments (CustomerID, Location, RoomID, PaymentDate, Charges, PaymentMethod, DiscountAmount, TotalAmount, PaidAmount, PaymentStatus)
        VALUES ($customerID, '$location', '$roomID', '$today', $baseRate, '$payMethod', $discountAmount, $totalAmount, $totalAmount, 'Paid')");
    $paymentID = mysqli_insert_id($conn);

    // INSERT BOOKING
    $adminVal = ($adminID === 'NULL') ? 'NULL' : $adminID;
    mysqli_query($conn, "INSERT INTO bookings (AdminID, CustomerID, RoomID, Location, PaymentID, ReservationType, CheckInDate, CheckOutDate, GuestNumber, Nights)
        VALUES ($adminVal, $customerID, '$roomID', '$location', $paymentID, 'Active', '$checkin', '$checkout', $guests, $nights)");
    $success = "Walk-in reservation added successfully!";

    require_once '../send_receipt_email.php';
    sendPaymentConfirmEmail($email, $name, [
        'bookingID'     => $paymentID,
        'customerName'  => $name,
        'roomNumber'    => $roomInfo['RoomType'] . ' - ' . $roomInfo['RoomNumber'],
        'location'      => $location,
        'checkIn'       => $checkin,
        'checkOut'      => $checkout,
        'nights'        => $nights,
        'guests'        => $guests,
        'paymentMethod' => $payMethod,
        'total'         => $totalAmount,
    ]);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Walk-in - Admin</title>
    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="admin-shell">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="admin-topbar">
            <h2>Add Walk-in Reservation</h2>
        </div>

        <?php if($success): ?><div class="success-msg"><?= $success ?></div><?php endif; ?>
        <?php if($error): ?><div class="error-msg"><?= $error ?></div><?php endif; ?>

        <div class="form-card">
            <form method="POST">

                <!-- STEP 1: Guest Info -->
                <h3 style="color:#c9a84c; margin-bottom:16px;">Guest Info</h3>
                <div class="form-grid">
                    <div class="form-field">
                        <label>Full Name</label>
                        <input type="text" name="name" placeholder="Juan Dela Cruz" required>
                    </div>
                    <div class="form-field">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="juan@email.com" required>
                    </div>
                    <div class="form-field">
                        <label>Phone Number</label>
                        <input type="text" name="phone" placeholder="09xx xxx xxxx" required>
                    </div>
                </div>

                <!-- STEP 2-5: Booking Info -->
                <h3 style="color:#c9a84c; margin:20px 0 16px;">Booking Info</h3>
                <div class="form-grid">

                    <!-- STEP 2: Branch -->
                    <div class="form-field">
                        <label>Branch</label>
                        <select name="location" id="locationSelect" onchange="fetchAvailableRooms()" required>
                            <option value="">Select Branch</option>
                            <option value="Casa FAM Appartelle">Casa Fam</option>
                            <option value="V.F Riton Appartelle">VF Riton</option>
                        </select>
                    </div>

                    <!-- STEP 3: Dates -->
                    <div class="form-field">
                        <label>Check In</label>
                        <input type="date" name="checkin" id="checkin"
                               min="<?= date('Y-m-d') ?>"
                               onchange="fetchAvailableRooms()" required>
                    </div>
                    <div class="form-field">
                        <label>Check Out</label>
                        <input type="date" name="checkout" id="checkout"
                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                               onchange="fetchAvailableRooms()" required>
                    </div>

                    <!-- STEP 4 & 5: Available Rooms -->
                    <div class="form-field">
                        <label>Room</label>
                        <select name="room" id="roomSelect" onchange="calcTotal()" required disabled>
                            <option value="">Select Branch & Dates First</option>
                        </select>
                        <small id="roomNote" style="font-size:11px;"></small>
                    </div>

                    <div class="form-field">
                        <label>Number of Guests</label>
                        <input type="number" name="guests" id="guestsInput" min="1" max="2" value="1" onchange="calcTotal()" required>
                    </div>

                    <div class="form-field">
                        <label>Payment Method</label>
                        <select name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="Cash">Cash</option>
                            <option value="GCash">GCash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>

                    <div class="form-field">
                        <label>Total Amount</label>
                        <input type="text" id="totalDisplay" readonly
                               style="background:#0d1017; color:#c9a84c; font-weight:bold;"
                               placeholder="Select room and dates first">
                        <input type="hidden" name="total_amount" id="totalHidden">
                    </div>
                </div>

                <div style="margin-top:20px; display:flex; gap:10px;">
                    <button type="submit">Save Reservation</button>
                    <a href="reservations.php"><button type="button" class="btn-cancel">Cancel</button></a>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="walkin.js"></script>
</body>
</html>
<?php
session_start();
require_once '../connection.php';


// ── GET values from rooms_availability.php 
$roomNumber = $_GET['RoomNumber'] ?? '';
$checkIn    = $_GET['CIdate']     ?? '';
$checkOut   = $_GET['COdate']     ?? '';
$roomType   = $_GET['RoomType']   ?? '';
$guestNumber = (int)($_GET['guests'] ?? 1);

if (empty($roomNumber) || empty($checkIn) || empty($checkOut)) {
    die("Invalid booking details.");
}

// ── Fetch room from DB
$roomQuery = mysqli_query($conn,
    "SELECT * FROM rooms WHERE RoomNumber = '" . mysqli_real_escape_string($conn, $roomNumber) . "' LIMIT 1"
);

if (!$roomQuery || mysqli_num_rows($roomQuery) == 0) {
    die("Room not found.");
}

$room     = mysqli_fetch_assoc($roomQuery);
$baseRate = $room['BaseRate'];
$roomID   = $room['RoomID'];
$location = $room['Location'];

// ── Compute nights 
$nights = (strtotime($checkOut) - strtotime($checkIn)) / (60 * 60 * 24);

if ($nights <= 0) {
    die("Invalid check-in and check-out dates.");
}

// ── Discount calculation (just for display) 
$discountRate    = 0.10;
$discountedWeeks = floor($nights / 7);
$remainingDays   = $nights % 7;
$discountedCost  = $discountedWeeks * 7 * $baseRate * (1 - $discountRate);
$remainingCost   = $remainingDays * $baseRate;
$total           = $discountedCost + $remainingCost;
$fullPrice       = $nights * $baseRate;
$discountAmount  = $fullPrice - $total;
$extraGuestRate  = $room['ExtraGuestRate'];
$maxOccupancies  = $room['MaxOccupancies'];
$extraGuests     = max(0, $guestNumber - $maxOccupancies);
$extraGuestFee   = $extraGuests * $extraGuestRate * $nights;
$total           = $total + $extraGuestFee;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    $firstName     = trim($_POST['first_name']     ?? '');
    $lastName      = trim($_POST['last_name']      ?? '');
    $contact       = trim($_POST['contact_number'] ?? '');
    $email         = trim($_POST['email']          ?? '');
    $notes         = trim($_POST['notes']          ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? '');
    $guestNumber   = (int)($_POST['guest_number']  ?? 1);

    // Re-read booking values from hidden fields
    $checkIn  = trim($_POST['CIdate']  ?? $checkIn);
    $checkOut = trim($_POST['COdate']  ?? $checkOut);
    $nights   = (int)($_POST['nights'] ?? $nights);
    $total    = (float)($_POST['total'] ?? $total);
 
    if (empty($firstName) || empty($lastName) || empty($contact) || empty($email) || empty($paymentMethod)) {
        die("Please fill in all required fields.");
    }
 
    $customerName  = mysqli_real_escape_string($conn, $firstName . ' ' . $lastName);
    $emailEsc      = mysqli_real_escape_string($conn, $email);
    $contactEsc    = mysqli_real_escape_string($conn, $contact);
    $payMethodEsc  = mysqli_real_escape_string($conn, $paymentMethod);
    $checkInEsc    = mysqli_real_escape_string($conn, $checkIn);
    $checkOutEsc   = mysqli_real_escape_string($conn, $checkOut);
    $roomTypeEsc   = mysqli_real_escape_string($conn, $roomType);
    $roomIDEsc     = mysqli_real_escape_string($conn, $roomID);
    $locationEsc   = mysqli_real_escape_string($conn, $location);
    $notesEsc = mysqli_real_escape_string($conn, $notes);
    $paymentDate   = date('Y-m-d');
    $paymentStatus = 'Pending';
 
    // ── Insert into customers 
    if (!mysqli_query($conn,
        "INSERT INTO customers (CustomerName, Email, PhoneNumber)
         VALUES ('$customerName', '$emailEsc', '$contactEsc')"
    )) {
        die("Error saving customer: " . mysqli_error($conn));
    }
 
    $customerID = mysqli_insert_id($conn);
 

    if (!mysqli_query($conn,
        "INSERT INTO payments
            (CustomerID, Location, RoomID, PaymentDate, Charges, PaymentMethod,
            DiscountAmount, TotalAmount, PaidAmount, PaymentStatus)
        VALUES
            ($customerID, '$locationEsc', '$roomIDEsc', '$paymentDate', $baseRate,
            '$payMethodEsc', $discountAmount, $total, 0, '$paymentStatus')"
    )) {
        die("Error saving payment: " . mysqli_error($conn));
    }

$paymentID = mysqli_insert_id($conn);

// 🔥 ADD RECEIPT UPLOAD HERE
$result = uploadPaymentReceipts(
    $conn,
    $customerID,
    $paymentID,
    $_FILES['payment_receipts']
);

    // ── Insert into bookings 
    if (!mysqli_query($conn,
        "INSERT INTO bookings
            (AdminID, CustomerID, RoomID, Location, PaymentID,
            ReservationType, CheckInDate, CheckOutDate, GuestNumber, Nights, Request)
        VALUES
            (NULL, $customerID, '$roomIDEsc', '$locationEsc', $paymentID,
            '$roomTypeEsc', '$checkInEsc', '$checkOutEsc', $guestNumber, $nights, '$notesEsc')"
    )) {
        die("Error saving booking: " . mysqli_error($conn));
    }
    $bookingID = mysqli_insert_id($conn);


    require_once '../send_receipt_email.php';
    sendReceiptEmail($email, $firstName . ' ' . $lastName, [
        'bookingID'     => $bookingID,
        'customerName'  => $firstName . ' ' . $lastName,
        'roomType'      => $roomType,
        'roomNumber'    => $roomNumber,
        'location'      => $location,
        'checkIn'       => $checkIn,
        'checkOut'      => $checkOut,
        'nights'        => $nights,
        'guests'        => $guestNumber,
        'paymentMethod' => $paymentMethod,
        'total'         => $total,
        'notes'         => $notes,
    ]);
    // ── Done — redirect 
    $_SESSION['bookingID']  = $bookingID;
    $_SESSION['customerID'] = $customerID;
    $_SESSION['paymentID']  = $paymentID;
    header("Location: receipt.php");
    exit;
}


function uploadPaymentReceipts($conn, $customerID, $paymentID, $files) {

    if (empty($files['name'][0])) {
        return "No receipt images selected.";
    }

    $customerID = (int)$customerID;
    $paymentID  = (int)$paymentID;

    // 📁 DIFFERENT FOLDER (RECEIPTS)
    $uploadDir = __DIR__ . "/../User/uploads/receipts/" . $paymentID . "/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $stmt = $conn->prepare("
        INSERT INTO payment_receipts (CustomerID, PaymentID, ReceiptPath)
        VALUES (?, ?, ?)
    ");

    if (!$stmt) {
        return "Prepare failed: " . $conn->error;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $uploadedCount = 0;

    for ($i = 0; $i < count($files['name']); $i++) {

        if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;

        $originalName = basename($files['name'][$i]);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) continue;

        $newFileName = uniqid('receipt_', true) . '.' . $ext;
        $targetPath = $uploadDir . $newFileName;

        if (move_uploaded_file($files['tmp_name'][$i], $targetPath)) {

            // relative path stored in DB
            $relativePath = 'uploads/receipts/' . $paymentID . '/' . $newFileName;

            $stmt->bind_param("iis", $customerID, $paymentID, $relativePath);

            if ($stmt->execute()) {
                $uploadedCount++;
            }
        }
    }

    $stmt->close();

    return $uploadedCount > 0
        ? "$uploadedCount receipt image(s) uploaded successfully!"
        : "No valid receipt images uploaded.";
}

// ── Payment Details
$gcash = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT ImagePath 
     FROM payment_details 
     WHERE Location = 'GCash' 
     ORDER BY ImageID DESC 
     LIMIT 1"
));

$metrobank = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT ImagePath 
     FROM payment_details 
     WHERE Location = 'Metrobank' 
     ORDER BY ImageID DESC 
     LIMIT 1"
));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="book_rooms.css">
        <link rel="stylesheet" href="ra_header.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<?php include 'ra_header.php'; ?>

<body>
    <div class="page-wrapper">
        <div class="left-side">
            <div class="room">
                <div class="room-pic">
                    <?php
                    $roomImage = 'homee.jpg';
                    $imageQuery = mysqli_query($conn, "
                        SELECT ImagePath
                        FROM room_images
                        WHERE RoomID = '$roomID'
                        ORDER BY ImageOrder ASC
                        LIMIT 1
                    ");
                    if ($imageQuery && mysqli_num_rows($imageQuery) > 0) {
                        $imageRow = mysqli_fetch_assoc($imageQuery);
                        $roomImage = '/Transient/User/' . $imageRow['ImagePath'];
                    }
                    ?>
                    <img src="<?= htmlspecialchars($roomImage) ?>"
                        alt="<?= htmlspecialchars($room['RoomType']) ?>"
                        class="selected-room-image">
                </div>

                <div class="room-info">
                    <h2><?= htmlspecialchars($room['RoomType']) ?> - <?= htmlspecialchars($room['RoomNumber']) ?></h2>
                    <p><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($room['Location']) ?></p>
                    <p><i class="fa-solid fa-users"></i> Max <?= $room['MaxOccupancies'] ?> Guests</p>
                    <p><i class="fa-solid fa-bed"></i> <?= htmlspecialchars($room['BedConfiguration']) ?></p>
                    <p><i class="fa-solid fa-bath"></i> <?= $room['NumberBathrooms'] ?> Bathroom(s)</p>
                    <p><strong>PHP <?= number_format($room['BaseRate'], 2) ?> / night</strong></p>
                    <p><?= htmlspecialchars($room['RoomAmenities']) ?></p>
                </div>
            </div>

            <form action="?RoomNumber=<?= urlencode($room['RoomNumber']) ?>&CIdate=<?= urlencode($checkIn) ?>&COdate=<?= urlencode($checkOut) ?>&RoomType=<?= urlencode($roomType) ?>" method="POST" enctype="multipart/form-data">
                <!-- PASS SELECTED ROOM DATA (NO ROOMID) -->
                <input type="hidden" name="RoomNumber" value="<?= htmlspecialchars($room['RoomNumber']) ?>">
                <input type="hidden" name="RoomType" value="<?= htmlspecialchars($room['RoomType']) ?>">
                <input type="hidden" name="CIdate" value="<?= htmlspecialchars($checkIn) ?>">
                <input type="hidden" name="COdate" value="<?= htmlspecialchars($checkOut) ?>">
                <input type="hidden" name="nights" value="<?= $nights ?>">
                <input type="hidden" name="total" value="<?= $total ?>">
                <input type="hidden" name="guest_number" value="<?= $guestNumber ?>">

                <div class="section">
                    <div class="section-header">
                        <div class="step-number">1</div>
                        <h2 class="section-title">Your Details</h2>
                    </div>

                    <div class="form-grid">
                        <input type="text" name="first_name" placeholder="First Name" required>
                        <input type="text" name="last_name" placeholder="Last Name" required>
                        <input type="text" name="contact_number" placeholder="Contact Number" required>
                        <input type="email" name="email" placeholder="Email Address" required>
                        <textarea name="notes" class="full-width" placeholder="Special requests"></textarea>
                    </div>
                </div>

            <!-- STEP 2 -->
                <div class="section">
                    <div class="section-header">
                        <div class="step-number">2</div>
                        <h2 class="section-title">Complete Your Booking</h2>
                    </div>

                    <select name="payment_method" required>
                        <option value="">Select Payment Method</option>
                        <option value="GCash">GCash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>

                    <div class="payment-options">
                        <div class="payment-card">
                            <h3><i class="fa-solid fa-wallet"></i> GCash Payment</h3>
                            <p>Scan the QR code to pay via GCash.</p>

                            <button type="button" onclick="openModal('gcashModal')">
                                View GCash QR Code
                            </button>
                        </div>

                        <div class="payment-card">
                            <h3><i class="fa-solid fa-building-columns"></i> Bank Details</h3>
                            <p>Type in the Metrobank details for bank transfer</p>

                            <button type="button" class="btn" onclick="openModal('bankModal')">
                                View Metrobank Details
                            </button>
                        </div>
                    </div>
                </div>

            <!-- STEP 3 -->
                <div class="section">
                    <div class="section-header">
                        <div class="step-number">3</div>
                        <h2 class="section-title">Upload Your Payment Proof</h2>
                    </div>

                    <p style="color:#888; font-size:12px; margin-bottom:10px;">
                        Upload screenshot or image of your payment receipt (GCash or Bank Transfer).
                    </p>

                    <!-- RECEIPT UPLOAD INPUT -->
                    <label class="upload-btn">
                        Upload Payment Proof
                        <input
                            type="file"
                            name="payment_receipts[]"
                            multiple
                            accept="image/*"
                            required
                            id="receiptInput"
                        >
                    </label>

                    <div id="preview"></div>
                    
                    <div class="submit-container">
                        <button type="submit">Confirm Booking</button>
                    </div>
                </div>
            </form>
        </div>


<!-- MOBILE BOOKING DRAWER -->
    <div class="mobile-booking-drawer" id="mobileDrawer">
    <!-- HANDLE -->
    <div class="mobile-drawer-handle"
         onclick="toggleMobileDrawer()">

        <div class="mobile-drawer-pill"></div>

        <div class="mobile-drawer-header">

            <span>
                <i class="fa-solid fa-receipt"></i>
                Booking Details
            </span>

            <svg id="mobileChevron"
                 class="mobile-chevron"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2.5"
                 stroke-linecap="round"
                 stroke-linejoin="round">

                <polyline points="18 15 12 9 6 15"></polyline>

            </svg>

        </div>
    </div>

    <!-- BODY -->
    <div class="mobile-drawer-body">

        <div class="booking-row">
            <span>Location:</span>
            <span><?= htmlspecialchars($location) ?></span>
        </div>

        <div class="booking-row">
            <span>Check-in:</span>
            <span><?= htmlspecialchars($checkIn) ?></span>
        </div>

        <div class="booking-row">
            <span>Check-out:</span>
            <span><?= htmlspecialchars($checkOut) ?></span>
        </div>

        <div class="booking-row">
            <span>Nights:</span>
            <span><?= $nights ?></span>
        </div>

        <div class="booking-row">
            <span>Guests:</span>
            <span><?= $guestNumber ?></span>
        </div>

        <div class="booking-row">
            <span>Room:</span>
            <span><?= htmlspecialchars($room['RoomNumber']) ?></span>
        </div>

        <div class="booking-row">
            <span>Price/Night:</span>
            <span>₱<?= number_format($room['BaseRate'], 2) ?></span>
        </div>

        <div class="booking-total">
            Total: ₱<?= number_format($total, 2) ?>
        </div>

    </div>
    </div>

        <div class="booking-details">
            <div class="booking-header">Booking Details</div>

            <div class="booking-row">
                <span>Check-in</span>
                <span><?= htmlspecialchars($checkIn) ?></span>
            </div>

            <div class="booking-row">
                <span>Check-out</span>
                <span><?= htmlspecialchars($checkOut) ?></span>
            </div>

            <div class="booking-row">
                <span>Nights</span>
                <span><?= $nights ?></span>
            </div>

            <div class="booking-row">
                <span>Guests</span>
                <span><?= $guestNumber ?></span>
            </div>

            <div class="booking-row">
                <span> <?= htmlspecialchars($room['RoomNumber']) ?></span>
            </div>

            <div class="booking-row">
                <span>Rate/night</span>
                <span>PHP <?= number_format($room['BaseRate'], 2) ?></span>
            </div>

            <div class="booking-total">
                Total: PHP <?= number_format($total, 2) ?>
            </div>
        </div>
    </div>

        <!-- GCash Modal -->
    <div id="gcashModal" class="modal">
        <div class="modal-content">
            <img src="../User/<?= htmlspecialchars($gcash['ImagePath'] ?? 'uploads/default.png') ?>" 
                alt="GCash QR Code">
            <span class="close" onclick="closeModal('gcashModal')">&times;</span>
        </div>
    </div>

    <!-- Bank Modal -->
    <div id="bankModal" class="modal">
        <div class="modal-content">
            <img src="../User/<?= htmlspecialchars($metrobank['ImagePath'] ?? 'uploads/default.png') ?>" 
                alt="Metrobank QR Code">
            <span class="close" onclick="closeModal('bankModal')">&times;</span>
        </div>
    </div>

    <script src="book_rooms.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const input = document.getElementById('receiptInput');
        const preview = document.getElementById('preview');

        input.addEventListener('change', function () {
            preview.innerHTML = "";

            const files = Array.from(this.files);

            files.forEach(file => {
                // only images safety check
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();

                reader.onload = function (e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                };

                reader.readAsDataURL(file);
            });
        });

/* MOBILE DRAWER */

let mobileDrawerOpen = false;

function toggleMobileDrawer() {

    const drawer =
        document.getElementById('mobileDrawer');

    const chevron =
        document.getElementById('mobileChevron');

    mobileDrawerOpen = !mobileDrawerOpen;

    if (mobileDrawerOpen) {

        drawer.classList.add('open');

        chevron.style.transform =
            'rotate(180deg)';

    } else {

        drawer.classList.remove('open');

        chevron.style.transform =
            'rotate(0deg)';
    }
}
</script>
</body>
</html>
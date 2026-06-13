<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit(); }
require_once '../connection.php';

// HANDLE EDIT SAVE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_booking') {
    header('Content-Type: application/json');
    $bookingID = (int)$_POST['booking_id'];
    $checkIn   = mysqli_real_escape_string($conn, $_POST['check_in']);
    $checkOut  = mysqli_real_escape_string($conn, $_POST['check_out']);
    $roomID    = mysqli_real_escape_string($conn, $_POST['room_id']);
    $guests    = (int)$_POST['guests'];

    $nights = (int)((strtotime($checkOut) - strtotime($checkIn)) / 86400);

    $room           = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rooms WHERE RoomID = '$roomID'"));
    $baseRate       = $room['BaseRate']       ?? 0;
    $extraGuestRate = $room['ExtraGuestRate'] ?? 0;
    $maxOccupancies = $room['MaxOccupancies'] ?? 0;

    $extraGuests   = max(0, $guests - $maxOccupancies);
    $extraGuestFee = $extraGuests * $extraGuestRate;

    $discountedWeeks = floor($nights / 7);
    $remainingDays   = $nights % 7;
    $discountAmount  = ($discountedWeeks * 7 * $baseRate) * 0.10;
    $charges         = $baseRate;
    $totalAmount     = ($discountedWeeks * 7 * $baseRate * 0.90)
                     + ($remainingDays * $baseRate)
                     + ($extraGuestFee * $nights);

    $current = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT b.*, p.PaymentID
        FROM bookings b
        JOIN payments p ON b.PaymentID = p.PaymentID
        WHERE b.BookingID = $bookingID
    "));

    mysqli_query($conn, "
        UPDATE bookings SET
            RoomID       = '$roomID',
            CheckInDate  = '$checkIn',
            CheckOutDate = '$checkOut',
            Nights       = $nights,
            GuestNumber  = $guests
        WHERE BookingID  = $bookingID
    ");

    mysqli_query($conn, "
        UPDATE payments SET
            RoomID         = '$roomID',
            Charges        = $charges,
            DiscountAmount = $discountAmount,
            TotalAmount    = $totalAmount
        WHERE PaymentID    = {$current['PaymentID']}
    ");

    echo json_encode(['success' => true]); exit();
}

// HANDLE CANCEL FROM EDIT MODAL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_booking') {
    header('Content-Type: application/json');
    $bookingID = (int)$_POST['booking_id'];

    $payInfo = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT p.*, c.CustomerName, c.Email, b.BookingID,
               b.CheckInDate, b.CheckOutDate, b.GuestNumber, b.Nights,
               b.RoomID, b.Location
        FROM payments p
        JOIN customers c ON p.CustomerID = c.CustomerID
        JOIN bookings b ON b.PaymentID = p.PaymentID
        WHERE b.BookingID = $bookingID
    "));

    mysqli_query($conn, "
        UPDATE payments SET PaymentStatus = 'Cancelled'
        WHERE PaymentID = (SELECT PaymentID FROM bookings WHERE BookingID = $bookingID)
    ");

    if ($payInfo && !empty($payInfo['Email'])) {
        require_once '../send_receipt_email.php';
        sendRejectionEmail($payInfo['Email'], $payInfo['CustomerName'], [
            'bookingID'     => $payInfo['BookingID'],
            'customerName'  => $payInfo['CustomerName'],
            'roomNumber'    => $payInfo['RoomID'],
            'location'      => $payInfo['Location'],
            'checkIn'       => $payInfo['CheckInDate'],
            'checkOut'      => $payInfo['CheckOutDate'],
            'nights'        => $payInfo['Nights'],
            'guests'        => $payInfo['GuestNumber'],
            'paymentMethod' => $payInfo['PaymentMethod'],
            'total'         => $payInfo['TotalAmount'],
        ]);
    }

    echo json_encode(['success' => true]); exit();
}

// HANDLE GET AVAILABLE ROOMS (AJAX)
if (isset($_GET['get_rooms'])) {
    header('Content-Type: application/json');
    $bookingID = (int)$_GET['booking_id'];
    $checkIn   = mysqli_real_escape_string($conn, $_GET['check_in']);
    $checkOut  = mysqli_real_escape_string($conn, $_GET['check_out']);

    $current  = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT b.Location, b.RoomID FROM bookings b WHERE b.BookingID = $bookingID
    "));
    $location = mysqli_real_escape_string($conn, $current['Location']);

    // ← ExtraGuestRate added here
    $rooms = mysqli_query($conn, "
        SELECT r.RoomID, r.RoomNumber, r.RoomType, r.BaseRate, r.MaxOccupancies, r.ExtraGuestRate
        FROM rooms r
        WHERE r.Location = '$location'
        AND r.RoomID NOT IN (
            SELECT b.RoomID FROM bookings b
            JOIN payments p ON b.PaymentID = p.PaymentID
            WHERE b.BookingID != $bookingID
            AND p.PaymentStatus NOT IN ('Cancelled', 'Rejected')
            AND b.CheckInDate  < '$checkOut'
            AND b.CheckOutDate > '$checkIn'
        )
        ORDER BY r.RoomType, r.RoomID
    ");

    $result = [];
    while ($r = mysqli_fetch_assoc($rooms)) {
        $result[] = $r;
    }
    echo json_encode($result); exit();
}

// HANDLE GET BOOKING INFO (AJAX)
if (isset($_GET['get_booking'])) {
    header('Content-Type: application/json');

    $id = (int)$_GET['booking_id'];

    $data = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT b.*, 
               c.CustomerName, c.Email, c.PhoneNumber,
               r.RoomType, r.BaseRate,
               p.PaymentID, p.PaymentStatus, p.PaymentMethod,
               p.Charges, p.DiscountAmount, p.TotalAmount, p.PaidAmount, p.PaymentDate,
               pr.ReceiptPath
        FROM bookings b
        JOIN customers c ON b.CustomerID = c.CustomerID
        JOIN rooms r ON b.RoomID = r.RoomID
        JOIN payments p ON b.PaymentID = p.PaymentID
        LEFT JOIN payment_receipts pr 
               ON pr.PaymentID = p.PaymentID
        WHERE b.BookingID = $id
        ORDER BY pr.CreatedAt DESC
        LIMIT 1
    "));

    // ✅ FIX RECEIPT PATH HERE
$data['ReceiptPath'] = "../User/" . $data['ReceiptPath'];

    echo json_encode($data);
    exit();
}

// HANDLE ACCEPT
if (isset($_GET['accept'])) {
    $id = (int)$_GET['accept'];
    mysqli_query($conn, "UPDATE payments SET PaymentStatus = 'Paid', PaidAmount = TotalAmount WHERE PaymentID = (SELECT PaymentID FROM bookings WHERE BookingID = $id)");

    $payInfo = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT p.*, c.CustomerName, c.Email, b.BookingID,
               b.CheckInDate, b.CheckOutDate, b.GuestNumber, b.Nights,
               b.RoomID, b.Location
        FROM payments p
        JOIN customers c ON p.CustomerID = c.CustomerID
        JOIN bookings b ON b.PaymentID = p.PaymentID
        WHERE b.BookingID = $id
    "));
    if ($payInfo && !empty($payInfo['Email'])) {
        require_once '../send_receipt_email.php';
        sendPaymentConfirmEmail($payInfo['Email'], $payInfo['CustomerName'], [
            'bookingID'     => $payInfo['BookingID'],
            'customerName'  => $payInfo['CustomerName'],
            'roomNumber'    => $payInfo['RoomID'],
            'location'      => $payInfo['Location'],
            'checkIn'       => $payInfo['CheckInDate'],
            'checkOut'      => $payInfo['CheckOutDate'],
            'nights'        => $payInfo['Nights'],
            'guests'        => $payInfo['GuestNumber'],
            'paymentMethod' => $payInfo['PaymentMethod'],
            'total'         => $payInfo['TotalAmount'],
        ]);
    }
    header('Location: reservations.php'); exit();
}

// HANDLE REJECT
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    mysqli_query($conn, "UPDATE payments SET PaymentStatus = 'Cancelled' WHERE PaymentID = (SELECT PaymentID FROM bookings WHERE BookingID = $id)");

    $payInfo = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT p.*, c.CustomerName, c.Email, b.BookingID,
               b.CheckInDate, b.CheckOutDate, b.GuestNumber, b.Nights,
               b.RoomID, b.Location
        FROM payments p
        JOIN customers c ON p.CustomerID = c.CustomerID
        JOIN bookings b ON b.PaymentID = p.PaymentID
        WHERE b.BookingID = $id
    "));
    if ($payInfo && !empty($payInfo['Email'])) {
        require_once '../send_receipt_email.php';
        sendRejectionEmail($payInfo['Email'], $payInfo['CustomerName'], [
            'bookingID'     => $payInfo['BookingID'],
            'customerName'  => $payInfo['CustomerName'],
            'roomNumber'    => $payInfo['RoomID'],
            'location'      => $payInfo['Location'],
            'checkIn'       => $payInfo['CheckInDate'],
            'checkOut'      => $payInfo['CheckOutDate'],
            'nights'        => $payInfo['Nights'],
            'guests'        => $payInfo['GuestNumber'],
            'paymentMethod' => $payInfo['PaymentMethod'],
            'total'         => $payInfo['TotalAmount'],
        ]);
    }
    header('Location: reservations.php'); exit();
}

$filter = $_GET['filter'] ?? 'all';
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 20;
$offset = ($page - 1) * $limit;

$where = "";
if ($filter == 'pending')   $where = "WHERE p.PaymentStatus = 'Pending'";
if ($filter == 'paid')      $where = "WHERE p.PaymentStatus = 'Paid' AND CURDATE() < b.CheckInDate";
if ($filter == 'active')    $where = "WHERE p.PaymentStatus = 'Paid' AND CURDATE() >= b.CheckInDate AND CURDATE() <= b.CheckOutDate";
if ($filter == 'done')      $where = "WHERE p.PaymentStatus = 'Paid' AND CURDATE() > b.CheckOutDate";
if ($filter == 'cancelled') $where = "WHERE p.PaymentStatus IN ('Cancelled', 'Rejected')";

$countResult = mysqli_query($conn, "
    SELECT COUNT(*) as total
    FROM bookings b
    JOIN customers c ON b.CustomerID = c.CustomerID
    JOIN rooms r ON b.RoomID = r.RoomID
    JOIN payments p ON b.PaymentID = p.PaymentID
    $where
");
$totalRows  = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

$bookings = mysqli_query($conn, "
    SELECT b.*, c.CustomerName, c.PhoneNumber, r.RoomType, p.PaymentStatus
    FROM bookings b
    JOIN customers c ON b.CustomerID = c.CustomerID
    JOIN rooms r ON b.RoomID = r.RoomID
    JOIN payments p ON b.PaymentID = p.PaymentID
    $where
    ORDER BY b.CheckInDate DESC, b.BookingID DESC
    LIMIT $limit OFFSET $offset
") or die(mysqli_error($conn));

$counts['Pending']   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM payments p JOIN bookings b ON b.PaymentID = p.PaymentID WHERE p.PaymentStatus = 'Pending'"))['cnt'];
$counts['Paid']      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM payments p JOIN bookings b ON b.PaymentID = p.PaymentID WHERE p.PaymentStatus = 'Paid' AND CURDATE() < b.CheckInDate"))['cnt'];
$counts['Active']    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM payments p JOIN bookings b ON b.PaymentID = p.PaymentID WHERE p.PaymentStatus = 'Paid' AND CURDATE() >= b.CheckInDate AND CURDATE() <= b.CheckOutDate"))['cnt'];
$counts['Done']      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM payments p JOIN bookings b ON b.PaymentID = p.PaymentID WHERE p.PaymentStatus = 'Paid' AND CURDATE() > b.CheckOutDate"))['cnt'];
$counts['Cancelled'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM payments p JOIN bookings b ON b.PaymentID = p.PaymentID WHERE p.PaymentStatus IN ('Cancelled', 'Rejected')"))['cnt'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reservations - Admin</title>
    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        /* MODAL BACKDROP */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-backdrop.open { display: flex; }

        /* MODAL BOX */
        .modal-box {
            background: #1e1e2e;
            border: 1px solid #2e2e3e;
            border-radius: 12px;
            padding: 28px 32px;
            width: 100%;
            max-width: 520px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            color: #e0e0e0;
        }
        .modal-box h3 {
            margin: 0 0 20px;
            font-size: 16px;
            color: #fff;
            border-bottom: 1px solid #2e2e3e;
            padding-bottom: 12px;
        }
        .modal-close {
            position: absolute;
            top: 16px; right: 18px;
            background: none;
            border: none;
            color: #888;
            font-size: 20px;
            cursor: pointer;
            line-height: 1;
        }
        .modal-close:hover { color: #fff; }

        /* FORM FIELDS */
        .modal-field { margin-bottom: 14px; }
        .modal-field label {
            display: block;
            font-size: 11px;
            color: #888;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .modal-field input,
        .modal-field select {
            width: 100%;
            background: #13131f;
            border: 1px solid #2e2e3e;
            border-radius: 7px;
            padding: 8px 11px;
            color: #e0e0e0;
            font-size: 13px;
            box-sizing: border-box;
        }
        .modal-field input:focus,
        .modal-field select:focus {
            outline: none;
            border-color: #4ade80;
        }
        .modal-row { display: flex; gap: 12px; }
        .modal-row .modal-field { flex: 1; }

        /* VIEW GRID */
        .view-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 20px;
            margin-bottom: 6px;
        }
        .view-item label {
            display: block;
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 3px;
        }
        .view-item span {
            font-size: 13px;
            color: #e0e0e0;
        }
        .view-divider {
            border: none;
            border-top: 1px solid #2e2e3e;
            margin: 16px 0;
        }

        /* MODAL FOOTER */
        .modal-footer {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            margin-top: 22px;
            padding-top: 14px;
            border-top: 1px solid #2e2e3e;
        }

        /* SAVING STATE */
        .btn-saving {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
</head>
<body>
<div class="admin-shell">

    <?php include 'sidebar.php'; ?>

    <div class="admin-content">
        <div class="admin-topbar">
            <h2>Reservations</h2>
            <a href="walkin.php"><button class="btn-xs accept" style="padding:7px 16px; font-size:12px;">+ Add Walk-in</button></a>
        </div>

        <!-- STATUS TABS -->
        <div class="tab-row">
            <a href="?filter=all"       class="tab <?= $filter=='all'       ? 'active' : '' ?>">All</a>
            <a href="?filter=pending"   class="tab <?= $filter=='pending'   ? 'active' : '' ?>">Pending (<?= $counts['Pending'] ?>)</a>
            <a href="?filter=paid"      class="tab <?= $filter=='paid'      ? 'active' : '' ?>">Paid (<?= $counts['Paid'] ?>)</a>
            <a href="?filter=active"    class="tab <?= $filter=='active'    ? 'active' : '' ?>">Active (<?= $counts['Active'] ?>)</a>
            <a href="?filter=done"      class="tab <?= $filter=='done'      ? 'active' : '' ?>">Done (<?= $counts['Done'] ?>)</a>
            <a href="?filter=cancelled" class="tab <?= $filter=='cancelled' ? 'active' : '' ?>">Cancelled / Rejected (<?= $counts['Cancelled'] ?>)</a>
        </div>

        <!-- BRANCH TOGGLE -->
        <div style="display:flex; gap:8px; margin-bottom:14px;">
            <button class="branch-btn active-btn" onclick="filterBranch('all', this)">All Branches</button>
            <button class="branch-btn" onclick="filterBranch('casa', this)">Casa Fam</button>
            <button class="branch-btn" onclick="filterBranch('riton', this)">VF Riton</button>
        </div>

        <!-- TABLE -->
        <div class="table-wrap">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Guest</th>
                    <th>Phone</th>
                    <th>Branch</th>
                    <th>Room</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Nights</th>
                    <th>Guests</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php if ($totalRows == 0): ?>
                <tr>
                    <td colspan="11" style="text-align:center; color:#555; padding:20px;">No reservations found.</td>
                </tr>
                <?php else: ?>
                <?php while($row = mysqli_fetch_assoc($bookings)):
                    $loc          = strtolower($row['Location']);
                    $branch_class = str_contains($loc, 'casa') ? 'casa' : 'riton';
                    $status       = strtolower($row['PaymentStatus']);
                    $today        = date('Y-m-d');

                    if ($status == 'paid') {
                        if ($today >= $row['CheckInDate'] && $today <= $row['CheckOutDate']) {
                            $displayStatus = 'active'; $displayLabel = 'Active';
                        } elseif ($today > $row['CheckOutDate']) {
                            $displayStatus = 'done'; $displayLabel = 'Done';
                        } else {
                            $displayStatus = 'paid'; $displayLabel = 'Paid';
                        }
                    } else {
                        $displayStatus = $status;
                        $displayLabel  = $row['PaymentStatus'];
                    }

                    $canEdit = ($status == 'paid' && $displayStatus !== 'done');
                ?>
                <tr class="res-row <?= $branch_class ?>">
                    <td>#<?= $row['BookingID'] ?></td>
                    <td><?= htmlspecialchars($row['CustomerName']) ?></td>
                    <td><?= htmlspecialchars($row['PhoneNumber']) ?></td>
                    <td><?= htmlspecialchars($row['Location']) ?></td>
                    <td><?= htmlspecialchars($row['RoomType']) ?></td>
                    <td><?= date('M d, Y', strtotime($row['CheckInDate'])) ?></td>
                    <td><?= date('M d, Y', strtotime($row['CheckOutDate'])) ?></td>
                    <td><?= $row['Nights'] ?></td>
                    <td><?= $row['GuestNumber'] ?></td>
                    <td><span class="pill <?= $displayStatus ?>"><?= $displayLabel ?></span></td>
                    <td>
                        <div class="actions">
                        <?php if ($status == 'pending'): ?>
                            <a href="?accept=<?= $row['BookingID'] ?>&filter=<?= $filter ?>">
                                <button class="btn-xs accept">Accept</button>
                            </a>
                            <a href="?reject=<?= $row['BookingID'] ?>&filter=<?= $filter ?>">
                                <button class="btn-xs del">Reject</button>
                            </a>
                        <?php endif; ?>

                        <?php if ($canEdit): ?>
                            <button class="btn-xs edit"
                                onclick="openEditModal(
                                    <?= $row['BookingID'] ?>,
                                    '<?= $row['CheckInDate'] ?>',
                                    '<?= $row['CheckOutDate'] ?>',
                                    '<?= htmlspecialchars($row['RoomID'], ENT_QUOTES) ?>',
                                    <?= $row['GuestNumber'] ?>
                                )">Edit</button>
                        <?php endif; ?>

                        <?php if ($status !== 'cancelled' && $status !== 'rejected'): ?>
                            <button class="btn-xs view" onclick="openViewModal(<?= $row['BookingID'] ?>)">View</button>
                        <?php endif; ?>
                    </div>
                </td>
                </tr>
                <?php endwhile; ?>
                <?php endif; ?>
            </table>
        </div>

        <!-- PAGINATION -->
        <?php if ($totalPages > 1): ?>
        <div style="display:flex; align-items:center; gap:6px; margin-top:16px; flex-wrap:wrap;">
            <?php if ($page > 1): ?>
                <a href="?filter=<?= $filter ?>&page=<?= $page - 1 ?>" class="tab">&#8592; Prev</a>
            <?php else: ?>
                <span class="tab" style="opacity:0.4; cursor:default;">&#8592; Prev</span>
            <?php endif; ?>

            <?php
            $start = max(1, $page - 2);
            $end   = min($totalPages, $page + 2);
            if ($start > 1): ?>
                <a href="?filter=<?= $filter ?>&page=1" class="tab">1</a>
                <?php if ($start > 2): ?><span style="color:#888;">…</span><?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $start; $i <= $end; $i++): ?>
                <a href="?filter=<?= $filter ?>&page=<?= $i ?>"
                   class="tab <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($end < $totalPages): ?>
                <?php if ($end < $totalPages - 1): ?><span style="color:#888;">…</span><?php endif; ?>
                <a href="?filter=<?= $filter ?>&page=<?= $totalPages ?>" class="tab"><?= $totalPages ?></a>
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?filter=<?= $filter ?>&page=<?= $page + 1 ?>" class="tab">Next &#8594;</a>
            <?php else: ?>
                <span class="tab" style="opacity:0.4; cursor:default;">Next &#8594;</span>
            <?php endif; ?>

            <span style="color:#888; font-size:12px; margin-left:6px;">
                Showing <?= $offset + 1 ?>–<?= min($offset + $limit, $totalRows) ?> of <?= $totalRows ?> reservations
            </span>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===== EDIT MODAL ===== -->
<div class="modal-backdrop" id="editModal">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('editModal')">&#10005;</button>
        <h3>Edit Reservation <span id="editModalTitle" style="color:#4ade80;"></span></h3>

        <div class="modal-row">
            <div class="modal-field">
                <label>Check In</label>
                <input type="date" id="editCheckIn" onchange="loadAvailableRooms(); updateEditPreview();">
            </div>
            <div class="modal-field">
                <label>Check Out</label>
                <input type="date" id="editCheckOut" onchange="loadAvailableRooms(); updateEditPreview();">
            </div>
        </div>

        <div class="modal-row">
            <div class="modal-field">
                <label>Room</label>
                <select id="editRoomID" onchange="updateEditPreview()">
                    <option value="">— pick dates first —</option>
                </select>
            </div>
            <div class="modal-field">
                <label>Number of Guests</label>
                <input type="number" id="editGuests" min="1" value="1" onchange="updateEditPreview()">
            </div>
        </div>

        <!-- Price Preview -->
        <div style="background:#13131f; border:1px solid #2e2e3e; border-radius:8px; padding:12px 14px; margin-top:4px; font-size:12px; color:#aaa;">
            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <span>Nights</span>
                <span id="previewNights" style="color:#e0e0e0;">—</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <span>Base Rate / night</span>
                <span id="previewRate" style="color:#e0e0e0;">—</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <span>Extra Guest Fee</span>
                <span id="previewExtraFee" style="color:#e0e0e0;">—</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <span>Weekly Discount (10%)</span>
                <span id="previewDiscount" style="color:#4ade80;">—</span>
            </div>
            <div style="display:flex; justify-content:space-between; border-top:1px solid #2e2e3e; padding-top:8px; margin-top:4px;">
                <span style="color:#fff; font-weight:600;">Total</span>
                <span id="previewTotal" style="color:#c9a84c; font-weight:700; font-size:14px;">—</span>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-xs del" id="editCancelBtn" onclick="cancelBooking()">Cancel Booking</button>
            <button class="btn-xs" onclick="closeModal('editModal')" style="background:#2e2e3e;">Close</button>
            <button class="btn-xs accept" id="editSaveBtn" onclick="saveEdit()">Save Changes</button>
        </div>
    </div>
</div>

<!-- ===== VIEW MODAL ===== -->
<div class="modal-backdrop" id="viewModal">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('viewModal')">&#10005;</button>
        <h3>Reservation Details <span id="viewModalTitle" style="color:#4ade80;"></span></h3>

        <div class="view-grid">
            <div class="view-item"><label>Guest</label><span id="vCustomerName">—</span></div>
            <div class="view-item"><label>Email</label><span id="vEmail">—</span></div>
            <div class="view-item"><label>Phone</label><span id="vPhone">—</span></div>
            <div class="view-item"><label>Branch</label><span id="vLocation">—</span></div>
            <div class="view-item"><label>Room</label><span id="vRoom">—</span></div>
            <div class="view-item"><label>Room Type</label><span id="vRoomType">—</span></div>
            <div class="view-item"><label>Check In</label><span id="vCheckIn">—</span></div>
            <div class="view-item"><label>Check Out</label><span id="vCheckOut">—</span></div>
            <div class="view-item"><label>Nights</label><span id="vNights">—</span></div>
            <div class="view-item"><label>Guests</label><span id="vGuests">—</span></div>
        </div>

        <hr class="view-divider">
        <div style="font-size:11px; color:#888; text-transform:uppercase; letter-spacing:.5px; margin-bottom:10px;">Payment</div>

        <div class="view-grid">
            <div class="view-item"><label>Payment ID</label><span id="vPaymentID">—</span></div>
            <div class="view-item"><label>Status</label><span id="vPayStatus">—</span></div>
            <div class="view-item"><label>Method</label><span id="vMethod">—</span></div>
            <div class="view-item"><label>Date</label><span id="vPayDate">—</span></div>
            <div class="view-item"><label>Charges</label><span id="vCharges">—</span></div>
            <div class="view-item"><label>Discount</label><span id="vDiscount">—</span></div>
            <div class="view-item"><label>Total</label><span id="vTotal">—</span></div>
            <div class="view-item"><label>Paid Amount</label><span id="vPaid">—</span></div>
        </div>

        <hr class="view-divider">

        <div style="margin-top:10px;">
            <div style="font-size:11px; color:#888; text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px;">
                Receipt
            </div>

            <img id="vReceipt"
                style="width:100%; max-height:320px; object-fit:contain; border-radius:8px; border:1px solid #2e2e3e; display:none;">
        </div>



        <div class="modal-footer">
            <button class="btn-xs" onclick="closeModal('viewModal')" style="background:#2e2e3e;">Close</button>
        </div>
    </div>
</div>

<script src="reservations.js"></script>
</body>
</html>
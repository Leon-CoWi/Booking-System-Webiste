<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) { 
    header('Location: login.php'); 
    exit(); 
}

require_once '../connection.php';

// =========================
// STAT COUNTS
// =========================

// Get today's date
$today = date('Y-m-d');

// Total Casa bookings today
$todayCasa = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as cnt 
    FROM bookings 
    WHERE CheckInDate = '$today' 
    AND Location LIKE '%Casa%'
"))['cnt'];

// Total VF Riton bookings today
$todayRiton = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as cnt 
    FROM bookings 
    WHERE CheckInDate = '$today' 
    AND Location LIKE '%Riton%'
"))['cnt'];

// Pending Casa payments
$pendingCasa = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as cnt 
    FROM bookings b 
    JOIN payments p ON b.PaymentID = p.PaymentID 
    WHERE p.PaymentStatus = 'Pending' 
    AND b.Location LIKE '%Casa%'
"))['cnt'];

// Pending VF Riton payments
$pendingRiton = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as cnt 
    FROM bookings b 
    JOIN payments p ON b.PaymentID = p.PaymentID 
    WHERE p.PaymentStatus = 'Pending' 
    AND b.Location LIKE '%Riton%'
"))['cnt'];

// Total pending
$pending = $pendingCasa + $pendingRiton;

// Active paid bookings
$active = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as cnt 
    FROM payments 
    WHERE PaymentStatus = 'Paid'
"))['cnt'];

// Revenue for today
$revenue = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(TotalAmount) as total 
    FROM payments 
    WHERE PaymentDate = '$today'
"))['total'];

// Prevent null value
$revenue = $revenue ?? 0;


// =========================
// ACTIVE BOOKINGS
// =========================

$activeBookings = mysqli_query($conn, "
    SELECT 
        b.BookingID, 
        c.CustomerName, 
        b.Location, 
        b.RoomID, 
        r.RoomType,
        b.CheckInDate, 
        b.CheckOutDate, 
        b.GuestNumber, 
        p.PaymentStatus

    FROM bookings b

    JOIN customers c 
        ON b.CustomerID = c.CustomerID

    JOIN rooms r 
        ON b.RoomID = r.RoomID

    JOIN payments p 
        ON b.PaymentID = p.PaymentID

    WHERE p.PaymentStatus IN ('Paid', 'Pending')

    ORDER BY b.CheckInDate ASC

") or die(mysqli_error($conn));

?>

<!DOCTYPE html>
<html>

<head>
    <title>Home - COMODO Admin</title>

    <!-- Styles -->
    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="admin.css">
</head>

<body>

<div class="admin-shell">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="admin-content">

        <!-- TOPBAR -->
        <div class="admin-topbar">
            <h2>Home</h2>

            <!-- Current date -->
            <span class="admin-date">
                Admin &mdash; <?= date('M d, Y') ?>
            </span>
        </div>

        <!-- STAT CARDS -->
        <div class="stat-grid">

            <!-- VF Riton bookings -->
            <div class="stat-card">
                <div class="stat-label">VFRiton Bookings Today</div>
                <div class="stat-value"><?= $todayRiton ?></div>
            </div>

            <!-- Casa Fam bookings -->
            <div class="stat-card">
                <div class="stat-label">Casa Fam Bookings Today</div>
                <div class="stat-value"><?= $todayCasa ?></div>
            </div>

            <!-- Pending bookings -->
            <div class="stat-card">
                <div class="stat-label">Pending</div>
                <div class="stat-value gold"><?= $pending ?></div>
            </div>

            <!-- Active bookings -->
            <div class="stat-card">
                <div class="stat-label">Active</div>
                <div class="stat-value green"><?= $active ?></div>
            </div>

        </div>

        <!-- BRANCH FILTER -->
        <div class="section-header">

            <h3>Active Reservations</h3>

            <div class="branch-toggles">

                <button class="branch-btn active-btn" onclick="filterBranch('all', this)">
                    All
                </button>

                <button class="branch-btn" onclick="filterBranch('casa', this)">
                    Casa Fam
                </button>

                <button class="branch-btn" onclick="filterBranch('riton', this)">
                    VF Riton
                </button>

            </div>
        </div>

        <!-- ACTIVE BOOKINGS TABLE -->
        <div class="table-wrap">

            <table>

                <!-- Table headers -->
                <tr>
                    <th>Guest</th>
                    <th>Branch</th>
                    <th>Room</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Guests</th>
                    <th>Status</th>
                </tr>

                <?php while($row = mysqli_fetch_assoc($activeBookings)):

                    // Convert location to lowercase
                    $loc = strtolower($row['Location']);

                    // Assign branch class
                    $branch_class = str_contains($loc, 'casa') ? 'casa' : 'riton';

                    // Lowercase payment status
                    $status = strtolower($row['PaymentStatus']);
                ?>

                <tr class="res-row <?= $branch_class ?>">

                    <!-- Customer name -->
                    <td><?= htmlspecialchars($row['CustomerName']) ?></td>

                    <!-- Branch -->
                    <td><?= htmlspecialchars($row['Location']) ?></td>

                    <!-- Room type -->
                    <td><?= htmlspecialchars($row['RoomType']) ?></td>

                    <!-- Check in -->
                    <td><?= date('M d', strtotime($row['CheckInDate'])) ?></td>

                    <!-- Check out -->
                    <td><?= date('M d', strtotime($row['CheckOutDate'])) ?></td>

                    <!-- Guest count -->
                    <td><?= $row['GuestNumber'] ?></td>

                    <!-- Booking status -->
                    <td>

                        <?php if($status == 'pending'): ?>

                            <span class="pill pending">Pending</span>

                        <?php elseif($status == 'paid'): ?>
                            
                            <!-- Fixed: changed active to paid -->
                            <span class="pill active">Active</span>

                        <?php endif; ?>

                    </td>

                </tr>

                <?php endwhile; ?>

                <!-- Empty table message -->
                <?php if(mysqli_num_rows($activeBookings) == 0): ?>

                <tr>
                    <td colspan="7" style="text-align:center; color:#555; padding:20px;">
                        No active reservations
                    </td>
                </tr>

                <?php endif; ?>

            </table>

        </div>

    </div>
</div>

<script>

    // Filter reservations by branch
    function filterBranch(branch, el) {

        // Remove active class from all buttons
        document.querySelectorAll('.branch-btn').forEach(b => 
            b.classList.remove('active-btn')
        );

        // Add active class to clicked button
        el.classList.add('active-btn');

        // Show matching rows only
        document.querySelectorAll('.res-row').forEach(row => {

            if (branch === 'all') {

                row.style.display = '';

            } else {

                row.style.display = row.classList.contains(branch) ? '' : 'none';
            }
        });
    }

</script>

</body>
</html>
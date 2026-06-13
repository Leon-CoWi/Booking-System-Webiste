<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit(); }
require_once '../connection.php';

$filter = $_GET['filter'] ?? 'all';
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 20;
$offset = ($page - 1) * $limit;

$where = "";
if ($filter == 'pending')   $where = "WHERE p.PaymentStatus = 'Pending'";
if ($filter == 'paid')      $where = "WHERE p.PaymentStatus = 'Paid'";
if ($filter == 'cancelled') $where = "WHERE p.PaymentStatus IN ('Cancelled', 'Rejected')";

// Total count for pagination
$countResult = mysqli_query($conn, "
    SELECT COUNT(*) as total
    FROM payments p
    JOIN customers c ON p.CustomerID = c.CustomerID
    LEFT JOIN bookings b ON b.PaymentID = p.PaymentID
    $where
");
$totalRows  = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Main query with pagination
$payments = mysqli_query($conn, "
    SELECT p.*, c.CustomerName, c.Email, b.BookingID
    FROM payments p
    JOIN customers c ON p.CustomerID = c.CustomerID
    LEFT JOIN bookings b ON b.PaymentID = p.PaymentID
    $where
    ORDER BY p.PaymentDate DESC, p.PaymentID DESC
    LIMIT $limit OFFSET $offset
");

$totalMonth = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(TotalAmount) as total
    FROM payments
    WHERE MONTH(PaymentDate) = MONTH(NOW())
    AND PaymentStatus = 'Paid'
"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payments - Admin</title>
    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="admin-shell">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="admin-topbar">
            <h2>Payments</h2>
            <span style="color:#4ade80; font-size:13px;">This Month: &#8369;<?= number_format($totalMonth, 2) ?></span>
        </div>

        <!-- Filter Tabs -->
        <div style="display:flex; gap:8px; margin-bottom:14px;">
            <a href="?filter=all"       class="tab <?= $filter=='all'       ? 'active' : '' ?>">All</a>
            <a href="?filter=pending"   class="tab <?= $filter=='pending'   ? 'active' : '' ?>">Pending</a>
            <a href="?filter=paid"      class="tab <?= $filter=='paid'      ? 'active' : '' ?>">Paid</a>
            <a href="?filter=cancelled" class="tab <?= $filter=='cancelled' ? 'active' : '' ?>">Cancelled / Rejected</a>
        </div>

        <!-- Table -->
        <div class="table-wrap">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Guest</th>
                    <th>Branch</th>
                    <th>Room</th>
                    <th>Charges</th>
                    <th>Discount</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
                <?php if ($totalRows == 0): ?>
                <tr>
                    <td colspan="11" style="text-align:center; padding:20px; color:#888;">No payments found.</td>
                </tr>
                <?php else: ?>
                <?php while($p = mysqli_fetch_assoc($payments)): ?>
                <tr>
                    <td>#<?= $p['PaymentID'] ?></td>
                    <td><?= htmlspecialchars($p['CustomerName']) ?></td>
                    <td><?= htmlspecialchars($p['Location']) ?></td>
                    <td><?= htmlspecialchars($p['RoomID']) ?></td>
                    <td>&#8369;<?= number_format($p['Charges'], 2) ?></td>
                    <td>&#8369;<?= number_format($p['DiscountAmount'], 2) ?></td>
                    <td>&#8369;<?= number_format($p['TotalAmount'], 2) ?></td>
                    <td>&#8369;<?= number_format($p['PaidAmount'], 2) ?></td>
                    <td><?= htmlspecialchars($p['PaymentMethod']) ?></td>
                    <td><?= $p['PaymentDate'] ? date('M d, Y', strtotime($p['PaymentDate'])) : 'N/A' ?></td>
                    <td><span class="pill <?= strtolower($p['PaymentStatus']) ?>"><?= $p['PaymentStatus'] ?></span></td>
                </tr>
                <?php endwhile; ?>
                <?php endif; ?>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div style="display:flex; align-items:center; gap:6px; margin-top:16px; flex-wrap:wrap;">

            <!-- Prev -->
            <?php if ($page > 1): ?>
                <a href="?filter=<?= $filter ?>&page=<?= $page - 1 ?>" class="tab">&#8592; Prev</a>
            <?php else: ?>
                <span class="tab" style="opacity:0.4; cursor:default;">&#8592; Prev</span>
            <?php endif; ?>

            <!-- Page Numbers -->
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

            <!-- Next -->
            <?php if ($page < $totalPages): ?>
                <a href="?filter=<?= $filter ?>&page=<?= $page + 1 ?>" class="tab">Next &#8594;</a>
            <?php else: ?>
                <span class="tab" style="opacity:0.4; cursor:default;">Next &#8594;</span>
            <?php endif; ?>

            <span style="color:#888; font-size:12px; margin-left:6px;">
                Showing <?= $offset + 1 ?>–<?= min($offset + $limit, $totalRows) ?> of <?= $totalRows ?> payments
            </span>

        </div>
        <?php endif; ?>

    </div>
</div>
</body>
</html>
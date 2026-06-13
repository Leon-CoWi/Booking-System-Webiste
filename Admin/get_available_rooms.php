<?php
session_start();

// check admin login
if (!isset($_SESSION['admin'])) { 
    header('Location: login.php'); 
    exit(); 
}

require_once '../connection.php';

// inputs
$checkin  = $_GET['checkin']  ?? '';
$checkout = $_GET['checkout'] ?? '';
$location = $_GET['location'] ?? '';

// return empty if missing
if (!$checkin || !$checkout || !$location) {
    echo json_encode([]);
    exit();
}

// clean input
$location = mysqli_real_escape_string($conn, $location);
$checkin  = mysqli_real_escape_string($conn, $checkin);
$checkout = mysqli_real_escape_string($conn, $checkout);

// get available rooms
$rooms = mysqli_query($conn, "
    SELECT * FROM rooms r
    WHERE r.Location = '$location'
    AND NOT EXISTS (
        SELECT 1 FROM bookings b
        WHERE b.RoomID = r.RoomID
        AND b.ReservationType != 'Cancelled'
        AND '$checkin'  < b.CheckOutDate
        AND '$checkout' > b.CheckInDate
    )
    ORDER BY
        CASE
            WHEN r.RoomType = 'Standard' THEN 1
            WHEN r.RoomType = 'Deluxe'   THEN 2
            WHEN r.RoomType = 'Family'   THEN 3
            WHEN r.RoomType = 'Premium'  THEN 4
            ELSE 5
        END
");

$result = [];

// build result
while ($r = mysqli_fetch_assoc($rooms)) {
    $result[] = $r;
}

// output json
header('Content-Type: application/json');
echo json_encode($result);
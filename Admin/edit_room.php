<?php
session_start();

// check admin login
if (!isset($_SESSION['admin'])) { 
    header('Location: login.php'); 
    exit(); 
}

require_once '../connection.php';

// get room id
$id = mysqli_real_escape_string($conn, $_GET['id'] ?? '');

// fetch room data
$room = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * 
    FROM rooms 
    WHERE RoomID = '$id'
"));

// redirect if not found
if (!$room) { 
    header('Location: rooms.php'); 
    exit(); 
}

// messages
$success = $error = "";

// update room
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // form inputs
    $roomNum   = mysqli_real_escape_string($conn, $_POST['room_number']);
    $baseRate  = (float)$_POST['base_rate'];
    $maxGuests = (int)$_POST['max_guests'];
    $beds      = mysqli_real_escape_string($conn, $_POST['bed_config']);
    $bathrooms = (int)$_POST['bathrooms'];
    $features  = mysqli_real_escape_string($conn, $_POST['features']);
    $amenities = mysqli_real_escape_string($conn, $_POST['amenities']);
    $extraRate = (float)$_POST['extra_rate'];
    $roomType  = mysqli_real_escape_string($conn, $_POST['room_type']);

    // update query
    mysqli_query($conn, "
        UPDATE rooms SET
            RoomNumber       = '$roomNum',
            RoomType         = '$roomType',
            BaseRate         = $baseRate,
            MaxOccupancies   = $maxGuests,
            BedConfiguration = '$beds',
            NumberBathrooms  = $bathrooms,
            RoomFeatures     = '$features',
            RoomAmenities    = '$amenities',
            ExtraGuestRate   = $extraRate
        WHERE RoomID = '$id'
    ");

    // success message
    $success = "Room updated successfully!";

    // reload updated data
    $room = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT * 
        FROM rooms 
        WHERE RoomID = '$id'
    "));
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Room - Admin</title>

    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="admin.css">
</head>

<body>

<div class="admin-shell">

    <!-- sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="admin-content">

        <!-- topbar -->
        <div class="admin-topbar">

            <h2>
                Edit Room — <?= htmlspecialchars($room['RoomNumber']) ?>
            </h2>

            <a href="rooms.php">
                <button class="btn-xs view">← Back</button>
            </a>

        </div>

        <!-- messages -->
        <?php if($success): ?>
            <div class="success-msg"><?= $success ?></div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>

        <div class="form-card">

            <form method="POST">

                <div class="form-grid">

                    <!-- Room ID (read only) -->
                    <div class="form-field">
                        <label>Room ID</label>
                        <input type="text" 
                               value="<?= htmlspecialchars($room['RoomID']) ?>" 
                               readonly 
                               style="background:#0d1017; color:#555;">
                    </div>

                    <!-- Branch (read only) -->
                    <div class="form-field">
                        <label>Branch</label>
                        <input type="text" 
                               value="<?= htmlspecialchars($room['Location']) ?>" 
                               readonly 
                               style="background:#0d1017; color:#555;">
                    </div>

                    <!-- Room number -->
                    <div class="form-field">
                        <label>Room Number / Name</label>
                        <input type="text" 
                               name="room_number" 
                               value="<?= htmlspecialchars($room['RoomNumber']) ?>" 
                               required>
                    </div>

                    <!-- Room type -->
                    <div class="form-field">
                        <label>Room Type</label>
                        <input type="text" 
                               name="room_type" 
                               value="<?= htmlspecialchars($room['RoomType']) ?>" 
                               required>
                    </div>

                    <!-- Base rate -->
                    <div class="form-field">
                        <label>Base Rate (₱/night)</label>
                        <input type="number" 
                               name="base_rate" 
                               value="<?= $room['BaseRate'] ?>" 
                               step="0.01" 
                               required>
                    </div>

                    <!-- Max guests -->
                    <div class="form-field">
                        <label>Max Occupancies</label>
                        <input type="number" 
                               name="max_guests" 
                               value="<?= $room['MaxOccupancies'] ?>" 
                               min="1" 
                               required>
                    </div>

                    <!-- Extra guest rate -->
                    <div class="form-field">
                        <label>Extra Guest Rate (₱)</label>
                        <input type="number" 
                               name="extra_rate" 
                               value="<?= $room['ExtraGuestRate'] ?>" 
                               step="0.01" 
                               required>
                    </div>

                    <!-- Bed config -->
                    <div class="form-field">
                        <label>Bed Configuration</label>
                        <input type="text" 
                               name="bed_config" 
                               value="<?= htmlspecialchars($room['BedConfiguration']) ?>" 
                               required>
                    </div>

                    <!-- Bathrooms -->
                    <div class="form-field">
                        <label>Number of Bathrooms</label>
                        <input type="number" 
                               name="bathrooms" 
                               value="<?= $room['NumberBathrooms'] ?>" 
                               min="1" 
                               required>
                    </div>

                    <!-- Features -->
                    <div class="form-field" style="grid-column:span 2;">
                        <label>Room Features / Description</label>
                        <textarea name="features" rows="5" required><?= htmlspecialchars($room['RoomFeatures']) ?></textarea>
                    </div>

                    <!-- Amenities -->
                    <div class="form-field" style="grid-column:span 2;">
                        <label>Room Amenities</label>
                        <textarea name="amenities" rows="3" required><?= htmlspecialchars($room['RoomAmenities']) ?></textarea>
                    </div>

                </div>

                <!-- buttons -->
                <div style="margin-top:20px; display:flex; gap:10px;">

                    <button type="submit">Save Changes</button>

                    <a href="rooms.php">
                        <button type="button" class="btn-cancel">Cancel</button>
                    </a>

                </div>

            </form>

        </div>

    </div>
</div>

</body>
</html>
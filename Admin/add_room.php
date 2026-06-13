<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) { 
    header('Location: login.php'); 
    exit(); 
}

require_once '../connection.php';

// Message variables
$success = $error = "";

// Get existing room types for Casa Fam
$casaTypes  = mysqli_query($conn, "SELECT DISTINCT RoomType FROM rooms WHERE Location = 'Casa FAM Appartelle' ORDER BY RoomType");

// Get existing room types for VF Riton
$ritonTypes = mysqli_query($conn, "SELECT DISTINCT RoomType FROM rooms WHERE Location = 'V.F Riton Appartelle' ORDER BY RoomType");

// Run when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get form values
    $roomID   = mysqli_real_escape_string($conn, $_POST['room_id']);
    $location  = mysqli_real_escape_string($conn, $_POST['location']);
    $roomNum   = mysqli_real_escape_string($conn, $_POST['room_number']);
    $baseRate  = (float)$_POST['base_rate'];
    $maxGuests = (int)$_POST['max_guests'];
    $beds      = mysqli_real_escape_string($conn, $_POST['bed_config']);
    $bathrooms = (int)$_POST['bathrooms'];
    $features  = mysqli_real_escape_string($conn, $_POST['features']);
    $amenities = mysqli_real_escape_string($conn, $_POST['amenities']);
    $extraRate = (float)$_POST['extra_rate'];

    // Use existing room type or add new one
    if ($_POST['room_type'] == '__new__') {
        $roomType = mysqli_real_escape_string($conn, trim($_POST['new_room_type']));
    } else {
        $roomType = mysqli_real_escape_string($conn, $_POST['room_type']);
    }

    // Check required fields
    if (empty($roomType) || empty($location) || empty($roomNum)) {

        $error = "Please fill in all required fields.";

    } else {

        // Check if Room ID already exists
        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT RoomID FROM rooms WHERE RoomID = '$roomID'"));

        if ($check) {

            $error = "Room ID $roomID already exists. Please check existing rooms.";

        } else {

            // Insert new room into database
            mysqli_query($conn, "INSERT INTO rooms
                (RoomID, RoomNumber, Location, RoomType, BaseRate, MaxOccupancies, BedConfiguration, NumberBathrooms, RoomFeatures, RoomAmenities, ExtraGuestRate)
                VALUES
                ('$roomID', '$roomNum', '$location', '$roomType', $baseRate, $maxGuests, '$beds', $bathrooms, '$features', '$amenities', $extraRate)
            ");

            $success = "Room $roomID ($roomType) added successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Room - Admin</title>

    <!-- Main styles -->
    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="admin.css">
</head>

<body>

<div class="admin-shell">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <div class="admin-content">

        <!-- Top bar -->
        <div class="admin-topbar">
            <h2>Add Room</h2>

            <!-- Back button -->
            <a href="rooms.php">
                <button class="btn-xs view">← Back</button>
            </a>
        </div>

        <!-- Success message -->
        <?php if($success): ?>
            <div class="success-msg"><?= $success ?></div>
        <?php endif; ?>

        <!-- Error message -->
        <?php if($error): ?>
            <div class="error-msg"><?= $error ?></div>
        <?php endif; ?>

        <div class="form-card">

            <!-- Add room form -->
            <form method="POST">

                <h3 style="color:#c9a84c; margin-bottom:16px;">Branch & Room Type</h3>

                <div class="form-grid">

                    <!-- Branch selection -->
                    <div class="form-field">
                        <label>Branch</label>

                        <select name="location" id="locationSelect" onchange="loadTypes()" required>
                            <option value="">Select Branch</option>
                            <option value="Casa FAM Appartelle">Casa Fam</option>
                            <option value="V.F Riton Appartelle">VF Riton</option>
                        </select>
                    </div>

                    <!-- Room type selection -->
                    <div class="form-field">
                        <label>Room Type</label>

                        <select name="room_type" id="roomTypeSelect" onchange="checkNewType()" required>
                            <option value="">Select Branch First</option>
                        </select>
                    </div>

                    <!-- New room type input -->
                    <div class="form-field" id="newTypeField" style="display:none; grid-column:span 2;">
                        <label>New Room Type Name</label>

                        <input type="text" name="new_room_type" id="newRoomType" placeholder="e.g. Barkada Suite">
                    </div>
                </div>

                <h3 style="color:#c9a84c; margin:20px 0 16px;">Room Details</h3>

                <div class="form-grid">

                    <!-- Room ID -->
                    <div class="form-field">
                        <label>Room ID</label>

                        <input type="text" name="room_id" placeholder="e.g. VF-6 or CF-12" required>
                    </div>

                    <!-- Room number -->
                    <div class="form-field">
                        <label>Room Number / Name</label>

                        <input type="text" name="room_number" placeholder="e.g. Room 12 or Casa 5" required>
                    </div>

                    <!-- Base rate -->
                    <div class="form-field">
                        <label>Base Rate (₱/night)</label>

                        <input type="number" name="base_rate" min="0" step="0.01" placeholder="e.g. 1500" required>
                    </div>

                    <!-- Maximum guests -->
                    <div class="form-field">
                        <label>Max Occupancies</label>

                        <input type="number" name="max_guests" min="1" max="20" value="2" required>
                    </div>

                    <!-- Extra guest rate -->
                    <div class="form-field">
                        <label>Extra Guest Rate (₱)</label>

                        <input type="number" name="extra_rate" min="0" step="0.01" value="400" required>
                    </div>

                    <!-- Bed setup -->
                    <div class="form-field">
                        <label>Bed Configuration</label>

                        <input type="text" name="bed_config" placeholder="e.g. 1 Double Bed, 1 Single Bed" required>
                    </div>

                    <!-- Bathrooms -->
                    <div class="form-field">
                        <label>Number of Bathrooms</label>

                        <input type="number" name="bathrooms" min="1" value="1" required>
                    </div>

                    <!-- Room description -->
                    <div class="form-field" style="grid-column:span 2;">
                        <label>Room Features / Description</label>

                        <textarea name="features" rows="4" placeholder="Full description of the room..." required></textarea>
                    </div>

                    <!-- Amenities -->
                    <div class="form-field" style="grid-column:span 2;">
                        <label>Room Amenities</label>

                        <textarea name="amenities" rows="3" placeholder="e.g. Free WiFi, Air Conditioning, Hot Shower..." required></textarea>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="margin-top:20px; display:flex; gap:10px;">

                    <button type="submit">Add Room</button>

                    <a href="rooms.php">
                        <button type="button" class="btn-cancel">Cancel</button>
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<script>

// Room types from PHP
const roomTypes = {

    'Casa FAM Appartelle': <?= json_encode(array_column(mysqli_fetch_all($casaTypes, MYSQLI_ASSOC), 'RoomType')) ?>,

    'V.F Riton Appartelle': <?= json_encode(array_column(mysqli_fetch_all($ritonTypes, MYSQLI_ASSOC), 'RoomType')) ?>
};

// Load room types based on selected branch
function loadTypes() {

    let loc     = document.getElementById('locationSelect').value;
    let select  = document.getElementById('roomTypeSelect');
    let types   = roomTypes[loc] || [];

    // Reset dropdown
    select.innerHTML = '<option value="">Select Room Type</option>';

    // Add room types
    types.forEach(t => {

        let opt = document.createElement('option');

        opt.value = t;
        opt.text  = t;

        select.appendChild(opt);
    });

    // Option for adding new room type
    let newOpt = document.createElement('option');

    newOpt.value = '__new__';
    newOpt.text  = '+ Add New Room Type';

    select.appendChild(newOpt);

    // Hide new room type field by default
    document.getElementById('newTypeField').style.display = 'none';
    document.getElementById('newRoomType').required = false;
}

// Show or hide new room type field
function checkNewType() {

    let val      = document.getElementById('roomTypeSelect').value;
    let newField = document.getElementById('newTypeField');
    let newInput = document.getElementById('newRoomType');

    // If add new room type selected
    if (val === '__new__') {

        newField.style.display = '';
        newInput.required = true;

    } else {

        newField.style.display = 'none';
        newInput.required = false;
    }
}

</script>

</body>
</html>
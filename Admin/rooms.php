<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit(); }
require_once '../connection.php';

/* DELETE ROOM */
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM rooms WHERE RoomID = '$id'");
    header('Location: rooms.php'); exit();
}

/* LOAD ROOMS */
$rooms = mysqli_query($conn, "SELECT * FROM rooms ORDER BY Location, RoomType");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rooms - Admin</title>
    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="admin-shell">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">

        <!-- HEADER -->
        <div class="admin-topbar">
            <h2>Rooms</h2>
            <a href="add_room.php"><button class="btn-xs accept">+ Add Room</button></a>
        </div>

        <!-- FILTER BUTTONS -->
        <div style="display:flex; gap:8px; margin-bottom:16px;">
            <button class="branch-btn active-btn" onclick="filterBranch('all',this)">All</button>
            <button class="branch-btn" onclick="filterBranch('casa',this)">Casa Fam</button>
            <button class="branch-btn" onclick="filterBranch('riton',this)">VF Riton</button>
        </div>

        <!-- ROOM LIST -->
        <div class="room-grid">
            <?php while($room = mysqli_fetch_assoc($rooms)):

                // branch type (casa or riton)
                $branch_class = str_contains(strtolower($room['Location']), 'casa') ? 'casa' : 'riton';

                // get first image
                $firstImg = mysqli_fetch_assoc(mysqli_query(
                    $conn,
                    "SELECT ImagePath FROM room_images WHERE RoomID = '{$room['RoomID']}' ORDER BY ImageOrder ASC LIMIT 1"
                ));

                $imgSrc = $firstImg ? '../User/' . $firstImg['ImagePath'] : '../User/placeholder.jpg';
            ?>
            <div class="room-card-admin <?= $branch_class ?>">

                <!-- IMAGE -->
                <div class="room-img">
                    <img src="<?= htmlspecialchars($imgSrc) ?>" onerror="this.src='../User/placeholder.jpg'">
                </div>

                <!-- INFO -->
                <div class="room-info">
                    <div class="room-name">Room ID: <?= htmlspecialchars($room['RoomID']) ?></div>
                    <div class="room-name"><?= htmlspecialchars($room['RoomType']) ?></div>

                    <div class="room-sub">
                        <?= htmlspecialchars($room['Location']) ?> · <?= htmlspecialchars($room['RoomNumber']) ?>
                    </div>

                    <div class="room-sub">
                        ₱<?= number_format($room['BaseRate'], 2) ?>/night · Max <?= $room['MaxOccupancies'] ?> guests
                    </div>

                    <div class="room-sub">
                        <?= htmlspecialchars($room['BedConfiguration']) ?>
                    </div>

                    <!-- ACTIONS -->
                    <div class="room-btns">
                        <a href="edit_room.php?id=<?= $room['RoomID'] ?>"><button class="btn-xs edit">Edit</button></a>
                        <a href="?delete=<?= $room['RoomID'] ?>" onclick="return confirm('Delete this room?')">
                            <button class="btn-xs del">Delete</button>
                        </a>
                    </div>
                </div>

            </div>
            <?php endwhile; ?>
        </div>

    </div>
</div>

<script>
/* FILTER ROOMS BY BRANCH */
function filterBranch(branch, el) {
    document.querySelectorAll('.branch-btn').forEach(b => b.classList.remove('active-btn'));
    el.classList.add('active-btn');

    document.querySelectorAll('.room-card-admin').forEach(card => {
        card.style.display = (branch === 'all' || card.classList.contains(branch)) ? '' : 'none';
    });
}
</script>

</body>
</html>
<?php
session_start();

// check login
if (!isset($_SESSION['admin'])) { 
    header('Location: login.php'); 
    exit(); 
}

require_once '../connection.php';

/* delete image */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $row = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT * 
        FROM room_images 
        WHERE ImageID = $id
    "));

    if ($row) {

        $fp = "../User/" . $row['ImagePath'];

        if (file_exists($fp)) unlink($fp);

        mysqli_query($conn, "
            DELETE FROM room_images 
            WHERE ImageID = $id
        ");
    }

    header('Location: gallery_view.php?room=' . urlencode($_GET['room'] ?? ''));
    exit();
}

/* move image up */
if (isset($_GET['moveup'])) {

    $id   = (int)$_GET['moveup'];
    $room = mysqli_real_escape_string($conn, $_GET['room']);

    $cur = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT * 
        FROM room_images 
        WHERE ImageID = $id
    "));

    if ($cur) {

        $prev = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT * 
            FROM room_images 
            WHERE RoomID = '$room' 
            AND ImageOrder < {$cur['ImageOrder']} 
            ORDER BY ImageOrder DESC 
            LIMIT 1
        "));

        if ($prev) {

            mysqli_query($conn, "
                UPDATE room_images 
                SET ImageOrder = {$cur['ImageOrder']} 
                WHERE ImageID = {$prev['ImageID']}
            ");

            mysqli_query($conn, "
                UPDATE room_images 
                SET ImageOrder = {$prev['ImageOrder']} 
                WHERE ImageID = $id
            ");
        }
    }

    header('Location: gallery_view.php?room=' . urlencode($_GET['room']));
    exit();
}

/* move image down */
if (isset($_GET['movedown'])) {

    $id   = (int)$_GET['movedown'];
    $room = mysqli_real_escape_string($conn, $_GET['room']);

    $cur = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT * 
        FROM room_images 
        WHERE ImageID = $id
    "));

    if ($cur) {

        $next = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT * 
            FROM room_images 
            WHERE RoomID = '$room' 
            AND ImageOrder > {$cur['ImageOrder']} 
            ORDER BY ImageOrder ASC 
            LIMIT 1
        "));

        if ($next) {

            mysqli_query($conn, "
                UPDATE room_images 
                SET ImageOrder = {$cur['ImageOrder']} 
                WHERE ImageID = {$next['ImageID']}
            ");

            mysqli_query($conn, "
                UPDATE room_images 
                SET ImageOrder = {$next['ImageOrder']} 
                WHERE ImageID = $id
            ");
        }
    }

    header('Location: gallery_view.php?room=' . urlencode($_GET['room']));
    exit();
}

/* load rooms */
$allRooms = mysqli_query($conn, "
    SELECT RoomID, RoomType, RoomNumber, Location 
    FROM rooms 
    ORDER BY Location, RoomType
");

/* selected room */
$selectedRoom = $_GET['room'] ?? '';

$images = [];

/* load images */
if ($selectedRoom) {

    $re = mysqli_real_escape_string($conn, $selectedRoom);

    $r = mysqli_query($conn, "
        SELECT * 
        FROM room_images 
        WHERE RoomID = '$re' 
        ORDER BY ImageOrder ASC, ImageID ASC
    ");

    while ($img = mysqli_fetch_assoc($r)) {
        $images[] = $img;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Photos - Admin</title>

    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="admin.css">

    <style>
        .img-grid { display:flex; flex-wrap:wrap; gap:12px; }

        .img-card {
            background:#1a2035;
            border-radius:8px;
            overflow:hidden;
            width:180px;
        }

        .img-card img {
            width:180px;
            height:120px;
            object-fit:cover;
            display:block;
        }

        .img-card .info { padding:8px; }

        .img-card .name {
            color:#888;
            font-size:10px;
            margin-bottom:6px;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }

        .img-card .btns { display:flex; gap:4px; }

        .order-badge {
            background:#0d1017;
            color:#c9a84c;
            font-size:10px;
            padding:2px 8px;
            border-radius:4px;
            margin-bottom:6px;
            display:inline-block;
        }

        .filter-row {
            display:flex;
            align-items:center;
            gap:12px;
            margin-bottom:20px;
        }

        .filter-row select {
            background:#1a2035;
            border:0.5px solid #333;
            border-radius:6px;
            color:white;
            font-size:12px;
            padding:8px 12px;
        }
    </style>
</head>

<body>

<div class="admin-shell">

    <?php include 'sidebar.php'; ?>

    <div class="admin-content">

        <div class="admin-topbar">
            <h2>Manage Room Photos</h2>

            <a href="rooms_gallery.php">
                <button class="btn-xs accept">+ Upload Photos</button>
            </a>
        </div>

        <!-- filter -->
        <div class="filter-row">

            <label style="color:#888; font-size:12px;">Select Room:</label>

            <form method="GET">

                <select name="room" onchange="this.form.submit()">

                    <option value="">-- Select Room --</option>

                    <?php
                    $currentLoc = '';

                    while($r = mysqli_fetch_assoc($allRooms)):

                        if ($r['Location'] != $currentLoc):

                            if ($currentLoc != '') echo '</optgroup>';

                            echo '<optgroup label="' . htmlspecialchars($r['Location']) . '">';

                            $currentLoc = $r['Location'];

                        endif;
                    ?>

                    <option value="<?= $r['RoomID'] ?>" 
                        <?= $selectedRoom==$r['RoomID']?'selected':'' ?>>
                        <?= htmlspecialchars($r['RoomType']) ?> (<?= $r['RoomNumber'] ?>)
                    </option>

                    <?php endwhile; if($currentLoc != '') echo '</optgroup>'; ?>

                </select>

            </form>

        </div>

        <!-- no images -->
        <?php if ($selectedRoom && count($images) == 0): ?>

            <p style="color:#555;">
                No photos yet. <a href="rooms_gallery.php">Upload some →</a>
            </p>

        <!-- show images -->
        <?php elseif (count($images) > 0): ?>

            <p style="color:#888; font-size:12px; margin-bottom:12px;">
                <?= count($images) ?> photo(s) — use ↑ ↓ to reorder. First photo is the cover.
            </p>

            <div class="img-grid">

                <?php foreach($images as $img): ?>

                <div class="img-card">

                    <img src="../User/<?= htmlspecialchars($img['ImagePath']) ?>" alt="">

                    <div class="info">

                        <div class="order-badge">
                            Order: <?= $img['ImageOrder'] ?>
                        </div>

                        <div class="name">
                            <?= htmlspecialchars($img['ImageName'] ?? '') ?>
                        </div>

                        <div class="btns">

                            <a href="?moveup=<?= $img['ImageID'] ?>&room=<?= urlencode($selectedRoom) ?>">
                                <button class="btn-xs view">↑</button>
                            </a>

                            <a href="?movedown=<?= $img['ImageID'] ?>&room=<?= urlencode($selectedRoom) ?>">
                                <button class="btn-xs view">↓</button>
                            </a>

                            <a href="?delete=<?= $img['ImageID'] ?>&room=<?= urlencode($selectedRoom) ?>"
                               onclick="return confirm('Delete?')">
                                <button class="btn-xs del">Delete</button>
                            </a>

                        </div>

                    </div>

                </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <p style="color:#555;">
                Select a room above to view its photos.
            </p>

        <?php endif; ?>

    </div>
</div>

</body>
</html>
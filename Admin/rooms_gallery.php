<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit(); }
require_once '../connection.php';

/* DELETE IMAGE */
if (isset($_GET['delete'])) {
    $id  = (int)$_GET['delete'];
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM room_images WHERE ImageID = $id"));

    if ($row) {
        $fp = "../User/" . $row['ImagePath'];
        if (file_exists($fp)) unlink($fp);

        mysqli_query($conn, "DELETE FROM room_images WHERE ImageID = $id");
    }

    header('Location: gallery_view.php?room=' . urlencode($_GET['room'] ?? ''));
    exit();
}

/* MOVE UP */
if (isset($_GET['moveup'])) {
    $id   = (int)$_GET['moveup'];
    $room = mysqli_real_escape_string($conn, $_GET['room']);

    $cur = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM room_images WHERE ImageID = $id"));

    if ($cur) {
        $prev = mysqli_fetch_assoc(mysqli_query(
            $conn,
            "SELECT * FROM room_images 
             WHERE RoomID = '$room' 
             AND ImageOrder < {$cur['ImageOrder']} 
             ORDER BY ImageOrder DESC LIMIT 1"
        ));

        if ($prev) {
            mysqli_query($conn, "UPDATE room_images SET ImageOrder = {$cur['ImageOrder']} WHERE ImageID = {$prev['ImageID']}");
            mysqli_query($conn, "UPDATE room_images SET ImageOrder = {$prev['ImageOrder']} WHERE ImageID = $id");
        }
    }

    header('Location: gallery_view.php?room=' . urlencode($_GET['room']));
    exit();
}

/* MOVE DOWN */
if (isset($_GET['movedown'])) {
    $id   = (int)$_GET['movedown'];
    $room = mysqli_real_escape_string($conn, $_GET['room']);

    $cur = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM room_images WHERE ImageID = $id"));

    if ($cur) {
        $next = mysqli_fetch_assoc(mysqli_query(
            $conn,
            "SELECT * FROM room_images 
             WHERE RoomID = '$room' 
             AND ImageOrder > {$cur['ImageOrder']} 
             ORDER BY ImageOrder ASC LIMIT 1"
        ));

        if ($next) {
            mysqli_query($conn, "UPDATE room_images SET ImageOrder = {$cur['ImageOrder']} WHERE ImageID = {$next['ImageID']}");
            mysqli_query($conn, "UPDATE room_images SET ImageOrder = {$next['ImageOrder']} WHERE ImageID = $id");
        }
    }

    header('Location: gallery_view.php?room=' . urlencode($_GET['room']));
    exit();
}

/* LOAD ROOMS + IMAGES */
$allRooms = mysqli_query(
    $conn,
    "SELECT RoomID, RoomType, RoomNumber, Location 
     FROM rooms 
     ORDER BY Location, RoomType"
);

$selectedRoom = $_GET['room'] ?? '';
$images = [];

if ($selectedRoom) {
    $re = mysqli_real_escape_string($conn, $selectedRoom);
    $r  = mysqli_query($conn, "SELECT * FROM room_images WHERE RoomID = '$re' ORDER BY ImageOrder ASC, ImageID ASC");

    while ($img = mysqli_fetch_assoc($r)) {
        $images[] = $img;
    }
}

/* UPLOAD IMAGES */
if (isset($_POST['upload'])) {
    $roomID = trim($_POST['RoomID']);

    if (!empty($_FILES['images']['name'][0])) {

        $uploadDir = __DIR__ . "/../User/uploads/rooms/" . $roomID . "/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $stmt = $conn->prepare(
            "INSERT INTO room_images (RoomID, ImagePath, ImageName, ImageOrder)
             VALUES (?, ?, ?, ?)"
        );

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $uploadedCount = 0;

        for ($i = 0; $i < count($_FILES['images']['name']); $i++) {

            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $originalName = basename($_FILES['images']['name'][$i]);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($extension, $allowed)) {
                continue;
            }

            $newFileName = uniqid('room_', true) . '.' . $extension;
            $targetPath = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $targetPath)) {

                $relativePath = 'uploads/rooms/' . $roomID . '/' . $newFileName;
                $imageOrder = $i + 1;

                $stmt->bind_param('sssi', $roomID, $relativePath, $originalName, $imageOrder);

                if ($stmt->execute()) {
                    $uploadedCount++;
                }
            }
        }

        $stmt->close();

        if ($uploadedCount > 0) {
            echo "<script>alert('$uploadedCount image(s) uploaded successfully!');</script>";
        } else {
            echo "<script>alert('No valid images were uploaded.');</script>";
        }

    } else {
        echo "<script>alert('Please select at least one image.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gallery - Admin</title>

    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="admin.css">

    <style>
        .img-grid { display:flex; flex-wrap:wrap; gap:12px; }
        .img-card { background:#1a2035; border-radius:8px; overflow:hidden; width:180px; }
        .img-card img { width:180px; height:120px; object-fit:cover; display:block; }
        .img-card .info { padding:8px; }
        .img-card .name { color:#888; font-size:10px; margin-bottom:6px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .img-card .btns { display:flex; gap:4px; }
        .order-badge { background:#0d1017; color:#c9a84c; font-size:10px; padding:2px 8px; border-radius:4px; margin-bottom:6px; display:inline-block; }
        .filter-row { display:flex; align-items:center; gap:12px; margin-bottom:20px; }
        .filter-row select { background:#1a2035; border:0.5px solid #333; border-radius:6px; color:white; font-size:12px; padding:8px 12px; }

        .upload-box {
            width: 400px;
            height: 300px;
            background: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            color: #fff;
        }
        
        .room-select {
            background:#1a2035;
            border:0.5px solid #333;
            border-radius:6px;
            color:white;
            font-size:12px;
            padding:8px 12px;
            width:100%;
            margin-top:5px;
        }

        .upload-box h2 { font-size: 18px; margin-bottom: 15px; color: #c9a84c; }
        .upload-box label { font-size: 12px; margin-top: 10px; display: block; }
        .upload-box input { width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #333; background: #111; color: #fff; }
        .upload-box button { margin-top: 15px; width: 100%; padding: 10px; background: #c9a84c; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; }
    </style>
</head>

<body>

<div class="admin-shell">
    <?php include 'sidebar.php'; ?>

    <div class="admin-content">

        <!-- UPLOAD SECTION -->
        <div class="admin-topbar">
            <h2>Photo Gallery</h2>
        </div>

        <div class="upload-box">
            <h2>Upload Room Images</h2>

            <form method="POST" enctype="multipart/form-data">
                <label for="RoomID">Room ID</label>
                <select name="RoomID" id="RoomID" required class="room-select">
                    <option value="">-- Select Room --</option>
                    <?php
                    $allRooms2 = mysqli_query($conn, "SELECT RoomID, RoomType, RoomNumber, Location FROM rooms ORDER BY Location, RoomType");

                    $currentLoc = '';

                    while ($r = mysqli_fetch_assoc($allRooms2)):

                        if ($r['Location'] != $currentLoc):
                            if ($currentLoc != '') echo '</optgroup>';
                            echo '<optgroup label="' . htmlspecialchars($r['Location']) . '">';
                            $currentLoc = $r['Location'];
                        endif;
                    ?>

                        <option value="<?= $r['RoomID'] ?>">
                            <?= htmlspecialchars($r['RoomType']) ?> (<?= $r['RoomNumber'] ?>)
                        </option>

                    <?php endwhile; if ($currentLoc != '') echo '</optgroup>'; ?>
                </select>

                <label>Select Images</label>
                <input type="file" name="images[]" multiple accept="image/*" required>

                <button type="submit" name="upload">Upload Images</button>
            </form>
        </div>

        <!-- MANAGE SECTION -->
        <div class="admin-topbar">
            <h2>Manage Room Photos</h2>
        </div>

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

                    <option value="<?= $r['RoomID'] ?>" <?= $selectedRoom==$r['RoomID']?'selected':'' ?>>
                        <?= htmlspecialchars($r['RoomType']) ?> (<?= $r['RoomNumber'] ?>)
                    </option>

                    <?php endwhile; if($currentLoc != '') echo '</optgroup>'; ?>
                </select>
            </form>
        </div>

        <?php if ($selectedRoom && count($images) == 0): ?>
            <p style="color:#555;">No photos yet.</p>

        <?php elseif (count($images) > 0): ?>
            <p style="color:#888; font-size:12px; margin-bottom:12px;">
                <?= count($images) ?> photo(s) — use ↑ ↓ to reorder.
            </p>

            <div class="img-grid">
                <?php foreach($images as $img): ?>
                <div class="img-card">

                    <img src="../User/<?= htmlspecialchars($img['ImagePath']) ?>">

                    <div class="info">
                        <div class="order-badge">Order: <?= $img['ImageOrder'] ?></div>
                        <div class="name"><?= htmlspecialchars($img['ImageName'] ?? '') ?></div>

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
        <?php endif; ?>

    </div>
</div>

</body>
</html>
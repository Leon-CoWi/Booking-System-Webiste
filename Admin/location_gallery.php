<?php
session_start();
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit(); }
require_once '../connection.php';

$branches = ['Casa FAM Appartelle', 'V.F Riton Appartelle'];

// upload image
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['photo'])) {
    $loc        = mysqli_real_escape_string($conn, $_POST['location']);
    $folderType = $_POST['type']; // location or signature

    $folderName = preg_replace('/[^a-zA-Z0-9]/', '_', $loc);
    $dir        = "../User/uploads/{$folderType}/{$folderName}/";

    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $filename = time() . '_' . basename($_FILES['photo']['name']);

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $filename)) {
        $dbpath = "uploads/{$folderType}/{$folderName}/{$filename}";

        $maxOrder = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT MAX(SortOrder) as m FROM location_images WHERE Location = '$loc'")
        )['m'] ?? 0;

        if ($folderType == 'location') {

            // remove old image (only one allowed)
            mysqli_query($conn,
                "DELETE FROM location_images WHERE Location = '$loc'"
            );

            $sort = 1;

        } else {

            // multiple images allowed
            $maxOrder = mysqli_fetch_assoc(
                mysqli_query($conn, "SELECT MAX(SortOrder) as m FROM location_images WHERE Location = '$loc'")
            )['m'] ?? 0;

            $sort = $maxOrder + 1;
        }

        mysqli_query(
            $conn,
            "INSERT INTO location_images (Location, ImagePath, SortOrder)
            VALUES ('$loc', '$dbpath', $sort)"
        );
    }

    header('Location: location_gallery.php');
    exit();
}

// delete image
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $row = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM location_images WHERE ImageID = $id")
    );

    if ($row) {
        $fp = "../User/" . $row['ImagePath'];
        if (file_exists($fp)) unlink($fp);
        mysqli_query($conn, "DELETE FROM location_images WHERE ImageID = $id");
    }

    header('Location: location_gallery.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Location Gallery - Admin</title>
    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="admin.css">
    <style>
        .gal-grid { display:flex; flex-wrap:wrap; gap:10px; margin-top:10px; }
        .gal-item { position:relative; width:130px; }
        .gal-item img { width:130px; height:90px; object-fit:cover; border-radius:6px; display:block; }
        .del-btn { position:absolute; top:4px; right:4px; background:rgba(180,0,0,0.85); color:#fff; border:none; border-radius:50%; width:22px; height:22px; cursor:pointer; font-size:12px; padding:0; }
        .up-box { width:130px; height:90px; border:2px dashed #444; border-radius:6px; display:flex; align-items:center; justify-content:center; background:#1a1a1a; }
        .branch-heading { color:#fff; font-size:18px; font-weight:700; border-left:4px solid #c9a84c; padding-left:12px; margin:28px 0 16px; }
        .section-title { color:#c9a84c; margin-top:20px; font-size:16px; }
    </style>
</head>
<body>
<div class="admin-shell">
    <?php include 'sidebar.php'; ?>
    <div class="admin-content">
        <div class="admin-topbar"><h2>Location Gallery</h2></div>

        <!-- location branches -->
        <?php foreach($branches as $branch):
            $branchEsc = mysqli_real_escape_string($conn, $branch);
        ?>
        <div class="branch-heading"><?= htmlspecialchars($branch) ?></div>

        <div class="gal-grid">
            <?php
            $imgs = mysqli_query(
                $conn,
                "SELECT * FROM location_images
                 WHERE Location = '$branchEsc'
                 AND ImagePath LIKE 'uploads/location/%'
                 ORDER BY SortOrder ASC"
            );

            while($img = mysqli_fetch_assoc($imgs)):
            ?>
            <div class="gal-item">
                <img src="../User/<?= htmlspecialchars($img['ImagePath']) ?>" alt="">
                <a href="?delete=<?= $img['ImageID'] ?>" onclick="return confirm('Delete?')">
                    <button class="del-btn">&#x2715;</button>
                </a>
            </div>
            <?php endwhile; ?>

            <div class="up-box">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="location" value="<?= htmlspecialchars($branch) ?>">
                    <input type="hidden" name="type" value="location">

                    <label style="cursor:pointer;color:#888;font-size:11px;text-align:center;">
                        + Upload
                        <input type="file" name="photo" accept="image/*" style="display:none;" onchange="this.form.submit()">
                    </label>
                </form>
            </div>
        </div>
        <?php endforeach; ?>


        <!-- signature elements -->
        <div class="branch-heading">Signature Elements</div>

        <div class="gal-grid">
            <?php
            $sig = mysqli_query(
                $conn,
                "SELECT * FROM location_images
                 WHERE Location = 'Signature Elements'
                 AND ImagePath LIKE 'uploads/signature/%'
                 ORDER BY SortOrder ASC"
            );

            while($img = mysqli_fetch_assoc($sig)):
            ?>
            <div class="gal-item">
                <img src="../User/<?= htmlspecialchars($img['ImagePath']) ?>" alt="">
                <a href="?delete=<?= $img['ImageID'] ?>" onclick="return confirm('Delete?')">
                    <button class="del-btn">&#x2715;</button>
                </a>
            </div>
            <?php endwhile; ?>

            <div class="up-box">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="location" value="Signature Elements">
                    <input type="hidden" name="type" value="signature">

                    <label style="cursor:pointer;color:#888;font-size:11px;text-align:center;">
                        + Upload
                        <input type="file" name="photo" accept="image/*" style="display:none;" onchange="this.form.submit()">
                    </label>
                </form>
            </div>
        </div>

    </div>
</div>
</body>
</html>
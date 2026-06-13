<?php
session_start();

// check admin login
if (!isset($_SESSION['admin'])) { 
    header('Location: login.php'); 
    exit(); 
}

require_once '../connection.php';

/* upload image */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['photo'])) {

    $basePath = "../User/uploads/";
    $type = $_POST['type'];

    // set location + folder
    if ($type == 'homepage') {
        $loc = 'homepage';
        $dir = $basePath . "homepage/";

    } elseif ($type == 'element') {
        $loc = 'element_section';
        $dir = $basePath . "element_section/";

    } elseif ($type == 'logos_casa') {
        $loc = 'logos_casa';
        $dir = $basePath . "logos/casa/";

    } elseif ($type == 'logos_vf') {
        $loc = 'logos_vf';
        $dir = $basePath . "logos/vf/";

    } elseif ($type == 'website_logo') {
        $loc = 'website_logo';
        $dir = $basePath . "logos/website/";
    }

    // create folder
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    // file name
    $filename = time() . '_' . basename($_FILES['photo']['name']);
    $fullpath = $dir . $filename;

    // db path
    $dbpath = str_replace("../User/", "", $fullpath);

    // upload file
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $fullpath)) {

        // single image types
        if (in_array($type, ['logos_casa', 'logos_vf', 'website_logo'])) {

            // delete old image
            $old = mysqli_query($conn, "
                SELECT ImagePath 
                FROM branch_images 
                WHERE Location = '$loc'
            ");

            while ($row = mysqli_fetch_assoc($old)) {

                $oldFile = "../User/" . $row['ImagePath'];

                if (file_exists($oldFile)) unlink($oldFile);
            }

            mysqli_query($conn, "
                DELETE FROM branch_images 
                WHERE Location = '$loc'
            ");

            $sort = 1;

        } else {

            // get next order
            $maxOrder = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT MAX(SortOrder) as m FROM branch_images WHERE Location = '$loc'"
            ))['m'] ?? 0;

            $sort = $maxOrder + 1;
        }

        // insert image
        mysqli_query($conn, "
            INSERT INTO branch_images (Location, ImagePath, SortOrder)
            VALUES ('$loc', '$dbpath', $sort)
        ");
    }

    header('Location: homepage_gallery.php');
    exit();
}

/* delete image */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $row = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT * 
        FROM branch_images 
        WHERE ImageID = $id
    "));

    if ($row) {

        $fp = "../User/" . $row['ImagePath'];

        if (file_exists($fp)) unlink($fp);

        mysqli_query($conn, "
            DELETE FROM branch_images 
            WHERE ImageID = $id
        ");
    }

    header('Location: homepage_gallery.php');
    exit();
}

/* load images */
$homeImgs = mysqli_query($conn,
    "SELECT * FROM branch_images WHERE Location='homepage' ORDER BY SortOrder ASC"
);

$elemImgs = mysqli_query($conn,
    "SELECT * FROM branch_images WHERE Location='element_section' ORDER BY SortOrder ASC"
);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Homepage Gallery - Admin</title>

    <link rel="stylesheet" href="../Style.css">
    <link rel="stylesheet" href="admin.css">

    <style>
        .gal-grid {
            display:flex;
            flex-wrap:wrap;
            gap:10px;
            margin-top:10px;
        }

        .gal-item {
            position:relative;
            width:150px;
        }

        .gal-item img {
            width:150px;
            height:100px;
            object-fit:cover;
            border-radius:6px;
            display:block;
        }

        .del-btn {
            position:absolute;
            top:4px;
            right:4px;
            background:rgba(180,0,0,0.85);
            color:#fff;
            border:none;
            border-radius:50%;
            width:22px;
            height:22px;
            cursor:pointer;
            font-size:12px;
            line-height:22px;
        }

        .up-box {
            width:150px;
            height:100px;
            border:2px dashed #444;
            border-radius:6px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:#1a1a1a;
        }

        .gal-sec {
            margin-bottom:32px;
        }

        .gal-sec h3 {
            color:#c9a84c;
            margin-bottom:4px;
        }

        .gal-sec p {
            color:#888;
            font-size:11px;
            margin-bottom:10px;
        }
    </style>
</head>

<body>

<div class="admin-shell">

<?php include 'sidebar.php'; ?>

<div class="admin-content">

    <div class="admin-topbar">
        <h2>Homepage Gallery</h2>
    </div>

    <!-- homepage -->
    <div class="gal-sec">
        <h3>Homepage Background Slideshow</h3>
        <p>Full-width hero images.</p>

        <div class="gal-grid">
            <?php while($img = mysqli_fetch_assoc($homeImgs)): ?>
            <div class="gal-item">
                <img src="../User/<?= htmlspecialchars($img['ImagePath']) ?>">
                <a href="?delete=<?= $img['ImageID'] ?>"><button class="del-btn">×</button></a>
            </div>
            <?php endwhile; ?>

            <div class="up-box">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="homepage">
                    <label style="cursor:pointer;color:#888;font-size:11px;">
                        + Upload
                        <input type="file" name="photo" hidden accept="image/*" onchange="this.form.submit()">
                    </label>
                </form>
            </div>
        </div>
    </div>

    <!-- casa logo -->
    <div class="gal-sec">
        <h3>Casa Fam Branch Logo</h3>

        <div class="gal-grid">
            <?php
            $casa = mysqli_query($conn, "SELECT * FROM branch_images WHERE Location='logos_casa'");
            while($img = mysqli_fetch_assoc($casa)):
            ?>
            <div class="gal-item">
                <img src="../User/<?= htmlspecialchars($img['ImagePath']) ?>">
                <a href="?delete=<?= $img['ImageID'] ?>"><button class="del-btn">×</button></a>
            </div>
            <?php endwhile; ?>

            <div class="up-box">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="logos_casa">
                    <label style="cursor:pointer;color:#888;font-size:11px;">
                        + Upload Casa Logo
                        <input type="file" name="photo" hidden accept="image/*" onchange="this.form.submit()">
                    </label>
                </form>
            </div>
        </div>
    </div>

    <!-- vf logo -->
    <div class="gal-sec">
        <h3>VF Riton Branch Logo</h3>

        <div class="gal-grid">
            <?php
            $vf = mysqli_query($conn, "SELECT * FROM branch_images WHERE Location='logos_vf'");
            while($img = mysqli_fetch_assoc($vf)):
            ?>
            <div class="gal-item">
                <img src="../User/<?= htmlspecialchars($img['ImagePath']) ?>">
                <a href="?delete=<?= $img['ImageID'] ?>"><button class="del-btn">×</button></a>
            </div>
            <?php endwhile; ?>

            <div class="up-box">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="logos_vf">
                    <label style="cursor:pointer;color:#888;font-size:11px;">
                        + Upload VF Logo
                        <input type="file" name="photo" hidden accept="image/*" onchange="this.form.submit()">
                    </label>
                </form>
            </div>
        </div>
    </div>

    <!-- website logo -->
    <div class="gal-sec">
        <h3>Website Logo</h3>

        <div class="gal-grid">
            <?php
            $websiteLogo = mysqli_query($conn, "SELECT * FROM branch_images WHERE Location='website_logo'");
            while($img = mysqli_fetch_assoc($websiteLogo)):
            ?>
            <div class="gal-item">
                <img src="../User/<?= htmlspecialchars($img['ImagePath']) ?>">
                <a href="?delete=<?= $img['ImageID'] ?>"><button class="del-btn">×</button></a>
            </div>
            <?php endwhile; ?>

            <div class="up-box">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="website_logo">
                    <label style="cursor:pointer;color:#888;font-size:11px;">
                        + Upload Website Logo
                        <input type="file" name="photo" hidden accept="image/*" onchange="this.form.submit()">
                    </label>
                </form>
            </div>
        </div>
    </div>

</div>
</div>
</body>
</html>
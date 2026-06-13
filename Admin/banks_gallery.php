<?php
session_start();

// check login
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
    if ($type == 'logos_casa') {

        $loc = 'GCash';
        $dir = $basePath . "payment_details/";

    } elseif ($type == 'logos_vf') {

        $loc = 'Metrobank';
        $dir = $basePath . "payment_details/";
    }

    // create folder if not exists
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    // file name
    $filename = time() . '_' . basename($_FILES['photo']['name']);
    $fullpath = $dir . $filename;

    // path for DB
    $dbpath = str_replace("../User/", "", $fullpath);

    // move file
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $fullpath)) {

        // delete old image
        $old = mysqli_query($conn, "
            SELECT ImagePath 
            FROM payment_details 
            WHERE Location = '$loc'
        ");

        while ($row = mysqli_fetch_assoc($old)) {

            $oldFile = "../User/" . $row['ImagePath'];

            if (file_exists($oldFile)) unlink($oldFile);
        }

        // remove old record
        mysqli_query($conn, "
            DELETE FROM payment_details 
            WHERE Location = '$loc'
        ");

        // insert new image
        mysqli_query($conn, "
            INSERT INTO payment_details (Location, ImagePath)
            VALUES ('$loc', '$dbpath')
        ");
    }

    header('Location: banks_gallery.php');
    exit();
}

/* delete image */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $row = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT * 
        FROM payment_details 
        WHERE ImageID = $id
    "));

    if ($row) {

        $fp = "../User/" . $row['ImagePath'];

        if (file_exists($fp)) unlink($fp);

        mysqli_query($conn, "
            DELETE FROM payment_details 
            WHERE ImageID = $id
        ");
    }

    header('Location: banks_gallery.php');
    exit();
}

/* load data */
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
        <h2>Payment Details Gallery</h2>
    </div>

    <!-- CASA LOGO -->
    <div class="gal-sec">
        <h3>GCash Details</h3>

        <div class="gal-grid">
            <?php
            $casa = mysqli_query($conn, "SELECT * FROM payment_details WHERE Location='GCash'");
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
                        + Upload GCash Details
                        <input type="file" name="photo" hidden accept="image/*" onchange="this.form.submit()">
                    </label>
                </form>
            </div>
        </div>
    </div>

    <!-- VF LOGO -->
    <div class="gal-sec">
        <h3>Metrobank Details</h3>

        <div class="gal-grid">
            <?php
            $vf = mysqli_query($conn, "SELECT * FROM payment_details WHERE Location='Metrobank'");
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
                        + Upload Metrobank Details
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

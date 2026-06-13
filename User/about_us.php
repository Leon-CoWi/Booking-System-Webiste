<?php 
session_start();
require_once '../connection.php';
include 'logos.php';
include 'background.php';
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>About Us - United Transient</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="about_us.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="logos.css">
    <link rel="stylesheet" href="background.css">
    <!-- For the icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Arima:wght@100..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ysabeau+SC:wght@1..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=LINE+Seed+JP&display=swap" rel="stylesheet">
</head>

<body>

<?php include 'header.php'; ?>

<main>

<!-- HERO -->
<div class="hero">

    <div class="bg-slider">

        <?php if (!empty($bgArr)): ?>

            <?php foreach ($bgArr as $i => $img): ?>

                <img class="bg-slide <?= $i === 0 ? 'active' : '' ?>"
                    src="../User/<?= htmlspecialchars(str_replace('../User/', '', $img)) ?>">

            <?php endforeach; ?>

        <?php else: ?>

            <img class="bg-slide active"
                src="../User/uploads/default.png">

        <?php endif; ?>

    </div>

    <div class="title">ABOUT US</div>

</div>

<div class="container">

    <!-- WHO WE ARE + FACILITIES -->
    <div class="about-grid">

        <div class="about-left">
            <h2>Who We Are</h2>
            <div class="branch-logos">
            <!-- CASA LOGO -->
            <img src="../User/<?= htmlspecialchars($casaLogo['ImagePath'] ?? 'uploads/default.png') ?>" 
                alt="Casa Fam Logo">

        

            <!-- VF LOGO -->
            <img src="../User/<?= htmlspecialchars($vfLogo['ImagePath'] ?? 'uploads/default.png') ?>" 
                alt="VF Riton Logo">
            </div>
            <p>
                Our apartelle is a locally owned accommodation brand offering comfortable, affordable, and convenient stays in Laoag City.
                Managed under one vision, we operate two branches—Casa Fam and V.F. Riton—designed for families, groups, and travelers.
                <br><br>
                We focus on providing practical yet comfortable spaces with complete essentials, modern amenities, and flexible room options.
                Whether short or long stay, we ensure a clean, safe, and hassle-free experience where guests can relax and feel at home.
            </p>
        </div>

        <div class="about-box">
            <h2>Room Facilities</h2>
            <ul style="list-style: none; padding: 0; line-height: 2;">
                <li><i class="fa-solid fa-snowflake" style="color:#0f0046; width:20px;"></i> Fully air-conditioned rooms with fan support</li>
                <li><i class="fa-solid fa-shower" style="color:#0f0046; width:20px;"></i> Private bathrooms with shower heater & bidet</li>
                <li><i class="fa-solid fa-bed" style="color:#0f0046; width:20px;"></i> Comfortable bedding</li>
                <li><i class="fa-solid fa-bath" style="color:#0f0046; width:20px;"></i> Complete linens and towels provided</li>
                <li><i class="fa-solid fa-wifi" style="color:#0f0046; width:20px;"></i> Free Wi-Fi in all rooms</li>
                <li><i class="fa-solid fa-tv" style="color:#0f0046; width:20px;"></i> 32-inch HD TV with cable channels</li>
                <li><i class="fa-solid fa-shirt" style="color:#0f0046; width:20px;"></i> Closets and essential room furniture</li>
                <li><i class="fa-solid fa-chair" style="color:#0f0046; width:20px;"></i> Table and seating space for work or dining per room</li>
                <li><i class="fa-solid fa-temperature-low" style="color:#0f0046; width:20px;"></i> Refrigerator available upon request</li>
            </ul>
        </div>

    </div>
</div>

<!-- SIGNATURE SECTION -->
<div class="signature-section">
<h1>Signature Elements and Facilities</h1>

    <div class="signature-outer">

        <div class="signature-wrapper">
        <div class="dots" id="sigDots"></div>
            <div class="signature-track" id="sigTrack">

                <?php
                $branchName = 'Signature Elements';

                $query = mysqli_query(
                    $conn,
                    "SELECT ImagePath
                     FROM location_images
                     WHERE Location = '$branchName'
                     AND ImagePath LIKE 'uploads/signature/%'
                     ORDER BY SortOrder ASC"
                );

                $images = [];
                while ($row = mysqli_fetch_assoc($query)) {
                    $images[] = $row['ImagePath'];
                }
                ?>

                <div class="signature-card">
                    <div class="signature-img-wrap">
                        <img src="<?= $images[0] ?? 'homee.jpg' ?>">
                    </div>
                    <div class="signature-content">
                        <h2>Comfortable and Functional Room Design</h2>
                        <p>Designed for families and groups with practical layouts and comfort.</p>
                    </div>
                </div>

                <div class="signature-card">
                    <div class="signature-img-wrap">
                        <img src="<?= $images[1] ?? 'CasaFam/ZCasa1/1C1.jpg' ?>">
                    </div>
                    <div class="signature-content">
                        <h2>Two Convenient Branch Locations</h2>
                        <p>Casa Fam and V.F. Riton offer flexible stay options.</p>
                    </div>
                </div>

                <div class="signature-card">
                    <div class="signature-img-wrap">
                        <img src="<?= $images[2] ?? 'CasaFam/ZCasa2/2C1.jpg' ?>">
                    </div>
                    <div class="signature-content">
                        <h2>Essential Amenities</h2>
                        <p>Rooms include air-conditioning, bathrooms, and complete essentials.</p>
                    </div>
                </div>

                <div class="signature-card">
                    <div class="signature-img-wrap">
                        <img src="<?= $images[3] ?? 'CasaFam/ZCasa3/3C1.jpg' ?>">
                    </div>
                    <div class="signature-content">
                        <h2>Reliable Connectivity</h2>
                        <p>Stay connected with free Wi-Fi and entertainment options.</p>
                    </div>
                </div>

                <div class="signature-card">
                    <div class="signature-img-wrap">
                        <img src="<?= $images[4] ?? 'room4.jpg' ?>">
                    </div>
                    <div class="signature-content">
                        <h2>Shared Spaces in Casa Fam</h2>
                        <p>Working and lounge area available for convenience.</p>
                    </div>
                </div>

                <div class="signature-card">
                    <div class="signature-img-wrap">
                        <img src="<?= $images[5] ?? 'room5.jpg' ?>">
                    </div>
                    <div class="signature-content">
                        <h2>Self-Service Stay</h2>
                        <p>Easy check-in and check-out for privacy and independence.</p>
                    </div>
                </div>

            </div>
        </div>


    </div>
</div>

<!-- HOW TO GET THERE -->
<div class="contact-section">

    <h1>How To Get There</h1>

    <!-- CASA FAM -->
    <div class="branch-row">

        <!-- LEFT SIDE (IMAGE + MAP) -->
        <div class="branch-media">
            <?php
            $branchName = 'Casa FAM Appartelle';

            $query = mysqli_query(
                $conn,
                "SELECT ImagePath
                FROM location_images
                WHERE Location = '$branchName'
                AND ImagePath LIKE 'uploads/location/%'
                ORDER BY SortOrder ASC
                LIMIT 1"
            );

            $image = mysqli_fetch_assoc($query);

            if ($image && !empty($image['ImagePath'])) {
                echo '<img src="' . htmlspecialchars($image['ImagePath']) . '" alt="' . htmlspecialchars($branchName) . '">';
            } else {
                echo '<img src=\"CasaFam/CasaFam/cf1.jpg\" alt=\"Default Image\">';
            }
            ?>
        </div>
        
        <!-- RIGHT SIDE (TEXT) -->
        <div class="branch-text">
            <h2>Casa Fam</h2>
            <p>
                <i class="fa-solid fa-location-dot" style="color:#0f0046; width:20px;"></i>
                Brgy. 48-B, Cabungaan South, Sitio 6, Laoag City<br>

                <i class="fa-solid fa-phone" style="color:#0f0046; width:20px;"></i>
                0943-593-44-80<br>

                <i class="fa-solid fa-envelope" style="color:#0f0046; width:20px;"></i>
                analiza.riton@yahoo.com<br><br>
            </p>

            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3273.076289444258!2d120.56246931537!3d18.17964256472243!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x338ec7a83357d11d%3A0xa1f599f7304664f2!2sRiton%20Apartelle%20Annex!5e0!3m2!1sen!2sph!4v1777103154782!5m2!1sen!2sph"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

    </div>

    <!-- VF RITON -->
    <div class="branch-row">

        <!-- LEFT SIDE (IMAGE + MAP) -->
<div class="branch-media">
    <?php
    $branchName = 'V.F Riton Appartelle';

    $query = mysqli_query(
        $conn,
        "SELECT ImagePath
         FROM location_images
         WHERE Location = '$branchName'
         AND ImagePath LIKE 'uploads/location/%'
         ORDER BY SortOrder ASC
         LIMIT 1"
    );

    $image = mysqli_fetch_assoc($query);

    if ($image && !empty($image['ImagePath'])) {
        echo '<img src="' . htmlspecialchars($image['ImagePath']) . '" alt="' . htmlspecialchars($branchName) . '">';
    } else {
        echo '<img src=\"CasaFam/CasaFam/cf1.jpg\" alt=\"Default Image\">';
    }
    ?>
</div>

        
        <!-- RIGHT SIDE (TEXT) -->
        <div class="branch-text">
            <h2>V.F Riton</h2>
            <p>
                <i class="fa-solid fa-location-dot" style="color:#0f0046; width:20px;"></i>
                Brgy. 6, Romero Street, Laoag City<br>

                <i class="fa-solid fa-phone" style="color:#0f0046; width:20px;"></i>
                0943-593-44-80<br>

                <i class="fa-solid fa-envelope" style="color:#0f0046; width:20px;"></i>
                analiza.riton@yahoo.com<br><br>
            </p>

            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3790.288621784256!2d120.58557637452904!3d18.196655878989706!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x338ec7a12d9bcde3%3A0x40c7d9ce87066358!2sV.F.%20Riton%20Apartelle!5e0!3m2!1sen!2sph!4v1777103274579!5m2!1sen!2sph" 
                allowfullscreen=" "
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

    </div>
</div>
</main>


<!-- JS SLIDER -->
<script src="about_us.js"></script>
<script src="background_functions.js"></script>


<!-- FOOTER -->
<?php include 'footer.php'; ?>

</body>
</html>
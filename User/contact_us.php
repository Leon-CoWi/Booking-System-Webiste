<?php 
session_start();
require_once '../connection.php';
include 'background.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Contact Us - United Transient</title>

    <!-- Load the page-wide base styles and page-specific contact layout -->
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="contact_us.css">

    <!-- Load the header and footer styles used across all pages -->
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="footer.css">

    <!-- Pull in Font Awesome for the icons used in cards and location rows -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Load the Google Fonts used throughout the site -->
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

<!-- Pull in the hero background slider from background.php -->
<div class="hero">
    <div class="bg-slider">

        <?php if (!empty($bgArr)): ?>

            <?php foreach ($bgArr as $i => $img): ?>

                <img class="bg-slide <?= $i === 0 ? 'active' : '' ?>"
                    src="../User/<?= htmlspecialchars(str_replace('../User/', '', $img)) ?>">

            <?php endforeach; ?>

        <?php else: ?>

            <!-- Fall back to a default image if no backgrounds are set -->
            <img class="bg-slide active"
                src="../User/uploads/default.png">

        <?php endif; ?>

    </div>

    <div class="title">CONTACT US</div>
</div>

<!-- Introduce both branches with a short description before the cards -->
<div class="intro">
    <div class="intro-inner">
        <h2>Find Us in the Heart of Laoag City</h2>
        <p> United Transient operates two branch locations in Laoag City, the Sunshine City of the North.<br>
            Whichever branch you choose, you'll have a comfortable, affordable home base for exploring Ilocos Norte.
        </p>
    </div>
    
    <!-- Show one card per branch describing its surroundings and nearby attractions -->
    <div class="intro-cards">
        <div class="intro-card">
            <div class="intro-card-icon"><i class="fa-solid fa-city"></i></div>
            <h3>V.F. Riton</h3>
            <p>
                Located along Romero Street in the city proper, V.F. Riton sits in the heart of downtown Laoag —
                within easy reach of St. William's Cathedral, the Sinking Bell Tower, Museo Ilocos Norte,
                the Provincial Capitol, and the Laoag Public Market.
            </p>
        </div>
        
        <div class="intro-card">
            <div class="intro-card-icon"><i class="fa-solid fa-tree"></i></div>
            <h3>Casa Fam</h3>
            <p>
                Nestled in the quieter residential area of Cabungaan South, Casa Fam is close to
                the La Paz Sand Dunes and Laoag International Airport — about 6–8 km away.
                The UNESCO-listed Paoay Church is also just a short drive from this branch.
            </p>
        </div>
    </div>

</div>

<!-- Display quick-glance contact info cards for both branches -->
<div class="cards-section">
    <div class="contact-cards">

        <div class="contact-card">
            <div class="card-icon"><i class="fa-solid fa-location-dot"></i></div>
            <h3>Casa Fam</h3>
            <p>Brgy. 48-B, Cabungaan South, Sitio 6, Laoag City</p>
        </div>

        <div class="contact-card">
            <div class="card-icon"><i class="fa-solid fa-location-dot"></i></div>
            <h3>V.F. Riton</h3>
            <p>Brgy. 6, Romero Street, Laoag City</p>
        </div>

        <div class="contact-card">
            <div class="card-icon"><i class="fa-solid fa-phone"></i></div>
            <h3>Phone</h3>
            <p><a href="tel:+639435934480">0943-593-44-80</a></p>
        </div>

        <div class="contact-card">
            <div class="card-icon"><i class="fa-solid fa-envelope"></i></div>
            <h3>Email</h3>
            <p><a href="mailto:analiza.riton@yahoo.com">analiza.riton@yahoo.com</a></p>
        </div>

    </div>
</div>

<!-- Show a photo, contact details, embedded map, and directions button for each branch -->
<div class="location-section">
    <h1>Our Location</h1>

    <!-- CASA FAM location row -->
    <div class="location-row">
        <div class="location-media">
            <?php
            $branchName = 'Casa FAM Appartelle';

            /* Fetch the first sorted location image for this branch from the database */
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

            /* Render the branch photo or fall back to a default if none is found */
            if ($image && !empty($image['ImagePath'])) {
                echo '<img src="' . htmlspecialchars($image['ImagePath']) . '" alt="' . htmlspecialchars($branchName) . '">';
            } else {
                echo '<img src=\"CasaFam/CasaFam/cf1.jpg\" alt=\"Default Image\">';
            }
            ?>
        </div>

        <div class="location-text">
            <h2>Casa Fam</h2>
            <p>
                <i class="fa-solid fa-location-dot"></i>
                Brgy. 48-B, Cabungaan South, Sitio 6, Laoag City
            </p>
            <p>
                <i class="fa-solid fa-phone"></i>
                <a href="tel:+639435934480">0943-593-44-80</a>
            </p>
            <p>
                <i class="fa-solid fa-envelope"></i>
                <a href="mailto:analiza.riton@yahoo.com">analiza.riton@yahoo.com</a>
            </p>

            <!-- Embed the Google Map pinned to the Casa Fam location -->
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3273.076289444258!2d120.56246931537!3d18.17964256472243!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x338ec7a83357d11d%3A0xa1f599f7304664f2!2sRiton%20Apartelle%20Annex!5e0!3m2!1sen!2sph!4v1777103154782!5m2!1sen!2sph"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>

            <!-- Open Google Maps directions to Casa Fam in a new tab -->
            <a
                href="https://www.google.com/maps/dir/?api=1&destination=Riton+Apartelle+Annex+Laoag+City"
                target="_blank"
                class="direction-btn">
                <i class="fa-solid fa-diamond-turn-right"></i> Get Directions
            </a>
        </div>

    </div>

    <!-- VF RITON location row, image flipped to the right via the reverse class -->
    <div class="location-row reverse">
        <div class="location-media">
            <?php
            $branchName = 'V.F Riton Appartelle';

            /* Fetch the first sorted location image for this branch from the database */
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

            /* Render the branch photo or fall back to a default if none is found */
            if ($image && !empty($image['ImagePath'])) {
                echo '<img src="' . htmlspecialchars($image['ImagePath']) . '" alt="' . htmlspecialchars($branchName) . '">';
            } else {
                echo '<img src=\"CasaFam/CasaFam/cf1.jpg\" alt=\"Default Image\">';
            }
            ?>
        </div>

        <div class="location-text">
            <h2>V.F. Riton</h2>
            <p>
                <i class="fa-solid fa-location-dot"></i>
                Brgy. 6, Romero Street, Laoag City
            </p>
            <p>
                <i class="fa-solid fa-phone"></i>
                <a href="tel:+639435934480">0943-593-44-80</a>
            </p>
            <p>
                <i class="fa-solid fa-envelope"></i>
                <a href="mailto:analiza.riton@yahoo.com">analiza.riton@yahoo.com</a>
            </p>

            <!-- Embed the Google Map pinned to the V.F. Riton location -->
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3790.288621784256!2d120.58557637452904!3d18.196655878989706!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x338ec7a12d9bcde3%3A0x40c7d9ce87066358!2sV.F.%20Riton%20Apartelle!5e0!3m2!1sen!2sph!4v1777103274579!5m2!1sen!2sph"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>

            <!-- Open Google Maps directions to V.F. Riton in a new tab -->
            <a
                href="https://www.google.com/maps/dir/?api=1&destination=V.F.+Riton+Apartelle+Laoag+City"
                target="_blank"
                class="direction-btn">
                <i class="fa-solid fa-diamond-turn-right"></i> Get Directions
            </a>
        </div>

    </div>
</div>

</main>

<?php include 'footer.php'; ?>

<!-- Run the background slider transitions after the page loads -->
<script src="background_functions.js"></script>

</body>
</html>
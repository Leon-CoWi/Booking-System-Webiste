<?php
    // Start user session for booking and login handling
    session_start();

    // Connect homepage to the database
    require_once '../connection.php';

    // Load homepage background images
    include 'background.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <!-- Make the homepage responsive on mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title>Comodo</title>

    <!-- Load global and page-specific styles -->
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="homepage.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="header.css">

    <!-- Load icon library used across the homepage -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Load custom fonts used for branding and typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Arima:wght@100..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ysabeau+SC:wght@1..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=LINE+Seed+JP&display=swap" rel="stylesheet">

    <style>

    /* ── MODAL STYLES ── */

    /* Create the dark overlay behind the room modal */
    .room-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9000;
        background: rgba(0,0,0,0.6);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    /* Display the modal once it is opened */
    .room-modal-overlay.open { display: flex; }

    /* Design the main modal container */
    .room-modal {
        background: #fff;
        border-radius: 14px;
        max-width: 700px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        animation: rmModalIn 0.3s ease;
    }

    /* Animate the modal entrance */
    @keyframes rmModalIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Position the modal close button */
    .rm-modal-close {
        position: absolute;
        top: 10px; right: 14px;
        background: none;
        border: none;
        font-size: 2rem;
        color: #fff;
        cursor: pointer;
        z-index: 10;
        line-height: 1;
    }

    /* Change close button color on hover */
    .rm-modal-close:hover { color: #ffaaaa; }

    /* Create the image slider container inside the modal */
    .rm-modal-slider {
        position: relative;
        height: 320px;
        overflow: hidden;
        background: #eee;
        border-radius: 14px 14px 0 0;
    }

    /* Design each modal image slide */
    .rm-modal-slide {
        position: absolute;
        inset: 0;
        width: 100%; height: 100%;
        object-fit: cover;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    /* Display the currently active slide */
    .rm-modal-slide.active { opacity: 1; }

    /* Design the slider navigation buttons */
    .rm-slide-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #fff;
        font-size: 1.4rem;
        padding: 0 20px;
        cursor: pointer;
        z-index: 3;
    }

    /* Adjust arrow color on hover */
    .rm-slide-btn:hover { color: #ccc; }

    /* Position previous button */
    .rm-slide-btn.prev { left: 0; }

    /* Position next button */
    .rm-slide-btn.next { right: 0; }

    /* Create the slide indicator container */
    .rm-slide-dots {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 6px;
        z-index: 3;
    }

    /* Design individual slide indicators */
    .rm-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: background 0.2s;
    }

    /* Highlight the active slide indicator */
    .rm-dot.active { background: #fff; }

    /* Organize the modal information section */
    .rm-modal-info {
        padding: 24px 28px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* Style the room type heading */
    .rm-info-type   { font-size: 1.4rem; font-weight: 700; color: #0f0046; }

    /* Style the room location text */
    .rm-info-number { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.1em; color: #888; margin-top: -6px; }

    /* Add separation line between modal sections */
    .rm-divider     { border: none; border-top: 1px solid #eee; }

    /* Organize room metadata details */
    .rm-meta-row    { display: flex; flex-wrap: wrap; gap: 8px 16px; font-size: 0.84rem; color: #333; }

    /* Align room metadata icons and text */
    .rm-meta-row span { display: flex; align-items: center; gap: 6px; }

    /* Style metadata icons */
    .rm-meta-row i  { color: #0f0046; }

    /* Design the room description text */
    .rm-desc        { font-size: 0.85rem; line-height: 1.72; color: #555; }

    /* Style modal section titles */
    .rm-section-title { font-size: 0.75rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #0f0046; margin-bottom: 4px; }

    /* Style normal modal paragraph text */
    .rm-plain-text  { font-size: 0.83rem; color: #555; line-height: 1.6; margin: 0; }

    /* Create the room rate container */
    .rm-rate-box    { background: #f4f4f8; border: 1px solid #ddd; border-radius: 8px; padding: 14px 16px; }

    /* Style the room rate label */
    .rm-rate-label  { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.08em; color: #888; }

    /* Highlight the room price */
    .rm-rate-val    { font-size: 1.5rem; font-weight: 700; color: #0f0046; }

    /* Display room rate notice */
    .rm-rate-note   { font-size: 0.72rem; color: #aaa; }

    /* Adjust modal image height on smaller devices */
    @media (max-width: 600px) {
        .rm-modal-slider { height: 220px; }
    }
    </style>
</head>

<body>

    <!-- Load the shared website header -->
    <?php include 'header.php'; ?>

    <!-- Display room details in a popup modal -->
    <div class="room-modal-overlay" id="rmOverlay">
        <div class="room-modal" id="rmModal">

            <!-- Allow users to close the modal -->
            <button class="rm-modal-close" id="rmClose" title="Close">&times;</button>

            <!-- Display room images inside a slider -->
            <div class="rm-modal-slider" id="rmSlider">

                <!-- Navigate to previous room image -->
                <button class="rm-slide-btn prev" id="rmPrev">&#10094;</button>

                <!-- Navigate to next room image -->
                <button class="rm-slide-btn next" id="rmNext">&#10095;</button>

                <!-- Display image slider indicators -->
                <div class="rm-slide-dots" id="rmDots"></div>
            </div>

            <!-- Display room information -->
            <div class="rm-modal-info">

                <!-- Show room type and branch -->
                <div>
                    <div class="rm-info-type"   id="rmType"></div>
                    <div class="rm-info-number" id="rmNumber"></div>
                </div>

                <hr class="rm-divider">

                <!-- Display room metadata -->
                <div class="rm-meta-row" id="rmMeta"></div>

                <!-- Display room description -->
                <p class="rm-desc" id="rmDesc"></p>

                <!-- Display room features -->
                <div>
                    <div class="rm-section-title">Room Features</div>
                    <p class="rm-plain-text" id="rmFeatures"></p>
                </div>

                <!-- Display room amenities -->
                <div>
                    <div class="rm-section-title">Room Amenities</div>
                    <p class="rm-plain-text" id="rmAmenities"></p>
                </div>

                <!-- Display room rates -->
                <div class="rm-rate-box" id="rmRateBox" style="display:none">
                    <div class="rm-rate-label">Rate per night</div>
                    <div class="rm-rate-val" id="rmRate"></div>
                    <div class="rm-rate-note">Rates are subject to availability</div>
                </div>
            </div>
        </div>
    </div>

    <main>
        <div class="container">

            <!-- HERO -->
            <div class="home-container">

                <!-- Display rotating homepage background images -->
                <div class="bg-slider">

                    <?php if (!empty($bgArr)): ?>

                        <?php foreach ($bgArr as $i => $img): ?>

                            <img class="bg-slide <?= $i === 0 ? 'active' : '' ?>"
                                src="../User/<?= htmlspecialchars(str_replace('../User/', '', $img)) ?>">

                        <?php endforeach; ?>

                    <?php else: ?>

                        <!-- Display fallback image if no background exists -->
                        <img class="bg-slide active" src="../User/uploads/default.png">

                    <?php endif; ?>
                </div>

                <!-- Display homepage overlay content -->
                <div class="home-overlay">

                    <!-- Display homepage branding -->
                    <h1>COMODO</h1>

                    <!-- Display homepage tagline -->
                    <h5>Your Comfortable Stay Starts Here</h5>

                    <!-- Allow guests to search room availability -->
                    <div class="search-container">

                        <form method="GET" action="rooms_availability.php" style="display:contents;">

                            <!-- Allow users to select a branch -->
                            <select name="room" required>
                                <option value="">Select Location</option>
                                <option value="Casa FAM Appartelle">Casa FAM Appartelle</option>
                                <option value="V.F Riton Appartelle">V.F Riton Appartelle</option>
                            </select>

                            <!-- Allow users to choose check-in and check-out dates -->
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                <input type="date" name="CIdate" required>
                                <input type="date" name="COdate" required>
                            </div>

                            <!-- Allow users to enter number of guests -->
                            <div class="guest-input">
                                <input type="number" name="guests" min="1" max="7" value="1" required>
                                <span>Guest(s)</span>
                            </div>

                            <!-- Submit room availability search -->
                            <button type="submit">Check Availability</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ROOMS -->
            <div class="rooms-section">

                <!-- Display booking reminder -->
                <h3>Book directly with us to enjoy a guaranteed full 24-hour stay!</h3>

                <!-- Display short room section introduction -->
                <p><i>All our generously appointed guest rooms are designed to provide exceptional comfort, privacy, and a relaxing stay.</i></p>

                <br><br>

                <!-- Display the room card slider -->
                <div class="slider-wrapper">

                    <!-- Navigate room cards to the left -->
                    <button onclick="slide(-1)">&#8249;</button>

                    <div class="slider-overflow">

                        <!-- Display room cards horizontally -->
                        <div class="slider-track" id="sliderTrack">

                            <?php

                                // Retrieve room categories grouped by location
                                $roomTypes = mysqli_query($conn, "
                                    SELECT DISTINCT RoomType, Location, MIN(RoomID) as FirstRoomID, RoomFeatures
                                    FROM rooms
                                    GROUP BY RoomType, Location
                                    ORDER BY Location, RoomType
                                    LIMIT 7
                                ");

                                while ($type = mysqli_fetch_assoc($roomTypes)):

                                    // Prepare room type and location values for queries
                                    $typeEsc = mysqli_real_escape_string($conn, $type['RoomType']);
                                    $locEsc  = mysqli_real_escape_string($conn, $type['Location']);

                                    // Retrieve room IDs under the same room type
                                    $roomsOfType = mysqli_query($conn, "SELECT RoomID FROM rooms WHERE RoomType = '$typeEsc' AND Location = '$locEsc'");

                                    $typePics = [];

                                    while ($r = mysqli_fetch_assoc($roomsOfType)) {

                                        // Retrieve the first image assigned to each room
                                        $pic = mysqli_fetch_assoc(mysqli_query($conn,
                                            "SELECT ImagePath FROM room_images WHERE RoomID = '{$r['RoomID']}' ORDER BY ImageOrder ASC LIMIT 1"
                                        ));

                                        if ($pic) $typePics[] = $pic['ImagePath'];
                                    }

                                    // Display placeholder image if no room images exist
                                    if (empty($typePics)) $typePics[] = 'placeholder.jpg';

                                    // Generate a unique slideshow ID for each room card
                                    $cardID    = 'card_' . preg_replace('/[^a-zA-Z0-9]/', '_', $type['RoomType'] . $type['Location']);

                                    // Retrieve room details for modal display
                                    $firstRoom = mysqli_fetch_assoc(mysqli_query($conn,
                                        "SELECT BedConfiguration, MaxOccupancies, NumberBathrooms, RoomAmenities, BaseRate
                                         FROM rooms WHERE RoomType = '$typeEsc' AND Location = '$locEsc' LIMIT 1"
                                    ));

                                    // Prepare modal data for Learn More button
                                    $modalData = json_encode([
                                        'roomType'     => $type['RoomType'],
                                        'roomNumber'   => $type['Location'],
                                        'bedConfig'    => $firstRoom['BedConfiguration']  ?? '',
                                        'maxOccupancy' => $firstRoom['MaxOccupancies']    ?? '',
                                        'bathrooms'    => $firstRoom['NumberBathrooms']   ?? '',
                                        'amenities'    => $firstRoom['RoomAmenities']     ?? '',
                                        'features'     => array_values(array_filter(array_map('trim', explode(',', $type['RoomFeatures'])))),
                                        'rate'         => $firstRoom['BaseRate']          ?? '',
                                        'description'  => '',
                                        'images'       => array_map(fn($p) => '../User/' . str_replace('../User/', '', $p), $typePics),
                                    ]);
                            ?>

                                <!-- Display each room card -->
                                <div class="card">

                                    <!-- Display room slideshow -->
                                    <div class="card-slideshow" id="<?= $cardID ?>">

                                        <?php foreach ($typePics as $ci => $pic): ?>

                                            <img class="card-slide <?= $ci == 0 ? 'active' : '' ?>"
                                                 src="<?= htmlspecialchars($pic) ?>"
                                                 alt="<?= htmlspecialchars($type['RoomType']) ?>">

                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Display room type -->
                                    <h3><?= htmlspecialchars($type['RoomType']) ?></h3>

                                    <!-- Display shortened room features -->
                                    <p><?= htmlspecialchars(substr($type['RoomFeatures'], 0, 80)) ?>...</p>

                                    <!-- Open room details modal -->
                                    <button class="btn-details"
                                            data-modal="<?= htmlspecialchars($modalData, ENT_QUOTES, 'UTF-8') ?>"
                                            onclick="handleLearnMore(this)">
                                        Learn More
                                    </button>
                                </div>

                            <?php endwhile; ?>
                        </div>
                    </div>

                    <!-- Navigate room cards to the right -->
                    <button onclick="slide(1)">&#8250;</button>
                </div>
            </div>

            <!-- WELCOME -->
            <div class="welcome-section">

                <!-- Display homepage welcome message -->
                <h1>Welcome to Comodo</h1><br>

                <!-- Introduce the business and branches -->
                <p>
                    Welcome to Comodo — your home away from home, offering comfort, convenience, and flexibility for every kind of stay.<br>
                    With two branches, Casa Fam and V.F. Riton, both managed under one vision, we are committed to providing a warm and welcoming experience for all our guests.
                    Designed for families, groups, and travelers alike, our spaces are thoughtfully arranged to give you the freedom to relax, unwind, and enjoy your stay at your own pace.<br>
                    Whether you're here for a short visit or an extended stay, we ensure a comfortable, private, and hassle-free experience.
                    At our apartelle, every stay is made personal — so you can truly feel at home.
                </p><br><br>

                <!-- Redirect users to the About Us page -->
                <button onclick="window.location.href='about_us.php';">Learn More</button>
            </div>

            <!-- SIGNATURE -->
            <?php

                // Store signature section images
                $sigArr   = [];

                // Retrieve signature images from the database
                $sigQuery = mysqli_query($conn, "
                    SELECT ImagePath FROM location_images
                    WHERE Location = 'Signature Elements'
                    ORDER BY SortOrder ASC
                ");

                if ($sigQuery) {

                    while ($row = mysqli_fetch_assoc($sigQuery)) {

                        $sigArr[] = $row['ImagePath'];
                    }
                }
            ?>

            <!-- Display signature features section -->
            <div class="signature-section">

                <div class="signature-split">

                    <!-- Display rotating signature images -->
                    <div class="signature-left">

                        <div class="sig-img-slider">

                            <?php if (!empty($sigArr)): ?>

                                <?php foreach ($sigArr as $i => $img): ?>

                                    <img class="sig-img-slide <?= $i === 0 ? 'active' : '' ?>"
                                        src="../User/<?= htmlspecialchars(str_replace('../User/', '', $img)) ?>"
                                        onerror="this.style.display='none'">

                                <?php endforeach; ?>

                            <?php else: ?>

                                <!-- Display default image if no signature image exists -->
                                <img class="sig-img-slide active" src="../User/uploads/default.png">

                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Display property highlights -->
                    <div class="signature-right">

                        <h2>6 Signature Elements Define Our Property</h2>

                        <ul class="sig-list">
                            <li><span class="sig-check">✓</span> Comfortable and Functional Room Design</li>
                            <li><span class="sig-check">✓</span> Two Convenient Branch Locations</li>
                            <li><span class="sig-check">✓</span> Essential Amenities Included</li>
                            <li><span class="sig-check">✓</span> Reliable Wi-Fi Connectivity</li>
                            <li><span class="sig-check">✓</span> Shared Working and Lounge Spaces</li>
                            <li><span class="sig-check">✓</span> Easy Self-Service Check-In &amp; Check-Out</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Load the shared website footer -->
    <?php include 'footer.php'; ?>

    <script>

        // ROOM SLIDER

        // Track the current room card position
        var current = 0;

        // Move the room card slider left or right
        var slide = function(dir) {

            let cards    = document.querySelectorAll('.card');

            let max      = cards.length - 3;

            current      = Math.max(0, Math.min(current + dir, max));

            let cardWidth = cards[0].offsetWidth + 10;

            document.getElementById('sliderTrack').style.transform = 'translateX(-' + (current * cardWidth) + 'px)';
        }

        // MODAL OPEN

        // Open the room modal and populate its contents
        function handleLearnMore(btn) {

            var data   = JSON.parse(btn.getAttribute('data-modal'));

            var slider = document.getElementById('rmSlider');

            var dots   = document.getElementById('rmDots');

            // Remove existing modal slides before loading new ones
            slider.querySelectorAll('.rm-modal-slide').forEach(s => s.remove());

            dots.innerHTML = '';

            // Generate modal slides dynamically
            (data.images || []).forEach(function(src, idx) {

                var img       = document.createElement('img');

                img.className = 'rm-modal-slide' + (idx === 0 ? ' active' : '');

                img.src       = src;

                img.alt       = data.roomType || '';

                slider.insertBefore(img, document.getElementById('rmPrev'));

                // Generate slide indicators
                var dot       = document.createElement('div');

                dot.className = 'rm-dot' + (idx === 0 ? ' active' : '');

                dot.onclick   = (function(i){ return function(){ rmGoTo(i); }; })(idx);

                dots.appendChild(dot);
            });

            // Display room details inside the modal
            document.getElementById('rmType').textContent   = data.roomType   || '';

            document.getElementById('rmNumber').textContent = data.roomNumber  || '';

            document.getElementById('rmDesc').textContent   = data.description || '';

            // Display room metadata information
            var metaHTML = '';

            if (data.bedConfig)    metaHTML += '<span><i class="fa-solid fa-bed"></i>'        + data.bedConfig + '</span>';

            if (data.maxOccupancy) metaHTML += '<span><i class="fa-solid fa-user-group"></i>Up to ' + data.maxOccupancy + ' guests</span>';

            if (data.bathrooms)    metaHTML += '<span><i class="fa-solid fa-bath"></i>'       + data.bathrooms + ' bathroom' + (data.bathrooms > 1 ? 's' : '') + '</span>';

            document.getElementById('rmMeta').innerHTML = metaHTML;

            // Display room features
            var featuresEl      = document.getElementById('rmFeatures');

            featuresEl.textContent = Array.isArray(data.features) ? data.features.join(', ') : (data.features || '');

            // Display room amenities
            document.getElementById('rmAmenities').textContent = data.amenities || '';

            // Display room rate if available
            var rateBox = document.getElementById('rmRateBox');

            if (data.rate) {

                document.getElementById('rmRate').textContent = '₱' + Number(data.rate).toLocaleString();

                rateBox.style.display = '';

            } else {

                rateBox.style.display = 'none';
            }

            // Open the room modal
            document.getElementById('rmOverlay').classList.add('open');

            document.body.style.overflow = 'hidden';
        }

        // MODAL SLIDER

        // Track the current modal image index
        var _rmCurrent = 0;

        // Change the displayed modal image
        function rmGoTo(idx) {

            var slides = document.querySelectorAll('.rm-modal-slide');

            var dots   = document.querySelectorAll('.rm-dot');

            if (!slides.length) return;

            slides[_rmCurrent].classList.remove('active');

            if (dots[_rmCurrent]) dots[_rmCurrent].classList.remove('active');

            _rmCurrent = (idx + slides.length) % slides.length;

            slides[_rmCurrent].classList.add('active');

            if (dots[_rmCurrent]) dots[_rmCurrent].classList.add('active');
        }

        // Close the room modal
        function closeModal() {

            document.getElementById('rmOverlay').classList.remove('open');

            document.body.style.overflow = '';
        }

        // Enable previous slide button
        document.getElementById('rmPrev').addEventListener('click', () => rmGoTo(_rmCurrent - 1));

        // Enable next slide button
        document.getElementById('rmNext').addEventListener('click', () => rmGoTo(_rmCurrent + 1));

        // Enable modal close button
        document.getElementById('rmClose').addEventListener('click', closeModal);

        // Close modal when clicking outside the content
        document.getElementById('rmOverlay').addEventListener('click', e => { if (e.target === e.currentTarget) closeModal(); });

        // Close modal when pressing Escape key
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    </script>

    <script>

        // SIGNATURE SLIDER

        // Rotate signature images automatically
        document.addEventListener("DOMContentLoaded", function() {

            var sigSlides = document.querySelectorAll('.sig-img-slide');

            var sigIdx    = 0;

            if (sigSlides.length <= 1) return;

            setInterval(function() {

                sigSlides[sigIdx].classList.remove('active');

                sigIdx = (sigIdx + 1) % sigSlides.length;

                sigSlides[sigIdx].classList.add('active');

            }, 4000);
        });

    </script>

    <script>
        // DATE VALIDATION
        const ciDate = document.querySelector('input[name="CIdate"]');
        const coDate = document.querySelector('input[name="COdate"]');

        // Set minimum check-in to today
        const today = new Date().toISOString().split('T')[0];
        ciDate.min = today;

        // When check-in changes, set check-out minimum to day after check-in
        ciDate.addEventListener('change', function() {
            const nextDay = new Date(this.value);
            nextDay.setDate(nextDay.getDate() + 1);
            const nextDayStr = nextDay.toISOString().split('T')[0];
            coDate.min = nextDayStr;
            // Reset check-out if it's not at least one day after check-in
            if (coDate.value && coDate.value <= this.value) {
                coDate.value = nextDayStr;
            }
        });

        // Prevent selecting check-out before check-in is chosen
        coDate.addEventListener('focus', function() {
            if (!ciDate.value) {
                alert('Please select a check-in date first.');
                ciDate.focus();
            }
        });
    </script>

    <!-- Enable homepage background functions -->
    <script src="background_functions.js"></script>

</body>
</html>
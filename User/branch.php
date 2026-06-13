<?php
session_start();
require_once '../connection.php';

$branchSlug = $_GET['branch'] ?? 'casa-fam';

if ($branchSlug === 'vf-riton') {
    $branchName     = 'V.F Riton Appartelle';
    $branchTitle    = 'V.F. Riton';
    $branchSubtitle = 'Quiet comfort in every corner.';
    $branchAddress  = 'V.F. Riton St., Laoag City, Ilocos Norte';
    $roomPrefix     = 'VF-%';
    $selectValue    = 'V.F Riton Appartelle';
    $amenities = [
        ['icon' => 'fa-wifi',           'label' => 'Free Wi-Fi'],
        ['icon' => 'fa-wind',           'label' => 'Air Conditioning'],
        ['icon' => 'fa-tv',             'label' => 'Cable TV'],
        ['icon' => 'fa-shower',         'label' => 'Private Bathroom'],
        ['icon' => 'fa-couch',          'label' => 'Lounge Area'],
        ['icon' => 'fa-box',            'label' => 'Storage Closet'],
        ['icon' => 'fa-plug',           'label' => 'Power Outlets'],
        ['icon' => 'fa-shield-halved',  'label' => 'Secure Premises'],
    ];
} else {
    $branchSlug     = 'casa-fam';
    $branchName     = 'Casa FAM Appartelle';
    $branchTitle    = 'Casa FAM';
    $branchSubtitle = 'Feel the warmth of home.';
    $branchAddress  = 'Casa FAM, Laoag City, Ilocos Norte';
    $roomPrefix     = 'CF-%';
    $selectValue    = 'Casa FAM Appartelle';
    $amenities = [
        ['icon' => 'fa-wifi',           'label' => 'Free Wi-Fi'],
        ['icon' => 'fa-wind',           'label' => 'Air Conditioning'],
        ['icon' => 'fa-tv',             'label' => 'Cable TV'],
        ['icon' => 'fa-shower',         'label' => 'Private Bathroom'],
        ['icon' => 'fa-utensils',       'label' => 'Kitchen Access'],
        ['icon' => 'fa-couch',          'label' => 'Lounge Area'],
        ['icon' => 'fa-plug',           'label' => 'Power Outlets'],
        ['icon' => 'fa-shield-halved',  'label' => 'Secure Premises'],
    ];
}

$prefixEsc  = mysqli_real_escape_string($conn, $roomPrefix);
$heroImages = [];
$heroQuery  = mysqli_query($conn, "
    SELECT ri.ImagePath
    FROM room_images ri
    JOIN rooms r ON r.RoomID = ri.RoomID
    WHERE r.RoomID LIKE '$prefixEsc'
    AND ri.ImageOrder = 1
    ORDER BY ri.RoomID ASC
");
while ($row = mysqli_fetch_assoc($heroQuery)) {
    $heroImages[] = $row['ImagePath'];
}
if (empty($heroImages)) $heroImages[] = 'uploads/default.png';

$locNameEsc = mysqli_real_escape_string($conn, $branchName);
$roomTypes = mysqli_query($conn, "
    SELECT r.RoomType, r.Location, r.RoomID as FirstRoomID, r.RoomFeatures
    FROM rooms r
    INNER JOIN (
        SELECT RoomType, MIN(RoomID) as MinRoomID
        FROM rooms
        WHERE Location = '$locNameEsc'
        GROUP BY RoomType
    ) sub ON r.RoomID = sub.MinRoomID
    ORDER BY r.RoomType
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title><?= htmlspecialchars($branchTitle) ?> — Comodo</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="branch.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Arima:wght@100..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ysabeau+SC:wght@1..1000&display=swap" rel="stylesheet">

    <style>
    /* ── MODAL STYLES ── */
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
    .room-modal-overlay.open { display: flex; }
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
    @keyframes rmModalIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .rm-modal-close {
        position: absolute;
        top: 10px;
        right: 14px;
        background: none;
        border: none;
        font-size: 2rem;
        color: #fff;
        cursor: pointer;
        z-index: 10;
        line-height: 1;
    }
    .rm-modal-close:hover { color: #ffaaaa; }
    .rm-modal-slider {
        position: relative;
        height: 320px;
        overflow: hidden;
        background: #eee;
        border-radius: 14px 14px 0 0;
    }
    .rm-modal-slide {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .rm-modal-slide.active { opacity: 1; }
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
    .rm-slide-btn:hover { color: #ccc; }
    .rm-slide-btn.prev { left: 0; }
    .rm-slide-btn.next { right: 0; }
    .rm-slide-dots {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 6px;
        z-index: 3;
    }
    .rm-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: background 0.2s;
    }
    .rm-dot.active { background: #fff; }
    .rm-modal-info {
        padding: 24px 28px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .rm-info-type { font-size: 1.4rem; font-weight: 700; color: #0f0046; }
    .rm-info-number { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.1em; color: #888; margin-top: -6px; }
    .rm-divider { border: none; border-top: 1px solid #eee; }
    .rm-meta-row { display: flex; flex-wrap: wrap; gap: 8px 16px; font-size: 0.84rem; color: #333; }
    .rm-meta-row span { display: flex; align-items: center; gap: 6px; }
    .rm-meta-row i { color: #0f0046; }
    .rm-desc { font-size: 0.85rem; line-height: 1.72; color: #555; }
    .rm-section-title { font-size: 0.75rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #0f0046; margin-bottom: 4px; }
    .rm-plain-text { font-size: 0.83rem; color: #555; line-height: 1.6; margin: 0; }
    .rm-rate-box { background: #f4f4f8; border: 1px solid #ddd; border-radius: 8px; padding: 14px 16px; }
    .rm-rate-label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.08em; color: #888; }
    .rm-rate-val { font-size: 1.5rem; font-weight: 700; color: #0f0046; }
    .rm-rate-note { font-size: 0.72rem; color: #aaa; }
    @media (max-width: 600px) { .rm-modal-slider { height: 220px; } }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<!-- ── MODAL HTML ── -->
<div class="room-modal-overlay" id="rmOverlay">
    <div class="room-modal" id="rmModal">
        <button class="rm-modal-close" id="rmClose" title="Close">&times;</button>
        <div class="rm-modal-slider" id="rmSlider">
            <button class="rm-slide-btn prev" id="rmPrev">&#10094;</button>
            <button class="rm-slide-btn next" id="rmNext">&#10095;</button>
            <div class="rm-slide-dots" id="rmDots"></div>
        </div>
        <div class="rm-modal-info">
            <div>
                <div class="rm-info-type"   id="rmType"></div>
                <div class="rm-info-number" id="rmNumber"></div>
            </div>
            <hr class="rm-divider">
            <div class="rm-meta-row" id="rmMeta"></div>
            <p class="rm-desc" id="rmDesc"></p>
            <div>
                <div class="rm-section-title">Room Features</div>
                <p class="rm-plain-text" id="rmFeatures"></p>
            </div>
            <div>
                <div class="rm-section-title">Room Amenities</div>
                <p class="rm-plain-text" id="rmAmenities"></p>
            </div>
            <div class="rm-rate-box" id="rmRateBox" style="display:none">
                <div class="rm-rate-label">Rate per night</div>
                <div class="rm-rate-val" id="rmRate"></div>
                <div class="rm-rate-note">Rates are subject to availability</div>
            </div>
        </div>
    </div>
</div>

<main>

    <div class="branch-hero">
        <div class="branch-bg-slider">
            <?php foreach ($heroImages as $i => $img): ?>
                <img class="branch-bg-slide <?= $i === 0 ? 'active' : '' ?>"
                     src="../User/<?= htmlspecialchars(str_replace('../User/', '', $img)) ?>"
                     alt="<?= htmlspecialchars($branchTitle) ?>">
            <?php endforeach; ?>
        </div>
        <div class="branch-hero-overlay"></div>
        <div class="branch-hero-text">
            <p class="branch-eyebrow">Comodo Appartelle</p>
            <h1><?= htmlspecialchars($branchTitle) ?></h1>
            <p class="branch-tagline"><?= htmlspecialchars($branchSubtitle) ?></p>
            <p class="branch-address"><i class="fa fa-location-dot"></i> <?= htmlspecialchars($branchAddress) ?></p>
        </div>
    </div>

    <div class="branch-book-bar">
        <form method="GET" action="rooms_availability.php" class="branch-book-form">
            <div class="bbook-field bbook-location">
                <label><i class="fa fa-map-pin"></i> Location</label>
                <select name="room" required>
                    <option value="<?= htmlspecialchars($selectValue) ?>" selected>
                        <?= htmlspecialchars($branchTitle) ?>
                    </option>
                    <?php if ($selectValue === 'Casa FAM Appartelle'): ?>
                        <option value="V.F Riton Appartelle">V.F. Riton</option>
                    <?php else: ?>
                        <option value="Casa FAM Appartelle">Casa FAM</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="bbook-field">
                <label><i class="fa fa-calendar-check"></i> Check-In</label>
                <input type="date" name="CIdate" required>
            </div>
            <div class="bbook-field">
                <label><i class="fa fa-calendar-xmark"></i> Check-Out</label>
                <input type="date" name="COdate" required>
            </div>
            <div class="bbook-field bbook-guests">
                <label><i class="fa fa-user-group"></i> Guests</label>
                <input type="number" name="guests" min="1" max="7" value="1" required>
            </div>
            <button type="submit" class="bbook-btn">
                <i class="fa fa-search"></i> Check Availability
            </button>
        </form>
    </div>

    <section class="branch-amenities">
        <h2>Branch Amenities</h2>
        <p class="branch-section-sub">Everything you need for a comfortable stay.</p>
        <div class="amenities-grid">
            <?php foreach ($amenities as $a): ?>
                <div class="amenity-card">
                    <i class="fa <?= $a['icon'] ?>"></i>
                    <span><?= htmlspecialchars($a['label']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="branch-rooms">
        <h2>Available Room Types</h2>
        <p class="branch-section-sub">All rooms at <?= htmlspecialchars($branchTitle) ?> — pick the one that fits you best.</p>

        <div class="slider-wrapper">
            <button class="slide-btn" onclick="branchSlide(-1)">&#8249;</button>
            <div class="slider-overflow">
                <div class="slider-track" id="branchSliderTrack">
                    <?php
                    while ($type = mysqli_fetch_assoc($roomTypes)):
                        $typeEsc = mysqli_real_escape_string($conn, $type['RoomType']);
                        $locEsc  = mysqli_real_escape_string($conn, $type['Location']);

                        $roomsOfType = mysqli_query($conn, "SELECT RoomID FROM rooms WHERE RoomType = '$typeEsc' AND Location = '$locEsc'");
                        $typePics = [];
                        while ($r = mysqli_fetch_assoc($roomsOfType)) {
                            $pic = mysqli_fetch_assoc(mysqli_query($conn,
                                "SELECT ImagePath FROM room_images WHERE RoomID = '{$r['RoomID']}' ORDER BY ImageOrder ASC LIMIT 1"
                            ));
                            if ($pic) $typePics[] = $pic['ImagePath'];
                        }
                        if (empty($typePics)) $typePics[] = 'uploads/default.png';

                        $cardID = 'bcard_' . preg_replace('/[^a-zA-Z0-9]/', '_', $type['RoomType'] . $type['Location']);
                        $firstRoom = mysqli_fetch_assoc(mysqli_query($conn,
                            "SELECT BedConfiguration, MaxOccupancies, NumberBathrooms, RoomAmenities, BaseRate
                            FROM rooms
                            WHERE RoomType = '$typeEsc' AND Location = '$locEsc'
                            LIMIT 1"
                        ));
                        $modalData = json_encode([
                            'roomType'     => $type['RoomType'],
                            'roomNumber'   => $type['Location'],  // or $branchTitle for branch.php
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
                    <div class="card">
                        <div class="card-slideshow" id="<?= $cardID ?>">
                            <?php foreach ($typePics as $ci => $pic): ?>
                                <img class="card-slide <?= $ci === 0 ? 'active' : '' ?>"
                                     src="../User/<?= htmlspecialchars(str_replace('../User/', '', $pic)) ?>"
                                     alt="<?= htmlspecialchars($type['RoomType']) ?>">
                            <?php endforeach; ?>
                        </div>
                        <h3><?= htmlspecialchars($type['RoomType']) ?></h3>
                        <p><?= htmlspecialchars(substr($type['RoomFeatures'], 0, 80)) ?>...</p>
                        <button class="btn-details"
                                data-modal="<?= htmlspecialchars($modalData, ENT_QUOTES, 'UTF-8') ?>"
                                onclick="handleLearnMore(this)">
                            Learn More
                        </button>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <button class="slide-btn" onclick="branchSlide(1)">&#8250;</button>
        </div>
    </section>

</main>

<?php include 'footer.php'; ?>

<script>
    // LEARN MORE — open modal
    function handleLearnMore(btn) {
        var raw  = btn.getAttribute('data-modal');
        var data = JSON.parse(raw);

        var slider = document.getElementById('rmSlider');
        var dots   = document.getElementById('rmDots');

        // clear old slides
        slider.querySelectorAll('.rm-modal-slide').forEach(function(s){ s.remove(); });
        dots.innerHTML = '';

        var images = data.images || [];
        _rmCurrent = 0;

        images.forEach(function(src, idx) {
            var img = document.createElement('img');
            img.className = 'rm-modal-slide' + (idx === 0 ? ' active' : '');
            img.src = src;
            img.alt = data.roomType || '';
            slider.insertBefore(img, document.getElementById('rmPrev'));

            var dot = document.createElement('div');
            dot.className = 'rm-dot' + (idx === 0 ? ' active' : '');
            dot.onclick = (function(i){ return function(){ rmGoTo(i); }; })(idx);
            dots.appendChild(dot);
        });

        document.getElementById('rmType').textContent   = data.roomType   || '';
        document.getElementById('rmNumber').textContent = data.roomNumber  || '';
        document.getElementById('rmDesc').textContent   = data.description || '';

        var metaHTML = '';
        if (data.bedConfig)    metaHTML += '<span><i class="fa-solid fa-bed"></i>' + data.bedConfig + '</span>';
        if (data.maxOccupancy) metaHTML += '<span><i class="fa-solid fa-user-group"></i>Up to ' + data.maxOccupancy + ' guests</span>';
        if (data.bathrooms)    metaHTML += '<span><i class="fa-solid fa-bath"></i>' + data.bathrooms + ' bathroom' + (data.bathrooms > 1 ? 's' : '') + '</span>';
        document.getElementById('rmMeta').innerHTML = metaHTML;

        var featuresEl = document.getElementById('rmFeatures');
        featuresEl.textContent = Array.isArray(data.features) ? data.features.join(', ') : (data.features || '');

        document.getElementById('rmAmenities').textContent = data.amenities || '';

        var rateBox = document.getElementById('rmRateBox');
        if (data.rate) {
            document.getElementById('rmRate').textContent = '₱' + Number(data.rate).toLocaleString();
            rateBox.style.display = '';
        } else {
            rateBox.style.display = 'none';
        }

        document.getElementById('rmOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    // MODAL SLIDER
    var _rmCurrent = 0;
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

    document.getElementById('rmPrev').addEventListener('click', function(){ rmGoTo(_rmCurrent - 1); });
    document.getElementById('rmNext').addEventListener('click', function(){ rmGoTo(_rmCurrent + 1); });
    document.getElementById('rmClose').addEventListener('click', function(){ closeModal(); });
    document.getElementById('rmOverlay').addEventListener('click', function(e){
        if (e.target === this) closeModal();
    });
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') closeModal();
    });

    function closeModal() {
        document.getElementById('rmOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }

    var branchCurrent = 0;
    function branchSlide(dir) {
        var cards = document.querySelectorAll('#branchSliderTrack .card');
        var max = cards.length - 2;
        branchCurrent = Math.max(0, Math.min(branchCurrent + dir, max));
        var cardWidth = cards[0].offsetWidth + 20; // 20 = gap
        document.getElementById('branchSliderTrack').style.transform = 'translateX(-' + (branchCurrent * cardWidth) + 'px)';
    }
</script>

<script src="background_functions.js"></script>
</body>
</html>
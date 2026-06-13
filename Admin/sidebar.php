<div class="sidebar">
    <div class="logo">&#9671; COMODO Admin</div>

    <!-- MAIN MENU -->
    <div class="section">main</div>
    <a href="admin.php" <?= basename($_SERVER['PHP_SELF'])=='admin.php'?'class="active"':'' ?>>Home</a>
    <a href="reservations.php" <?= basename($_SERVER['PHP_SELF'])=='reservations.php'?'class="active"':'' ?>>Reservations</a>
    <a href="walkin.php" <?= basename($_SERVER['PHP_SELF'])=='walkin.php'?'class="active"':'' ?>>Walk-in</a>

    <!-- MANAGEMENT -->
    <div class="section">manage</div>
    <a href="rooms.php" <?= basename($_SERVER['PHP_SELF'])=='rooms.php'?'class="active"':'' ?>>Rooms</a>
    <a href="payments.php" <?= basename($_SERVER['PHP_SELF'])=='payments.php'?'class="active"':'' ?>>Payments</a>

    <!-- GALLERY -->
    <div class="section">gallery</div>
    <a href="homepage_gallery.php" <?= basename($_SERVER['PHP_SELF'])=='homepage_gallery.php'?'class="active"':'' ?>>Homepage Gallery</a>
    <a href="location_gallery.php" <?= basename($_SERVER['PHP_SELF'])=='location_gallery.php'?'class="active"':'' ?>>Location Gallery</a>
    <a href="rooms_gallery.php" <?= basename($_SERVER['PHP_SELF'])=='rooms_gallery.php'?'class="active"':'' ?>>Rooms Gallery</a>
    <a href="banks_gallery.php" <?= basename($_SERVER['PHP_SELF'])=='banks_gallery.php'?'class="active"':'' ?>>Banks Gallery</a>

    <!-- SYSTEM -->
    <div class="section">system</div>
    <a href="settings.php" <?= basename($_SERVER['PHP_SELF'])=='settings.php'?'class="active"':'' ?>>Settings</a>
    <a href="logout.php">Logout</a>
</div>
<?php
/* Load the website logo from the database */
$websiteLogo = mysqli_query($conn, "
    SELECT ImagePath
    FROM branch_images
    WHERE Location = 'website_logo'
    LIMIT 1
");

/* Store the logo result for display */
$logo = mysqli_fetch_assoc($websiteLogo);
?>

<!-- Configure Tailwind before loading the CDN and disable Preflight so existing CSS styles remain unchanged -->
<script>
    window.tailwind = window.tailwind || {};
    tailwind.config = { corePlugins: { preflight: false } };
</script>

<script src="https://cdn.tailwindcss.com"></script>

<nav class="hdr-nav">

    <!-- Create the navbar background layer -->
    <div class="hdr-bg"></div>

    <!-- Organize all navbar content -->
    <div class="hdr-content">

        <!-- Display the website logo -->
        <a href="homepage.php" class="hdr-logo-link">
            <div class="hdr-logo">

                <?php if ($logo): ?>
                    <img src="../User/<?= htmlspecialchars($logo['ImagePath']) ?>"
                        alt="COMODO HOMES Logo"
                        class="hdr-logo-img">
                <?php endif; ?>

                <div class="hr-comodo">COMODO</div>
            </div>
        </a>

        <!-- Display desktop navigation links -->
        <div class="hdr-links hidden md:flex">

            <div class="hdr-dropdown">
                <a href="#">Branches ⏷</a>

                <!-- Show the branch dropdown links -->
                <div class="hdr-dropdown-menu">
                    <a href="branch.php?branch=casa-fam">Casa Fam</a>
                    <a href="branch.php?branch=vf-riton">V.F. Riton</a>
                </div>
            </div>

            <a href="photo_gallery.php">Photo Gallery</a>
            <a href="about_us.php">About Us</a>
            <a href="faqs.php">FAQs</a>
            <a href="contact_us.php">Contact Us</a>
        </div>

        <!-- Allow guests to book directly -->
        <button class="hdr-book-btn" onclick="window.location.href='rooms_availability.php';">
            BOOK NOW
        </button>

        <!-- Display the hamburger menu on mobile -->
        <button
            id="hamburger-btn"
            onclick="toggleMobileMenu()"
            aria-label="Open navigation menu"
            aria-expanded="false"
            class="md:hidden flex flex-col justify-center items-center gap-[5px] rounded border-none"
            style="padding:8px; margin-left:10px; width:30px; height:30px; flex-shrink:0;">

            <span id="hbr-line1"
                class="block w-6 h-[2px] bg-white"
                style="transition: transform 0.3s ease, opacity 0.3s ease; transform-origin:center;"></span>

            <span id="hbr-line2"
                class="block w-6 h-[2px] bg-white"
                style="transition: opacity 0.3s ease;"></span>

            <span id="hbr-line3"
                class="block w-6 h-[2px] bg-white"
                style="transition: transform 0.3s ease; transform-origin:center;"></span>
        </button>
    </div>


    <!-- Display the mobile dropdown navigation -->
    <div
        id="mobile-menu"
        class="md:hidden hidden flex-col w-full"
        style="background:#021532; border-top:1px solid rgba(255,255,255,0.1);">

        <!-- Create the mobile branches accordion -->
        <div>

            <button
                onclick="toggleBranches()"
                class="w-full text-left px-6 py-4 text-white text-sm flex justify-between items-center"
                style="background:transparent; border:none; border-bottom:1px solid rgba(255,255,255,0.08); cursor:pointer; font-size:0.875rem;">

                Branches

                <span id="branch-arrow"
                    style="transition: transform 0.3s ease; display:inline-block;">⏷</span>
            </button>

            <!-- Display branch links inside the accordion -->
            <div id="branch-sub" class="hidden flex-col" style="background:rgba(255,255,255,0.05);">

                <a href="branch.php?branch=casa-fam"
                    class="block px-10 py-3 text-white text-sm"
                    style="border-bottom:1px solid rgba(255,255,255,0.06); text-decoration:none;">
                    Casa Fam
                </a>

                <a href="branch.php?branch=vf-riton"
                    class="block px-10 py-3 text-white text-sm"
                    style="border-bottom:1px solid rgba(255,255,255,0.06); text-decoration:none;">
                    V.F. Riton
                </a>
            </div>
        </div>

        <!-- Display mobile navigation links -->
        <a href="photo_gallery.php"
            class="block px-6 py-4 text-white text-sm"
            style="border-bottom:1px solid rgba(255,255,255,0.08); text-decoration:none;">
            Photo Gallery
        </a>

        <a href="about_us.php"
            class="block px-6 py-4 text-white text-sm"
            style="border-bottom:1px solid rgba(255,255,255,0.08); text-decoration:none;">
            About Us
        </a>

        <a href="faqs.php"
            class="block px-6 py-4 text-white text-sm"
            style="border-bottom:1px solid rgba(255,255,255,0.08); text-decoration:none;">
            FAQs
        </a>

        <a href="contact_us.php"
            class="block px-6 py-4 text-white text-sm"
            style="text-decoration:none;">
            Contact Us
        </a>
    </div>
</nav>

<script>

    /* Hide and reveal the navbar during scrolling */
    let lastScroll = 0;
    const navbar = document.querySelector(".hdr-nav");

    window.addEventListener("scroll", () => {

        let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

        if (currentScroll > lastScroll && currentScroll > 80) {

            navbar.classList.add("hide");

            /* Close the mobile menu while scrolling */
            if (menuOpen) toggleMobileMenu();

        } else {
            navbar.classList.remove("hide");
        }

        lastScroll = Math.max(0, currentScroll);

    }, { passive: true });


    /* Toggle the mobile navigation menu */
    let menuOpen = false;

    function toggleMobileMenu() {

        menuOpen = !menuOpen;

        const menu = document.getElementById('mobile-menu');
        const btn  = document.getElementById('hamburger-btn');
        const l1   = document.getElementById('hbr-line1');
        const l2   = document.getElementById('hbr-line2');
        const l3   = document.getElementById('hbr-line3');

        btn.setAttribute('aria-expanded', menuOpen);

        if (menuOpen) {

            menu.classList.remove('hidden');
            menu.classList.add('flex');

            l1.style.transform = 'translateY(7px) rotate(45deg)';
            l2.style.opacity   = '0';
            l3.style.transform = 'translateY(-7px) rotate(-45deg)';

        } else {

            menu.classList.add('hidden');
            menu.classList.remove('flex');

            l1.style.transform = '';
            l2.style.opacity   = '1';
            l3.style.transform = '';

            /* Close the branches accordion together with the menu */
            closeBranches();
        }
    }


    /* Toggle the mobile branches accordion */
    function toggleBranches() {
        const sub    = document.getElementById('branch-sub');
        const arrow  = document.getElementById('branch-arrow');
        const isOpen = !sub.classList.contains('hidden');

        if (isOpen) {
            closeBranches();
        } else {
            sub.classList.remove('hidden');
            sub.classList.add('flex');
            arrow.style.transform = 'rotate(180deg)';
        }
    }


    /* Close the mobile branches accordion */
    function closeBranches() {
        const sub   = document.getElementById('branch-sub');
        const arrow = document.getElementById('branch-arrow');

        sub.classList.add('hidden');
        sub.classList.remove('flex');
        arrow.style.transform = '';
    }


    /* Close the mobile menu when clicking outside the navbar */
    document.addEventListener('click', function(e) {
        const nav = document.querySelector('.hdr-nav');
        
        if (menuOpen && !nav.contains(e.target)) {
            toggleMobileMenu();
        }
    });
</script>
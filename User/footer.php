<!-- Import logo data used in the footer -->
<?php 
include 'logos.php';
?>

<!-- Load the footer design and layout -->
<link rel="stylesheet" href="footer.css">

<footer>

    <!-- Organize footer content into sections -->
    <div class="ft-main">

        <!-- Display branding and branch logos -->
        <div class="ft-col">
            <div class="comodo">COMODO</div>

            <!-- Show the logos of both branches -->
            <div class="ft-logo">

            <img src="../User/<?= htmlspecialchars($casaLogo['ImagePath'] ?? 'uploads/default.png') ?>" 
                alt="Casa Fam Logo">

            <img src="../User/<?= htmlspecialchars($vfLogo['ImagePath'] ?? 'uploads/default.png') ?>" 
                alt="VF Riton Logo">

            </div>

            <!-- Display branch names below the logos -->
            <div class="ft-branches">CASA FAM &bull; V.F. RITON</div>
        </div>

        <!-- Provide quick access to important pages -->
        <div class="ft-col ft-col-sites">
            <div class="ft-col-title">Site Navigation</div>

            <ul class="ft-nav">
                <li><a href="about_us.php">About Us</a></li>
                <li><a href="photo_gallery.php">Rooms</a></li>
                <li><a href="faqs.php">FAQs</a></li>
                <li><a href="contact_us.php">Contact Us</a></li>
            </ul>
        </div>

        <!-- Display contact details and social links -->
        <div class="ft-col ft-col-getintouch">
            <div class="ft-col-title">Get in Touch</div>

            <!-- Show branch addresses -->
            <div class="ft-contact-row">
                <i class="fa-solid fa-location-dot ft-icon"></i>
                <span class="ft-contact-text">
                    Casa Fam: Brgy. 48-B, Cabungaan South, Sitio 6, Laoag City
                </span>
            </div>

            <div class="ft-contact-row">
                <i class="fa-solid fa-location-dot ft-icon"></i>
                <span class="ft-contact-text">
                    V.F. Riton: Brgy. 6, Romero Street, Laoag City
                </span>
            </div>

            <!-- Display phone number -->
            <div class="ft-contact-row">
                <i class="fa-solid fa-phone ft-icon"></i>
                <span class="ft-contact-text">0943-593-44-80</span>
            </div>

            <!-- Display email address -->
            <div class="ft-contact-row">
                <i class="fa-solid fa-envelope ft-icon"></i>
                <span class="ft-contact-text">analiza.riton@yahoo.com</span>
            </div>

            <!-- Connect users to the official Facebook page -->
            <div class="ft-contact-row">
                <div class="ft-icon-circle">
                    <a href="https://www.facebook.com/v.f.riton.apartelle.2024" target="_blank" style="color:#0a0a0a; display:flex; align-items:center; justify-content:center;">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                </div>

                <span class="ft-contact-text">
                    <a href="https://www.facebook.com/v.f.riton.apartelle.2024" target="_blank" style="color:#aaa; text-decoration:none;">
                        Comodo Homes
                    </a>
                </span>
            </div>
        </div>
    </div>

    <!-- Display copyright and policy links -->
    <div class="ft-bottom">

        <span class="ft-copy">
            <span style="font-size: 30px; line-height: 1; vertical-align: middle; position: relative; top: 4px;">&copy;</span> 
                Copyrights 2020 Comodo. All Rights Reserved.
        </span>

        <!-- Allow users to open policy popups -->
        <div class="ft-bottom-links">
                <!-- <a href="#" onclick="openModal('terms')">Terms &amp; Conditions</a>
                <span class="ft-sep">|</span>
                <a href="#" onclick="openModal('privacy')">Privacy Policy</a> -->

                <a href="#" onclick="openFooterModal('terms')">Terms &amp; Conditions</a>
                <a href="#" onclick="openFooterModal('privacy')">Privacy Policy</a>
        <button class="ft-modal-close" onclick="closeFooterModal('terms')">&times;</button>
        <button class="ft-modal-close"  onclick="closeFooterModal('privacy')">&times;</button>
        </div>
    </div>

    <!-- Display terms and conditions in a modal -->
    <div id="termsModal" class="ft-modal">
        <div class="ft-modal-content">

            <!-- Allow users to close the modal -->
            <span class="ft-modal-close" onclick="closeModal('terms')">&times;</span>

            <h2>Terms &amp; Conditions</h2>
            <hr>

            <p>1. All rates are quoted in Philippine Peso (₱).</p>
            <p>2. Full payment is required to confirm your booking. Payment may be made through the following: (a) Cash upon check-in at the property (b) GCash (c) Bank Transfer at least 3 banking days before check-in.</p>
            <p>3. A valid government-issued ID with photo must be presented upon check-in.</p>
            <p>4. Standard check-in time is 2:00 PM and check-out time is 12:00 NN. Early check-in and late check-out are subject to availability and may incur additional charges.</p>
            <p>5. Extension of stay is subject to availability and must be coordinated with us in advance.</p>
            <p>6. Any changes or revisions to your booking must be communicated to our reservations team as soon as possible.</p>
            <p>7. Room rates may be adjusted based on prevailing rates in the event of a date change.</p>
            <p>8. If your expected time of arrival is beyond 6:00 PM, please inform us in advance. Comodot reserves the right to release unconfirmed rooms at 6:00 PM.</p>
            <p>9. <strong>Cancellation &amp; Refund Policy:</strong> Bookings are refundable within 24 hours from the time of transaction. Cancellations made after 24 hours will no longer be eligible for a refund.</p>
            <p>10. <strong>No Show:</strong> Guests who fail to arrive within twelve (12) hours of the scheduled check-in time will be considered a no-show and will be charged the full amount of the reservation.</p>
            <p>11. During peak seasons and holidays, full prepayment is required at least 7 days before check-in to guarantee your reservation. Comodo reserves the right to cancel unconfirmed reservations after the set deadline.</p>
            <p>12. The Hotel Keepers Act of the Philippines applies to all guests staying at Comodo properties.</p>
        </div>
    </div>

    <!-- Display the privacy policy in a modal -->
    <div id="privacyModal" class="ft-modal">
        <div class="ft-modal-content">

            <!-- Allow users to close the modal -->
            <span class="ft-modal-close" onclick="closeModal('privacy')">&times;</span>

            <h2>Privacy Policy</h2>
            <hr>

            <p>At Comodo, we are committed to protecting your privacy and ensuring that your personal information is handled with care and respect.</p>

            <p><strong>I. Information We Collect</strong><br>
            When you make a booking or contact us, we may collect the following: your full name, address, email address, contact number, and payment-related information such as proof of bank transfer or GCash receipts.</p>

            <p><strong>II. How We Use Your Information</strong><br>
            Your personal information is used to process and confirm your reservation, communicate booking updates and reminders, respond to your inquiries or concerns, and comply with legal and regulatory obligations.</p>

            <p><strong>III. Sharing of Information</strong><br>
            We do not sell or share your personal information with third parties for marketing purposes. Your data may only be shared with personnel directly involved in managing your reservation, or when required by law.</p>

            <p><strong>IV. Data Security</strong><br>
            We take reasonable steps to protect your personal information from unauthorized access, misuse, or disclosure. Access to your data is limited to authorized personnel only.</p>

            <p><strong>V. Data Retention</strong><br>
            We retain your personal information for as long as necessary to fulfill the purposes outlined in this policy, or as required by applicable law. Data that is no longer needed will be securely deleted or anonymized.</p>

            <p><strong>VI. Your Rights</strong><br>
            You have the right to access, update, or request the deletion of your personal information. To make a request, please contact us using the details below.</p>

            <p><strong>VII. Updates to This Policy</strong><br>
            We may update this Privacy Policy from time to time. Any changes will be reflected on this page and will take effect immediately.</p>

            <p><strong>VIII. Contact Us</strong><br>
            If you have any questions or concerns about this Privacy Policy, feel free to reach us:<br>
            <strong>Email:</strong> analiza.riton@yahoo.com<br>
            <strong>Phone:</strong> 0943-593-44-80<br>
            <strong>Casa Fam:</strong> Brgy. 48-B, Cabungaan South, Sitio 6, Laoag City<br>
            <strong>V.F. Riton:</strong> Brgy. 6, Romero Street, Laoag City</p>
        </div>
    </div>

    <!-- Enable footer interactions -->
    <script src="footer.js"></script>

</footer>
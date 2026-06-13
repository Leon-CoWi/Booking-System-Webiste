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
    <title>FAQs - United Transient</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="faqs.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="background.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Arima:wght@100..700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ysabeau+SC:wght@1..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=LINE+Seed+JP&display=swap" rel="stylesheet">
</head>
<body>

<!-- Pull in the shared navigation from header.php -->
<?php include 'header.php'; ?>

<main>

<!-- Hero section with background slider and page title -->
<div class="hero">

    <div class="bg-slider">

        <!-- Cycle through background images fetched in background.php -->
        <?php if (!empty($bgArr)): ?>

            <?php foreach ($bgArr as $i => $img): ?>

                <img class="bg-slide <?= $i === 0 ? 'active' : '' ?>"
                    src="../User/<?= htmlspecialchars(str_replace('../User/', '', $img)) ?>">

            <?php endforeach; ?>

        <!-- Fall back to the default image if no backgrounds are set -->
        <?php else: ?>

            <img class="bg-slide active"
                src="../User/uploads/default.png">

        <?php endif; ?>

    </div>

    <div class="title">FREQUENTLY ASK QUESTIONS</div>
</div>

<!-- Introductory message above the FAQ list -->
<div class="intro">
    <div class="intro-inner">
        <h2>WE AIM TO MAKE YOUR STAY WITH US <br> 
            AS COMFORTABLE AS POSSIBLE</h2>
        <p>We have compiled a list of some of the most frequently asked questions about staying at TITLE.<br>
            If you still do not find the answer to your query, feel free to get in touch, and we'd be more than willing to assist you.
        </p>
    </div>
</div>

<!-- All FAQ categories and their accordion items -->
<div class="faqs-section">
    <div class="faqs-container">

        <!-- Category: Check-In & Check-Out policies -->
        <div class="faq-category">
            <div class="faq-category-header">
                <i class="fa-solid fa-clock"></i>
                <h3>Check-In &amp; Check-Out</h3>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    What are your check-in and check-out times?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Check-in starts at <strong>1:00 PM</strong> and check-out is at <strong>11:00 AM</strong>.</p>
                        <ul>
                            <li>Early check-in is available subject to room availability and will incur a fee of <strong>₱100 per hour</strong> prior to 1:00 PM.</li>
                            <li>Guests are kindly requested to check out on or before 11:00 AM, as additional charges will apply <br> for late check-out.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Do you allow early check-in or late check-out?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Yes, both early check-in and late check-out are available <strong>subject to room availability</strong>. <br>
                            A fee of <strong>₱100 per hour</strong> will be charged.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Can I change my room type upon check-in?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Room type changes upon check-in are <strong>not allowed</strong>. <br>
                        Please ensure your preferred room type is selected during the booking process.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category: Reservations & Payments policies -->
        <div class="faq-category">
            <div class="faq-category-header">
                <i class="fa-solid fa-credit-card"></i>
                <h3>Reservations &amp; Payments</h3>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Is a reservation down payment required?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p><strong>Full payment is required</strong> to confirm your reservation. <br>
                            Please note that payments are <strong>non-refundable</strong> if cancellation is made less than one day before the scheduled check-in.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    What payment methods do you accept?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>We accept the following payment methods:</p>

                        <!-- Split into walk-in and online payment columns -->
                        <div class="payment-grid">
                            <div class="payment-col">
                                <p class="payment-label"><i class="fa-solid fa-store"></i> Walk-in Payments</p>
                                <ul>
                                    <li>Cash</li>
                                    <li>GCash</li>
                                    <li>Bank Transfer (Metrobank only)</li>
                                </ul>
                            </div>
                            <div class="payment-col">
                                <p class="payment-label"><i class="fa-solid fa-wifi"></i> Online Payments</p>
                                <ul>
                                    <li>GCash</li>
                                    <li>Bank Transfer (Metrobank only)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Can I cancel my booking?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Yes, cancellations are allowed. Guests may cancel within <strong>24 hours of booking</strong> 
                            for a full refund and provided the cancellation is made at least one day before the check-in date.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Can I book a room on behalf of someone else?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Yes, you may book a room on behalf of another guest.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Do you offer discounts?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Yes! Guests staying for at least<strong>one (1) week </strong> 
                        are eligible for a <strong> 10% discount</strong> on each week's total payment.
                        This discount is applied per week and does not accumulate across multiple weeks.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category: Guest and room occupancy rules -->
        <div class="faq-category">
            <div class="faq-category-header">
                <i class="fa-solid fa-users"></i>
                <h3>Guests &amp; Rooms</h3>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Do children stay for free?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Children <strong>8 years old and below</strong> stay free of charge.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    What is the fee for an extra guest?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>An additional fee of <strong>₱400 per extra guest</strong> applies. <br>
                            Please note that only <strong>one (1) extra guest</strong> is allowed per room once max occupancies is reached.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Are extra mattresses available?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Yes, extra mattresses are available upon request at a cost of <strong>₱400</strong>.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category: Available amenities and house rules -->
        <div class="faq-category">
            <div class="faq-category-header">
                <i class="fa-solid fa-concierge-bell"></i>
                <h3>Amenities &amp; Services</h3>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Can I request housekeeping services?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Yes, housekeeping services are available upon request from <strong>9:00 AM to 2:00 PM only</strong>.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Do you offer parking for guests?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">

                        <!-- Parking details differ per branch -->
                        <div class="branch-info">
                            <div class="branch-item">
                                <span class="branch-name"><i class="fa-solid fa-city"></i> V.F. Riton</span>
                                <p>Street parking is available in front of the building.</p>
                            </div>
                            <div class="branch-item">
                                <span class="branch-name"><i class="fa-solid fa-tree"></i> Casa Fam</span>
                                <p>Free parking is available for guests.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    What is your smoking policy?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Smoking is <strong>strictly prohibited inside the rooms</strong>. <br>
                            However, designated smoking areas are available within the property.</p>
                    </div>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Are there designated smoking areas available?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>Yes, designated smoking areas are provided within the property.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category: Contact info for guest support -->
        <div class="faq-category">
            <div class="faq-category-header">
                <i class="fa-solid fa-headset"></i>
                <h3>Contact &amp; Support</h3>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(this)">
                    Who should I contact if I need assistance during my stay?
                    <i class="fa-solid fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-content">
                        <p>For any assistance, please contact us at <a href="tel:+639435934480"><strong>0943-593-4480</strong></a>. We're happy to help!</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Bottom call-to-action nudging guests to reach out -->
<div class="faqs-cta">
    <div class="faqs-cta-inner">
        <i class="fa-solid fa-comment-dots"></i>
        <h2>Still have questions?</h2>
        <p>Feel free to reach out to us directly — we're always happy to help.</p>
    </div>
</div>

</main>

<!-- Pull in the shared footer from footer.php -->
<?php include 'footer.php'; ?>

<!-- Handle accordion open/close toggle per category -->
<script>
function toggleFaq(btn) {
    const item = btn.closest('.faq-item');
    const answer = item.querySelector('.faq-answer');
    const icon = btn.querySelector('.faq-icon');
    const isOpen = item.classList.contains('open');

    const category = btn.closest('.faq-category');
    category.querySelectorAll('.faq-item.open').forEach(openItem => {
        openItem.classList.remove('open');
        openItem.querySelector('.faq-answer').style.maxHeight = null;
        openItem.querySelector('.faq-icon').style.transform = 'rotate(0deg)';
    });

    if (!isOpen) {
        item.classList.add('open');
        answer.style.maxHeight = answer.scrollHeight + 'px';
        icon.style.transform = 'rotate(180deg)';
    }
}
</script>

<!-- Drive the background slider from background_functions.js -->
<script src="background_functions.js"></script>

</body>
</html>
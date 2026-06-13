document.querySelector('form').addEventListener('submit', function (e) {
    const firstName     = document.querySelector('[name="first_name"]').value.trim();
    const lastName      = document.querySelector('[name="last_name"]').value.trim();
    const contact       = document.querySelector('[name="contact_number"]').value.trim();
    const email         = document.querySelector('[name="email"]').value.trim();
    const paymentMethod = document.querySelector('[name="payment_method"]').value;

    if (!firstName || !lastName || !contact || !email || !paymentMethod) {
        e.preventDefault();
        alert('Please fill in all required fields before confirming.');
        return;
    }

    // All good — lock the button
    const btn       = document.querySelector('.submit-container button[type="submit"]');
    btn.disabled    = true;
    btn.textContent = 'Processing...';
});

function openModal(id) {
    document.getElementById(id).style.display = "block";
}

function closeModal(id) {
    document.getElementById(id).style.display = "none";
}

window.onclick = function(event) {
    if (event.target.classList.contains("modal")) {
        event.target.style.display = "none";
    }
}

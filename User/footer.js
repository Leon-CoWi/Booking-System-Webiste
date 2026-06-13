/* Open the selected footer modal when a footer link is clicked */
function openFooterModal(type) {
    document.getElementById(type + 'Modal').classList.add('active');
}

/* Close the selected footer modal */
function closeFooterModal(type) {
    document.getElementById(type + 'Modal').classList.remove('active');
}

/* Allow users to close the modal by clicking outside the modal content */
window.addEventListener('click', function(e) {
    ['terms', 'privacy'].forEach(type => {
        const modal = document.getElementById(type + 'Modal');
        if (e.target === modal) closeModal(type);
    });
});

/* Close any open footer modal when the Escape key is pressed */
window.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        ['terms', 'privacy'].forEach(type => {
            const modal = document.getElementById(type + 'Modal');
            if (modal && modal.classList.contains('active')) {
                closeFooterModal(type);
            }
        });
    }
});
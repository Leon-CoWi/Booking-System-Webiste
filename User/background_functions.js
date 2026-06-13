document.addEventListener("DOMContentLoaded", () => {

    /* Grab all background slides rendered by background.php */
    let bgSlides = document.querySelectorAll('.bg-slide');

    if (bgSlides.length > 0) {
        let bgIdx = 0;

        /* Cycle through each slide every 5 seconds by toggling the active class */
        setInterval(() => {
            bgSlides[bgIdx].classList.remove('active');
            bgIdx = (bgIdx + 1) % bgSlides.length;
            bgSlides[bgIdx].classList.add('active');
        }, 5000);
    }

    /* Trigger the fade-in on each room card once its image has finished loading */
    document.querySelectorAll(".image-card").forEach(card => {
        const img = card.querySelector("img");
        const show = () => card.classList.add("loaded");

        /* If the image is already cached, show it immediately without waiting */
        if (img.complete) {
            show();
        } else {
            img.addEventListener("load", show);
        }
    });

});
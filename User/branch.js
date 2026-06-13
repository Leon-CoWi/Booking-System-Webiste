    // ── HERO SLIDESHOW ─────────────────────────────────────
    const heroSlides = document.querySelectorAll('.branch-bg-slide');
    let heroIdx = 0;
    setInterval(() => {
        heroSlides[heroIdx].classList.remove('active');
        heroIdx = (heroIdx + 1) % heroSlides.length;
        heroSlides[heroIdx].classList.add('active');
    }, 4500);

    // ── ROOM CARD SLIDER ───────────────────────────────────
    let branchCurrent = 0;
    function branchSlide(dir) {
        const track     = document.getElementById('branchSliderTrack');
        const cards     = track.querySelectorAll('.card');
        const cardWidth = cards[0].offsetWidth + 20;
        const visible   = Math.round(track.parentElement.offsetWidth / cardWidth);
        const max       = Math.max(0, cards.length - visible);
        branchCurrent   = Math.max(0, Math.min(branchCurrent + dir, max));
        track.style.transform = 'translateX(-' + (branchCurrent * cardWidth) + 'px)';
    }

    // ── CARD SLIDESHOW AUTO-PLAY ───────────────────────────
    document.querySelectorAll('.card-slideshow').forEach(show => {
        const slides = show.querySelectorAll('.card-slide');
        if (slides.length < 2) return;
        let idx = 0;
        setInterval(() => {
            slides[idx].classList.remove('active');
            idx = (idx + 1) % slides.length;
            slides[idx].classList.add('active');
        }, 3000);
    });

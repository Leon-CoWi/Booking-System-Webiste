// SIGNATURE SLIDER
let sigIndex = 0;

const track = document.getElementById("sigTrack");
const slides = track.children.length;
const dotsContainer = document.getElementById("sigDots");


// CREATE DOTS
for (let i = 0; i < slides; i++) {
    const dot = document.createElement("span");
    dot.onclick = () => goToSlide(i);
    dotsContainer.appendChild(dot);
}


// UPDATE ACTIVE DOT
function updateDots() {
    const dots = dotsContainer.children;
    for (let i = 0; i < dots.length; i++) {
        dots[i].classList.remove("active");
    }

    if (dots[sigIndex]) {
        dots[sigIndex].classList.add("active");
    }
}


// GO TO SPECIFIC SLIDE
function goToSlide(i) {
    sigIndex = i;
    move();
}


// MOVE SLIDES
function move() {
    const slideHeight = document.querySelector(".signature-card").offsetHeight;
    track.style.transform = `translateY(-${sigIndex * slideHeight}px)`;
    updateDots();
}


// NEXT SLIDE
function nextSlide() {
    sigIndex++;
    if (sigIndex >= slides) {
        sigIndex = 0;
    }
    move();
}

// AUTO SLIDE
setInterval(nextSlide, 3000);

updateDots();
export function imageSlider() {
    const buttons = document.querySelectorAll("[data-carousel-button]");
    const slidesContainer = document.querySelector("[data-carousel]");
    const slides = slidesContainer.querySelector("[data-slides]");

    let currentIndex = 0;

    const showSlide = (index) => {
        const activeSlide = slides.querySelector("[data-active]");
        delete activeSlide.dataset.active;

        slides.children[index].dataset.active = true;
    };

    const changeSlide = (offset) => {
        currentIndex = (currentIndex + offset + slides.children.length) % slides.children.length;
        showSlide(currentIndex);
    };

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const offset = button.CDATA_SECTION_NODE.carouselButton === "next" ? 1 : -1;
            changeSlide(offset);
        });
    });

    // Automatically change slide every 4500 milliseconds (adjust as needed)
    setInterval(() => {
        changeSlide(1); // Change to the next slide
    }, 4500);
}




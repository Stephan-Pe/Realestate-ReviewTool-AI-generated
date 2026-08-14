const scrollToTopBtn = document.querySelector(".totop__btn");
const rootElement = document.documentElement;
const header = document.querySelector('.nav');
const draggable = document.getElementById('draggable');
let prevScrollPos = window.scrollY;


export function scrollToTop() {
    // Scroll to top logic
    rootElement.scrollTo({
        top: 0,
        behavior: "smooth"
    });
}

export function showToTopBtn(scrollPos) {
    // Do something with the scroll position
    if (scrollPos > 400) {
       
        scrollToTopBtn?.classList.add('active');
        scrollToTopBtn?.addEventListener('click', scrollToTop);
        draggable?.classList.add('active');
    }
    if (scrollPos < 400) {
        scrollToTopBtn?.classList.remove('active');
        scrollToTopBtn?.removeEventListener('click', scrollToTop);
        draggable?.classList.remove('active');
    }
    if (scrollPos > prevScrollPos) {
        header.classList.add('hide');
    }
    if (scrollPos < prevScrollPos) {
        header.classList.remove('hide');
    }
    prevScrollPos = scrollPos;
}





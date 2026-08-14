export function showMap() {
    let wrapper = document.querySelector('body');
    const overlay = document.querySelector('.overlay-container');
    wrapper.addEventListener('click', (e) => {
        if (e.target.matches('.parallax__text--map') || e.target.matches('#showMap')) {
            overlay.classList.toggle('active');
            wrapper.classList.toggle('noscroll');
        }
        else if (e.target.matches('[data-btn-overlay]')) {

            overlay.classList.toggle('active');
            wrapper.classList.toggle('noscroll');
        }
    });


}
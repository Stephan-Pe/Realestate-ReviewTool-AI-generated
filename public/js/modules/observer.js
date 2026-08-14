export const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        const intersecting = entry.isIntersecting;
        entry.target.style.animation = intersecting ? `zoomIn 0.35s ease ${entry.target.dataset.delay} forwards` : `none`;
    });
});
export const btnObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        const intersecting = entry.isIntersecting;
        if (intersecting) {
            // Add a CSS class (e.g., 'active') when the element is in view
            entry.target.classList.add('active');
        } else {
            // Remove the CSS class when the element is out of view
            entry.target.classList.remove('active');
        }
    });
});
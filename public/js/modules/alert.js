// Add Flash message to image validation
export function flashMessage(message) {
    let timeout = 1500;
    const header = document.querySelector('.header');
    const alert = document.createElement('div');
    alert.className = 'alert alert-warning';
    alert.innerHTML = message;
    header.insertAdjacentElement('afterend', alert);
}

// error messenger
export function errorMsg(container, message) {

    // Add Flash message to image validation
    const alert = document.createElement('span');
    alert.className = 'error';
    alert.innerHTML = message;
    container.insertAdjacentElement('afterend', alert);

}
export function showFlash(text, type = 'info') {
    // Check if flash container exists, if not create one
    let flashContainer = document.querySelector('.review-flash-container');
    if (!flashContainer) {
        flashContainer = document.createElement('div');
        flashContainer.className = 'review-flash-container';
        const main = document.querySelector('.review-main__wrapper');
        if (main) {
            main.insertBefore(flashContainer, main.firstChild);
        }
    }

    const iconMap = {
        success: 'fa-check-circle',
        warning: 'fa-exclamation-circle',
        error: 'fa-times-circle',
        info: 'fa-info-circle',
    };

    const flash = document.createElement('div');
    flash.className = `review-flash review-flash--${type}`;
    flash.innerHTML = `<i class="fas ${iconMap[type] || iconMap.info}"></i> ${text}`;

    flashContainer.appendChild(flash);

    // Auto-remove after 4 seconds
    setTimeout(() => {
        flash.style.opacity = '0';
        flash.style.transition = 'opacity 0.3s';
        setTimeout(() => flash.remove(), 300);
    }, 4000);
}
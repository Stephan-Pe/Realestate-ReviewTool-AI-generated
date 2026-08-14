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
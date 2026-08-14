export function showImpressum() {
    const impressum = document.getElementById('impressum');
    let body = document.querySelector('body');
    body.addEventListener('click', (e) => {
        if (e.target.matches('#impressumBtn')) {

            impressum.classList.toggle('active');
        }
        if (e.target.matches('[data-btn-impressum]')) {
            impressum.classList.toggle('active');
        }

    });
}


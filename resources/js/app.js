document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('accountButton');
    const dropdown = document.getElementById('accountDropdown');

    if (!button || !dropdown) return;

    button.addEventListener('click', () => {
        const isOpen = !dropdown.hidden;

        dropdown.hidden = isOpen;
        button.setAttribute('aria-expanded', String(!isOpen));
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('#accountMenu')) {
            dropdown.hidden = true;
            button.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            dropdown.hidden = true;
            button.setAttribute('aria-expanded', 'false');
        }
    });
});
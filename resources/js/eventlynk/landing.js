/** Landing page mobile menu (App.tsx menuOpen). */
export function initLanding() {
    const page = document.querySelector('[data-landing]');
    if (!page) return;

    const menu = page.querySelector('[data-landing-menu]');
    const toggle = page.querySelector('[data-menu-toggle]');

    const setOpen = (open) => {
        menu.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', String(open));
    };

    toggle.addEventListener('click', () => setOpen(!menu.classList.contains('is-open')));
    page.querySelectorAll('[data-close-menu]').forEach((button) => {
        button.addEventListener('click', () => setOpen(false));
    });
}

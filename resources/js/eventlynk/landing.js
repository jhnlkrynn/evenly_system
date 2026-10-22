/** Landing page mobile menu (App.tsx menuOpen). */
export function initLanding() {
    const page = document.querySelector('[data-landing]');
    if (!page) return;

    const revealItems = page.querySelectorAll('[data-scroll-reveal]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    revealItems.forEach((item, index) => {
        item.style.setProperty('--reveal-delay', `${index % 4}`);
    });

    if (reduceMotion || !('IntersectionObserver' in window)) {
        revealItems.forEach((item) => item.classList.add('is-visible'));
    } else {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.14, rootMargin: '0px 0px -8% 0px' });

        revealItems.forEach((item) => revealObserver.observe(item));
    }

    const hero = page.querySelector('[data-scroll-parallax]');
    const heroImage = hero?.querySelector('.hero-visual > img');

    if (hero && heroImage && !reduceMotion) {
        const updateHeroParallax = () => {
            const progress = Math.min(Math.max(window.scrollY / Math.max(hero.offsetHeight, 1), 0), 1);
            heroImage.style.setProperty('--hero-image-shift', `${progress * 34}px`);
        };

        updateHeroParallax();
        window.addEventListener('scroll', updateHeroParallax, { passive: true });
    }

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

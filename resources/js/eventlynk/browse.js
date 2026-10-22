/**
 * Browse Services. Filtering happens on the server (ServiceController /
 * App\Support\ServiceSearch); this submits the #browse-filters GET form as
 * filters change, keeps empty fields out of the URL, and handles the mobile
 * filter drawer and wishlist hearts.
 */

function setupBrowse(root) {
    const form = root.querySelector('[data-browse-form]');
    if (!form) return;

    const submit = () => form.requestSubmit();

    // Text search submits on Enter or the Search button; other filters submit as soon as they change.
    root.addEventListener('change', (event) => {
        if (event.target.matches('[data-auto-submit]')) submit();
    });

    root.querySelectorAll('[data-searchable-select][data-auto-submit]').forEach((select) => {
        select.addEventListener('select:change', submit);
    });

    // Leave empty and default values out of the query string so shared URLs stay short…
    form.addEventListener('submit', () => {
        const available = form.elements.namedItem('available');
        const freeOnly = [...(available?.length ? available : [available])].some((field) => field?.type === 'checkbox' && field.checked);

        [...form.elements].forEach((field) => {
            const isDefault =
                field.value === '' ||
                field.value === field.dataset.default ||
                (field.name === 'available' && freeOnly); // "free only" is the default
            if (field.name && isDefault) field.disabled = true;
        });
    });

    // …and re-enable them if the page is restored from the back/forward cache.
    window.addEventListener('pageshow', () => {
        [...form.elements].forEach((field) => {
            field.disabled = false;
        });
    });

    root.querySelectorAll('[data-rating]').forEach((pill) => {
        pill.addEventListener('click', () => {
            form.querySelector('[data-rating-input]').value = pill.dataset.rating;
            submit();
        });
    });

    const panel = root.querySelector('[data-filter-panel]');
    const toggle = root.querySelector('[data-toggle-filters]');
    toggle?.addEventListener('click', () => {
        const open = !panel.classList.contains('open');
        panel.classList.toggle('open', open);
        toggle.textContent = open ? 'Hide filters' : 'Show filters';
    });

    root.addEventListener('click', (event) => {
        const heart = event.target.closest('[data-wishlist]');
        if (!heart) return;
        const wished = heart.textContent === '♡';
        heart.textContent = wished ? '♥' : '♡';
        heart.setAttribute('aria-label', wished ? 'Remove from favorites' : 'Add to favorites');
    });
}

/** Plain GET search forms (homepage hero): drop empty fields from the URL. */
function setupCleanForms() {
    document.querySelectorAll('form[data-clean-get]').forEach((form) => {
        form.addEventListener('submit', () => {
            [...form.elements].forEach((field) => {
                if (field.name && field.value === '') field.disabled = true;
            });
        });
        window.addEventListener('pageshow', () => {
            [...form.elements].forEach((field) => {
                field.disabled = false;
            });
        });
    });
}

export function initBrowse() {
    document.querySelectorAll('[data-browse]').forEach(setupBrowse);
    setupCleanForms();
}

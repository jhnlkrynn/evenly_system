/**
 * Browse Services marketplace filters (BrowseServicesPage.tsx):
 * search, location, date, service / event checkboxes, budget, rating,
 * availability, sort, removable chips, wishlist hearts and the mobile filter drawer.
 */
import { setSelectValue } from './searchable-select';

const SORTERS = {
    'Highest rated': (a, b) => b.rating - a.rating,
    'Price: low to high': (a, b) => a.price - b.price,
    'Price: high to low': (a, b) => b.price - a.price,
    Recommended: (a, b) => b.reviews - a.reviews,
};

function formatDate(value, options) {
    if (!value) return 'your date';
    return new Date(`${value}T00:00:00`).toLocaleDateString('en-PH', options);
}

function setupBrowse(root) {
    const $ = (selector) => root.querySelector(selector);
    const $$ = (selector) => [...root.querySelectorAll(selector)];

    const grid = $('[data-vendor-grid]');
    const empty = $('[data-browse-empty]');
    const chips = $('[data-filter-chips]');
    const panel = $('[data-filter-panel]');
    const mobileToggle = $('[data-toggle-filters]');
    const locationSelect = $('[data-filter-location]');

    // Cards in their original (catalogue) order, so equal sort keys keep the prototype's order.
    const vendors = $$('[data-vendor-card]')
        .map((card) => ({
            card,
            id: Number(card.dataset.id),
            search: card.dataset.search,
            badge: card.dataset.badge,
            barangay: card.dataset.barangay,
            tags: card.dataset.tags.split('|'),
            price: Number(card.dataset.price),
            rating: Number(card.dataset.rating),
            reviews: Number(card.dataset.reviews),
        }))
        .sort((a, b) => a.id - b.id);

    const state = {
        query: $('[data-filter-query]').value,
        location: locationSelect.dataset.value || '',
        date: $('[data-filter-date]').value,
        services: [],
        events: [],
        minPrice: 0,
        maxPrice: 30000,
        rating: 0,
        availableOnly: true,
        sort: 'Recommended',
    };

    const toggleIn = (list, value) => (list.includes(value) ? list.filter((item) => item !== value) : [...list, value]);

    function matches(vendor) {
        const query = state.query.trim().toLowerCase();
        const matchesEvent =
            state.events.length === 0 ||
            state.events.some((event) =>
                vendor.tags.some((tag) => tag.includes(event.toLowerCase().replace(/s$/, ''))),
            );
        return (
            (!query || vendor.search.includes(query)) &&
            (!state.location || vendor.barangay === state.location) &&
            (state.services.length === 0 || state.services.includes(vendor.badge)) &&
            matchesEvent &&
            vendor.price >= state.minPrice &&
            vendor.price <= state.maxPrice &&
            vendor.rating >= state.rating
        );
    }

    function renderChips() {
        const active = [
            ...state.services,
            ...state.events,
            ...(state.rating ? [`${state.rating}+ stars`] : []),
            ...(state.availableOnly ? ['Available on date'] : []),
        ];
        chips.hidden = active.length === 0;
        chips.replaceChildren(
            ...active.map((chip) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = `${chip} ×`;
                button.addEventListener('click', () => {
                    if (state.services.includes(chip)) state.services = state.services.filter((item) => item !== chip);
                    else if (state.events.includes(chip)) state.events = state.events.filter((item) => item !== chip);
                    else if (chip.includes('stars')) state.rating = 0;
                    else state.availableOnly = false;
                    syncControls();
                    render();
                });
                return button;
            }),
        );
    }

    function render() {
        const filtered = vendors.filter(matches).sort(SORTERS[state.sort] || SORTERS.Recommended);
        const visible = new Set(filtered);

        vendors.forEach((vendor) => {
            vendor.card.hidden = !visible.has(vendor);
        });
        filtered.forEach((vendor) => grid.appendChild(vendor.card));

        grid.hidden = filtered.length === 0;
        empty.hidden = filtered.length > 0;

        $('[data-result-label]').textContent = `${filtered.length} vendors`;
        $$('[data-result-count]').forEach((node) => {
            node.textContent = filtered.length;
        });
        $$('[data-date-short]').forEach((node) => {
            node.textContent = formatDate(state.date, { day: 'numeric', month: 'short' });
        });
        $$('[data-date-long]').forEach((node) => {
            node.textContent = formatDate(state.date, { weekday: 'short', day: 'numeric', month: 'short' });
        });

        renderChips();
    }

    /** Push state back into the form controls (after chips / clear all). */
    function syncControls() {
        $('[data-filter-query]').value = state.query;
        $$('[data-filter-service]').forEach((box) => {
            box.checked = state.services.includes(box.value);
        });
        $$('[data-filter-event]').forEach((box) => {
            box.checked = state.events.includes(box.value);
        });
        $('[data-filter-min]').value = state.minPrice;
        $('[data-filter-max]').value = state.maxPrice;
        $('[data-filter-available]').checked = state.availableOnly;
        $$('[data-filter-rating]').forEach((pill) => {
            pill.classList.toggle('active', Number(pill.dataset.filterRating) === state.rating);
        });
        setSelectValue(locationSelect, state.location, { silent: true });
    }

    $('[data-filter-query]').addEventListener('input', (event) => {
        state.query = event.target.value;
        render();
    });
    locationSelect.addEventListener('select:change', (event) => {
        state.location = event.detail.value;
        render();
    });
    $('[data-filter-date]').addEventListener('change', (event) => {
        state.date = event.target.value;
        render();
    });
    $$('[data-filter-service]').forEach((box) =>
        box.addEventListener('change', () => {
            state.services = toggleIn(state.services, box.value);
            render();
        }),
    );
    $$('[data-filter-event]').forEach((box) =>
        box.addEventListener('change', () => {
            state.events = toggleIn(state.events, box.value);
            render();
        }),
    );
    $('[data-filter-min]').addEventListener('input', (event) => {
        state.minPrice = Number(event.target.value);
        render();
    });
    $('[data-filter-max]').addEventListener('input', (event) => {
        state.maxPrice = Number(event.target.value);
        render();
    });
    $$('[data-filter-rating]').forEach((pill) =>
        pill.addEventListener('click', () => {
            state.rating = Number(pill.dataset.filterRating);
            syncControls();
            render();
        }),
    );
    $('[data-filter-available]').addEventListener('change', (event) => {
        state.availableOnly = event.target.checked;
        render();
    });
    $('[data-filter-sort]').addEventListener('change', (event) => {
        state.sort = event.target.value;
        render();
    });
    $$('[data-clear-filters]').forEach((button) =>
        button.addEventListener('click', () => {
            Object.assign(state, {
                services: [],
                events: [],
                minPrice: 0,
                maxPrice: 30000,
                rating: 0,
                availableOnly: true,
                location: '',
                query: '',
            });
            syncControls();
            render();
        }),
    );

    const setMobileFilters = (open) => {
        panel.classList.toggle('open', open);
        mobileToggle.textContent = open ? 'Hide filters' : 'Show filters';
    };
    mobileToggle.addEventListener('click', () => setMobileFilters(!panel.classList.contains('open')));
    $('[data-close-filters]').addEventListener('click', () => setMobileFilters(false));

    root.addEventListener('click', (event) => {
        const heart = event.target.closest('[data-wishlist]');
        if (!heart) return;
        const wished = heart.textContent === '♡';
        heart.textContent = wished ? '♥' : '♡';
        heart.setAttribute('aria-label', wished ? 'Remove from favorites' : 'Add to favorites');
    });

    render();
}

export function initBrowse() {
    document.querySelectorAll('[data-browse]').forEach(setupBrowse);
}

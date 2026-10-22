/**
 * Type-to-filter dropdown (SearchableSelect.tsx).
 * Emits a bubbling `select:change` event with { value } when an option is picked.
 */

function setOpen(root, open) {
    const menu = root.querySelector('[data-select-menu]');
    const trigger = root.querySelector('[data-select-trigger]');
    const caret = root.querySelector('[data-select-caret]');
    const search = root.querySelector('[data-select-search]');

    menu.hidden = !open;
    trigger.setAttribute('aria-expanded', String(open));
    caret.style.transform = `translateY(-50%) rotate(${open ? 180 : 0}deg)`;

    if (open) {
        setTimeout(() => search.focus(), 50);
    } else {
        search.value = '';
        filterOptions(root, '');
    }
}

function filterOptions(root, query) {
    const needle = query.trim().toLowerCase();
    let visible = 0;
    root.querySelectorAll('[data-select-option]').forEach((option) => {
        if (option.dataset.selectOption === '') return; // the "all" option is always listed
        const match = !needle || option.dataset.selectOption.toLowerCase().includes(needle);
        option.hidden = !match;
        if (match) visible += 1;
    });
    root.querySelector('[data-select-empty]').hidden = visible > 0;
}

export function setSelectValue(root, value, { silent = false } = {}) {
    root.dataset.value = value;
    const trigger = root.querySelector('[data-select-trigger]');
    // The label is the trigger's leading text node (no wrapper, so page-level span rules can't restyle it).
    trigger.firstChild.nodeValue = value || root.dataset.placeholder;
    trigger.style.color = value ? '#1a110a' : '#9c8f7a';

    root.querySelectorAll('[data-select-option]').forEach((option) => {
        option.classList.toggle('is-selected', option.dataset.selectOption === value);
    });

    const hidden = root.querySelector('[data-select-value]');
    if (hidden) hidden.value = value;

    if (!silent) {
        root.dispatchEvent(new CustomEvent('select:change', { bubbles: true, detail: { value } }));
    }
}

function initSelect(root) {
    const trigger = root.querySelector('[data-select-trigger]');
    const menu = root.querySelector('[data-select-menu]');
    const search = root.querySelector('[data-select-search]');
    const selected = root.querySelector('.searchable-option.is-selected');
    root.dataset.value = selected ? selected.dataset.selectOption : '';

    // The select often sits inside a <label>; the click that follows an option
    // pick would otherwise be forwarded to the trigger and reopen the menu.
    let suppressUntil = 0;

    trigger.addEventListener('click', () => {
        if (Date.now() < suppressUntil) return;
        setOpen(root, menu.hidden);
    });

    search.addEventListener('input', () => filterOptions(root, search.value));

    menu.addEventListener('mousedown', (event) => {
        const option = event.target.closest('[data-select-option]');
        if (!option) return;
        event.preventDefault();
        suppressUntil = Date.now() + 400;
        setSelectValue(root, option.dataset.selectOption);
        setOpen(root, false);
    });

    menu.addEventListener('click', (event) => event.preventDefault());

    document.addEventListener('mousedown', (event) => {
        if (!menu.hidden && !root.contains(event.target)) setOpen(root, false);
    });

    root.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !menu.hidden) {
            event.stopPropagation();
            setOpen(root, false);
            trigger.focus();
        }
    });
}

export function initSearchableSelects() {
    document.querySelectorAll('[data-searchable-select]').forEach(initSelect);
}

/**
 * Behaviour shared by every Eventlynk page:
 *  - [data-href]            navigation buttons (the design styles <button>s, so links stay buttons)
 *  - [data-open-modal] / [data-modal-open], [data-modal-close], backdrop click, Escape
 *  - [data-toast-message]   dashboard toast
 *  - [data-toggle-password], [data-scroll-top], [data-print]
 */

const TOAST_MS = 2800;
let toastTimer;

export function showToast(message) {
    const toast = document.querySelector('[data-toast]');
    if (!toast) return;
    toast.querySelector('[data-toast-text]').textContent = message;
    toast.hidden = false;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
        toast.hidden = true;
    }, TOAST_MS);
}

export function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.hidden = false;
    modal.dispatchEvent(new CustomEvent('modal:open', { bubbles: true }));
}

export function closeModal(modal) {
    if (!modal || modal.hidden) return;
    modal.hidden = true;
    modal.dispatchEvent(new CustomEvent('modal:close', { bubbles: true }));
}

/** True when the click started inside a [data-stop-propagation] region nested in `owner`. */
export function isShielded(event, owner) {
    const shield = event.target.closest('[data-stop-propagation]');
    return Boolean(shield && shield !== owner && owner.contains(shield));
}

export function initCore() {
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-confirm]');
        if (form && !window.confirm(form.dataset.confirm)) event.preventDefault();
    });
    document.addEventListener('click', (event) => {
        const { target } = event;

        const opener = target.closest('[data-modal-open], [data-open-modal]');
        if (opener && !isShielded(event, opener)) {
            openModal(opener.dataset.modalOpen || opener.dataset.openModal);
        }

        const closer = target.closest('[data-modal-close]');
        if (closer) {
            closeModal(closer.closest('[data-modal]'));
        }

        // Clicking the dimmed backdrop (the modal element itself) closes it.
        if (target.matches('[data-modal]')) {
            closeModal(target);
        }

        const toastButton = target.closest('[data-toast-message]');
        if (toastButton && !toastButton.disabled) {
            showToast(toastButton.dataset.toastMessage);
        }

        const link = target.closest('[data-href]');
        if (link && !link.disabled) {
            window.location.href = link.dataset.href;
        }

        const passwordToggle = target.closest('[data-toggle-password]');
        if (passwordToggle) {
            const input = passwordToggle.parentElement.querySelector('input');
            const reveal = input.type === 'password';
            input.type = reveal ? 'text' : 'password';
            passwordToggle.textContent = reveal ? 'Hide' : 'Show';
        }

        if (target.closest('[data-scroll-top]')) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        if (target.closest('[data-print]')) {
            window.print();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const open = [...document.querySelectorAll('[data-modal]:not([hidden])')].pop();
        closeModal(open);
    });
}

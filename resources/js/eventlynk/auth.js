/**
 * Sign-up / role selection UI (SignUpPage.tsx, RoleSelector.tsx).
 * Forms post to Laravel; validation happens in the Form Requests.
 */

function passwordStrength(length) {
    if (length >= 12) return 4;
    if (length >= 10) return 3;
    if (length >= 8) return 2;
    return length ? 1 : 0;
}

function initSignup() {
    const form = document.querySelector('[data-signup]');
    if (!form) return;

    const password = form.querySelector('[data-strength-input]');
    const meter = form.querySelector('[data-strength-meter]');
    const label = form.querySelector('[data-strength-label]');
    const note = form.querySelector('[data-role-note]');

    password.addEventListener('input', () => {
        const strength = passwordStrength(password.value.length);
        meter.querySelectorAll('span').forEach((bar, index) => bar.classList.toggle('active', strength >= index + 1));
        meter.setAttribute('aria-label', `Password strength ${strength} out of 4`);
        label.textContent = strength >= 3 ? 'Strong' : strength >= 2 ? 'Good' : 'Keep going';
    });

    form.addEventListener('role:change', (event) => {
        note.textContent = event.detail.role === 'vendor' ? note.dataset.noteVendor : note.dataset.noteCustomer;
    });
}

function initRoleSelector() {
    document.querySelectorAll('[data-role-selector]').forEach((root) => {
        const input = root.querySelector('[data-role-value]');
        root.querySelectorAll('[data-role-option]').forEach((option) => {
            option.addEventListener('click', () => {
                input.value = option.dataset.roleOption;
                root.querySelectorAll('[data-role-option]').forEach((other) => {
                    const selected = other === option;
                    other.classList.toggle('is-selected', selected);
                    other.setAttribute('aria-pressed', String(selected));
                });
                root.dispatchEvent(new CustomEvent('role:change', { bubbles: true, detail: { role: input.value } }));
            });
        });
    });
}

export function initAuth() {
    initSignup();
    initRoleSelector();
}

/**
 * Log in / sign up / role selection screens (LoginPage.tsx, SignUpPage.tsx, RoleSelector.tsx).
 * Static phase: forms do not post yet. Login jumps to the dashboard picked in
 * "Demo destination"; sign-up validates and then redirects to log in.
 */

function initLogin() {
    const form = document.querySelector('[data-demo-login]');
    if (!form) return;
    const destinations = JSON.parse(form.dataset.destinations);

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const role = form.querySelector('[data-demo-role]').value;
        window.location.href = destinations[role];
    });
}

function passwordStrength(length) {
    if (length >= 12) return 4;
    if (length >= 10) return 3;
    if (length >= 8) return 2;
    return length ? 1 : 0;
}

function initSignup() {
    const form = document.querySelector('[data-demo-signup]');
    if (!form) return;

    const error = form.querySelector('[data-signup-error]');
    const success = form.querySelector('[data-signup-success]');
    const submit = form.querySelector('[data-signup-submit]');
    const note = form.querySelector('[data-role-note]');
    const password = form.querySelector('[data-strength-input]');
    const meter = form.querySelector('[data-strength-meter]');
    const label = form.querySelector('[data-strength-label]');

    form.querySelectorAll('[data-account-choice] input[type="radio"]').forEach((radio) => {
        radio.addEventListener('change', () => {
            form.querySelectorAll('[data-account-choice] label').forEach((option) => {
                option.classList.toggle('selected', option.contains(radio) && radio.checked);
            });
            note.textContent = radio.value === 'vendor' ? note.dataset.noteVendor : note.dataset.noteCustomer;
        });
    });

    password.addEventListener('input', () => {
        const strength = passwordStrength(password.value.length);
        meter.querySelectorAll('span').forEach((bar, index) => bar.classList.toggle('active', strength >= index + 1));
        meter.setAttribute('aria-label', `Password strength ${strength} out of 4`);
        label.textContent = strength >= 3 ? 'Strong' : strength >= 2 ? 'Good' : 'Keep going';
    });

    const showError = (message) => {
        error.textContent = message;
        error.hidden = !message;
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        showError('');
        const value = (name) => form.elements[name].value;

        if (!value('name') || !value('email') || !value('phone') || !value('password')) {
            showError('Please complete all required fields.');
            return;
        }
        if (value('password').length < 8) {
            showError('Use at least 8 characters for your password.');
            return;
        }
        if (!form.querySelector('[data-terms]').checked) {
            showError('Please agree to the Terms of Service.');
            return;
        }

        success.hidden = false;
        submit.disabled = true;
        submit.textContent = 'Creating account…';
        setTimeout(() => {
            window.location.href = form.dataset.loginUrl;
        }, 1200);
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
            });
        });
    });
}

export function initAuth() {
    initLogin();
    initSignup();
    initRoleSelector();
}

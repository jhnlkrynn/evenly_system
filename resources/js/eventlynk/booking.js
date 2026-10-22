/**
 * Booking form (BookingPage.tsx design). Holds the form state, renders the
 * month calendar with the vendor's booked dates (GET /vendors/{id}/booked-dates),
 * keeps the hidden inputs in sync and checks each step before moving on.
 * The server validates everything again (StoreBookingRequest).
 */

const WEEKDAYS = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
const DAY_NAMES = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

const fmt = (value) => value.toLocaleString('en-PH');
const pad = (n) => String(n).padStart(2, '0');
const isoDate = (y, m, d) => `${y}-${pad(m + 1)}-${pad(d)}`;

function parseIso(iso) {
    const [y, m, d] = iso.split('-').map(Number);
    return new Date(y, m - 1, d);
}

function time12(value) {
    if (!value) return '';
    const [h, m] = value.split(':').map(Number);
    return `${h % 12 || 12}:${pad(m)} ${h < 12 ? 'AM' : 'PM'}`;
}

export function initBooking() {
    const root = document.querySelector('[data-booking]');
    if (!root) return;

    const form = root.querySelector('[data-booking-form]');
    const $ = (selector) => root.querySelector(selector);
    const $$ = (selector) => [...root.querySelectorAll(selector)];

    const pricing = JSON.parse(root.dataset.pricing);
    const today = root.dataset.today;
    const state = { step: Number(root.dataset.initialStep) || 1, ...JSON.parse(root.dataset.initial), agreed: false };
    const booked = new Set();
    let bookedLoaded = false;

    const start = state.date ? parseIso(state.date) : parseIso(today);
    const view = { year: start.getFullYear(), month: start.getMonth() };

    /* -------------------------------- derived values -------------------------------- */

    function derived() {
        const decorationCost = state.decoration ? pricing.decoration : 0;
        const servingCost = state.serving * pricing.serving;
        const dishwashingCost = state.dishwashing * pricing.dishwashing;
        const total = decorationCost + servingCost + dishwashingCost;
        const reservationFee = Math.round(total * pricing.reservationRate);
        const date = state.date ? parseIso(state.date) : null;

        return {
            decorationCost,
            servingCost,
            dishwashingCost,
            total,
            reservationFee,
            balance: total - reservationFee,
            hasServices: state.decoration || state.serving > 0 || state.dishwashing > 0,
            dateLong: date ? `${DAY_NAMES[date.getDay()]}, ${date.getDate()} ${MONTHS[date.getMonth()].slice(0, 3)} ${date.getFullYear()}` : 'Pick a date',
            dateShort: date ? `${DAY_NAMES[date.getDay()]}, ${date.getDate()} ${MONTHS[date.getMonth()].slice(0, 3)}` : 'Date not set',
            timeRange: state.startTime && state.endTime ? `${time12(state.startTime)}–${time12(state.endTime)}` : 'Time not set',
            servingSuggested: Math.ceil(state.guests / 12.5),
            dishwashingSuggested: Math.ceil(state.guests / 25),
        };
    }

    function dateProblem() {
        if (!state.date) return 'Choose a date on the calendar.';
        if (state.date < today) return 'That date has already passed. Please choose an upcoming date.';
        if (booked.has(state.date)) return 'This vendor is already booked on that date. Please choose another date.';
        return '';
    }

    function stepProblem(step) {
        if (step === 1) {
            const problem = dateProblem();
            if (problem) return problem;
            if (!state.startTime || !state.endTime) return 'Set when your event starts and ends.';
            if (state.endTime <= state.startTime) return 'The event must end after it starts.';
            if (!state.venue.trim()) return 'Enter the venue name or address.';
            if (!state.barangay) return 'Choose the barangay where your event will be held.';
        }
        if (step === 2 && !derived().hasServices) return 'Choose at least one service.';
        return '';
    }

    /* ----------------------------------- calendar ----------------------------------- */

    function renderCalendar() {
        const grid = $('[data-calendar]');
        const first = new Date(view.year, view.month, 1);
        const days = new Date(view.year, view.month + 1, 0).getDate();
        const nodes = WEEKDAYS.map((name) => Object.assign(document.createElement('span'), { className: 'weekday', textContent: name }));

        for (let i = 0; i < first.getDay(); i += 1) nodes.push(document.createElement('span'));

        for (let day = 1; day <= days; day += 1) {
            const iso = isoDate(view.year, view.month, day);
            const past = iso < today;
            const isBooked = booked.has(iso);
            const button = document.createElement('button');
            button.type = 'button';
            button.dataset.day = iso;
            button.className = [iso === state.date ? 'selected' : '', isBooked ? 'booked' : ''].filter(Boolean).join(' ');
            button.setAttribute('aria-disabled', past || isBooked ? 'true' : 'false');
            button.setAttribute('aria-label', `${day} ${MONTHS[view.month]}${isBooked ? ', booked' : ''}${past ? ', past' : ''}`);
            button.innerHTML = `<span>${day}</span><i class="${past || isBooked || !bookedLoaded ? '' : 'free'}"></i>`;
            nodes.push(button);
        }

        grid.replaceChildren(...nodes);
        $('[data-month-label]').textContent = `${MONTHS[view.month]} ${view.year}`;

        const now = parseIso(today);
        $('[data-month-step="-1"]').disabled = view.year === now.getFullYear() && view.month <= now.getMonth();
    }

    /* ------------------------------------ render ------------------------------------ */

    function render() {
        const d = derived();

        $$('[data-step]').forEach((node) => {
            node.hidden = Number(node.dataset.step) !== state.step;
        });
        $$('[data-sidebar-step]').forEach((node) => {
            node.hidden = Number(node.dataset.sidebarStep) !== state.step;
        });
        const title = $('[data-step-title]');
        title.textContent = title.getAttribute(`data-title-${state.step}`);
        $$('[data-progress]').forEach((button) => {
            const number = Number(button.dataset.progress);
            button.classList.toggle('active', number === state.step);
            button.classList.toggle('done', number < state.step);
            button.disabled = number > state.step;
            button.querySelector('[data-progress-label]').textContent = `Step ${number}${number < state.step ? ' · Done' : ''}`;
        });

        const values = {
            ...state,
            ...d,
            decorationCost: fmt(d.decorationCost),
            servingCost: fmt(d.servingCost),
            dishwashingCost: fmt(d.dishwashingCost),
            total: fmt(d.total),
            reservationFee: fmt(d.reservationFee),
            balance: fmt(d.balance),
            eventLabel: `${state.occasion} · ${state.guests} guests`,
            fullVenue: [state.venue.trim(), state.barangay ? `${state.barangay}, Nasugbu, Batangas` : ''].filter(Boolean).join(' · ') || 'Select a venue',
            estimateMeta: `${state.occasion} · ${d.dateShort} · ${state.guests} guests`,
        };
        $$('[data-bind]').forEach((node) => {
            node.textContent = values[node.dataset.bind] ?? '';
        });

        // Hidden inputs the form submits
        const fields = { ...state, decoration: state.decoration ? '1' : '0' };
        $$('[data-field]').forEach((input) => {
            input.value = fields[input.dataset.field] ?? '';
        });

        $$('[data-occasion]').forEach((pill) => pill.classList.toggle('selected', pill.dataset.occasion === state.occasion));
        $$('[data-guests-preset]').forEach((pill) => pill.classList.toggle('active', Number(pill.dataset.guestsPreset) === state.guests));
        $$('[data-theme]').forEach((pill) => pill.classList.toggle('active', pill.dataset.theme === state.theme));

        const selected = { decoration: state.decoration, serving: state.serving > 0, dishwashing: state.dishwashing > 0 };
        Object.entries(selected).forEach(([key, on]) => {
            const card = $(`[data-service-card="${key}"]`);
            card.classList.toggle('selected', on);
            card.querySelector('[data-service-toggle]').checked = on;
            card.querySelector('[data-service-body]').hidden = !on;
            $$(`[data-review-line="${key}"]`).forEach((line) => {
                line.hidden = !on;
            });
        });
        const costs = { decoration: d.decorationCost, serving: d.servingCost, dishwashing: d.dishwashingCost };
        Object.entries(costs).forEach(([key, cost]) => {
            $$(`[data-estimate-line="${key}"]`).forEach((line) => {
                line.hidden = cost <= 0;
            });
        });
        $$('[data-review-notes]').forEach((note) => {
            note.hidden = !state.notes.trim();
        });

        // Date status under the selected date
        const status = $('[data-date-status]');
        const problem = state.date ? dateProblem() : '';
        status.textContent = !state.date ? 'Choose a date on the calendar' : problem || 'The vendor is free on this date';
        status.classList.toggle('is-warning', Boolean(problem));

        $('[data-requires-services]').disabled = !d.hasServices;
        $('[data-submit-booking]').disabled = !state.agreed || !d.hasServices;
        $$('[data-payment]').forEach((radio) => {
            radio.checked = radio.value === state.payment;
            radio.closest('label').classList.toggle('selected', radio.checked);
        });

        renderCalendar();
    }

    const update = (patch) => {
        Object.assign(state, patch);
        render();
    };

    function showStepError(step, message) {
        const box = $(`[data-step-error="${step}"]`);
        if (!box) return;
        box.textContent = message;
        box.hidden = !message;
    }

    function goTo(step) {
        // Moving forward requires every earlier step to be complete.
        for (let s = 1; s < step; s += 1) {
            const problem = stepProblem(s);
            if (problem) {
                update({ step: s });
                showStepError(s, problem);
                return;
            }
        }
        showStepError(1, '');
        showStepError(2, '');
        update({ step });
        root.querySelector('.booking-shell').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    /* ------------------------------------ events ------------------------------------ */

    root.addEventListener('click', (event) => {
        const t = event.target;
        const hit = (selector) => t.closest(selector);

        if (hit('[data-occasion]')) update({ occasion: hit('[data-occasion]').dataset.occasion });
        if (hit('[data-day]')) {
            update({ date: hit('[data-day]').dataset.day });
            showStepError(1, dateProblem());
        }
        if (hit('[data-month-step]')) {
            const next = new Date(view.year, view.month + Number(hit('[data-month-step]').dataset.monthStep), 1);
            view.year = next.getFullYear();
            view.month = next.getMonth();
            renderCalendar();
        }
        if (hit('[data-guests-step]')) update({ guests: Math.max(10, Math.min(2000, state.guests + Number(hit('[data-guests-step]').dataset.guestsStep))) });
        if (hit('[data-guests-preset]')) update({ guests: Number(hit('[data-guests-preset]').dataset.guestsPreset) });
        if (hit('[data-theme]')) update({ theme: hit('[data-theme]').dataset.theme });

        if (hit('[data-qty-step]')) {
            const stepper = hit('[data-qty-step]');
            const key = stepper.dataset.qtyKey;
            const max = key === 'serving' ? 50 : 30;
            update({ [key]: Math.max(0, Math.min(max, state[key] + Number(stepper.dataset.qtyStep))) });
        }

        if (hit('[data-full-package]')) {
            update({ decoration: true, serving: Math.max(state.serving, 8), dishwashing: Math.max(state.dishwashing, 4) });
        }

        const goStep = hit('[data-go-step]');
        if (goStep && !goStep.disabled) goTo(Number(goStep.dataset.goStep));
    });

    root.addEventListener('change', (event) => {
        const t = event.target;
        if (t.matches('[data-service-toggle]')) {
            const key = t.dataset.serviceToggle;
            const d = derived();
            if (key === 'decoration') update({ decoration: !state.decoration });
            if (key === 'serving') update({ serving: state.serving > 0 ? 0 : d.servingSuggested });
            if (key === 'dishwashing') update({ dishwashing: state.dishwashing > 0 ? 0 : d.dishwashingSuggested });
        }
        if (t.matches('[data-payment]')) update({ payment: t.value });
        if (t.matches('[data-terms]')) update({ agreed: t.checked });
    });

    root.addEventListener('input', (event) => {
        const t = event.target;
        if (t.matches('[data-input]')) update({ [t.dataset.input]: t.value });
    });

    $('[data-venue-select]').addEventListener('select:change', (event) => update({ barangay: event.detail.value }));

    form.addEventListener('submit', (event) => {
        for (let s = 1; s <= 2; s += 1) {
            const problem = stepProblem(s);
            if (problem) {
                event.preventDefault();
                update({ step: s });
                showStepError(s, problem);
                return;
            }
        }
        form.querySelector('[data-submit-booking]').disabled = true; // no double submits
    });

    // Fill the real inputs from the initial state (old() values after a failed submit).
    $$('[data-input]').forEach((input) => {
        input.value = state[input.dataset.input] ?? '';
    });

    render();

    fetch(root.dataset.bookedUrl, { headers: { Accept: 'application/json' } })
        .then((response) => (response.ok ? response.json() : { booked: [] }))
        .then((data) => {
            data.booked.forEach((date) => booked.add(date));
        })
        .catch(() => {})
        .finally(() => {
            bookedLoaded = true;
            render();
        });
}

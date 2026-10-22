/**
 * Three-stage booking flow (BookingPage.tsx). Holds the same state the React
 * component did and re-renders every [data-bind] placeholder from it.
 */

const PRICES = { decoration: 5000, serving: 750, dishwashing: 500 };
const fmt = (value) => value.toLocaleString('en-PH');

export function initBooking() {
    const root = document.querySelector('[data-booking]');
    if (!root) return;
    const success = document.querySelector('[data-booking-success]');

    const $ = (selector) => root.querySelector(selector);
    const $$ = (selector) => [...document.querySelectorAll(selector)];

    const state = {
        step: 1,
        occasion: 'Debut',
        date: 12,
        startTime: '18:00',
        endTime: '23:00',
        venue: 'Bucana',
        guests: 100,
        customerName: 'Naicel Apolona',
        contactNumber: '+63 906 407 4540',
        decoration: true,
        serving: 8,
        dishwashing: 4,
        theme: 'Gold & ivory',
        notes: '18 roses setup, stage backdrop.',
        payment: 'GCash',
        agreed: false,
    };

    function derived() {
        const decorationCost = state.decoration ? PRICES.decoration : 0;
        const servingCost = state.serving * PRICES.serving;
        const dishwashingCost = state.dishwashing * PRICES.dishwashing;
        const total = decorationCost + servingCost + dishwashingCost;
        const reservationFee = Math.round(total * 0.3);
        return {
            decorationCost,
            servingCost,
            dishwashingCost,
            total,
            reservationFee,
            balance: total - reservationFee,
            hasServices: state.decoration || state.serving > 0 || state.dishwashing > 0,
            eventLabel: `${state.occasion} · ${state.guests} guests`,
            fullVenue: state.venue ? `${state.venue}, Nasugbu, Batangas` : 'Select a venue',
        };
    }

    function render() {
        const d = derived();

        // Step visibility, title and progress
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

        // Text bindings
        const values = {
            ...state,
            ...d,
            decorationCost: fmt(d.decorationCost),
            servingCost: fmt(d.servingCost),
            dishwashingCost: fmt(d.dishwashingCost),
            total: fmt(d.total),
            reservationFee: fmt(d.reservationFee),
            balance: fmt(d.balance),
        };
        $$('[data-bind]').forEach((node) => {
            node.textContent = values[node.dataset.bind];
        });

        // Selection states
        $$('[data-occasion]').forEach((pill) => pill.classList.toggle('selected', pill.dataset.occasion === state.occasion));
        $$('[data-day]').forEach((day) => day.classList.toggle('selected', Number(day.dataset.day) === state.date));
        $$('[data-guests-preset]').forEach((pill) => pill.classList.toggle('active', Number(pill.dataset.guestsPreset) === state.guests));
        $$('[data-theme]').forEach((pill) => pill.classList.toggle('active', pill.dataset.theme === state.theme));

        // Service cards: checkbox, highlight and the detail row only while selected
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
            note.hidden = !state.notes;
        });

        $('[data-requires-services]').disabled = !d.hasServices;
        $('[data-submit-booking]').disabled = !state.agreed;

        $$('[data-payment]').forEach((radio) => {
            radio.checked = radio.value === state.payment;
            radio.closest('label').classList.toggle('selected', radio.checked);
        });
    }

    const update = (patch) => {
        Object.assign(state, patch);
        render();
    };

    root.addEventListener('click', (event) => {
        const t = event.target;
        const button = (selector) => t.closest(selector);

        if (button('[data-occasion]')) update({ occasion: button('[data-occasion]').dataset.occasion });
        if (button('[data-day]')) update({ date: Number(button('[data-day]').dataset.day) });
        if (button('[data-guests-step]')) update({ guests: Math.max(10, state.guests + Number(button('[data-guests-step]').dataset.guestsStep)) });
        if (button('[data-guests-preset]')) update({ guests: Number(button('[data-guests-preset]').dataset.guestsPreset) });
        if (button('[data-theme]')) update({ theme: button('[data-theme]').dataset.theme });

        if (button('[data-qty-step]')) {
            const stepper = button('[data-qty-step]');
            const key = stepper.dataset.qtyKey;
            update({ [key]: Math.max(0, state[key] + Number(stepper.dataset.qtyStep)) });
        }

        if (button('[data-full-package]')) {
            update({ decoration: true, serving: Math.max(state.serving, 8), dishwashing: Math.max(state.dishwashing, 4) });
        }

        if (button('[data-change-contact]')) {
            const nextName = window.prompt('Contact name', state.customerName);
            const nextNumber = window.prompt('Contact number', state.contactNumber);
            update({
                customerName: nextName || state.customerName,
                contactNumber: nextNumber || state.contactNumber,
            });
        }

        const goStep = button('[data-go-step]');
        if (goStep && !goStep.disabled) update({ step: Number(goStep.dataset.goStep) });

        const pay = button('[data-submit-booking]');
        if (pay && !pay.disabled && state.agreed) {
            render();
            root.hidden = true;
            success.hidden = false;
        }
    });

    root.addEventListener('change', (event) => {
        const t = event.target;
        if (t.matches('[data-service-toggle]')) {
            const key = t.dataset.serviceToggle;
            if (key === 'decoration') update({ decoration: !state.decoration });
            if (key === 'serving') update({ serving: state.serving > 0 ? 0 : 8 });
            if (key === 'dishwashing') update({ dishwashing: state.dishwashing > 0 ? 0 : 4 });
        }
        if (t.matches('[data-payment]')) update({ payment: t.value });
        if (t.matches('[data-terms]')) update({ agreed: t.checked });
    });

    root.addEventListener('input', (event) => {
        const t = event.target;
        if (t.matches('[data-input]')) update({ [t.dataset.input]: t.value });
    });

    $('[data-venue-select]').addEventListener('select:change', (event) => update({ venue: event.detail.value }));

    render();
}

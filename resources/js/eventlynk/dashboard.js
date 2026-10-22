/**
 * Dashboard behaviour shared by the customer, vendor, admin and super admin
 * portals, plus the small per-portal interactions the React components held
 * in state. Static phase: changes live in the page only (no persistence).
 */
import { closeModal, showToast } from './core';

const DESKTOP = window.matchMedia('(min-width: 768px)');

let palettes = {};

/** Restyle a [data-status-badge] pill for a new status. */
function paintBadge(badge, status) {
    const colors = palettes[badge.dataset.statusBadge]?.[status] ?? { bg: '#f5f5f5', color: '#555' };
    badge.dataset.status = status;
    badge.style.background = colors.bg;
    badge.style.color = colors.color;
    badge.textContent = status;
}

/* ------------------------------------------------------------------ */
/* Sidebar + tabs                                                      */
/* ------------------------------------------------------------------ */

function initSidebar(root) {
    const sidebar = root.querySelector('[data-sidebar]');
    if (!sidebar) return;
    const classes = sidebar.dataset.openClasses.split(' ');
    const setOpen = (open) => classes.forEach((name) => sidebar.classList.toggle(name, open));

    setOpen(DESKTOP.matches);
    DESKTOP.addEventListener('change', (event) => setOpen(event.matches));
    sidebar.querySelector('[data-sidebar-toggle]').addEventListener('click', () => {
        setOpen(!sidebar.classList.contains('open'));
    });
}

function initTabs(root) {
    const main = root.querySelector('[data-dashboard-main]');
    const title = root.querySelector('[data-dashboard-title]');
    const navButtons = [...root.querySelectorAll('[data-tab]')];
    const defaultPadding = main.style.padding;

    function activate(id, { updateHash = true } = {}) {
        const nav = navButtons.find((button) => button.dataset.tab === id);
        if (!nav) return;

        navButtons.forEach((button) => button.classList.toggle('active', button === nav));
        title.textContent = nav.dataset.tabLabel;

        let activePanel = null;
        root.querySelectorAll('[data-panel]').forEach((panel) => {
            const show = panel.dataset.panel === id && !panel.hasAttribute('data-detail');
            panel.hidden = !show;
            if (show) activePanel = panel;
        });
        root.querySelectorAll('[data-show-on-tab]').forEach((node) => {
            node.hidden = node.dataset.showOnTab !== id;
        });
        main.style.padding = activePanel?.dataset.mainPadding ?? defaultPadding;
        main.scrollTop = 0;
        root.dataset.activeTab = id;

        if (updateHash) history.replaceState(null, '', `#${id}`);
    }

    root.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-tab], [data-tab-link]');
        if (trigger) activate(trigger.dataset.tab || trigger.dataset.tabLink);
    });

    const initial = window.location.hash.slice(1);
    activate(navButtons.some((b) => b.dataset.tab === initial) ? initial : navButtons.find((b) => b.classList.contains('active')).dataset.tab, {
        updateHash: false,
    });

}

/* ------------------------------------------------------------------ */
/* Generic widgets                                                     */
/* ------------------------------------------------------------------ */

function initFilters(root) {
    root.querySelectorAll('[data-filter-group]').forEach((group) => {
        const name = group.dataset.filterGroup;
        group.addEventListener('click', (event) => {
            const pill = event.target.closest('[data-filter-value]');
            if (!pill) return;
            group.querySelectorAll('[data-filter-value]').forEach((other) => other.classList.toggle('active', other === pill));
            group.dataset.active = pill.dataset.filterValue;
            applyFilter(root, name);
        });
    });
}

function applyFilter(root, name) {
    const group = root.querySelector(`[data-filter-group="${name}"]`);
    const value = group?.dataset.active ?? 'All';
    root.querySelectorAll(`[data-filter-item="${name}"]`).forEach((item) => {
        item.hidden = value !== 'All' && item.dataset.filterKey !== value;
    });
}

function initSwitches(root) {
    root.querySelectorAll('[data-switch]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const on = toggle.getAttribute('aria-checked') !== 'true';
            toggle.setAttribute('aria-checked', String(on));
            toggle.style.background = on ? '#c9a84c' : '#ddd5c8';
            toggle.querySelector('span').style.left = on ? '22px' : '3px';
        });
    });
}

function initFlash(root) {
    root.querySelectorAll('[data-flash-target]').forEach((button) => {
        const target = root.querySelector(`#${button.dataset.flashTarget}`);
        let timer;
        button.addEventListener('click', () => {
            target.hidden = false;
            clearTimeout(timer);
            timer = setTimeout(() => {
                target.hidden = true;
            }, 2500);
        });
    });
}

/* ------------------------------------------------------------------ */
/* Customer                                                            */
/* ------------------------------------------------------------------ */

function initCustomer(root) {
    // Mark all notifications as read
    root.querySelector('[data-mark-all-read]')?.addEventListener('click', () => {
        root.querySelectorAll('[data-notification]').forEach((item) => {
            item.style.background = '#fff';
        });
        root.querySelectorAll('[data-unread-dot]').forEach((dot) => {
            // The overview list keeps a spacer where the dot was.
            if (dot.parentElement.closest('[data-panel="overview"]')) {
                dot.removeAttribute('style');
                dot.style.width = '7px';
                dot.style.flexShrink = '0';
            } else {
                dot.remove();
            }
            dot.removeAttribute('data-unread-dot');
        });
        root.querySelectorAll('[data-badge="unread"]').forEach((badge) => badge.remove());
    });

    // FAQ accordion
    root.querySelectorAll('[data-faq]').forEach((faq) => {
        const toggle = faq.querySelector('[data-faq-toggle]');
        toggle.addEventListener('click', () => {
            const open = toggle.getAttribute('aria-expanded') !== 'true';
            toggle.setAttribute('aria-expanded', String(open));
            faq.querySelector('[data-faq-answer]').hidden = !open;
            faq.querySelector('[data-faq-icon]').style.transform = open ? 'rotate(45deg)' : 'none';
        });
    });

    // Star ratings
    root.querySelectorAll('[data-review-card]').forEach((card) => {
        const stars = [...card.querySelectorAll('[data-star]')];
        const submit = card.querySelector('[data-review-submit]');
        let rating = 0;

        const paint = (upTo) => stars.forEach((star) => star.classList.toggle('is-lit', Number(star.dataset.star) <= upTo));

        stars.forEach((star) => {
            const value = Number(star.dataset.star);
            star.addEventListener('mouseenter', () => paint(value));
            star.addEventListener('mouseleave', () => paint(rating));
            star.addEventListener('click', () => {
                rating = value;
                paint(rating);
                submit.style.background = '#1a110a';
                submit.style.cursor = 'pointer';
            });
        });

        submit.addEventListener('click', () => {
            if (rating === 0) return;
            const text = card.querySelector('[data-review-text]').value;
            card.querySelector('[data-review-form]').hidden = true;
            card.querySelector('[data-review-done]').hidden = false;
            card.querySelector('[data-review-stars]').textContent = '★'.repeat(rating);
            card.querySelector('[data-review-quote]').textContent = text || 'No written review.';
            card.querySelector('[data-review-result]').hidden = false;
        });
    });
}

/* ------------------------------------------------------------------ */
/* Vendor                                                              */
/* ------------------------------------------------------------------ */

function initVendor(root) {
    // Pause / activate services
    root.querySelectorAll('[data-toggle-service]').forEach((button) => {
        button.addEventListener('click', () => {
            const card = button.closest('[data-service]');
            const badge = card.querySelector('[data-service-status]');
            const wasActive = badge.dataset.status === 'Active';
            paintBadge(badge, wasActive ? 'Paused' : 'Active');
            button.textContent = wasActive ? 'Activate' : 'Pause';
            showToast(`${card.querySelector('[data-service-name]').textContent} ${wasActive ? 'paused' : 'activated'}`);
        });
    });

    // Edit service modal
    root.querySelectorAll('[data-edit-service]').forEach((modal) => {
        modal.querySelector('[data-save-service]').addEventListener('click', () => {
            const card = root.querySelector(`[data-service="${modal.dataset.editService}"]`);
            card.querySelector('[data-service-name]').textContent = modal.querySelector('[data-edit-name]').value;
            card.querySelector('[data-service-price]').textContent = modal.querySelector('[data-edit-price]').value;
            showToast('Service updated');
            closeModal(modal);
        });
    });

    // Staff assignment chips
    root.querySelectorAll('[data-staff-booking]').forEach((booking) => {
        const id = booking.dataset.staffBooking;
        const count = booking.querySelector('[data-staff-count]');
        booking.querySelectorAll('[data-assign-staff]').forEach((chip) => {
            chip.addEventListener('click', () => {
                const assigned = chip.getAttribute('aria-pressed') !== 'true';
                const name = chip.dataset.staffName;
                chip.setAttribute('aria-pressed', String(assigned));
                Object.assign(chip.style, {
                    borderColor: assigned ? '#c9a84c' : '#e8dfd0',
                    background: assigned ? '#1a110a' : '#faf8f4',
                    color: assigned ? '#c9a84c' : '#6b5a47',
                    fontWeight: assigned ? '600' : '400',
                });
                chip.textContent = `${assigned ? '✓ ' : ''}${name}`;

                const total = booking.querySelectorAll('[data-assign-staff][aria-pressed="true"]').length;
                count.hidden = total === 0;
                count.textContent = `${total} staff assigned`;
                showToast(`${name} ${assigned ? 'assigned to' : 'removed from'} ${id}`);
            });
        });
    });

    // Remove inventory rows
    root.querySelectorAll('[data-remove-row]').forEach((button) => {
        button.addEventListener('click', () => {
            showToast(button.dataset.removeRow);
            button.closest('tr').remove();
        });
    });

    // Availability calendar: block / unblock free days
    root.querySelectorAll('[data-availability-day]').forEach((day) => {
        day.addEventListener('click', () => {
            if (day.dataset.state === 'booked') return;
            const block = day.dataset.state !== 'blocked';
            day.dataset.state = block ? 'blocked' : 'available';
            day.style.background = block ? '#fce4ec' : '#e6f4ea';
            day.style.color = block ? '#c62828' : '#2e7d32';
            day.title = block ? 'Click to unblock' : 'Click to block';
            showToast(`Day ${day.dataset.availabilityDay} ${block ? 'blocked' : 'unblocked'}`);
        });
    });
}

/* ------------------------------------------------------------------ */
/* Admin                                                               */
/* ------------------------------------------------------------------ */

function initAdmin(root) {
    const data = JSON.parse(root.querySelector('[data-admin-bookings]').textContent);
    const peso = (value) => value.toLocaleString();

    const showList = () => {
        root.querySelectorAll('[data-detail]').forEach((detail) => {
            detail.hidden = true;
        });
        root.querySelector('[data-panel="bookings"]:not([data-detail])').hidden = false;
    };

    root.addEventListener('click', (event) => {
        const view = event.target.closest('[data-show-detail]');
        if (view) {
            root.querySelector('[data-panel="bookings"]:not([data-detail])').hidden = true;
            root.querySelector(`[data-detail="${view.dataset.showDetail}"]`).hidden = false;
            root.querySelector('[data-dashboard-main]').scrollTop = 0;
        }
        if (event.target.closest('[data-hide-detail]')) showList();
    });

    // Confirm / complete / cancel
    root.querySelectorAll('[data-set-status]').forEach((button) => {
        button.addEventListener('click', () => {
            const detail = button.closest('[data-detail]');
            const id = detail.dataset.detail;
            const status = button.dataset.setStatus;
            if (button.dataset.confirm && !window.confirm(button.dataset.confirm)) return;

            data[id].status = status;
            root.querySelectorAll(`[data-booking-status="${id}"]`).forEach((badge) => paintBadge(badge, status));
            root.querySelectorAll(`[data-filter-status-of="${id}"]`).forEach((row) => {
                row.dataset.filterKey = status;
            });
            applyFilter(root, 'admin-bookings');
            detail.querySelectorAll('[data-when-status]').forEach((action) => {
                action.hidden = !action.dataset.whenStatus.split(' ').includes(status);
            });
            showToast(`Booking ${id} marked as ${status}`);
            if (status === 'Cancelled') showList();
        });
    });

    // Record payment
    root.querySelectorAll('[data-record-payment]').forEach((button) => {
        button.addEventListener('click', () => {
            const id = button.dataset.recordPayment;
            const modal = button.closest('[data-modal]');
            const input = modal.querySelector('[data-payment-amount]');
            const amount = parseFloat(input.value) || 0;
            const booking = data[id];

            booking.paid += amount;
            booking.payment = booking.paid >= booking.total ? 'Paid' : 'Partial';

            root.querySelectorAll(`[data-fee="${id}"]`).forEach((node) => {
                node.textContent = peso(booking.paid);
            });
            root.querySelectorAll(`[data-balance="${id}"]`).forEach((node) => {
                node.textContent = peso(booking.total - booking.paid);
            });
            root.querySelectorAll(`[data-booking-payment="${id}"]`).forEach((badge) => paintBadge(badge, booking.payment));
            const colors = palettes.payment[booking.payment];
            root.querySelectorAll(`[data-pay-box="${id}"]`).forEach((box) => {
                box.style.background = colors.bg;
            });
            root.querySelectorAll(`[data-pay-text="${id}"]`).forEach((text) => {
                text.style.color = colors.color;
                text.textContent = `Payment Status: ${booking.payment.toUpperCase()}`;
            });

            closeModal(modal);
            input.value = 0;
            showToast(`Payment of ₱${amount.toLocaleString()} recorded for ${id}`);
        });
    });

    // The invoice list hides behind an open invoice while on the Invoices tab.
    root.addEventListener('modal:open', (event) => {
        const tab = event.target.dataset.hidesOnTab;
        if (tab && root.dataset.activeTab === tab) root.querySelector(`[data-panel="${tab}"]`).hidden = true;
    });
    root.addEventListener('modal:close', (event) => {
        const tab = event.target.dataset.hidesOnTab;
        if (tab && root.dataset.activeTab === tab) root.querySelector(`[data-panel="${tab}"]`).hidden = false;
    });
}

/* ------------------------------------------------------------------ */
/* Super admin                                                         */
/* ------------------------------------------------------------------ */

function initSuperAdmin(root) {
    // Vendor rows and dispute cards open their modal on click; their action
    // cells are marked data-stop-propagation, which core.js respects.

    // Approve / reject / suspend / reinstate
    root.addEventListener('click', (event) => {
        const button = event.target.closest('[data-vendor-set]');
        if (!button) return;
        const id = button.closest('[data-vendor-actions]').dataset.vendorActions;
        const status = button.dataset.vendorSet;

        root.querySelectorAll(`[data-vendor-status="${id}"]`).forEach((badge) => paintBadge(badge, status));
        root.querySelectorAll(`[data-vendor-row="${id}"]`).forEach((row) => {
            row.dataset.filterKey = status;
        });
        applyFilter(root, 'vendors');
        root.querySelectorAll(`[data-vendor-actions="${id}"] [data-when-vendor]`).forEach((action) => {
            action.hidden = action.dataset.whenVendor !== status;
        });
        showToast(`Vendor ${id} ${status.toLowerCase()}`);
    });

    // Activate / deactivate categories
    root.querySelectorAll('[data-toggle-category]').forEach((button) => {
        button.addEventListener('click', () => {
            const card = button.closest('[data-category]');
            const badge = card.querySelector('[data-category-status]');
            const next = badge.dataset.status === 'Active' ? 'Inactive' : 'Active';
            paintBadge(badge, next);
            button.textContent = next === 'Active' ? 'Deactivate' : 'Activate';
            button.style.borderColor = next === 'Active' ? '#e8dfd0' : '#c9a84c';
            button.style.color = next === 'Active' ? '#6b5a47' : '#c9a84c';
            showToast(`${card.querySelector('[data-category-name]').textContent} set to ${next}`);
        });
    });

    const setRate = (id, rate) => {
        root.querySelectorAll(`[data-category="${id}"] [data-category-rate]`).forEach((node) => {
            node.textContent = `${rate}%`;
        });
        root.querySelectorAll(`[data-commission-row="${id}"] [data-category-rate-value]`).forEach((node) => {
            node.textContent = rate;
        });
        root.querySelectorAll(`[data-commission-row="${id}"] [data-commission-input]`).forEach((input) => {
            input.value = rate;
        });
    };

    // Edit category modal
    root.querySelectorAll('[data-edit-category]').forEach((modal) => {
        modal.querySelector('[data-save-category]').addEventListener('click', () => {
            const id = modal.dataset.editCategory;
            const card = root.querySelector(`[data-category="${id}"]`);
            const name = modal.querySelector('[data-edit-name]').value;
            const rate = parseFloat(modal.querySelector('[data-edit-rate]').value);
            card.querySelector('[data-category-name]').textContent = name;
            card.querySelector('[data-category-icon]').textContent = modal.querySelector('[data-edit-icon]').value;
            if (!Number.isNaN(rate)) setRate(id, rate);
            showToast(`${name} updated`);
            closeModal(modal);
        });
    });

    // Inline commission editing
    root.querySelectorAll('[data-commission-row]').forEach((row) => {
        const editor = row.querySelector('[data-commission-editor]');
        const edit = row.querySelector('[data-commission-edit]');
        const input = row.querySelector('[data-commission-input]');
        const close = () => {
            editor.hidden = true;
            edit.hidden = false;
        };
        edit.addEventListener('click', () => {
            editor.hidden = false;
            edit.hidden = true;
            input.value = row.querySelector('[data-category-rate-value]').textContent;
            input.focus();
        });
        row.querySelector('[data-commission-save]').addEventListener('click', () => {
            const rate = parseFloat(input.value);
            if (!Number.isNaN(rate) && rate >= 0 && rate <= 100) {
                setRate(row.dataset.commissionRow, rate);
                showToast('Commission rate updated');
            }
            close();
        });
        row.querySelector('[data-commission-cancel]').addEventListener('click', close);
    });

    // Dispute workflow
    root.addEventListener('click', (event) => {
        const button = event.target.closest('[data-dispute-set]');
        if (!button) return;
        const id = button.dataset.disputeId || button.closest('[data-dispute]').dataset.dispute;
        const status = button.dataset.disputeSet;

        root.querySelectorAll(`[data-dispute-status="${id}"]`).forEach((badge) => paintBadge(badge, status));
        const card = root.querySelector(`[data-dispute="${id}"]`);
        card.dataset.filterKey = status;
        applyFilter(root, 'disputes');
        card.querySelector('[data-dispute-actions]').hidden = !['Open', 'In Review'].includes(status);
        card.querySelector('[data-dispute-set="In Review"]').hidden = status !== 'Open';
        showToast(`Dispute ${id} marked as ${status}`);
    });
}

/* ------------------------------------------------------------------ */

export function initDashboard() {
    const root = document.querySelector('[data-dashboard]');
    if (!root) return;

    palettes = JSON.parse(root.querySelector('[data-palettes]').textContent);

    initSidebar(root);
    initTabs(root);
    initFilters(root);
    initSwitches(root);
    initFlash(root);

    switch (root.dataset.dashboard) {
        case 'customer':
            initCustomer(root);
            break;
        case 'vendor':
            initVendor(root);
            break;
        case 'admin':
            initAdmin(root);
            break;
        case 'superadmin':
            initSuperAdmin(root);
            break;
    }
}

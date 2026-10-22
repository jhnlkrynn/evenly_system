const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;

export function initNotifications() {
    const center = document.querySelector('[data-notification-center]');
    if (!center) return;
    const count = center.querySelector('[data-notification-count]');
    const refresh = () => fetch('/notifications/unread-count', { headers: { Accept: 'application/json' } })
        .then((response) => response.json()).then((data) => { count.textContent = data.count; count.hidden = data.count < 1; }).catch(() => {});
    center.addEventListener('click', (event) => {
        const link = event.target.closest('[data-notification-link]');
        if (!link) return;
        event.preventDefault();
        fetch(`/notifications/${link.dataset.notificationLink}/read`, { method: 'PATCH', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf() } })
            .then((response) => response.json()).then((data) => { window.location.href = data.url; });
    });
    window.setInterval(refresh, 30000);
}

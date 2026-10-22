@auth
@php($inNavbar = $inNavbar ?? false)
<div class="dropdown" data-notification-center style="{{ $inNavbar ? '' : 'position:fixed;right:24px;top:14px;z-index:1050;' }}">
    <button class="btn btn-light position-relative rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
        <span style="font-size:18px">🔔</span><span data-notification-count class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" @if(auth()->user()->unreadNotifications()->count() === 0) hidden @endif>{{ auth()->user()->unreadNotifications()->count() }}</span>
    </button>
    <div class="dropdown-menu dropdown-menu-end shadow p-0" style="min-width:340px;max-width:calc(100vw - 32px)">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom"><strong>Notifications</strong><a href="{{ route('notifications.index') }}" class="small">View all</a></div>
        <div data-notification-list>
            @forelse(auth()->user()->notifications()->latest()->limit(5)->get() as $notification)
                <a href="{{ data_get($notification->data, 'url', route('notifications.index')) }}" data-notification-link="{{ $notification->id }}" class="dropdown-item text-wrap py-2 {{ $notification->read_at ? '' : 'bg-warning-subtle' }}"><span>{{ data_get($notification->data, 'icon', '🔔') }}</span> {{ data_get($notification->data, 'message', 'Notification') }}<small class="d-block text-muted">{{ $notification->created_at->diffForHumans() }}</small></a>
            @empty
                <div class="p-3 text-muted small">No notifications yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endauth

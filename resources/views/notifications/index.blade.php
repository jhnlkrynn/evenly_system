@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width:780px">
    <div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h3 mb-0">Notifications</h1><form method="POST" action="{{ route('notifications.read-all') }}">@csrf @method('PATCH')<button class="btn btn-outline-dark" type="submit">Mark all as read</button></form></div>
    <div class="list-group shadow-sm">
        @forelse($notifications as $notification)
            <a href="{{ route('notifications.read', $notification->id) }}" class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'bg-warning-subtle' }}"><div class="d-flex gap-3"><span class="fs-4">{{ data_get($notification->data, 'icon', '🔔') }}</span><div><div>{{ data_get($notification->data, 'message', 'Notification') }}</div><small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small></div></div></a>
        @empty <div class="list-group-item text-muted">No notifications yet.</div> @endforelse
    </div>
    <div class="mt-3">{{ $notifications->links() }}</div>
</div>
@endsection

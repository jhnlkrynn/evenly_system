{{-- Signed-in user's name with a Dashboard / Profile / Log out dropdown (Bootstrap dropdown JS). --}}
@php($user = auth()->user())

<div class="user-menu dropdown" {{ $attributes }}>
    <button type="button" class="user-menu-toggle" data-bs-toggle="dropdown" data-bs-offset="0,10" aria-expanded="false">
        <span class="user-menu-avatar" aria-hidden="true">{{ $user->initials() }}</span>
        <span class="user-menu-name">{{ $user->name }}</span>
        <span class="user-menu-caret" aria-hidden="true">▾</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end user-menu-list">
        <li class="user-menu-header">
            <strong>{{ $user->name }}</strong>
            <small>{{ $user->email }}</small>
        </li>
        <li><a class="user-menu-item" href="{{ $user->dashboardUrl() }}">Dashboard</a></li>
        <li><a class="user-menu-item" href="{{ route('profile.edit') }}">Profile</a></li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="user-menu-item">Log out</button>
            </form>
        </li>
    </ul>
</div>

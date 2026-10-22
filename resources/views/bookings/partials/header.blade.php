<header class="booking-header">
    <div>
        <x-brand class="booking-brand" />
        <div class="account-header-actions">
            <button type="button" data-href="{{ route('services.index') }}">Browse services</button>
            @auth
                <x-user-menu />
            @else
                <button type="button" data-href="{{ route('login') }}">Log in</button>
            @endauth
        </div>
    </div>
</header>

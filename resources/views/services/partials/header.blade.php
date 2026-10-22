{{-- Dark Browse header (BrowseServicesPage.tsx) shared by the listing and detail pages. --}}
<header class="browse-clean-header">
    <div class="browse-clean-nav">
        <x-brand class="browse-brand" />
        <nav>
            <button type="button" data-href="{{ route('home') }}">Home</button>
            <button type="button" class="active" data-href="{{ route('services.index') }}">Browse Services</button>
            <button type="button" data-open-modal="browse-how">How It Works</button>
            <button type="button" data-open-modal="browse-about">About Us</button>
        </nav>
        <div>
            @auth
                <x-user-menu />
            @else
                <button type="button" class="browse-login" data-href="{{ route('login') }}">Log in</button>
                <button type="button" class="browse-signup" data-href="{{ route('register') }}">Sign up</button>
            @endauth
        </div>
    </div>
</header>

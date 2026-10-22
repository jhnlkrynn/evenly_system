@extends('layouts.base')

@section('body')
@php($steps ??= \App\Support\MockData::STEPS)
<div class="landing-page" data-landing>
    <header class="landing-header">
        <div class="landing-container landing-nav">
            <button type="button" class="logo-button" data-scroll-top aria-label="Go to homepage">
                <x-logo />
            </button>
            <nav class="landing-links" data-landing-menu aria-label="Main navigation">
                <button type="button" class="active" data-scroll-top data-close-menu>Home</button>
                <button type="button" data-href="{{ route('services.index') }}">Browse Services</button>
                <button type="button" data-open-modal="landing-how" data-close-menu>How It Works</button>
                <button type="button" data-open-modal="landing-about" data-close-menu>About Us</button>
                @auth
                    <button type="button" class="mobile-only" data-href="{{ auth()->user()->dashboardUrl() }}">Dashboard</button>
                    <button type="button" class="mobile-only" data-href="{{ route('profile.edit') }}">Profile</button>
                    <form method="POST" action="{{ route('logout') }}" class="mobile-only">
                        @csrf
                        <button type="submit">Log out</button>
                    </form>
                @else
                    <button type="button" class="mobile-only" data-href="{{ route('login') }}">Log in</button>
                    <button type="button" class="mobile-only" data-href="{{ route('register') }}">Sign up free</button>
                @endauth
            </nav>
            <div class="landing-auth">
                @auth
                    <x-user-menu />
                @else
                    <button type="button" class="text-button" data-href="{{ route('login') }}">Log in</button>
                    <button type="button" class="gold-button small" data-href="{{ route('register') }}">Sign up free</button>
                @endauth
            </div>
            <button type="button" class="menu-button" data-menu-toggle aria-label="Toggle navigation" aria-expanded="false">
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="landing-footer">
        <div class="landing-container footer-grid">
            <div>
                <x-logo />
                <p>A multi-vendor, on-demand booking platform for event services, with real-time management and analytics.</p>
            </div>
            <div>
                <h3>Explore</h3>
                <button type="button" data-href="{{ route('services.index') }}">Browse services</button>
                <button type="button" data-open-modal="landing-how">How it works</button>
                <button type="button" data-open-modal="landing-about">About us</button>
            </div>
            <div>
                <h3>Vendors</h3>
                <button type="button" data-href="{{ route('register', ['role' => 'vendor']) }}">Register as vendor</button>
                <button type="button" data-href="{{ route('vendor.dashboard') }}">Vendor dashboard</button>
                <button type="button" data-href="{{ route('login') }}">Help center</button>
            </div>
            <div>
                <h3>Legal</h3>
                <button type="button">Terms of service</button>
                <button type="button">Privacy policy</button>
                <button type="button">Refund policy</button>
            </div>
        </div>
        <div class="landing-container footer-bottom">
            <span>© 2026 Eventlynk. All rights reserved.</span>
            <span>Nasugbu, Batangas · Customer · Vendor · Admin</span>
        </div>
    </footer>

    {{-- How It Works modal --}}
    <div class="landing-modal" id="landing-how" data-modal hidden>
        <div role="dialog" aria-modal="true" aria-labelledby="landing-how-title">
            <button type="button" class="modal-close" data-modal-close aria-label="Close">×</button>
            <p class="eyebrow">A simpler way to book</p>
            <h2 id="landing-how-title">How Eventlynk works</h2>
            <div class="modal-steps">
                @foreach ($steps as $step)
                    <p>
                        <strong>{{ $step['number'] }} · {{ $step['title'] }}</strong>
                        <span>{{ $step['text'] }}</span>
                    </p>
                @endforeach
            </div>
            <button type="button" class="gold-button" data-href="{{ route('services.index') }}">Browse services</button>
        </div>
    </div>

    {{-- About modal --}}
    <div class="landing-modal" id="landing-about" data-modal hidden>
        <div role="dialog" aria-modal="true" aria-labelledby="landing-about-title">
            <button type="button" class="modal-close" data-modal-close aria-label="Close">×</button>
            <p class="eyebrow">Eventlynk</p>
            <h2 id="landing-about-title">Events feel easier when everything is connected</h2>
            <p class="modal-copy">
                Eventlynk connects customers with trusted event-service vendors
                in Nasugbu, Batangas. Our platform brings discovery, transparent
                packages, booking management, and vendor analytics into one
                calm, coordinated experience.
            </p>
            <button type="button" class="gold-button" data-href="{{ route('register') }}">Join Eventlynk</button>
        </div>
    </div>
</div>
@endsection

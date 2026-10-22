@extends('layouts.base')

@section('body')
<div class="account-page">
    <header class="booking-header">
        <div>
            <x-brand class="booking-brand" />
            @auth
                <div class="account-header-actions">
                    <button type="button" data-href="{{ auth()->user()->dashboardUrl() }}">Back to dashboard</button>
                    <x-user-menu />
                </div>
            @endauth
        </div>
    </header>

    <main class="account-shell">
        @yield('content')
    </main>

    @include('bookings.partials.footer')
</div>
@endsection

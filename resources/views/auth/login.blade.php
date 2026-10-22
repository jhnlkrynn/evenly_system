@extends('layouts.auth')

@section('title', 'Log in · Eventlynk')

@section('story')
<section class="auth-story login-story">
    <x-brand class="auth-brand" aria-label="Back to Eventlynk home" />
    <div class="auth-photo-frame">
        <img src="{{ asset('images/image-4.png') }}" alt="Elegant event reception decoration">
    </div>
    <div class="auth-story-copy">
        <h2>Welcome back.<br><em>Your events are waiting.</em></h2>
        <p>
            Pick up where you left off — check booking status, message vendors,
            and confirm your next date.
        </p>
        <div class="auth-pills">
            <span>Real-time booking status</span>
            <span>Verified vendors</span>
            <span>Vendor analytics</span>
        </div>
    </div>
</section>
@endsection

@section('form')
<form class="auth-form" method="POST" action="{{ route('login') }}" novalidate>
    @csrf
    <button type="button" class="auth-back" data-href="{{ route('home') }}">
        ← <span>Back to home</span>
    </button>
    <div class="auth-heading">
        <h1>Log in</h1>
        <p>
            New to Eventlynk?
            <button type="button" data-href="{{ route('register') }}">Create an account</button>
        </p>
    </div>

    <div class="auth-social-grid login-socials">
        <button type="button" class="wide">Continue with Google</button>
        <button type="button">Facebook</button>
        <button type="button">Apple</button>
    </div>

    <div class="auth-divider">
        <span>or with email</span>
    </div>

    @if (session('status'))
        <div class="auth-message success" role="status">{{ session('status') }}</div>
    @endif

    <label class="auth-field">
        <span>Email</span>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required autofocus>
    </label>

    <label class="auth-field">
        <span class="field-label-row">
            Password
            @if (Route::has('password.request'))
                <button type="button" data-href="{{ route('password.request') }}">Forgot password?</button>
            @endif
        </span>
        <div class="password-field">
            <input type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
            <button type="button" data-toggle-password>Show</button>
        </div>
    </label>

    <div class="auth-form-row">
        <label class="check-label">
            <input type="checkbox" name="remember" value="1" @checked(old('remember', true))>
            <span>Keep me logged in</span>
        </label>
    </div>

    @include('auth.partials.errors')

    <button class="auth-submit" type="submit">Log in</button>
    <p class="auth-note">You’ll be taken to your dashboard — customers, vendors and admins each have their own.</p>
</form>
@endsection

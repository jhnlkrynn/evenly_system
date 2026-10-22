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
{{-- Static phase: the "Demo destination" select decides where the form goes (see resources/js/eventlynk/auth.js). --}}
@php
    $destinations = [
        'customer' => route('customer.dashboard'),
        'vendor' => route('vendor.dashboard'),
        'admin' => route('admin.dashboard'),
        'superadmin' => route('superadmin.dashboard'),
    ];
@endphp
<form class="auth-form" method="POST" action="{{ route('login') }}" data-demo-login data-destinations="{{ json_encode($destinations) }}">
    @csrf
    <button type="button" class="auth-back" data-href="{{ route('home') }}">
        ← <span>Back to<br>home</span>
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

    <label class="auth-field">
        <span>Email</span>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required>
    </label>

    <label class="auth-field">
        <span class="field-label-row">
            Password <button type="button">Forgot password?</button>
        </span>
        <div class="password-field">
            <input type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
            <button type="button" data-toggle-password>Show</button>
        </div>
    </label>

    <div class="auth-form-row">
        <label class="check-label">
            <input type="checkbox" name="remember" checked>
            <span>Keep me logged in</span>
        </label>
        <label class="role-select">
            <span>Demo destination</span>
            <select name="demo_role" data-demo-role>
                <option value="customer">Customer</option>
                <option value="vendor">Vendor</option>
                <option value="admin">Admin</option>
                <option value="superadmin">Super Admin</option>
            </select>
        </label>
    </div>

    <button class="auth-submit" type="submit">Log in</button>
    <p class="auth-note">We’ll take you to the selected dashboard for this interactive mockup.</p>
</form>
@endsection

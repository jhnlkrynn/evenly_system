@extends('layouts.auth')

@section('title', 'Create your account · Eventlynk')
@section('page_class', 'signup-page')
@section('panel_class', 'signup-form-panel')

@php($role = request('role') === 'vendor' ? 'vendor' : 'customer')

@section('story')
    @include('auth.partials.signup-story')
@endsection

@section('form')
{{-- Static phase: validation and the success message run client-side (resources/js/eventlynk/auth.js). --}}
<form class="auth-form signup-form" method="POST" action="{{ route('register') }}" data-demo-signup data-login-url="{{ route('login') }}">
    @csrf
    <button type="button" class="auth-back" data-href="{{ route('home') }}">
        ← <span>Back to<br>home</span>
    </button>
    <div class="auth-heading">
        <h1>Create your account</h1>
        <p>
            Already have one?
            <button type="button" data-href="{{ route('login') }}">Log in</button>
        </p>
    </div>

    <fieldset class="account-choice" data-account-choice>
        <legend>I want to</legend>
        <label class="{{ $role === 'customer' ? 'selected' : '' }}">
            <input type="radio" name="role" value="customer" @checked($role === 'customer')>
            <span>
                <strong>Book services</strong>
                <small>For my own events</small>
            </span>
        </label>
        <label class="{{ $role === 'vendor' ? 'selected' : '' }}">
            <input type="radio" name="role" value="vendor" @checked($role === 'vendor')>
            <span>
                <strong>Offer services</strong>
                <small>List my business as a vendor</small>
            </span>
        </label>
    </fieldset>

    <div class="auth-social-grid signup-socials">
        <button type="button">Sign up with Google</button>
        <button type="button">Facebook</button>
        <button type="button">Apple</button>
    </div>
    <div class="auth-divider">
        <span>or with email</span>
    </div>

    <label class="auth-field">
        <span>Full name</span>
        <input name="name" value="{{ old('name') }}" placeholder="Juan dela Cruz" autocomplete="name" required>
    </label>
    <div class="signup-field-row">
        <label class="auth-field">
            <span>Email</span>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required>
        </label>
        <label class="auth-field">
            <span>Mobile number</span>
            <div class="phone-field">
                <span>+63</span>
                <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="917 123 4567" autocomplete="tel-national" required>
            </div>
        </label>
    </div>
    <label class="auth-field">
        <span>Password</span>
        <div class="password-field">
            <input type="password" name="password" placeholder="At least 8 characters" autocomplete="new-password" required data-strength-input>
            <button type="button" data-toggle-password>Show</button>
        </div>
    </label>
    <div class="password-strength" data-strength-meter aria-label="Password strength 0 out of 4">
        <span></span><span></span><span></span><span></span>
    </div>
    <p class="strength-copy">
        <strong data-strength-label>Keep going</strong>
        · Use 8+ characters with a number and symbol.
    </p>

    <label class="check-label terms-check">
        <input type="checkbox" name="terms" data-terms>
        <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</span>
    </label>
    <div class="auth-message error" data-signup-error hidden></div>
    <div class="auth-message success" data-signup-success hidden>Account created. Redirecting to login…</div>
    <button class="auth-submit" type="submit" data-signup-submit>Create account</button>
    <p class="auth-note" data-role-note
        data-note-customer="Your account is ready for browsing and booking services."
        data-note-vendor="We’ll ask for your business details on the next step.">
        {{ $role === 'vendor' ? 'We’ll ask for your business details on the next step.' : 'Your account is ready for browsing and booking services.' }}
    </p>
</form>
@endsection

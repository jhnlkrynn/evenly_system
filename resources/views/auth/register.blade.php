@extends('layouts.auth')

@section('title', 'Create your account · Eventlynk')
@section('page_class', 'signup-page')
@section('panel_class', 'signup-form-panel')

@section('story')
    @include('auth.partials.signup-story')
@endsection

@section('form')
<form class="auth-form signup-form" method="POST" action="{{ route('register') }}" data-signup novalidate>
    @csrf
    <button type="button" class="auth-back" data-href="{{ route('home') }}">
        ← <span>Back to home</span>
    </button>
    <div class="auth-heading">
        <h1>Create your account</h1>
        <p>
            Already have one?
            <button type="button" data-href="{{ route('login') }}">Log in</button>
        </p>
    </div>

    <fieldset class="account-choice">
        <legend>I want to sign up as</legend>
        <div style="grid-column: 1 / -1;">
            <x-role-selector :roles="$roles" :value="$role" />
        </div>
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
        <input name="name" value="{{ old('name') }}" placeholder="Juan dela Cruz" autocomplete="name" maxlength="255" required>
    </label>
    <div class="signup-field-row">
        <label class="auth-field">
            <span>Email</span>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required>
        </label>
        <label class="auth-field">
            <span>Mobile number</span>
            <div class="phone-field">
                <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="09XX XXX XXXX" autocomplete="tel-national" inputmode="numeric" required>
            </div>
        </label>
    </div>
    <label class="auth-field">
        <span>Password</span>
        <div class="password-field">
            <input type="password" name="password" placeholder="At least 8 characters" autocomplete="new-password" minlength="8" required data-strength-input>
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
    <label class="auth-field">
        <span>Confirm password</span>
        <div class="password-field">
            <input type="password" name="password_confirmation" placeholder="Type your password again" autocomplete="new-password" required>
            <button type="button" data-toggle-password>Show</button>
        </div>
    </label>

    <label class="check-label terms-check">
        <input type="checkbox" name="terms" value="1" @checked(old('terms'))>
        <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</span>
    </label>

    @include('auth.partials.errors')

    <button class="auth-submit" type="submit">Create account</button>
    <p class="auth-note" data-role-note
        data-note-customer="Your account is ready for browsing and booking services."
        data-note-vendor="Your vendor account will be reviewed by Eventlynk before it appears to customers.">
        {{ $role === 'vendor' ? 'Your vendor account will be reviewed by Eventlynk before it appears to customers.' : 'Your account is ready for browsing and booking services.' }}
    </p>
</form>
@endsection

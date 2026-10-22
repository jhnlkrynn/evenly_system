@extends('layouts.auth')

@section('title', 'Create a new password · Eventlynk')

@section('story')
<section class="auth-story password-story">
    <x-brand class="auth-brand" aria-label="Back to Eventlynk home" />
    <div class="auth-photo-frame">
        <img src="{{ asset('images/image-2.png') }}" alt="Warmly lit event venue prepared for guests">
    </div>
    <div class="auth-story-copy">
        <p class="eyebrow">Almost there</p>
        <h2>Keep your plans<br><em>beautifully secure.</em></h2>
        <p>Choose a fresh password, then get back to discovering the right vendors for your next event.</p>
    </div>
</section>
@endsection

@section('form')
<form class="auth-form password-form" method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <button type="button" class="auth-back" data-href="{{ route('login') }}">
        ← <span>Back to log in</span>
    </button>

    <div class="auth-heading">
        <p class="eyebrow">Secure your account</p>
        <h1>Create a new password</h1>
        <p>Use a strong password you don’t use anywhere else.</p>
    </div>

    @include('auth.partials.errors')

    <label class="auth-field">
        <span>Email address</span>
        <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" placeholder="you@example.com" autocomplete="email" required autofocus>
    </label>

    <label class="auth-field">
        <span>Password</span>
        <div class="password-field">
            <input id="password" type="password" name="password" placeholder="Create a password" autocomplete="new-password" required>
            <button type="button" data-toggle-password>Show</button>
        </div>
    </label>

    <label class="auth-field">
        <span>Confirm password</span>
        <div class="password-field">
            <input id="password-confirm" type="password" name="password_confirmation" placeholder="Repeat your password" autocomplete="new-password" required>
            <button type="button" data-toggle-password>Show</button>
        </div>
    </label>

    <button class="auth-submit" type="submit">Reset password <span>↗</span></button>
</form>
@endsection

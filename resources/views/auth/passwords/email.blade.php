@extends('layouts.auth')

@section('title', 'Reset your password · Eventlynk')

@section('story')
<section class="auth-story password-story">
    <x-brand class="auth-brand" aria-label="Back to Eventlynk home" />
    <div class="auth-photo-frame">
        <img src="{{ asset('images/image-4.png') }}" alt="Elegant event reception decoration">
    </div>
    <div class="auth-story-copy">
        <p class="eyebrow">A little reset</p>
        <h2>Make room for<br><em>new memories.</em></h2>
        <p>We’ll help you get back to planning beautiful celebrations with the people who matter most.</p>
        <div class="auth-pills">
            <span>Secure account recovery</span>
            <span>Verified Eventlynk access</span>
        </div>
    </div>
</section>
@endsection

@section('form')
<form class="auth-form password-form" method="POST" action="{{ route('password.email') }}">
    @csrf

    <button type="button" class="auth-back" data-href="{{ route('login') }}">
        ← <span>Back to log in</span>
    </button>

    <div class="auth-heading">
        <p class="eyebrow">Account recovery</p>
        <h1>Reset your password</h1>
        <p>Enter the email connected to your Eventlynk account and we’ll send you a secure reset link.</p>
    </div>

    @if (session('status'))
        <div class="auth-message success" role="status">{{ session('status') }}</div>
    @endif

    @include('auth.partials.errors')

    <label class="auth-field">
        <span>Email address</span>
        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required autofocus>
    </label>

    <button class="auth-submit" type="submit">Send reset link <span>↗</span></button>
    <p class="auth-note">The link will expire for your security. Check your spam folder if it doesn’t arrive.</p>
</form>
@endsection

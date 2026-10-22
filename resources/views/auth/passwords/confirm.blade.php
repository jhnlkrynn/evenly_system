@extends('layouts.auth')

@section('title', 'Confirm your password · Eventlynk')

@section('story')
<section class="auth-story password-story">
    <x-brand class="auth-brand" aria-label="Back to Eventlynk home" />
    <div class="auth-photo-frame">
        <img src="{{ asset('images/image-4.png') }}" alt="Elegant event reception decoration">
    </div>
    <div class="auth-story-copy">
        <p class="eyebrow">One last check</p>
        <h2>Your event details<br><em>deserve privacy.</em></h2>
        <p>Confirm your password to keep your Eventlynk account and celebrations protected.</p>
    </div>
</section>
@endsection

@section('form')
<form class="auth-form password-form" method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <button type="button" class="auth-back" data-href="{{ route('home') }}">
        ← <span>Back to home</span>
    </button>

    <div class="auth-heading">
        <p class="eyebrow">Protected area</p>
        <h1>Confirm your password</h1>
        <p>Please confirm your password before continuing to your account.</p>
    </div>

    @include('auth.partials.errors')

    <label class="auth-field">
        <span>Password</span>
        <div class="password-field">
            <input id="password" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required autofocus>
            <button type="button" data-toggle-password>Show</button>
        </div>
    </label>

    <button class="auth-submit" type="submit">Confirm password <span>↗</span></button>

    @if (Route::has('password.request'))
        <button type="button" class="auth-secondary-action" data-href="{{ route('password.request') }}">Forgot your password?</button>
    @endif
</form>
@endsection

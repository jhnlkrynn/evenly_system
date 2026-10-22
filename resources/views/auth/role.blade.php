@extends('layouts.auth')

@section('title', 'Choose your role · Eventlynk')
@section('page_class', 'signup-page')
@section('panel_class', 'signup-form-panel')

@section('story')
    @include('auth.partials.signup-story')
@endsection

@section('form')
<form class="auth-form signup-form" method="GET" action="{{ route('register') }}">
    <button type="button" class="auth-back" data-href="{{ route('home') }}">
        ← <span>Back to home</span>
    </button>
    <div class="auth-heading">
        <h1>How will you use Eventlynk?</h1>
        <p>
            Already have an account?
            <button type="button" data-href="{{ route('login') }}">Log in</button>
        </p>
    </div>

    <x-role-selector :roles="$roles" value="customer" />

    <button class="auth-submit" type="submit" style="margin-top: 28px;">Continue</button>
    <p class="auth-note">You can book services as a customer or list your business as a vendor. Admin accounts are created by Eventlynk.</p>
</form>
@endsection

@extends('layouts.account')

@section('title', 'Access denied · Eventlynk')

@section('content')
<section class="success-panel" style="padding-top: 40px;">
    <div class="success-mark" style="background: #fbf3dc; color: #8f6c20;">!</div>
    <p class="booking-eyebrow">Error 403</p>
    <h1 style="font-family: 'Playfair Display', serif; font-size: clamp(2.3rem, 5vw, 3.6rem); font-weight: 600; letter-spacing: -0.045em; line-height: 1; margin: 0;">This area isn’t for your account.</h1>
    <p style="max-width: 520px; margin: 20px auto 28px; color: #6d635a; font-size: 12px; line-height: 1.7;">
        {{ $exception->getMessage() ?: 'You don’t have permission to open this page.' }}
    </p>
    <button type="button" data-href="{{ auth()->check() ? auth()->user()->dashboardUrl() : route('home') }}">
        {{ auth()->check() ? 'Go to my dashboard' : 'Return home' }}
    </button>
</section>
@endsection

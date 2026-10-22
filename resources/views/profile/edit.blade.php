@extends('layouts.account')

@section('title', 'Your profile · Eventlynk')

@section('content')
<p class="booking-eyebrow">Account</p>
<h1>Your profile</h1>

<section class="account-card" aria-labelledby="profile-details-title">
    <div class="account-card-heading">
        <div class="account-avatar" aria-hidden="true">{{ $user->initials() }}</div>
        <div>
            <h2 id="profile-details-title">Profile details</h2>
            <p>Member since {{ $user->created_at->format('F Y') }}</p>
            <span class="account-role">{{ $user->role === 'superadmin' ? 'Super admin' : $user->role }}</span>
        </div>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" novalidate>
        @csrf
        @method('PUT')

        @if (session('profile_status'))
            <div class="auth-message success" role="status">{{ session('profile_status') }}</div>
        @endif
        @include('auth.partials.errors', ['errors' => $errors->profile])

        <label class="auth-field">
            <span>Full name</span>
            <div class="phone-field"><input name="name" value="{{ old('name', $user->name) }}" autocomplete="name" maxlength="255" required></div>
        </label>
        <div class="account-field-row">
            <label class="auth-field">
                <span>Email</span>
                <div class="phone-field"><input type="email" value="{{ $user->email }}" disabled></div>
                <small>Contact support to change your email.</small>
            </label>
            <label class="auth-field">
                <span>Mobile number</span>
                <div class="phone-field"><input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="09XX XXX XXXX" autocomplete="tel-national" inputmode="numeric" required></div>
            </label>
        </div>
        <div class="account-actions">
            <button class="auth-submit" type="submit">Save changes</button>
        </div>
    </form>
</section>

<section class="account-card" aria-labelledby="password-title">
    <div class="account-card-heading">
        <div>
            <h2 id="password-title">Change password</h2>
            <p>Use at least 8 characters. You’ll stay signed in on this device.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('profile.password') }}" novalidate>
        @csrf
        @method('PUT')

        @if (session('password_status'))
            <div class="auth-message success" role="status">{{ session('password_status') }}</div>
        @endif
        @include('auth.partials.errors', ['errors' => $errors->password])

        <label class="auth-field">
            <span>Current password</span>
            <div class="password-field">
                <input type="password" name="current_password" autocomplete="current-password" required>
                <button type="button" data-toggle-password>Show</button>
            </div>
        </label>
        <div class="account-field-row">
            <label class="auth-field">
                <span>New password</span>
                <div class="password-field">
                    <input type="password" name="password" autocomplete="new-password" minlength="8" required>
                    <button type="button" data-toggle-password>Show</button>
                </div>
            </label>
            <label class="auth-field">
                <span>Confirm new password</span>
                <div class="password-field">
                    <input type="password" name="password_confirmation" autocomplete="new-password" required>
                    <button type="button" data-toggle-password>Show</button>
                </div>
            </label>
        </div>
        <div class="account-actions">
            <button class="auth-submit" type="submit">Update password</button>
        </div>
    </form>
</section>
@endsection

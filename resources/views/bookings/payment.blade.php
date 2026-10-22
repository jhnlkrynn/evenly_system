@extends('layouts.base')

@section('title', 'Demo payment · '.$booking->booking_code.' · Eventlynk')

@section('body')
<main class="booking-page">
    @include('bookings.partials.header')

    <form class="booking-shell" method="POST" action="{{ route('customer.payments.store', $booking) }}">
        @csrf
        <p class="booking-eyebrow">Demo payment · {{ $booking->booking_code }}</p>
        <h1>Pay for your booking</h1>

        <div class="booking-layout" style="margin-top: 28px;">
            <div class="booking-main-column">
                <div class="demo-banner" role="note" style="margin: 0;">
                    <span aria-hidden="true">⚠</span>
                    <div>
                        <strong>Demo payment — no real money is moved</strong>
                        This screen only records a sample payment so you can try the booking flow.
                        It never asks for account numbers, card numbers or PINs. Never enter real payment details here.
                    </div>
                </div>

                @include('auth.partials.errors')

                <section class="account-card" style="margin: 0;">
                    <h2>1. What are you paying?</h2>
                    <p style="margin: 0; color: #776d64; font-size: 11px;">The amount is set by Eventlynk from your booking — you can’t edit it.</p>
                    <div class="payment-grid is-two">
                        @foreach ($options as $type => $amount)
                            <label class="{{ $loop->first ? 'selected' : '' }}">
                                <input type="radio" name="type" value="{{ $type }}" @checked(old('type', array_key_first($options)) === $type) data-pay-choice>
                                <span>
                                    <strong>{{ ['Reservation' => 'Reservation fee (30%)', 'Full' => 'Pay in full', 'Balance' => 'Remaining balance'][$type] }}</strong>
                                    <span class="payment-amount">₱{{ number_format($amount, 2) }}</span>
                                    <small>{{ ['Reservation' => 'Secures your date; the rest is due on the event day.', 'Full' => 'Settle the whole booking now.', 'Balance' => 'Clears everything still owed.'][$type] }}</small>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </section>

                <section class="account-card" style="margin: 0;">
                    <h2>2. How are you paying?</h2>
                    <p style="margin: 0; color: #776d64; font-size: 11px;">Demo only — we generate a sample reference number for GCash and Maya.</p>
                    <div class="payment-grid">
                        @foreach (['GCash' => 'Demo e-wallet payment', 'Maya' => 'Demo e-wallet payment', 'Cash' => 'Recorded as paid to the vendor in person'] as $method => $detail)
                            <label class="{{ $preferredMethod === $method ? 'selected' : '' }}">
                                <input type="radio" name="method" value="{{ $method }}" @checked($preferredMethod === $method) data-pay-choice>
                                <span>
                                    <strong>{{ $method }}</strong>
                                    <small>{{ $detail }}</small>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </section>

                <div class="booking-actions">
                    <button type="button" class="secondary" data-href="{{ route('bookings.show', $booking) }}">← Back to booking</button>
                    <button type="submit" class="pay-button">Record demo payment</button>
                </div>
            </div>

            <aside class="booking-sidebar">
                <section class="booking-summary-dark payment-summary">
                    <h2>{{ $booking->service?->name ?? 'Your booking' }}</h2>
                    <dl>
                        <div>
                            <dt>Vendor</dt>
                            <dd>{{ $booking->vendor->business_name }}</dd>
                        </div>
                        <div>
                            <dt>Event</dt>
                            <dd>{{ $booking->event_type }} · {{ $booking->event_date->format('j M Y') }}</dd>
                        </div>
                        <div>
                            <dt>Services total</dt>
                            <dd>₱{{ number_format((float) $booking->total_amount) }}</dd>
                        </div>
                        <div>
                            <dt>Paid so far</dt>
                            <dd>₱{{ number_format((float) $booking->amount_paid) }}</dd>
                        </div>
                    </dl>
                    <div class="estimate-total">
                        <span>Balance</span>
                        <strong>₱{{ number_format($booking->balance()) }}</strong>
                    </div>
                    <p>Payment status updates automatically: Partial after the reservation fee, Paid once the balance is cleared.</p>
                </section>
            </aside>
        </div>
    </form>
    @include('bookings.partials.footer')
</main>
@endsection

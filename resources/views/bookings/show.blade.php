@extends('layouts.base')

@section('title', $booking->booking_code.' · Eventlynk')

@section('body')
<main class="booking-page">
    @include('bookings.partials.header')

    <div class="booking-shell">
        @if ($justBooked)
            <section class="success-panel" style="padding: 10px 0 40px;">
                <div class="success-mark">✓</div>
                <p class="booking-eyebrow">Booking received</p>
                <h1>Your event is in motion.</h1>
                <p>
                    Booking <strong>{{ $booking->booking_code }}</strong> was sent to {{ $booking->vendor->business_name }}.
                    Pay the ₱{{ number_format((float) $booking->reservation_fee) }} reservation fee to secure your date.
                </p>
            </section>
        @else
            <p class="booking-eyebrow">Booking</p>
            <h1>{{ $booking->booking_code }}</h1>
        @endif

        @if (session('status'))
            <div class="auth-message success" role="status" style="margin-top: 20px;">{{ session('status') }}</div>
        @endif

        <div class="booking-layout" style="margin-top: 28px;">
            <div class="booking-main-column">
                <section class="account-card" style="margin: 0;">
                    <h2>{{ $booking->service?->name ?? 'Event services' }}</h2>
                    <p style="margin: 0; color: #776d64; font-size: 11px;">
                        {{ $booking->vendor->business_name }} · Booking code <strong style="color: #17100c;">{{ $booking->booking_code }}</strong>
                    </p>
                    <div class="booking-status-row">
                        <span class="status-pill is-{{ strtolower($booking->status) }}">{{ $booking->status }}</span>
                        <span class="status-pill is-{{ strtolower($booking->payment_status) }}">{{ $booking->payment_status }}</span>
                    </div>
                    <dl class="booking-detail-list">
                        <div>
                            <dt>Occasion</dt>
                            <dd>{{ $booking->event_type }} · {{ $booking->pax }} guests</dd>
                        </div>
                        <div>
                            <dt>Date &amp; time</dt>
                            <dd>{{ $booking->event_date->format('D, j M Y') }} · {{ $booking->timeRange() }}</dd>
                        </div>
                        <div>
                            <dt>Venue</dt>
                            <dd>{{ $booking->venue }}{{ $booking->barangay ? ' · '.$booking->barangay.', Nasugbu' : '' }}</dd>
                        </div>
                        <div>
                            <dt>Services</dt>
                            <dd>{{ $booking->servicesSummary() ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt>Customer</dt>
                            <dd>{{ $booking->customer->name }}{{ $booking->customer->phone ? ' · '.$booking->customer->phone : '' }}</dd>
                        </div>
                        <div>
                            <dt>Assigned staff</dt>
                            <dd>{{ $booking->staff->pluck('full_name')->join(', ') ?: 'Not yet assigned' }}</dd>
                        </div>
                        @if ($booking->special_requests)
                            <div style="grid-column: 1 / -1;">
                                <dt>Special requests</dt>
                                <dd>{{ $booking->special_requests }}</dd>
                            </div>
                        @endif
                    </dl>

                    <div class="booking-doc-actions">
                        @if ($canPay)
                            <a class="is-primary" href="{{ route('customer.payments.create', $booking) }}">Pay {{ (float) $booking->amount_paid > 0 ? 'balance' : 'reservation fee' }} (demo)</a>
                        @endif
                        @can('downloadDocuments', $booking)
                            <a href="{{ route('bookings.slip', $booking) }}">⬇ Booking slip (PDF)</a>
                            <a href="{{ route('bookings.invoice', $booking) }}">⬇ Invoice / receipt (PDF)</a>
                        @endcan
                        <a class="is-dark" href="{{ auth()->user()->dashboardUrl() }}">Go to dashboard</a>
                    </div>
                </section>

                <section class="account-card" style="margin: 0;">
                    <h2>Payments</h2>
                    @if ($booking->payments->isEmpty())
                        <p style="margin: 0; color: #776d64; font-size: 11px;">No payments recorded yet.</p>
                    @else
                        <table class="payment-history">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Method</th>
                                    <th>Reference</th>
                                    <th style="text-align: right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($booking->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->created_at->format('M j, Y') }}</td>
                                        <td>{{ $payment->type }}</td>
                                        <td>{{ $payment->method }}</td>
                                        <td>{{ $payment->reference_no ?? '—' }}</td>
                                        <td style="text-align: right; font-weight: 700;">₱{{ number_format((float) $payment->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </section>
            </div>

            <aside class="booking-sidebar">
                <section class="booking-summary-dark payment-summary">
                    <h2>Payment summary</h2>
                    <dl>
                        <div>
                            <dt>Services total</dt>
                            <dd>₱{{ number_format((float) $booking->total_amount) }}</dd>
                        </div>
                        <div>
                            <dt>Reservation fee (30%)</dt>
                            <dd>₱{{ number_format((float) $booking->reservation_fee) }}</dd>
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
                    <p>{{ $booking->progressNote() }}</p>
                </section>
            </aside>
        </div>
    </div>
    @include('bookings.partials.footer')
</main>
@endsection

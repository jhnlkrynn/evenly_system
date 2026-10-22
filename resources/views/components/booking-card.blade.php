{{-- Floating booking summary shown over the landing hero image. --}}
@props([
    'label' => 'Booking confirmed',
    'booking' => null,
])

@php
    $status = $booking?->status ?? 'Paid';
    $title = $booking?->service?->name ?? 'Full Package';
    $meta = $booking
        ? $booking->event_date->format('D, j M').' · '.($booking->barangay ?: 'Nasugbu').', Nasugbu'
        : 'Sat, 12 Dec · Calayo, Nasugbu';
    $note = $booking?->progressNote() ?? 'Vendor confirmed · Setup team assigned';
@endphp

<div class="booking-card">
    <div>
        <span>{{ $label }}</span>
        <strong>{{ $status }}</strong>
    </div>
    <h3>{{ $title }}</h3>
    <p>{{ $meta }}</p>
    <hr>
    <small>{{ $note }}</small>
</div>

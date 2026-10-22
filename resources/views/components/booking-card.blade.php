{{-- Floating booking summary shown over the landing hero image. --}}
@props([
    'label' => 'Booking confirmed',
    'status' => 'Paid',
    'title',
    'meta',
    'note',
])

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

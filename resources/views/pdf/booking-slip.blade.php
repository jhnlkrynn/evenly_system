@extends('pdf.layout')

@section('title', $booking->booking_code.' booking slip')
@section('heading', 'Booking Slip')
@section('subheading', $booking->booking_code.' · Booked '.$booking->created_at->format('M j, Y'))

@section('content')
    <div class="section">
        <span class="pill pill-{{ strtolower($booking->status) }}">{{ $booking->status }}</span>
        <span class="pill pill-{{ strtolower($booking->payment_status) }}">{{ $booking->payment_status }}</span>
        <span class="muted" style="margin-left: 6px;">{{ $booking->progressNote() }}</span>
    </div>

    <div class="section">
        <h2>Event</h2>
        <table class="grid">
            <tr>
                <td><span class="label">Occasion</span><span class="value">{{ $booking->event_type }}</span></td>
                <td><span class="label">Guests</span><span class="value">{{ $booking->pax }} pax</span></td>
            </tr>
            <tr>
                <td><span class="label">Date</span><span class="value">{{ $booking->event_date->format('l, F j, Y') }}</span></td>
                <td><span class="label">Time</span><span class="value">{{ $booking->timeRange() }}</span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="label">Venue</span><span class="value">{{ $booking->venue }}{{ $booking->barangay ? ', '.$booking->barangay : '' }}, Nasugbu, Batangas</span></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Customer &amp; vendor</h2>
        <table class="grid">
            <tr>
                <td>
                    <span class="label">Customer</span>
                    <span class="value">{{ $booking->customer->name }}</span><br>
                    <span class="muted">{{ $booking->customer->email }}{{ $booking->customer->phone ? ' · '.$booking->customer->phone : '' }}</span>
                </td>
                <td>
                    <span class="label">Vendor</span>
                    <span class="value">{{ $booking->vendor->business_name }}</span><br>
                    <span class="muted">{{ $booking->service?->name }}{{ $booking->vendor->contact_number ? ' · '.$booking->vendor->contact_number : '' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Services booked</h2>
        <table class="lines">
            <thead><tr><th>Service</th><th>Details</th></tr></thead>
            <tbody>
                <tr><td>Decoration</td><td>{{ $booking->needs_decoration ? 'Full decoration setup' : '—' }}</td></tr>
                <tr><td>Serving staff</td><td>{{ $booking->serving_count ? $booking->serving_count.' staff' : '—' }}</td></tr>
                <tr><td>Dishwashing</td><td>{{ $booking->dishwashing_count ? $booking->dishwashing_count.' staff' : '—' }}</td></tr>
            </tbody>
        </table>
        @if ($booking->special_requests)
            <div class="note"><strong>Special requests:</strong><br>{!! nl2br(e($booking->special_requests)) !!}</div>
        @endif
    </div>

    <div class="section">
        <h2>Assigned staff</h2>
        @if ($booking->staff->isEmpty())
            <p class="muted">No staff assigned yet — the vendor assigns the team after confirming the booking.</p>
        @else
            <table class="lines">
                <thead><tr><th>Name</th><th>Role</th></tr></thead>
                <tbody>
                    @foreach ($booking->staff->sortBy('role') as $member)
                        <tr><td>{{ $member->full_name }}</td><td>{{ $member->role }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="note">Present this slip on the event day. Free cancellation until 7 days before the event; after that the reservation fee is non-refundable.</div>
@endsection

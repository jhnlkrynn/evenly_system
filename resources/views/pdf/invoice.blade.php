@extends('pdf.layout')

@section('title', $booking->booking_code.' invoice')
@section('heading', $booking->payment_status === 'Paid' ? 'Official Receipt' : 'Invoice')
@section('subheading', 'INV-'.$booking->booking_code.' · Issued '.now()->format('M j, Y'))

@section('content')
    <div class="section">
        <table class="grid">
            <tr>
                <td>
                    <span class="label">Billed to</span>
                    <span class="value">{{ $booking->customer->name }}</span><br>
                    <span class="muted">{{ $booking->customer->email }}{{ $booking->customer->phone ? ' · '.$booking->customer->phone : '' }}</span>
                </td>
                <td>
                    <span class="label">Vendor</span>
                    <span class="value">{{ $booking->vendor->business_name }}</span><br>
                    <span class="muted">{{ $booking->event_type }} · {{ $booking->event_date->format('M j, Y') }} · {{ $booking->pax }} pax</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Charges</h2>
        <table class="lines">
            <thead><tr><th>Item</th><th>Details</th><th class="right">Amount</th></tr></thead>
            <tbody>
                @foreach ($lines as $line)
                    <tr>
                        <td>{{ $line['label'] }}</td>
                        <td class="muted">{{ $line['detail'] }}</td>
                        <td class="right">{{ $line['amount'] < 0 ? '−' : '' }}₱{{ number_format(abs($line['amount']), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr class="grand"><td>Total</td><td class="right">₱{{ number_format((float) $booking->total_amount, 2) }}</td></tr>
            <tr><td class="muted">Reservation fee (30%)</td><td class="right muted">₱{{ number_format((float) $booking->reservation_fee, 2) }}</td></tr>
            <tr><td>Amount paid</td><td class="right">₱{{ number_format((float) $booking->amount_paid, 2) }}</td></tr>
            <tr class="balance"><td>Balance due</td><td class="right">₱{{ number_format($booking->balance(), 2) }}</td></tr>
        </table>
    </div>

    <div class="section">
        <h2>Payments made <span class="pill pill-{{ strtolower($booking->payment_status) }}" style="margin-left: 6px;">{{ $booking->payment_status }}</span></h2>
        @if ($booking->payments->isEmpty())
            <p class="muted">No payments recorded yet.</p>
        @else
            <table class="lines">
                <thead><tr><th>Date</th><th>Type</th><th>Method</th><th>Reference</th><th class="right">Amount</th></tr></thead>
                <tbody>
                    @foreach ($booking->payments as $payment)
                        <tr>
                            <td>{{ $payment->created_at->format('M j, Y') }}</td>
                            <td>{{ $payment->type }}</td>
                            <td>{{ $payment->method }}</td>
                            <td>{{ $payment->reference_no ?? '—' }}</td>
                            <td class="right">₱{{ number_format((float) $payment->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="note">Payments recorded through Eventlynk’s demo payment screen are samples for testing and do not represent real transactions.</div>
@endsection

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\StorePaymentRequest;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Demo payments: no money moves and no account or card details are collected.
 * The amount comes from the payment type, never from the form.
 */
class PaymentController extends Controller
{
    public function create(Request $request, Booking $booking): View|RedirectResponse
    {
        if (! $request->user()->can('pay', $booking)) {
            $this->authorize('view', $booking);

            return redirect()->route('bookings.show', $booking)->with('status', 'This booking is already fully paid.');
        }

        $booking->load(['vendor', 'service', 'payments']);

        return view('bookings.payment', [
            'booking' => $booking,
            'options' => $booking->paymentOptions(),
            'methods' => Payment::METHODS,
            'preferredMethod' => old('method', $request->session()->get('preferred_method', 'GCash')),
        ]);
    }

    public function store(StorePaymentRequest $request, Booking $booking): RedirectResponse
    {
        $payment = DB::transaction(function () use ($request, $booking) {
            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $type = $request->validated('type');
            $amount = $booking->paymentOptions()[$type] ?? null;

            if ($amount === null) {
                throw ValidationException::withMessages(['type' => 'That payment type isn’t available for this booking any more.']);
            }

            $method = $request->validated('method');
            $payment = $booking->payments()->create([
                'amount' => $amount,
                'method' => $method,
                'type' => $type,
                'reference_no' => $method === 'Cash' ? null : 'DEMO-'.strtoupper(Str::random(10)),
            ]);

            $paid = (float) $booking->amount_paid + $amount;
            $booking->update([
                'amount_paid' => $paid,
                'payment_status' => $paid >= (float) $booking->total_amount ? 'Paid' : 'Partial',
            ]);

            ActivityLog::record($request->user()->id, 'payment_recorded', sprintf(
                'Demo %s payment of ₱%s via %s for %s (now %s)',
                strtolower($type), number_format($amount, 2), $method, $booking->booking_code, $booking->payment_status,
            ));

            return $payment;
        });

        return redirect()->route('bookings.show', $booking)->with('status', sprintf(
            'Demo payment of ₱%s recorded via %s.', number_format((float) $payment->amount, 2), $payment->method,
        ));
    }
}

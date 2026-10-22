<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\UpdateBookingStatusRequest;
use App\Models\ActivityLog;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;

class VendorBookingController extends Controller
{
    public function updateStatus(UpdateBookingStatusRequest $request, Booking $booking): RedirectResponse
    {
        $status = $request->validated('status');
        $allowed = [
            'Pending' => ['Confirmed', 'Cancelled'],
            'Confirmed' => ['Completed', 'Cancelled'],
        ];

        abort_unless(in_array($status, $allowed[$booking->status] ?? [], true), 422, 'That booking transition is not allowed.');
        $booking->update(['status' => $status]);

        ActivityLog::record($request->user()->id, 'booking_status_updated', sprintf(
            '%s changed to %s by vendor.', $booking->booking_code, $status,
        ));

        return back()->with('status', $booking->booking_code.' marked '.$status.'.');
    }
}

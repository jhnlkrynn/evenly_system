<?php

namespace App\Http\Controllers;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Notifications\ReviewReceivedNotification;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Booking $booking): RedirectResponse
    {
        $review = $booking->review()->create([
            'customer_id' => $request->user()->id,
            'vendor_id' => $booking->vendor_id,
            ...$request->validated(),
        ]);

        ActivityLog::record($request->user()->id, 'review_created', sprintf(
            'Left a %d-star review for %s (%s).',
            $review->rating,
            $booking->vendor->business_name,
            $booking->booking_code,
        ));
        $booking->load('vendor.user');
        $booking->vendor->user->notify(new ReviewReceivedNotification($review));

        return back()->with('status', 'Thanks — your review was saved.');
    }
}

<?php

namespace App\Notifications;

use App\Models\Review;

class ReviewReceivedNotification extends InAppNotification
{
    public function __construct(Review $review)
    {
        $review->loadMissing('booking');
        parent::__construct("You received a {$review->rating}-star review.", '⭐', $review->booking?->booking_code, $review->booking ? route('bookings.show', $review->booking) : route('vendor.dashboard'));
    }
}

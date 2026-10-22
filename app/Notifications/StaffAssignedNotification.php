<?php

namespace App\Notifications;

use App\Models\Booking;

class StaffAssignedNotification extends InAppNotification
{
    public function __construct(Booking $booking)
    {
        parent::__construct("Staff has been assigned to booking {$booking->booking_code}.", '👥', $booking->booking_code, route('bookings.show', $booking));
    }
}

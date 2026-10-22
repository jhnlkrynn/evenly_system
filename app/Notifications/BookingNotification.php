<?php

namespace App\Notifications;

use App\Models\Booking;

class BookingNotification extends InAppNotification
{
    public function __construct(Booking $booking, string $event = 'created')
    {
        $message = match ($event) {
            'confirmed' => "Booking {$booking->booking_code} was confirmed.",
            'cancelled' => "Booking {$booking->booking_code} was cancelled.",
            'completed' => "Booking {$booking->booking_code} was marked completed.",
            default => "New booking {$booking->booking_code} was received.",
        };
        parent::__construct($message, match ($event) {
            'confirmed' => '✅', 'cancelled' => '❌', 'completed' => '🎉', default => '📅'
        }, $booking->booking_code, route('bookings.show', $booking));
    }
}

<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

/**
 * Who may see a booking and its PDFs: the customer who made it, the vendor
 * it was made with, and admins. Only that customer can pay for it.
 */
class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return match ($user->role) {
            'admin', 'superadmin' => true,
            'customer' => $booking->customer_id === $user->id,
            'vendor' => $booking->vendor_id === $user->vendor?->id,
            default => false,
        };
    }

    public function downloadDocuments(User $user, Booking $booking): bool
    {
        return $this->view($user, $booking);
    }

    public function pay(User $user, Booking $booking): bool
    {
        return $user->hasRole('customer')
            && $booking->customer_id === $user->id
            && $booking->paymentOptions() !== [];
    }
}

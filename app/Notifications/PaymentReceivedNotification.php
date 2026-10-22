<?php

namespace App\Notifications;

use App\Models\Payment;

class PaymentReceivedNotification extends InAppNotification
{
    public function __construct(Payment $payment)
    {
        $booking = $payment->booking;
        parent::__construct('Payment of ₱'.number_format((float) $payment->amount, 2)." received for {$booking->booking_code}.", '💳', $booking->booking_code, route('bookings.show', $booking));
    }
}

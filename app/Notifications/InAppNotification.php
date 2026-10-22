<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Generic in-system notification shown in the dashboard bell and
 * Notifications tab (stored in the `notifications` table).
 */
class InAppNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $message,
        public string $icon = '🔔',
        public ?string $bookingCode = null,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'icon' => $this->icon,
            'message' => $this->message,
            'booking_code' => $this->bookingCode,
        ];
    }
}

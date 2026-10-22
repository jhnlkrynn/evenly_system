<?php

namespace App\Notifications;

use App\Models\User;

class VendorRegistrationNotification extends InAppNotification
{
    public function __construct(User $vendor)
    {
        parent::__construct("New vendor registration from {$vendor->name} is awaiting verification.", '🏪');
    }

    public function toArray(object $notifiable): array
    {
        return [...parent::toArray($notifiable), 'url' => route($notifiable->hasRole('superadmin') ? 'superadmin.dashboard' : 'admin.dashboard')];
    }
}

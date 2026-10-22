<?php

namespace App\Notifications;

use App\Models\Vendor;

class VendorVerificationNotification extends InAppNotification
{
    public function __construct(Vendor $vendor, string $status)
    {
        parent::__construct("Your vendor account was {$status}.", $status === 'approved' ? '✅' : '⚠️', null, route('vendor.dashboard'));
    }
}

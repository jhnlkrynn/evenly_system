<?php

namespace App\Models;

use Database\Factories\StaffFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * A crew member. vendor_id is null for Eventlynk's own crew (managed in the admin panel).
 */
class Staff extends Model
{
    /** @use HasFactory<StaffFactory> */
    use HasFactory;

    public const ROLES = ['Decoration', 'Serving', 'Dishwashing'];

    protected $table = 'staff';

    protected $fillable = [
        'vendor_id',
        'full_name',
        'role',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_staff')->withTimestamps();
    }
}

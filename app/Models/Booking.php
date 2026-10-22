<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    public const STATUSES = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];

    public const PAYMENT_STATUSES = ['Unpaid', 'Partial', 'Paid'];

    protected $fillable = [
        'booking_code',
        'customer_id',
        'vendor_id',
        'service_id',
        'event_type',
        'event_date',
        'start_time',
        'end_time',
        'venue',
        'barangay',
        'pax',
        'needs_decoration',
        'serving_count',
        'dishwashing_count',
        'special_requests',
        'total_amount',
        'reservation_fee',
        'amount_paid',
        'status',
        'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'pax' => 'integer',
            'needs_decoration' => 'boolean',
            'serving_count' => 'integer',
            'dishwashing_count' => 'integer',
            'total_amount' => 'decimal:2',
            'reservation_fee' => 'decimal:2',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'booking_staff')->withTimestamps();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function balance(): float
    {
        return (float) $this->total_amount - (float) $this->amount_paid;
    }
}

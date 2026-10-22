<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const METHODS = ['GCash', 'Maya', 'Cash'];

    public const TYPES = ['Reservation', 'Balance', 'Full'];

    protected $fillable = [
        'booking_id',
        'amount',
        'method',
        'type',
        'reference_no',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}

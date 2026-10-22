<?php

namespace App\Models;

use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    public const STATUSES = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];

    public const PAYMENT_STATUSES = ['Unpaid', 'Partial', 'Paid'];

    /** Occasions offered on the booking form (BookingPage.tsx EVENT_TYPES). */
    public const EVENT_TYPES = ['Debut', 'Wedding', 'Birthday', 'Corporate', 'Christening', 'Other'];

    public const THEMES = ['Gold & ivory', 'Blush', 'Sage', 'Custom...'];

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
        return max(0, (float) $this->total_amount - (float) $this->amount_paid);
    }

    /** Bookings that hold the vendor's date (anything not cancelled). */
    public function scopeHoldingDate(Builder $query): void
    {
        $query->where('status', '!=', 'Cancelled');
    }

    public static function vendorIsBookedOn(int $vendorId, string $date): bool
    {
        return self::where('vendor_id', $vendorId)->whereDate('event_date', $date)->holdingDate()->exists();
    }

    /**
     * What the customer can pay right now, with the amount for each type:
     * nothing paid yet → Reservation (30% fee) or Full; part paid → Balance.
     *
     * @return array<string, float>
     */
    public function paymentOptions(): array
    {
        $paid = (float) $this->amount_paid;
        $balance = $this->balance();

        if ($balance <= 0 || $this->status === 'Cancelled') {
            return [];
        }

        if ($paid <= 0) {
            return [
                'Reservation' => min((float) $this->reservation_fee, $balance),
                'Full' => $balance,
            ];
        }

        return ['Balance' => $balance];
    }

    /** "6:00 PM – 11:00 PM" */
    public function timeRange(): string
    {
        $format = fn ($time) => Carbon::parse($time)->format('g:i A');

        return $format($this->start_time).' – '.$format($this->end_time);
    }

    /** "Decoration · 8 serving staff · 4 dishwashers" */
    public function servicesSummary(): string
    {
        return collect([
            $this->needs_decoration ? 'Decoration' : null,
            $this->serving_count ? $this->serving_count.' serving staff' : null,
            $this->dishwashing_count ? $this->dishwashing_count.' '.Str::plural('dishwasher', $this->dishwashing_count) : null,
        ])->filter()->join(' · ');
    }

    /** One-line state for summaries ("Awaiting vendor confirmation", ...). */
    public function progressNote(): string
    {
        return match ($this->status) {
            'Pending' => 'Awaiting vendor confirmation',
            'Confirmed' => 'Vendor confirmed · '.($this->staff()->exists() ? 'Setup team assigned' : 'Team being assigned'),
            'Completed' => 'Event completed · Thank you!',
            default => 'This booking was cancelled',
        };
    }
}

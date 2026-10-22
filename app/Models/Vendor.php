<?php

namespace App\Models;

use Database\Factories\VendorFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    /** @use HasFactory<VendorFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'short_name',
        'description',
        'barangay',
        'contact_number',
        'logo',
        'is_verified',
        'verification_reason',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** Verified vendor with an active account — visible to customers. */
    public function scopeListed(Builder $query): void
    {
        $query->where('is_verified', true)
            ->whereHas('user', fn (Builder $user) => $user->where('is_active', true));
    }

    public function isListed(): bool
    {
        return $this->is_verified && $this->user?->is_active;
    }

    /** "Poblacion, Nasugbu, Batangas" */
    public function locationLabel(): string
    {
        return collect([$this->barangay, 'Nasugbu, Batangas'])->filter()->join(', ');
    }

    public function initials(): string
    {
        preg_match_all('/\b\p{L}/u', $this->business_name, $letters);

        return mb_strtoupper(implode('', array_slice($letters[0], 0, 2)));
    }
}

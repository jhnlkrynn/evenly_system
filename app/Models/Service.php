<?php

namespace App\Models;

use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory;

    /**
     * Event types customers browse by (homepage chips, "Good for" filter).
     * A service matches when its text mentions a keyword, or its vendor has
     * already been booked for that kind of event.
     */
    public const EVENTS = [
        'debut' => ['label' => 'Debuts', 'keywords' => ['debut']],
        'wedding' => ['label' => 'Weddings', 'keywords' => ['wedding']],
        'corporate' => ['label' => 'Corporate', 'keywords' => ['corporate', 'gala']],
        'birthday' => ['label' => 'Birthdays', 'keywords' => ['birthday', 'celebration', 'party']],
    ];

    protected $fillable = [
        'vendor_id',
        'service_category_id',
        'name',
        'description',
        'price',
        'price_unit',
        'image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** Reviews are left per vendor; this exposes the vendor's reviews on each of its services. */
    public function vendorReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'vendor_id', 'vendor_id');
    }

    /** Service image, or the category's photo when the vendor hasn't uploaded one. */
    public function imageUrl(): string
    {
        return $this->image ?: $this->category->imageUrl();
    }

    /** "₱15,000" */
    public function formattedPrice(): string
    {
        return '₱'.number_format((float) $this->price);
    }

    /* ------------------------------------------------------------------
     | Query scopes (Browse Services filters)
     * ------------------------------------------------------------------ */

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** Bookable: active service from a verified vendor whose account is active. */
    public function scopeListed(Builder $query): void
    {
        $query->where('services.is_active', true)
            ->whereHas('vendor', fn (Builder $vendor) => $vendor->listed());
    }

    /** Adds vendor_reviews_avg_rating and vendor_reviews_count. */
    public function scopeWithVendorRatings(Builder $query): void
    {
        $query->withAvg('vendorReviews', 'rating')->withCount('vendorReviews');
    }

    public function scopeSearch(Builder $query, ?string $term): void
    {
        if (blank($term)) {
            return;
        }

        $like = '%'.addcslashes($term, '%_\\').'%';

        $query->where(fn (Builder $q) => $q
            ->where('services.name', 'like', $like)
            ->orWhere('services.description', 'like', $like)
            ->orWhereHas('vendor', fn (Builder $vendor) => $vendor->where('business_name', 'like', $like))
            ->orWhereHas('category', fn (Builder $category) => $category->where('name', 'like', $like)));
    }

    /** @param  array<int, int>  $categoryIds */
    public function scopeInCategories(Builder $query, array $categoryIds): void
    {
        if ($categoryIds) {
            $query->whereIn('service_category_id', $categoryIds);
        }
    }

    /** Vendors based in the barangay. "Barangay 1–12 (Pob.)" also matches vendors listed as "Poblacion". */
    public function scopeInBarangay(Builder $query, ?string $barangay): void
    {
        if (blank($barangay)) {
            return;
        }

        $names = str_contains($barangay, '(Pob.)') ? [$barangay, 'Poblacion'] : [$barangay];

        $query->whereHas('vendor', fn (Builder $vendor) => $vendor->whereIn('barangay', $names));
    }

    /** Excludes vendors that already have a non-cancelled booking on the date. */
    public function scopeAvailableOn(Builder $query, ?Carbon $date): void
    {
        if (! $date) {
            return;
        }

        $query->whereDoesntHave('vendor.bookings', fn (Builder $booking) => $booking
            ->whereDate('event_date', $date)
            ->where('status', '!=', 'Cancelled'));
    }

    public function scopePriceBetween(Builder $query, ?float $min, ?float $max): void
    {
        $query
            ->when($min !== null, fn (Builder $q) => $q->where('price', '>=', $min))
            ->when($max !== null, fn (Builder $q) => $q->where('price', '<=', $max));
    }

    public function scopeMinVendorRating(Builder $query, float $rating): void
    {
        if ($rating > 0) {
            // "+ 0" keeps the comparison numeric on SQLite, which binds parameters as text.
            $query->whereRaw('(select avg(rating) from reviews where reviews.vendor_id = services.vendor_id) >= ? + 0', [$rating]);
        }
    }

    /** @param  array<int, string>  $events  keys of self::EVENTS */
    public function scopeForEvents(Builder $query, array $events): void
    {
        $keywords = collect($events)->flatMap(fn ($event) => self::EVENTS[$event]['keywords'] ?? [])->unique();

        if ($keywords->isEmpty()) {
            return;
        }

        $query->where(function (Builder $q) use ($keywords) {
            foreach ($keywords as $word) {
                $q->orWhere('services.name', 'like', "%{$word}%")
                    ->orWhere('services.description', 'like', "%{$word}%");
            }

            $q->orWhereHas('vendor.bookings', fn (Builder $booking) => $booking->where(function (Builder $types) use ($keywords) {
                foreach ($keywords as $word) {
                    $types->orWhere('event_type', 'like', "%{$word}%");
                }
            }));
        });
    }

    /** Requires scopeWithVendorRatings for the rating-based orders. */
    public function scopeSortBy(Builder $query, string $sort): void
    {
        match ($sort) {
            'price_asc' => $query->orderBy('price')->orderBy('services.id'),
            'price_desc' => $query->orderByDesc('price')->orderBy('services.id'),
            'rating' => $query->orderByRaw('vendor_reviews_avg_rating is null')
                ->orderByDesc('vendor_reviews_avg_rating')
                ->orderByDesc('vendor_reviews_count')
                ->orderBy('services.id'),
            // Recommended: most-reviewed vendors first, then best rated, then newest listings.
            default => $query->orderByDesc('vendor_reviews_count')
                ->orderByDesc('vendor_reviews_avg_rating')
                ->orderBy('services.id'),
        };
    }
}

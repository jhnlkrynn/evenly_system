<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    /**
     * Homepage cards (copy + photo from the Figma design), in display order.
     * Categories not listed here use DEFAULT_IMAGE and are not featured.
     */
    public const SHOWCASE = [
        'Decoration' => [
            'description' => 'Balloons, tablescapes, centerpieces, and custom themes made for your event.',
            'image' => 'images/image-2.png',
        ],
        'Serving Staff' => [
            'description' => 'Uniformed, trained staff for buffets, cocktails, and formal dinners.',
            'image' => 'https://images.unsplash.com/photo-1759646827844-bbbdbfd0ada2?auto=format&fit=crop&w=900&q=82',
        ],
        'Dishwashing' => [
            'description' => 'Post-event cleanup with full equipment for a spotless close to your occasion.',
            'image' => 'https://images.unsplash.com/photo-1769812343266-323d6b508f24?auto=format&fit=crop&w=900&q=82',
        ],
        'Full Package' => [
            'description' => 'Decoration, serving, and dishwashing combined in one coordinated service.',
            'image' => 'https://images.unsplash.com/photo-1766393524464-e5eb1b05e4c8?auto=format&fit=crop&w=900&q=82',
            'highlight' => 'Best value',
        ],
    ];

    public const DEFAULT_IMAGE = 'images/image-3.png';

    protected $fillable = [
        'name',
        'icon',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function imageUrl(): string
    {
        $image = self::SHOWCASE[$this->name]['image'] ?? self::DEFAULT_IMAGE;

        return str_starts_with($image, 'http') ? $image : asset($image);
    }

    public function scopeFeatured(Builder $query): void
    {
        $query->whereIn('name', array_keys(self::SHOWCASE));
    }
}

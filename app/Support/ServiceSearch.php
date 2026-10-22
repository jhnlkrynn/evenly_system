<?php

namespace App\Support;

use App\Models\Barangay;
use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Browse Services: reads the filters from the query string, runs the
 * Service scopes and builds everything the browser partial needs
 * (results, filter options, removable chips, URLs that keep the filters).
 *
 * Used by /services and by the vendor dashboard's embedded Browse tab.
 */
class ServiceSearch
{
    public const PER_PAGE = 12;

    public const SORTS = [
        'recommended' => 'Recommended',
        'rating' => 'Highest rated',
        'price_asc' => 'Price: low to high',
        'price_desc' => 'Price: high to low',
    ];

    public const RATINGS = ['0' => 'Any', '4.5' => '4.5+', '4.8' => '4.8+'];

    /** @var array{search: string, categories: array<int,int>, barangay: string, date: ?Carbon, min_price: ?float, max_price: ?float, rating: float, events: array<int,string>, available: bool, sort: string} */
    public array $filters;

    /**
     * @param  string  $route  route the filter form and links point at
     * @param  array  $baseQuery  query params always kept (e.g. the dashboard tab)
     */
    public function __construct(Request $request, private string $route = 'services.index', private array $baseQuery = [])
    {
        $this->filters = self::parse($request);
    }

    public static function parse(Request $request): array
    {
        $text = fn (string $key) => is_string($request->query($key)) ? trim(mb_substr($request->query($key), 0, 100)) : '';
        $price = fn (string $key) => is_numeric($request->query($key)) && $request->query($key) >= 0 ? (float) $request->query($key) : null;
        $list = fn (string $key) => array_values(array_filter((array) $request->query($key, []), 'is_string'));

        $date = null;
        if ($text('date') !== '') {
            try {
                $date = Carbon::createFromFormat('!Y-m-d', $text('date'));
            } catch (\Throwable) {
                $date = null;
            }
        }

        return [
            'search' => $text('search'),
            'categories' => array_values(array_unique(array_filter(array_map('intval', $list('categories'))))),
            'barangay' => $text('barangay'),
            'date' => $date,
            'min_price' => $price('min_price'),
            'max_price' => $price('max_price'),
            'rating' => array_key_exists($text('rating'), self::RATINGS) ? (float) $text('rating') : 0.0,
            'events' => array_values(array_intersect(array_keys(Service::EVENTS), $list('events'))),
            'available' => $request->query('available', '1') !== '0',
            'sort' => array_key_exists($text('sort'), self::SORTS) ? $text('sort') : 'recommended',
        ];
    }

    public function results(): LengthAwarePaginator
    {
        $f = $this->filters;

        return Service::query()
            ->listed()
            ->with(['vendor', 'category'])
            ->withVendorRatings()
            ->search($f['search'])
            ->inCategories($f['categories'])
            ->inBarangay($f['barangay'])
            ->when($f['available'], fn ($query) => $query->availableOn($f['date']))
            ->priceBetween($f['min_price'], $f['max_price'])
            ->minVendorRating($f['rating'])
            ->forEvents($f['events'])
            ->sortBy($f['sort'])
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    /** Vendors already booked on the chosen date (to label cards when "free only" is off). */
    public function bookedVendorIds(): Collection
    {
        if (! $this->filters['date']) {
            return collect();
        }

        return Booking::whereDate('event_date', $this->filters['date'])
            ->where('status', '!=', 'Cancelled')
            ->distinct()
            ->pluck('vendor_id');
    }

    /** Categories that have listed services, with how many. */
    public function categories(): Collection
    {
        return ServiceCategory::query()
            ->withCount(['services' => fn ($query) => $query->listed()])
            ->orderBy('id')
            ->get()
            ->where('services_count', '>', 0)
            ->values();
    }

    /** Query string for the current filters (defaults left out). */
    public function query(array $overrides = []): array
    {
        $f = $this->filters;

        $query = [
            'search' => $f['search'],
            'categories' => $f['categories'],
            'barangay' => $f['barangay'],
            'date' => $f['date']?->format('Y-m-d'),
            'min_price' => $f['min_price'],
            'max_price' => $f['max_price'],
            'rating' => $f['rating'] ? (string) $f['rating'] : null,
            'events' => $f['events'],
            'available' => $f['available'] ? null : '0',
            'sort' => $f['sort'] === 'recommended' ? null : $f['sort'],
            ...$overrides,
        ];

        return array_filter([...$this->baseQuery, ...$query], fn ($value) => $value !== null && $value !== '' && $value !== []);
    }

    public function url(array $overrides = []): string
    {
        return route($this->route, $this->query($overrides));
    }

    /** Active filters as removable chips: [['label' => ..., 'url' => ...]]. */
    public function chips(Collection $categories): array
    {
        $f = $this->filters;
        $chips = [];
        $add = function (string $label, array $without) use (&$chips) {
            $chips[] = ['label' => $label, 'url' => $this->url($without)];
        };

        if ($f['search'] !== '') {
            $add("“{$f['search']}”", ['search' => null]);
        }
        foreach ($f['categories'] as $id) {
            $name = $categories->firstWhere('id', $id)?->name;
            if ($name) {
                $add($name, ['categories' => array_values(array_diff($f['categories'], [$id]))]);
            }
        }
        if ($f['barangay'] !== '') {
            $add($f['barangay'], ['barangay' => null]);
        }
        if ($f['date']) {
            $add($f['available'] ? 'Free on '.$f['date']->format('M j') : $f['date']->format('M j'), ['date' => null]);
        }
        if ($f['min_price'] !== null) {
            $add('From ₱'.number_format($f['min_price']), ['min_price' => null]);
        }
        if ($f['max_price'] !== null) {
            $add('Up to ₱'.number_format($f['max_price']), ['max_price' => null]);
        }
        if ($f['rating']) {
            $add("{$f['rating']}+ stars", ['rating' => null]);
        }
        foreach ($f['events'] as $event) {
            $add(Service::EVENTS[$event]['label'], ['events' => array_values(array_diff($f['events'], [$event]))]);
        }

        return $chips;
    }

    /** Everything resources/views/services/partials/browser.blade.php expects. */
    public function toView(): array
    {
        $categories = $this->categories();

        return [
            'results' => $this->results(),
            'filters' => $this->filters,
            'categories' => $categories,
            'chips' => $this->chips($categories),
            'bookedVendorIds' => $this->bookedVendorIds(),
            'barangays' => Barangay::orderBy('id')->pluck('name'),
            'events' => Service::EVENTS,
            'sorts' => self::SORTS,
            'ratings' => self::RATINGS,
            'formAction' => route($this->route),
            'baseQuery' => $this->baseQuery,
            'clearUrl' => route($this->route, $this->baseQuery),
        ];
    }
}

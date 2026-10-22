<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Vendor;
use App\Support\MockData;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    /**
     * Landing page.
     */
    public function index(): View
    {
        $categories = ServiceCategory::orderBy('id')->get();

        return view('home', [
            'categories' => $categories,
            'featured' => $this->featuredServices($categories),
            'barangays' => Barangay::orderBy('id')->pluck('name'),
            'stats' => [
                'vendors' => Vendor::listed()->count(),
                'completed' => Booking::where('status', 'Completed')->count(),
                'rating' => Review::avg('rating'),
            ],
            'reviews' => Review::where('rating', 5)
                ->with(['customer', 'booking'])
                ->latest()
                ->limit(3)
                ->get(),
            'steps' => MockData::STEPS,
        ]);
    }

    /**
     * One card per showcase category: its lowest "per event" listing, or the
     * lowest "per staff" one when the category is only priced per staff.
     *
     * @return Collection<int, array{category: ServiceCategory, service: Service}>
     */
    private function featuredServices(Collection $categories): Collection
    {
        $showcase = $categories->whereIn('name', array_keys(ServiceCategory::SHOWCASE))->keyBy('id');

        $services = Service::listed()
            ->with(['vendor', 'category'])
            ->whereIn('service_category_id', $showcase->keys())
            ->orderByRaw("price_unit = 'event' desc")
            ->orderBy('price')
            ->get()
            ->unique('service_category_id')
            ->keyBy('service_category_id');

        return collect(array_keys(ServiceCategory::SHOWCASE))
            ->map(fn ($name) => $showcase->firstWhere('name', $name))
            ->filter(fn ($category) => $category && $services->has($category->id))
            ->map(fn ($category) => ['category' => $category, 'service' => $services[$category->id]])
            ->values();
    }
}

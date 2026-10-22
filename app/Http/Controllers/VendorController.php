<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class VendorController extends Controller
{
    public function show(Vendor $vendor): View
    {
        $vendor->load('user');
        abort_unless($vendor->isListed(), 404);

        return view('vendors.show', [
            'vendor' => $vendor,
            'services' => $vendor->services()
                ->listed()
                ->with(['vendor', 'category'])
                ->withVendorRatings()
                ->orderBy('price')
                ->get(),
            'completedBookings' => $vendor->bookings()->where('status', 'Completed')->count(),
            ...self::reviewData($vendor),
        ]);
    }

    /** Dates this vendor is already booked (today onwards), for the booking calendar. */
    public function bookedDates(Vendor $vendor): JsonResponse
    {
        abort_unless($vendor->loadMissing('user')->isListed(), 404);

        return response()->json([
            'vendor_id' => $vendor->id,
            'today' => now()->toDateString(),
            'booked' => $vendor->bookings()
                ->holdingDate()
                ->whereDate('event_date', '>=', now()->toDateString())
                ->orderBy('event_date')
                ->pluck('event_date')
                ->map(fn ($date) => Carbon::parse($date)->toDateString())
                ->unique()
                ->values(),
        ]);
    }

    /**
     * Reviews with their customer and booking, plus the rating summary.
     *
     * @return array{reviews: Collection, rating: ?float, reviewCount: int, distribution: array<int,int>}
     */
    public static function reviewData(Vendor $vendor): array
    {
        $reviews = $vendor->reviews()->with(['customer', 'booking'])->latest()->get();
        $counts = $reviews->countBy('rating');

        return [
            'reviews' => $reviews,
            'rating' => $reviews->isEmpty() ? null : round($reviews->avg('rating'), 1),
            'reviewCount' => $reviews->count(),
            'distribution' => collect([5, 4, 3, 2, 1])->mapWithKeys(fn ($stars) => [$stars => $counts->get($stars, 0)])->all(),
        ];
    }
}

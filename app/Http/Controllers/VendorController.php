<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Contracts\View\View;

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

    /**
     * Reviews with their customer and booking, plus the rating summary.
     *
     * @return array{reviews: \Illuminate\Support\Collection, rating: ?float, reviewCount: int, distribution: array<int,int>}
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

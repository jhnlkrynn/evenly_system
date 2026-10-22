<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Support\ServiceSearch;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Browse Services. Filters (all optional, kept in the query string):
     * search, categories[], barangay, date, available, min_price, max_price,
     * rating, events[], sort, page.
     */
    public function index(Request $request): View
    {
        return view('services.index', (new ServiceSearch($request))->toView());
    }

    public function show(Service $service): View
    {
        $service->load(['category', 'vendor.user']);
        abort_unless($service->is_active && $service->vendor->isListed(), 404);

        $vendor = $service->vendor;

        return view('services.show', [
            'service' => $service,
            'vendor' => $vendor,
            'otherServices' => $vendor->services()
                ->listed()
                ->whereKeyNot($service->id)
                ->with(['vendor', 'category'])
                ->withVendorRatings()
                ->orderBy('price')
                ->get(),
            ...VendorController::reviewData($vendor),
        ]);
    }
}

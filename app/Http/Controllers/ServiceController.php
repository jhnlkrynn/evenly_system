<?php

namespace App\Http\Controllers;

use App\Support\MockData;
use Illuminate\Contracts\View\View;

class ServiceController extends Controller
{
    /**
     * Browse Services marketplace. Accepts ?q=, ?location= and ?date= from the landing search.
     */
    public function index(): View
    {
        return view('services.index', [
            'vendors' => MockData::MARKET_VENDORS,
            'barangays' => MockData::BARANGAYS,
            'serviceTypes' => MockData::SERVICE_TYPES,
            'eventFilters' => MockData::EVENT_FILTERS,
        ]);
    }
}

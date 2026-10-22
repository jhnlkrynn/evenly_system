<?php

namespace App\Http\Controllers;

use App\Support\MockData;
use Illuminate\Contracts\View\View;

class BookingController extends Controller
{
    /**
     * Three-step booking flow (event details → services → review & pay).
     */
    public function create(): View
    {
        return view('bookings.create', [
            'eventTypes' => ['Debut', 'Wedding', 'Birthday', 'Corporate', 'Christening', 'Other'],
            'barangays' => MockData::BARANGAYS,
        ]);
    }
}

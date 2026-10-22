<?php

namespace App\Http\Controllers;

use App\Support\MockData;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Landing page.
     */
    public function index(): View
    {
        return view('home', [
            'services' => MockData::landingServices(),
            'steps' => MockData::STEPS,
            'reviews' => MockData::REVIEWS,
        ]);
    }
}

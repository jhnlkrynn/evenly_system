<?php

namespace App\Http\Controllers;

use App\Support\DashboardData;
use App\Support\ServiceSearch;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Dashboards for each role (routes are guarded by RoleMiddleware).
 * Panels still show the prototype's sample data except the vendor's Browse tab.
 */
class DashboardController extends Controller
{
    public function customer(): View
    {
        return view('dashboards.customer', DashboardData::customer());
    }

    public function vendor(Request $request): View
    {
        // The Browse Services tab embeds the live marketplace; its filters stay on this page (?tab=browse).
        $browse = new ServiceSearch($request, 'vendor.dashboard', ['tab' => 'browse']);

        return view('dashboards.vendor', [...DashboardData::vendor(), ...$browse->toView()]);
    }

    public function admin(): View
    {
        return view('dashboards.admin', DashboardData::admin());
    }

    public function superAdmin(): View
    {
        return view('dashboards.superadmin', DashboardData::superAdmin());
    }
}

<?php

namespace App\Http\Controllers;

use App\Support\DashboardData;
use Illuminate\Contracts\View\View;

/**
 * Static dashboards for each role. Access control is added once
 * authentication is wired to the database.
 */
class DashboardController extends Controller
{
    public function customer(): View
    {
        return view('dashboards.customer', DashboardData::customer());
    }

    public function vendor(): View
    {
        return view('dashboards.vendor', DashboardData::vendor());
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

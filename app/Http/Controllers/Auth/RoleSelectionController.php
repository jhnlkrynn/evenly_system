<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class RoleSelectionController extends Controller
{
    /**
     * Customer / vendor picker shown before the sign-up form.
     */
    public function show(): View
    {
        return view('auth.role', [
            'roles' => [
                ['id' => 'customer', 'icon' => '🎉', 'label' => 'Customer', 'desc' => 'Book services for my events'],
                ['id' => 'vendor', 'icon' => '🏪', 'label' => 'Vendor', 'desc' => 'List my event business'],
            ],
        ]);
    }
}

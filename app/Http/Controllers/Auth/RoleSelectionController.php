<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;

class RoleSelectionController extends Controller
{
    /**
     * Customer / vendor picker shown before the sign-up form.
     */
    public function show(): View
    {
        return view('auth.role', ['roles' => User::REGISTRATION_ROLES]);
    }
}

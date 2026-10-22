<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | laravel/ui's AuthenticatesUsers handles throttling and the session; this
    | controller adds Form Request validation, blocks deactivated accounts and
    | sends each role to its own dashboard. Logins and logouts are written to
    | activity_logs by App\Listeners\LogAuthenticationActivity.
    |
    */

    use AuthenticatesUsers {
        login as protected attemptLoginFlow;
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function login(LoginRequest $request)
    {
        return $this->attemptLoginFlow($request);
    }

    /**
     * Only active accounts may sign in. A correct password on a deactivated
     * account gets a specific message instead of "credentials do not match".
     */
    protected function attemptLogin(Request $request): bool
    {
        $user = User::where('email', $request->input('email'))->first();

        if ($user && ! $user->is_active && Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([$this->username() => __('auth.deactivated')]);
        }

        return $this->guard()->attempt(
            [...$this->credentials($request), 'is_active' => true],
            $request->boolean('remember'),
        );
    }

    /** Send the user to the page they wanted if their role allows it, otherwise to their dashboard. */
    protected function authenticated(Request $request, User $user): RedirectResponse
    {
        $intended = $request->session()->pull('url.intended');

        return redirect()->to($intended && $user->canVisit($intended) ? $intended : $user->dashboardUrl());
    }

    protected function loggedOut(Request $request): RedirectResponse
    {
        return redirect()->route('home');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Notifications\VendorRegistrationNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | Creates customer and vendor accounts. A vendor sign-up also creates an
    | unverified vendors row that the super admin approves later.
    |
    */

    use RegistersUsers;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm(): View
    {
        return view('auth.register', [
            'roles' => User::REGISTRATION_ROLES,
            'role' => old('role', request('role') === 'vendor' ? 'vendor' : 'customer'),
        ]);
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = DB::transaction(fn () => $this->create($request->validated()));

        event(new Registered($user));
        if ($user->hasRole('vendor')) {
            User::whereIn('role', ['admin', 'superadmin'])->where('is_active', true)->get()->each->notify(new VendorRegistrationNotification($user));
        }
        $this->guard()->login($user);
        $request->session()->regenerate();

        return redirect()->to($user->dashboardUrl())->with('status', $user->hasRole('vendor')
            ? 'Welcome to Eventlynk! Your vendor account is awaiting verification.'
            : 'Welcome to Eventlynk!');
    }

    protected function create(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'], // hashed by the User model cast
            'role' => $data['role'],
            'is_active' => true,
        ]);

        if ($user->hasRole('vendor')) {
            // Business details are completed later; the name is a placeholder until then.
            $user->vendor()->create([
                'business_name' => $data['name'],
                'contact_number' => $data['phone'],
                'is_verified' => false,
            ]);
        }

        return $user;
    }
}

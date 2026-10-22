<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return back()->with('profile_status', 'Your profile has been updated.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->update(['password' => $request->validated('password')]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'password_changed',
            'description' => 'Changed account password',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('password_status', 'Your password has been changed.');
    }
}

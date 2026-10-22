<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Vendor\UpdateVendorProfileRequest;
use App\Models\ActivityLog;
use App\Models\Vendor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        ActivityLog::record($user->id, 'password_changed', 'Changed account password');

        return back()->with('password_status', 'Your password has been changed.');
    }

    public function updateVendor(UpdateVendorProfileRequest $request, Vendor $vendor): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('logo')) {
            if ($vendor->logo) {
                Storage::disk('public')->delete($vendor->logo);
            }
            $data['logo'] = $request->file('logo')->store('vendor-logos', 'public');
        }

        $vendor->update($data);
        ActivityLog::record($request->user()->id, 'vendor_profile_updated', 'Updated vendor profile.');

        return back()->with('status', 'Business profile updated.');
    }
}

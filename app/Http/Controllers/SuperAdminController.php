<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuperAdmin\StoreAdminRequest;
use App\Http\Requests\SuperAdmin\StoreBarangayRequest;
use App\Http\Requests\SuperAdmin\StoreCategoryRequest;
use App\Http\Requests\SuperAdmin\UpdateCategoryRequest;
use App\Http\Requests\SuperAdmin\UpdatePlatformSettingsRequest;
use App\Models\ActivityLog;
use App\Models\Barangay;
use App\Models\PlatformSetting;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class SuperAdminController extends Controller
{
    public function storeAdmin(StoreAdminRequest $request): RedirectResponse
    {
        User::create(['name' => $request->name, 'email' => $request->email, 'password' => $request->password, 'role' => 'admin', 'is_active' => true]);
        ActivityLog::record($request->user()->id, 'admin.created', 'Admin account created: '.$request->email);

        return back()->with('success', 'Admin account created.');
    }

    public function toggleAdmin(User $user): RedirectResponse
    {
        abort_unless($user->role === 'admin', 404);
        $user->update(['is_active' => ! $user->is_active]);
        ActivityLog::record(request()->user()->id, 'admin.status', "{$user->email} ".($user->is_active ? 'activated' : 'deactivated'));

        return back()->with('success', 'Admin status updated.');
    }

    public function storeCategory(StoreCategoryRequest $request): RedirectResponse
    {
        ServiceCategory::create($request->validated());
        ActivityLog::record($request->user()->id, 'category.created', 'Category created: '.$request->name);

        return back()->with('success', 'Category created.');
    }

    public function updateCategory(UpdateCategoryRequest $request, ServiceCategory $category): RedirectResponse
    {
        $category->update($request->validated());
        ActivityLog::record($request->user()->id, 'category.updated', 'Category updated: '.$category->name);

        return back()->with('success', 'Category updated.');
    }

    public function storeBarangay(StoreBarangayRequest $request): RedirectResponse
    {
        Barangay::create($request->validated());
        ActivityLog::record($request->user()->id, 'barangay.created', 'Barangay created: '.$request->name);

        return back()->with('success', 'Barangay created.');
    }

    public function updateSettings(UpdatePlatformSettingsRequest $request): RedirectResponse
    {
        foreach ($request->validated() as $key => $value) {
            PlatformSetting::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        }

        ActivityLog::record($request->user()->id, 'settings.updated', 'Platform settings updated');

        return back()->with('success', 'Platform settings saved.');
    }
}

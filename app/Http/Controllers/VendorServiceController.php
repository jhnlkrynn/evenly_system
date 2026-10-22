<?php

namespace App\Http\Controllers;

use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Models\ActivityLog;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class VendorServiceController extends Controller
{
    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service = $request->user()->vendor->services()->create($data);
        ActivityLog::record($request->user()->id, 'service_created', 'Added service '.$service->name.'.');

        return back()->with('status', 'Service added.');
    }

    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);
        ActivityLog::record($request->user()->id, 'service_updated', 'Updated service '.$service->name.'.');

        return back()->with('status', 'Service updated.');
    }

    public function toggle(Service $service): RedirectResponse
    {
        $this->authorize('update', $service);
        $service->update(['is_active' => ! $service->is_active]);

        return back()->with('status', $service->name.' is now '.($service->is_active ? 'active' : 'paused').'.');
    }
}

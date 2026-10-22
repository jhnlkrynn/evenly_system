<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\AssignStaffRequest;
use App\Http\Requests\Staff\StoreStaffRequest;
use App\Http\Requests\Staff\UpdateStaffRequest;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    public function store(StoreStaffRequest $request): RedirectResponse
    {
        $staff = $request->user()->vendor->staff()->create($request->validated());
        ActivityLog::record($request->user()->id, 'staff_created', 'Added '.$staff->full_name.' to the vendor roster.');

        return back()->with('status', 'Staff member added.');
    }

    public function update(UpdateStaffRequest $request, Staff $staff): RedirectResponse
    {
        $staff->update($request->validated());
        ActivityLog::record($request->user()->id, 'staff_updated', 'Updated '.$staff->full_name.'.');

        return back()->with('status', 'Staff member updated.');
    }

    public function deactivate(Staff $staff): RedirectResponse
    {
        $this->authorize('delete', $staff);
        $staff->update(['is_active' => false]);

        return back()->with('status', 'Staff member deactivated.');
    }

    public function assign(AssignStaffRequest $request, Booking $booking): RedirectResponse
    {
        $staffIds = $request->validated('staff_ids') ?? [];

        DB::transaction(function () use ($booking, $staffIds): void {
            $lockedBooking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $staff = Staff::whereIn('id', $staffIds)
                ->where('vendor_id', $lockedBooking->vendor_id)
                ->where('is_active', true)
                ->get();

            if ($staff->count() !== count($staffIds)) {
                throw ValidationException::withMessages(['staff_ids' => 'Choose active staff from your own roster.']);
            }

            $conflict = Staff::whereIn('id', $staffIds)
                ->whereHas('bookings', function ($query) use ($lockedBooking): void {
                    $query->whereKeyNot($lockedBooking->id)
                        ->whereDate('event_date', $lockedBooking->event_date)
                        ->where('status', '!=', 'Cancelled');
                })
                ->exists();

            if ($conflict) {
                throw ValidationException::withMessages(['staff_ids' => 'A selected staff member is already assigned on this event date.']);
            }

            $lockedBooking->staff()->sync($staffIds);
        });

        ActivityLog::record($request->user()->id, 'staff_assigned', 'Updated staff assignment for '.$booking->booking_code.'.');

        return back()->with('status', 'Staff assignment saved.');
    }
}

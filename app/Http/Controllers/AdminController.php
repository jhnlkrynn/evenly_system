<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\ReportRequest;
use App\Http\Requests\Admin\UpdateBookingStatusRequest;
use App\Http\Requests\Admin\UpdateServiceStatusRequest;
use App\Http\Requests\Admin\UpdateUserStatusRequest;
use App\Http\Requests\Admin\UpdateVendorVerificationRequest;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\BookingNotification;
use App\Notifications\VendorVerificationNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    public function verifyVendor(UpdateVendorVerificationRequest $request, Vendor $vendor): RedirectResponse
    {
        $this->authorize('verify', $vendor);
        $vendor->update(['is_verified' => $request->status === 'Approved', 'verification_reason' => $request->reason]);
        ActivityLog::record($request->user()->id, 'vendor.verification', $request->status.' '.$vendor->business_name);
        $vendor->load('user');
        $vendor->user->notify(new VendorVerificationNotification($vendor, strtolower($request->status)));

        return back()->with('success', 'Vendor verification updated.');
    }

    public function updateUserStatus(UpdateUserStatusRequest $request, User $user): RedirectResponse
    {
        abort_if($user->hasRole('superadmin') && ! $request->user()->hasRole('superadmin'), 403);
        $user->update(['is_active' => $request->boolean('is_active')]);
        ActivityLog::record($request->user()->id, 'user.status', "{$user->email} ".($user->is_active ? 'activated' : 'deactivated'));

        return back()->with('success', 'User status updated.');
    }

    public function updateServiceStatus(UpdateServiceStatusRequest $request, Service $service): RedirectResponse
    {
        $this->authorize('adminUpdate', $service);
        $service->update(['is_active' => $request->boolean('is_active')]);
        ActivityLog::record($request->user()->id, 'service.status', "{$service->name} ".($service->is_active ? 'activated' : 'deactivated'));

        return back()->with('success', 'Service status updated.');
    }

    public function updateBookingStatus(UpdateBookingStatusRequest $request, Booking $booking): RedirectResponse
    {
        $this->authorize('changeStatus', $booking);
        $booking->update(['status' => $request->string('status')->toString()]);
        ActivityLog::record($request->user()->id, 'booking.status', "{$booking->booking_code} status changed to {$booking->status}");
        $booking->load('customer');
        $booking->customer->notify(new BookingNotification($booking, strtolower($booking->status)));

        return back()->with('success', 'Booking status updated.');
    }

    public function report(ReportRequest $request)
    {
        $bookings = Booking::with(['vendor', 'service.category'])->whereBetween('event_date', [$request->date_from, $request->date_to])->where('status', '!=', 'Cancelled')->get();
        $topVendors = $bookings->groupBy('vendor_id')->map(fn ($rows) => ['name' => $rows->first()->vendor?->business_name ?? '—', 'bookings' => $rows->count(), 'revenue' => $rows->sum('total_amount')])->sortByDesc('revenue')->take(10);

        return Pdf::loadView('pdf.admin-report', ['from' => $request->date_from, 'to' => $request->date_to, 'bookings' => $bookings, 'revenue' => $bookings->sum('total_amount'), 'topVendors' => $topVendors])->download('eventlynk-report.pdf');
    }
}

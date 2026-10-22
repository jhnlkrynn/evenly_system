<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\StoreBookingRequest;
use App\Models\ActivityLog;
use App\Models\Barangay;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Vendor;
use App\Support\BookingPricing;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /** Pre-ticked services for each category (staff counts follow the prototype's 100-guest suggestion). */
    private const DEFAULT_SERVICES = [
        'Decoration' => ['decoration' => true, 'serving' => 0, 'dishwashing' => 0],
        'Serving Staff' => ['decoration' => false, 'serving' => 8, 'dishwashing' => 0],
        'Dishwashing' => ['decoration' => false, 'serving' => 0, 'dishwashing' => 4],
    ];

    /** Three-step booking form (BookingPage.tsx) for the selected service. */
    public function create(Request $request): View|RedirectResponse
    {
        $service = Service::listed()->with(['vendor', 'category'])->find($request->integer('service'));

        if (! $service) {
            return redirect()->route('services.index')
                ->with('status', 'Choose a service first, then press “Book this service”.');
        }

        $defaults = self::DEFAULT_SERVICES[$service->category->name] ?? ['decoration' => true, 'serving' => 8, 'dishwashing' => 4];

        return view('bookings.create', [
            'service' => $service,
            'vendor' => $service->vendor,
            'eventTypes' => Booking::EVENT_TYPES,
            'themes' => Booking::THEMES,
            'barangays' => Barangay::orderBy('id')->pluck('name'),
            'pricing' => BookingPricing::forJavaScript(),
            // Restores the form after a failed submit.
            'initial' => [
                'occasion' => old('event_type', 'Debut'),
                'date' => old('event_date', ''),
                'startTime' => old('start_time', '18:00'),
                'endTime' => old('end_time', '23:00'),
                'venue' => old('venue', ''),
                'barangay' => old('barangay', $service->vendor->barangay && Barangay::where('name', $service->vendor->barangay)->exists() ? $service->vendor->barangay : ''),
                'guests' => (int) old('pax', 100),
                'decoration' => $this->oldBoolean($request, 'needs_decoration', $defaults['decoration']),
                'serving' => (int) old('serving_count', $defaults['serving']),
                'dishwashing' => (int) old('dishwashing_count', $defaults['dishwashing']),
                'theme' => old('theme', 'Gold & ivory'),
                'notes' => old('special_requests', ''),
                'payment' => old('payment_method', 'GCash'),
            ],
            'initialStep' => $this->stepWithFirstError($request),
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $service = $request->service();
        $customer = $request->user();

        $booking = DB::transaction(function () use ($data, $service, $customer) {
            // Lock the vendor so two customers can't take the same date at once.
            Vendor::whereKey($service->vendor_id)->lockForUpdate()->first();

            if (Booking::vendorIsBookedOn($service->vendor_id, $data['event_date'])) {
                throw ValidationException::withMessages([
                    'event_date' => 'Someone just booked '.$service->vendor->business_name.' on that date. Please choose another date.',
                ]);
            }

            $decoration = (bool) $data['needs_decoration'];
            $total = BookingPricing::total($decoration, (int) $data['serving_count'], (int) $data['dishwashing_count']);

            $booking = Booking::create([
                'booking_code' => 'TMP-'.Str::random(12),
                'customer_id' => $customer->id,
                'vendor_id' => $service->vendor_id,
                'service_id' => $service->id,
                'event_type' => $data['event_type'],
                'event_date' => $data['event_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'venue' => $data['venue'],
                'barangay' => $data['barangay'],
                'pax' => $data['pax'],
                'needs_decoration' => $decoration,
                'serving_count' => $data['serving_count'],
                'dishwashing_count' => $data['dishwashing_count'],
                'special_requests' => $this->specialRequests($data, $decoration),
                'total_amount' => $total,
                'reservation_fee' => BookingPricing::reservationFee($total),
                'amount_paid' => 0,
                'status' => 'Pending',
                'payment_status' => 'Unpaid',
            ]);

            // BK-001, BK-002, ... from the row id, so codes never collide.
            $booking->update(['booking_code' => 'BK-'.str_pad((string) $booking->id, 3, '0', STR_PAD_LEFT)]);

            ActivityLog::record($customer->id, 'booking_created', sprintf(
                'Booked %s (%s) with %s for %s — total ₱%s',
                $booking->booking_code, $service->name, $service->vendor->business_name,
                $booking->event_date->format('M j, Y'), number_format($total),
            ));

            return $booking;
        });

        return redirect()->route('bookings.show', $booking)
            ->with('booked', true)
            ->with('preferred_method', $data['payment_method'] ?? 'GCash');
    }

    /** Confirmation / details page for the customer, its vendor and admins. */
    public function show(Request $request, Booking $booking): View
    {
        $this->authorize('view', $booking);

        $booking->load(['customer', 'vendor', 'service.category', 'staff', 'payments' => fn ($q) => $q->latest()]);

        return view('bookings.show', [
            'booking' => $booking,
            'justBooked' => (bool) $request->session()->get('booked'),
            'canPay' => $request->user()->can('pay', $booking),
        ]);
    }

    private function specialRequests(array $data, bool $decoration): ?string
    {
        $parts = array_filter([
            $decoration && ! empty($data['theme']) ? 'Theme: '.$data['theme'] : null,
            trim($data['special_requests'] ?? '') ?: null,
        ]);

        return $parts ? implode("\n", $parts) : null;
    }

    private function oldBoolean(Request $request, string $key, bool $default): bool
    {
        return $request->hasOldInput($key)
            ? filter_var($request->old($key), FILTER_VALIDATE_BOOLEAN)
            : $default;
    }

    /** After a failed submit, reopen the step whose field has the first error. */
    private function stepWithFirstError(Request $request): int
    {
        $errors = $request->session()->get('errors')?->keys() ?? [];
        $first = $errors[0] ?? null;

        return match (true) {
            $first === null => 1,
            in_array($first, ['needs_decoration', 'serving_count', 'dishwashing_count', 'theme', 'special_requests'], true) => 2,
            in_array($first, ['terms', 'payment_method'], true) => 3,
            default => 1,
        };
    }
}

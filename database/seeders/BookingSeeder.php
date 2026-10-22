<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Staff;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\InAppNotification;
use App\Support\DashboardData;
use App\Support\MockData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Customers, the 8 shared bookings (with crew and payments), the three
 * completed bookings behind the homepage testimonials, and Maria Santos'
 * dashboard notifications.
 */
class BookingSeeder extends Seeder
{
    /** Barangay of each mock venue. */
    private const VENUE_BARANGAYS = [
        'BK-001' => 'Poblacion',
        'BK-002' => 'Wawa',
        'BK-003' => 'Poblacion',
        'BK-004' => 'Calayo',
        'BK-005' => 'Bucana',
        'BK-006' => 'Calayo',
        'BK-007' => 'Reparo',
        'BK-008' => 'Balaytigue',
    ];

    /** How each paid amount was settled. */
    private const PAYMENT_METHODS = [
        'BK-001' => 'GCash',
        'BK-002' => 'Maya',
        'BK-003' => 'GCash',
        'BK-004' => 'Cash',
        'BK-007' => 'GCash',
        'BK-008' => 'Maya',
    ];

    /**
     * Completed bookings backing the homepage reviews (App.tsx REVIEWS).
     * The testimonials name an event and place that none of the 8 mock bookings
     * match, so each gets its own past booking with the vendor based in that barangay.
     */
    private const TESTIMONIAL_BOOKINGS = [
        'Maria Santos' => ['code' => 'BK-009', 'vendor' => 'Elegant Events & Co.', 'event' => 'Debut', 'date' => '2026-03-14', 'start' => '17:00', 'end' => '22:00', 'venue' => 'Private residence, Nasugbu, Batangas', 'barangay' => null, 'pax' => 120, 'decoration' => true, 'serving' => 0, 'dishwashing' => 0, 'method' => 'GCash'],
        'Carla Mendoza' => ['code' => 'BK-010', 'vendor' => 'Prime Serve Staffing', 'event' => 'Wedding', 'date' => '2026-05-23', 'start' => '15:00', 'end' => '22:00', 'venue' => 'Brgy. Wawa, Nasugbu, Batangas', 'barangay' => 'Wawa', 'pax' => 150, 'decoration' => false, 'serving' => 8, 'dishwashing' => 0, 'method' => 'Maya'],
        'Paolo Lim' => ['code' => 'BK-011', 'vendor' => 'All-in-One Events PH', 'event' => 'Corporate Event', 'date' => '2026-06-19', 'start' => '08:00', 'end' => '17:00', 'venue' => 'Brgy. Calayo, Nasugbu, Batangas', 'barangay' => 'Calayo', 'pax' => 100, 'decoration' => true, 'serving' => 6, 'dishwashing' => 3, 'method' => 'Cash'],
    ];

    private array $customers = [];

    public function run(): void
    {
        $crew = Staff::whereNull('vendor_id')->pluck('id', 'full_name');

        foreach (MockData::bookings() as $mock) {
            $vendor = Vendor::where('business_name', $mock['vendorName'])->firstOrFail();
            $service = $vendor->services()->where('name', $mock['serviceName'])->firstOrFail();
            [$start, $end] = array_map(
                fn ($time) => Carbon::createFromFormat('g:i A', trim($time))->format('H:i'),
                explode('–', $mock['time']),
            );

            $booking = Booking::create([
                'booking_code' => $mock['id'],
                'customer_id' => $this->customer($mock['client'])->id,
                'vendor_id' => $vendor->id,
                'service_id' => $service->id,
                'event_type' => $mock['eventType'],
                'event_date' => Carbon::createFromFormat('M j, Y', $mock['date'])->toDateString(),
                'start_time' => $start,
                'end_time' => $end,
                'venue' => $mock['venue'],
                'barangay' => self::VENUE_BARANGAYS[$mock['id']],
                'pax' => $mock['pax'],
                'needs_decoration' => $mock['services']['decoration'],
                'serving_count' => $mock['services']['serving'],
                'dishwashing_count' => $mock['services']['dishwashing'],
                'special_requests' => $mock['specialRequests'],
                'total_amount' => $mock['totalAmount'],
                'reservation_fee' => round($mock['totalAmount'] * 0.3),
                'amount_paid' => $mock['reservationFee'],
                'status' => $mock['status'],
                'payment_status' => $mock['paymentStatus'],
            ]);

            $assigned = array_merge(...array_values($mock['assignedStaff']));
            $booking->staff()->attach($crew->only($assigned)->values());

            if ($mock['reservationFee'] > 0) {
                $this->recordPayment($booking, $mock['reservationFee'], self::PAYMENT_METHODS[$mock['id']]);
            }
        }

        $this->seedTestimonials();
        $this->seedNotifications();
    }

    private function seedTestimonials(): void
    {
        $reviews = collect(MockData::REVIEWS)->keyBy('name');

        foreach (self::TESTIMONIAL_BOOKINGS as $client => $data) {
            $vendor = Vendor::where('business_name', $data['vendor'])->firstOrFail();
            // Their main Browse listing (the first service created for each marketplace vendor).
            $service = $vendor->services()->oldest('id')->firstOrFail();
            $total = $service->price_unit === 'staff'
                ? $service->price * $data['serving']
                : (float) $service->price;

            $booking = Booking::create([
                'booking_code' => $data['code'],
                'customer_id' => $this->customer($client)->id,
                'vendor_id' => $vendor->id,
                'service_id' => $service->id,
                'event_type' => $data['event'],
                'event_date' => $data['date'],
                'start_time' => $data['start'],
                'end_time' => $data['end'],
                'venue' => $data['venue'],
                'barangay' => $data['barangay'],
                'pax' => $data['pax'],
                'needs_decoration' => $data['decoration'],
                'serving_count' => $data['serving'],
                'dishwashing_count' => $data['dishwashing'],
                'total_amount' => $total,
                'reservation_fee' => round($total * 0.3),
                'amount_paid' => $total,
                'status' => 'Completed',
                'payment_status' => 'Paid',
            ]);

            $this->recordPayment($booking, $total, $data['method']);

            $booking->review()->create([
                'customer_id' => $booking->customer_id,
                'vendor_id' => $vendor->id,
                'rating' => 5,
                'comment' => $reviews[$client]['quote'],
            ]);
        }
    }

    /** Maria Santos' notification feed from the customer dashboard. */
    private function seedNotifications(): void
    {
        $maria = $this->customer('Maria Santos');
        $ages = ['2 hours ago' => 2, '4 hours ago' => 4, '6 hours ago' => 6, '1 day ago' => 24, '2 days ago' => 48, '3 days ago' => 72, '4 days ago' => 96];

        foreach (DashboardData::customer()['notifications'] as $item) {
            preg_match('/BK-\d{3}/', $item['text'], $code);
            $notification = new InAppNotification($item['text'], $item['icon'], $code[0] ?? null);
            $sentAt = now()->subHours($ages[$item['time']]);

            $maria->notifications()->create([
                'id' => (string) Str::uuid(),
                'type' => InAppNotification::class,
                'data' => $notification->toArray($maria),
                'read_at' => $item['unread'] ? null : $sentAt,
                'created_at' => $sentAt,
                'updated_at' => $sentAt,
            ]);
        }
    }

    private function recordPayment(Booking $booking, float $amount, string $method): void
    {
        $paidOn = $booking->event_date->copy()->subDays(21);

        $booking->payments()->create([
            'amount' => $amount,
            'method' => $method,
            'type' => $amount >= (float) $booking->total_amount ? 'Full' : 'Reservation',
            'reference_no' => $method === 'Cash' ? null : strtoupper(substr($method, 0, 2)).$paidOn->format('ymd').substr($booking->booking_code, 3),
        ])->forceFill(['created_at' => $paidOn, 'updated_at' => $paidOn])->save();
    }

    /** One login per booking client (first.last@email.com). */
    private function customer(string $name): User
    {
        return $this->customers[$name] ??= DatabaseSeeder::account(
            name: $name,
            email: Str::of($name)->lower()->replace(' ', '.').'@email.com',
            role: 'customer',
            // Maria's number is the one shown on her profile settings.
            phone: $name === 'Maria Santos' ? '09171234567' : null,
        );
    }
}

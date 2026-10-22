<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_booking_form_redirects_to_login_with_intended_url(): void
    {
        $service = $this->service();

        $this->get(route('customer.bookings.create', ['service' => $service->id]))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_booked_dates_endpoint_returns_upcoming_non_cancelled_dates(): void
    {
        $service = $this->service();
        Booking::factory()->for($service)->create([
            'vendor_id' => $service->vendor_id,
            'event_date' => now()->addDays(20)->toDateString(),
            'status' => 'Confirmed',
        ]);
        Booking::factory()->for($service)->create([
            'vendor_id' => $service->vendor_id,
            'event_date' => now()->addDays(21)->toDateString(),
            'status' => 'Cancelled',
        ]);

        $this->getJson(route('vendors.booked-dates', $service->vendor))
            ->assertOk()
            ->assertJsonPath('booked.0', now()->addDays(20)->toDateString())
            ->assertJsonCount(1, 'booked');
    }

    public function test_customer_can_create_a_booking_with_server_calculated_amounts(): void
    {
        $customer = User::factory()->create();
        $service = $this->service();
        $date = now()->addDays(30)->toDateString();

        $response = $this->actingAs($customer)->post(route('customer.bookings.store'), [
            'service_id' => $service->id,
            'event_type' => 'Wedding',
            'event_date' => $date,
            'start_time' => '18:00',
            'end_time' => '23:00',
            'venue' => 'Beach house',
            'barangay' => 'Wawa',
            'pax' => 100,
            'needs_decoration' => '1',
            'serving_count' => 8,
            'dishwashing_count' => 4,
            'theme' => 'Gold & ivory',
            'special_requests' => 'No shellfish',
            'payment_method' => 'GCash',
            'terms' => '1',
        ]);

        $booking = Booking::query()->latest('id')->firstOrFail();

        $response->assertRedirectToRoute('bookings.show', $booking);
        $this->assertSame('BK-'.str_pad((string) $booking->id, 3, '0', STR_PAD_LEFT), $booking->booking_code);
        $this->assertSame('Pending', $booking->status);
        $this->assertSame('Unpaid', $booking->payment_status);
        $this->assertSame('13000.00', $booking->total_amount);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $customer->id,
            'action' => 'booking_created',
        ]);
    }

    public function test_customer_cannot_book_a_vendor_date_that_is_already_held(): void
    {
        $customer = User::factory()->create();
        $service = $this->service();
        $date = now()->addDays(30)->toDateString();
        Booking::factory()->for($service)->create([
            'vendor_id' => $service->vendor_id,
            'event_date' => $date,
            'status' => 'Pending',
        ]);

        $this->actingAs($customer)->post(route('customer.bookings.store'), $this->bookingPayload($service, $date))
            ->assertSessionHasErrors(['event_date']);

        $this->assertSame(1, Booking::where('vendor_id', $service->vendor_id)->whereDate('event_date', $date)->count());
    }

    public function test_customer_can_record_demo_payment_and_payment_status_updates(): void
    {
        $customer = User::factory()->create();
        $booking = Booking::factory()->for($customer, 'customer')->create([
            'total_amount' => 10000,
            'reservation_fee' => 3000,
            'amount_paid' => 0,
            'payment_status' => 'Unpaid',
        ]);

        $this->actingAs($customer)
            ->post(route('customer.payments.store', $booking), ['method' => 'GCash', 'type' => 'Reservation'])
            ->assertRedirectToRoute('bookings.show', $booking);

        $booking->refresh();
        $this->assertSame('3000.00', $booking->amount_paid);
        $this->assertSame('Partial', $booking->payment_status);
        $this->assertDatabaseHas('payments', ['booking_id' => $booking->id, 'type' => 'Reservation', 'method' => 'GCash']);
        $this->assertDatabaseHas('activity_logs', ['user_id' => $customer->id, 'action' => 'payment_recorded']);
    }

    public function test_customer_sees_latest_booking_on_homepage(): void
    {
        $customer = User::factory()->create();
        $service = $this->service(['name' => 'Latest Wedding Setup']);
        Booking::factory()->for($customer, 'customer')->for($service)->create([
            'vendor_id' => $service->vendor_id,
            'event_date' => now()->addDays(40)->toDateString(),
        ]);

        $this->actingAs($customer)->get(route('home'))
            ->assertOk()
            ->assertSee('Latest Wedding Setup')
            ->assertSee('Booking confirmed');
    }

    public function test_booking_documents_are_forbidden_to_another_customer(): void
    {
        $owner = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $booking = Booking::factory()->for($owner, 'customer')->create();

        $this->actingAs($otherCustomer)->get(route('bookings.slip', $booking))
            ->assertForbidden();
    }

    /** @param array<string, mixed> $attributes */
    private function service(array $attributes = []): Service
    {
        Barangay::firstOrCreate(['name' => 'Wawa']);
        $category = ServiceCategory::firstOrCreate(['name' => 'Decoration'], ['icon' => '🌸']);

        return Service::factory()->for(Vendor::factory()->state([
            'barangay' => 'Wawa',
        ]))->create([
            'service_category_id' => $category->id,
            'price' => 12000,
            'price_unit' => 'event',
            ...$attributes,
        ]);
    }

    /** @return array<string, mixed> */
    private function bookingPayload(Service $service, string $date): array
    {
        return [
            'service_id' => $service->id,
            'event_type' => 'Wedding',
            'event_date' => $date,
            'start_time' => '18:00',
            'end_time' => '23:00',
            'venue' => 'Beach house',
            'barangay' => 'Wawa',
            'pax' => 100,
            'needs_decoration' => '1',
            'serving_count' => 8,
            'dishwashing_count' => 4,
            'theme' => 'Gold & ivory',
            'terms' => '1',
        ];
    }
}

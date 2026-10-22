<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_dashboard_renders_owned_booking_and_can_cancel_pending_booking(): void
    {
        $customer = User::factory()->create();
        $booking = Booking::factory()->for($customer, 'customer')->create(['status' => 'Pending']);

        $this->actingAs($customer)->get(route('customer.dashboard'))
            ->assertOk()
            ->assertSee($booking->booking_code)
            ->assertSee($booking->vendor->business_name);

        $this->actingAs($customer)->post(route('customer.bookings.cancel', $booking), ['confirmation' => '1'])
            ->assertRedirect();

        $this->assertSame('Cancelled', $booking->refresh()->status);
    }

    public function test_customer_can_leave_only_one_review_for_a_completed_booking(): void
    {
        $customer = User::factory()->create();
        $booking = Booking::factory()->for($customer, 'customer')->create(['status' => 'Completed']);

        $this->actingAs($customer)->post(route('customer.bookings.reviews.store', $booking), [
            'rating' => 5,
            'comment' => 'Excellent service.',
        ])->assertRedirect();

        $this->assertDatabaseHas('reviews', ['booking_id' => $booking->id, 'customer_id' => $customer->id, 'rating' => 5]);
        $this->actingAs($customer)->post(route('customer.bookings.reviews.store', $booking), [
            'rating' => 4,
            'comment' => 'Duplicate.',
        ])->assertForbidden();
    }

    public function test_vendor_dashboard_renders_owned_data_and_can_confirm_a_booking(): void
    {
        $vendorUser = User::factory()->vendor()->create();
        $vendor = Vendor::factory()->for($vendorUser)->create();
        $service = Service::factory()->for($vendor)->create();
        $booking = Booking::factory()->for($service)->create([
            'vendor_id' => $vendor->id,
            'status' => 'Pending',
        ]);

        $this->actingAs($vendorUser)->get(route('vendor.dashboard'))
            ->assertOk()
            ->assertSee($service->name)
            ->assertSee($booking->booking_code);

        $this->actingAs($vendorUser)->patch(route('vendor.bookings.status', $booking), ['status' => 'Confirmed'])
            ->assertRedirect();

        $this->assertSame('Confirmed', $booking->refresh()->status);
    }

    public function test_vendor_staff_cannot_be_assigned_to_two_bookings_on_the_same_date(): void
    {
        $vendorUser = User::factory()->vendor()->create();
        $vendor = Vendor::factory()->for($vendorUser)->create();
        $service = Service::factory()->for($vendor)->create();
        $staff = Staff::factory()->for($vendor)->create();
        $date = now()->addDays(20)->toDateString();
        $first = Booking::factory()->for($service)->create(['vendor_id' => $vendor->id, 'event_date' => $date, 'status' => 'Confirmed']);
        $second = Booking::factory()->for($service)->create(['vendor_id' => $vendor->id, 'event_date' => $date, 'status' => 'Confirmed']);
        $first->staff()->attach($staff);

        $this->actingAs($vendorUser)->post(route('vendor.bookings.staff', $second), ['staff_ids' => [$staff->id]])
            ->assertSessionHasErrors('staff_ids');
    }
}

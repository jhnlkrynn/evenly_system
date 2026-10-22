<?php

namespace Tests\Feature;

use App\Models\Barangay;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Vendor;
use App\Support\MockData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BrowseServicesTest extends TestCase
{
    use RefreshDatabase;

    private ServiceCategory $decoration;

    private ServiceCategory $serving;

    protected function setUp(): void
    {
        parent::setUp();

        $this->decoration = ServiceCategory::create(['name' => 'Decoration', 'icon' => '🌸']);
        $this->serving = ServiceCategory::create(['name' => 'Serving Staff', 'icon' => '🍽']);
        ServiceCategory::create(['name' => 'Dishwashing', 'icon' => '🫧']);
        ServiceCategory::create(['name' => 'Full Package', 'icon' => '🎁']);
        foreach (MockData::BARANGAYS as $name) {
            Barangay::create(['name' => $name]);
        }
    }

    private function service(array $attributes = [], array $vendor = []): Service
    {
        return Service::factory()
            ->for(Vendor::factory()->state(['barangay' => 'Wawa', ...$vendor]))
            ->create(['service_category_id' => $this->decoration->id, 'price' => 10000, 'price_unit' => 'event', ...$attributes]);
    }

    /** Names of the services on the results page, in order. */
    private function resultNames(string $query = ''): array
    {
        $response = $this->get('/services'.($query ? "?{$query}" : ''))->assertOk();
        preg_match_all('/class="vendor-service-name"[^>]*>([^<]+)</', $response->getContent(), $matches);

        return array_map('html_entity_decode', $matches[1]);
    }

    public function test_only_active_services_from_verified_active_vendors_are_listed(): void
    {
        $this->service(['name' => 'Visible Setup']);
        $this->service(['name' => 'Paused Setup', 'is_active' => false]);
        $this->service(['name' => 'Unverified Setup'], ['is_verified' => false]);
        $this->service(['name' => 'Suspended Setup'], ['user_id' => User::factory()->vendor()->inactive()]);

        $this->assertSame(['Visible Setup'], $this->resultNames());
    }

    public function test_search_matches_service_vendor_and_category(): void
    {
        $this->service(['name' => 'Balloon Arch', 'description' => 'Pastel arches']);
        $this->service(['name' => 'Waiters', 'service_category_id' => $this->serving->id], ['business_name' => 'Golden Hands']);

        $this->assertSame(['Balloon Arch'], $this->resultNames('search=pastel'));
        $this->assertSame(['Waiters'], $this->resultNames('search=golden'));
        $this->assertSame(['Waiters'], $this->resultNames('search=serving'));
    }

    public function test_category_barangay_and_price_filters(): void
    {
        $this->service(['name' => 'Cheap Decor', 'price' => 2000]);
        $this->service(['name' => 'Waiters', 'service_category_id' => $this->serving->id, 'price' => 750], ['barangay' => 'Calayo']);
        $this->service(['name' => 'Town Decor', 'price' => 20000], ['barangay' => 'Poblacion']);

        $this->assertSame(['Waiters'], $this->resultNames("categories[]={$this->serving->id}"));
        $this->assertSame(['Waiters'], $this->resultNames('barangay=Calayo'));
        $this->assertSame(['Town Decor'], $this->resultNames('barangay='.urlencode('Barangay 3 (Pob.)')));
        $this->assertSame(['Cheap Decor', 'Waiters'], $this->resultNames('max_price=5000&sort=price_desc'));
        $this->assertSame(['Cheap Decor', 'Town Decor'], $this->resultNames('min_price=1000&sort=price_asc'));
    }

    public function test_date_excludes_vendors_with_a_non_cancelled_booking_that_day(): void
    {
        $booked = $this->service(['name' => 'Booked Vendor']);
        $cancelled = $this->service(['name' => 'Cancelled Booking Vendor']);
        $this->service(['name' => 'Free Vendor']);

        Booking::factory()->create(['service_id' => $booked->id, 'event_date' => '2026-12-12', 'status' => 'Confirmed']);
        Booking::factory()->create(['service_id' => $cancelled->id, 'event_date' => '2026-12-12', 'status' => 'Cancelled']);

        $this->assertEqualsCanonicalizing(['Cancelled Booking Vendor', 'Free Vendor'], $this->resultNames('date=2026-12-12'));
        $this->assertCount(3, $this->resultNames('date=2026-12-13'));

        // "Only show vendors free on…" switched off: everyone is listed, booked vendors are labelled.
        $this->get('/services?date=2026-12-12&available=0')->assertOk()->assertSee('Booked on Dec 12');
    }

    public function test_sorting_by_rating_and_event_filter(): void
    {
        $plain = $this->service(['name' => 'Unrated']);
        $loved = $this->service(['name' => 'Five Star Debut Decor', 'description' => 'Perfect for a debut']);

        $booking = Booking::factory()->create(['service_id' => $loved->id, 'status' => 'Completed']);
        Review::factory()->create(['booking_id' => $booking->id, 'customer_id' => $booking->customer_id, 'vendor_id' => $loved->vendor_id, 'rating' => 5]);

        $this->assertSame(['Five Star Debut Decor', 'Unrated'], $this->resultNames('sort=rating'));
        $this->assertSame(['Five Star Debut Decor'], $this->resultNames('rating=4.8'));
        $this->assertSame(['Five Star Debut Decor'], $this->resultNames('events[]=debut'));
        $this->assertNotNull($plain);
    }

    public function test_pagination_keeps_filters_in_the_query_string(): void
    {
        foreach (range(1, 15) as $i) {
            $this->service(['name' => "Setup {$i}", 'price' => 1000 + $i]);
        }

        $page1 = $this->get('/services?sort=price_asc&max_price=99999')->assertOk();
        $this->assertCount(12, $this->resultNames('sort=price_asc&max_price=99999'));
        $page1->assertSee('page=2', false)->assertSee('sort=price_asc', false)->assertSee('max_price=99999', false);
        $this->assertSame(['Setup 13', 'Setup 14', 'Setup 15'], $this->resultNames('sort=price_asc&max_price=99999&page=2'));
    }

    public function test_empty_state_and_invalid_filters(): void
    {
        $this->service(['name' => 'Only One']);

        $this->get('/services?search=nothing-matches')->assertOk()->assertSee('No services match those filters');
        $this->assertSame(['Only One'], $this->resultNames('date=not-a-date&min_price=-5&rating=9&sort=hack&categories[]=x'));
    }

    public function test_listing_runs_a_fixed_number_of_queries(): void
    {
        $count = function () {
            DB::flushQueryLog();
            DB::enableQueryLog();
            $this->get('/services?sort=rating')->assertOk();

            return count(DB::getQueryLog());
        };

        $this->service();
        $few = $count();

        foreach (range(1, 10) as $ignored) {
            $this->service();
        }

        $this->assertSame($few, $count(), 'Query count grew with the number of services (N+1).');
    }

    public function test_service_and_vendor_pages_show_details_services_and_reviews(): void
    {
        $service = $this->service(['name' => 'Garden Arch', 'description' => 'Fresh flowers.'], ['business_name' => 'Bloom House']);
        $other = Service::factory()->for($service->vendor)->create(['name' => 'Table Florals', 'service_category_id' => $this->decoration->id]);
        $booking = Booking::factory()->create(['service_id' => $service->id, 'status' => 'Completed', 'event_type' => 'Wedding']);
        Review::factory()->create(['booking_id' => $booking->id, 'customer_id' => $booking->customer_id, 'vendor_id' => $service->vendor_id, 'rating' => 4, 'comment' => 'Lovely arch.']);

        $this->get("/services/{$service->id}")->assertOk()
            ->assertSee('Garden Arch')->assertSee('Fresh flowers.')->assertSee('Bloom House')
            ->assertSee('Table Florals')->assertSee('Lovely arch.')->assertSee('4.0 out of 5 stars');

        $this->get("/vendors/{$service->vendor_id}")->assertOk()
            ->assertSee('Garden Arch')->assertSee('Table Florals')->assertSee('Lovely arch.');

        $this->assertNotNull($other);
    }

    public function test_hidden_services_and_vendors_return_404(): void
    {
        $paused = $this->service(['is_active' => false]);
        $unverified = $this->service([], ['is_verified' => false]);

        $this->get("/services/{$paused->id}")->assertNotFound();
        $this->get("/services/{$unverified->id}")->assertNotFound();
        $this->get("/vendors/{$unverified->vendor_id}")->assertNotFound();
    }

    public function test_homepage_uses_database_data(): void
    {
        $service = $this->service(['price' => 12000]);
        $booking = Booking::factory()->create(['service_id' => $service->id, 'status' => 'Completed', 'event_type' => 'Debut', 'barangay' => 'Wawa']);
        Review::factory()->create(['booking_id' => $booking->id, 'customer_id' => $booking->customer_id, 'vendor_id' => $service->vendor_id, 'rating' => 5, 'comment' => 'Simply perfect.']);

        $response = $this->get('/')->assertOk()
            ->assertSee('From ₱12,000')
            ->assertSee('Simply perfect.')
            ->assertSee('Debut · Wawa')
            ->assertSee('5.0 ★')
            ->assertSee('categories%5B0%5D='.$this->decoration->id, false)
            ->assertSee('events%5B0%5D=debut', false);

        $this->assertSame(44, substr_count($response->getContent(), '<option>') - 0);
    }
}

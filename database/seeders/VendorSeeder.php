<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use App\Models\Staff;
use App\Models\Vendor;
use App\Support\DashboardData;
use App\Support\MockData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Vendors, their logins, services and crews.
 *
 *  - The 8 marketplace vendors from Browse Services (the same 8 that appear on the bookings)
 *  - Gourmet Creations Catering, the vendor-portal demo account, with its 8 services and crew
 *  - The 6 vendors on the super admin approval list
 *  - Eventlynk's own 18-person crew (vendor_id = null) from the admin panel
 */
class VendorSeeder extends Seeder
{
    /** Short names used on the bookings (SHARED_BOOKINGS.vendorShort). */
    private const SHORT_NAMES = [
        'Elegant Events & Co.' => 'Elegant Events',
        'Prime Serve Staffing' => 'Prime Serve',
        'Clean Event Solutions' => 'Clean Events',
        'All-in-One Events PH' => 'AIO Events',
        'Bloom & Elegance Decor' => 'Bloom Decor',
        'Royal Serve Co.' => 'Royal Serve',
        'SparkClean Services' => 'SparkClean',
        'Fiesta Complete Events' => 'Fiesta Events',
    ];

    /** Name of a marketplace vendor's main listing, by its Browse badge. */
    private const LISTING_NAMES = [
        'Decoration' => 'Event Decoration',
        'Serving Staff' => 'Serving Staff Team',
        'Dishwashing' => 'Dishwashing Crew',
        'Full Package' => 'Full Event Package',
    ];

    /** The vendor portal's crew titles mapped onto the three staff roles. */
    private const PORTAL_STAFF_ROLES = [
        'Head Server' => 'Serving',
        'Sous Chef' => 'Serving',
        'Waitstaff' => 'Serving',
        'Kitchen Aide' => 'Dishwashing',
    ];

    public function run(): void
    {
        $categories = ServiceCategory::pluck('id', 'name');
        $catalogue = collect(DashboardData::vendor()['services'])->keyBy('name');

        $this->seedMarketplaceVendors($categories, $catalogue);
        $this->seedPortalVendor($categories, $catalogue);
        $this->seedApplicants();
        $this->seedPlatformCrew();
    }

    private function seedMarketplaceVendors($categories, $catalogue): void
    {
        $bookedServices = collect(MockData::bookings())->keyBy('vendorName');

        foreach (MockData::MARKET_VENDORS as $listing) {
            $vendor = $this->createVendor(
                name: $listing['name'],
                email: Str::slug($listing['name']).'@email.com',
                vendor: [
                    'business_name' => $listing['name'],
                    'short_name' => self::SHORT_NAMES[$listing['name']],
                    'description' => $listing['desc'],
                    'barangay' => $listing['barangay'],
                    'is_verified' => true,
                ],
            );

            // Main listing, exactly as Browse Services shows it.
            $vendor->services()->create([
                'service_category_id' => $categories[$listing['badge']],
                'name' => self::LISTING_NAMES[$listing['badge']],
                'description' => $listing['desc'],
                'price' => $listing['priceValue'],
                'price_unit' => str_contains($listing['unit'], 'staff') ? 'staff' : 'event',
                'image' => $listing['image'],
            ]);

            // The package this vendor was booked for (named as on the bookings).
            $booked = $bookedServices[$listing['name']];
            $package = $catalogue[$booked['serviceName']];
            $vendor->services()->create([
                'service_category_id' => $categories[$booked['category']],
                'name' => $booked['serviceName'],
                'description' => $package['desc'],
                ...$this->parsePrice($package['price']),
            ]);
        }
    }

    /** Gourmet Creations Catering — the account the vendor dashboard is designed around. */
    private function seedPortalVendor($categories, $catalogue): void
    {
        $vendor = $this->createVendor(
            name: 'Gourmet Creations',
            email: 'gourmet@events.ph',
            phone: '09284567890',
            vendor: [
                'business_name' => 'Gourmet Creations Catering',
                'short_name' => 'Gourmet Creations',
                'contact_number' => '09284567890',
                'is_verified' => true,
            ],
        );

        foreach ($catalogue as $service) {
            $vendor->services()->create([
                'service_category_id' => $categories[$service['category']],
                'name' => $service['name'],
                'description' => $service['desc'],
                'is_active' => $service['status'] === 'Active',
                ...$this->parsePrice($service['price']),
            ]);
        }

        foreach (DashboardData::vendor()['staff'] as $member) {
            $vendor->staff()->create([
                'full_name' => $member['name'],
                'role' => self::PORTAL_STAFF_ROLES[$member['role']],
            ]);
        }
    }

    /** Vendors on the super admin approval list (Approved / Pending / Rejected). */
    private function seedApplicants(): void
    {
        foreach (DashboardData::superAdmin()['vendors'] as $applicant) {
            $phone = str_replace('-', '', $applicant['phone']);

            $this->createVendor(
                name: $applicant['owner'],
                email: $applicant['email'],
                phone: $phone,
                active: $applicant['status'] !== 'Rejected',
                vendor: [
                    'business_name' => $applicant['name'],
                    'contact_number' => $phone,
                    'is_verified' => $applicant['status'] === 'Approved',
                ],
            );
        }
    }

    private function seedPlatformCrew(): void
    {
        foreach (MockData::PLATFORM_STAFF as $member) {
            Staff::create([
                'vendor_id' => null,
                'full_name' => $member['name'],
                'role' => $member['role'],
            ]);
        }
    }

    private function createVendor(string $name, string $email, array $vendor, ?string $phone = null, bool $active = true): Vendor
    {
        $user = DatabaseSeeder::account($name, $email, 'vendor', $phone, $active);

        return $user->vendor()->create($vendor);
    }

    /** "₱12,000/event" → ['price' => 12000, 'price_unit' => 'event'] */
    private function parsePrice(string $label): array
    {
        [$amount, $unit] = explode('/', $label);

        return [
            'price' => (float) preg_replace('/[^\d.]/', '', $amount),
            'price_unit' => $unit,
        ];
    }
}

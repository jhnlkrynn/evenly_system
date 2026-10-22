<?php

namespace App\Support;

/**
 * Static sample data ported from the Figma Make prototype
 * (figma-reference/src/mockData.ts and the data constants in each page).
 *
 * Every view reads its data from here so that a later phase can swap
 * these arrays for Eloquent queries without touching the Blade markup.
 */
class MockData
{
    public const BARANGAYS = [
        'Aga', 'Balaytigue', 'Balok-Balok', 'Banilad',
        'Barangay 1 (Pob.)', 'Barangay 2 (Pob.)', 'Barangay 3 (Pob.)', 'Barangay 4 (Pob.)',
        'Barangay 5 (Pob.)', 'Barangay 6 (Pob.)', 'Barangay 7 (Pob.)', 'Barangay 8 (Pob.)',
        'Barangay 9 (Pob.)', 'Barangay 10 (Pob.)', 'Barangay 11 (Pob.)', 'Barangay 12 (Pob.)',
        'Bilaran', 'Bucana', 'Bulihan', 'Bunducan', 'Butucan', 'Calayo', 'Catandaan', 'Cogunan',
        'Dayap', 'Kaylaway', 'Kayrilaw', 'Latag', 'Looc', 'Lumbangan', 'Malapad Na Bato',
        'Malapad na Parang', 'Mataas Na Pulo', 'Maugat', 'Munting Indang', 'Natipuan', 'Pantalan',
        'Papaya', 'Putat', 'Reparo', 'Talangan', 'Tumalim', 'Utod', 'Wawa',
    ];

    /** September 2026 calendar grid (Sunday-first). */
    public const SEP_GRID = [
        [null, 1, 2, 3, 4, 5, 6],
        [7, 8, 9, 10, 11, 12, 13],
        [14, 15, 16, 17, 18, 19, 20],
        [21, 22, 23, 24, 25, 26, 27],
        [28, 29, 30, null, null, null, null],
    ];

    public const PLATFORM_STAFF = [
        ['name' => 'Rowena Bautista', 'role' => 'Decoration'],
        ['name' => 'Ivan Dela Torre', 'role' => 'Decoration'],
        ['name' => 'Pamela Ramos', 'role' => 'Decoration'],
        ['name' => 'Ernesto Villafuerte', 'role' => 'Serving'],
        ['name' => 'Theresa Morales', 'role' => 'Serving'],
        ['name' => 'Danilo Aquino', 'role' => 'Serving'],
        ['name' => 'Felisa Buenaventura', 'role' => 'Serving'],
        ['name' => 'Sonny Castillo', 'role' => 'Serving'],
        ['name' => 'Nora Ignacio', 'role' => 'Serving'],
        ['name' => 'Manny Pascual', 'role' => 'Serving'],
        ['name' => 'Corazon Bacani', 'role' => 'Serving'],
        ['name' => 'Lorena Delgado', 'role' => 'Serving'],
        ['name' => 'Arnel Ocampo', 'role' => 'Serving'],
        ['name' => 'Gina Fernandez', 'role' => 'Dishwashing'],
        ['name' => 'Romulo Castillo', 'role' => 'Dishwashing'],
        ['name' => 'Perla Santiago', 'role' => 'Dishwashing'],
        ['name' => 'Edgar Velasquez', 'role' => 'Dishwashing'],
        ['name' => 'Susan Tagalog', 'role' => 'Dishwashing'],
    ];

    /** Badge colours, keyed by palette then status (STATUS_STYLE / PAY_STYLE / STATUS_COLOR). */
    public const PALETTES = [
        'booking' => [
            'Confirmed' => ['bg' => '#e6f4ea', 'color' => '#2e7d32'],
            'Pending' => ['bg' => '#fff8e8', 'color' => '#a07a2a'],
            'Completed' => ['bg' => '#e8f0fe', 'color' => '#4a6fdc'],
            'Cancelled' => ['bg' => '#fce4ec', 'color' => '#c62828'],
            'Active' => ['bg' => '#e6f4ea', 'color' => '#2e7d32'],
            'Paused' => ['bg' => '#f5f0e8', 'color' => '#6b5a47'],
        ],
        'payment' => [
            'Unpaid' => ['bg' => '#fce4ec', 'color' => '#c62828'],
            'Partial' => ['bg' => '#fff8e8', 'color' => '#a07a2a'],
            'Paid' => ['bg' => '#e6f4ea', 'color' => '#2e7d32'],
        ],
        'platform' => [
            'Approved' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
            'Pending' => ['bg' => '#fff8e1', 'color' => '#e65100'],
            'Rejected' => ['bg' => '#fce4ec', 'color' => '#c62828'],
            'Suspended' => ['bg' => '#f3e5f5', 'color' => '#6a1b9a'],
            'Open' => ['bg' => '#fce4ec', 'color' => '#c62828'],
            'In Review' => ['bg' => '#fff8e1', 'color' => '#e65100'],
            'Resolved' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
            'Closed' => ['bg' => '#f5f5f5', 'color' => '#555'],
            'Active' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
            'Inactive' => ['bg' => '#f5f5f5', 'color' => '#777'],
            'High' => ['bg' => '#fce4ec', 'color' => '#c62828'],
            'Medium' => ['bg' => '#fff8e1', 'color' => '#e65100'],
            'Low' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
        ],
    ];

    public static function palette(string $palette, string $status): array
    {
        return self::PALETTES[$palette][$status] ?? ['bg' => '#f5f5f5', 'color' => '#555'];
    }

    /** SHARED_BOOKINGS — single source of truth for bookings across dashboards. */
    public static function bookings(): array
    {
        return [
            [
                'id' => 'BK-001', 'client' => 'Maria Santos', 'eventType' => 'Birthday',
                'date' => 'Sep 15, 2026', 'time' => '3:00 PM – 9:00 PM',
                'venue' => 'Olave Balangay Hall, Brgy. Poblacion, Nasugbu, Batangas', 'pax' => 100,
                'services' => ['decoration' => true, 'serving' => 4, 'dishwashing' => 2],
                'status' => 'Confirmed', 'totalAmount' => 12000, 'reservationFee' => 5000, 'paymentStatus' => 'Partial',
                'specialRequests' => 'Floral theme, blue and white motif',
                'assignedStaff' => [
                    'decoration' => ['Rowena Bautista', 'Ivan Dela Torre', 'Pamela Ramos'],
                    'serving' => ['Ernesto Villafuerte', 'Theresa Morales', 'Danilo Aquino', 'Felisa Buenaventura'],
                    'dishwashing' => ['Gina Fernandez', 'Romulo Castillo'],
                ],
                'vendorName' => 'Elegant Events & Co.', 'vendorShort' => 'Elegant Events',
                'serviceName' => 'Full Event Package (Standard)', 'category' => 'Full Package',
            ],
            [
                'id' => 'BK-002', 'client' => 'Jose Reyes', 'eventType' => 'Wedding',
                'date' => 'Oct 2, 2026', 'time' => '5:00 PM – 11:00 PM',
                'venue' => 'Villa de Nasugbu Events, Brgy. Wawa, Nasugbu, Batangas', 'pax' => 200,
                'services' => ['decoration' => true, 'serving' => 8, 'dishwashing' => 4],
                'status' => 'Pending', 'totalAmount' => 25000, 'reservationFee' => 10000, 'paymentStatus' => 'Partial',
                'specialRequests' => null,
                'assignedStaff' => ['decoration' => [], 'serving' => [], 'dishwashing' => []],
                'vendorName' => 'Prime Serve Staffing', 'vendorShort' => 'Prime Serve',
                'serviceName' => 'Premium Floral Setup', 'category' => 'Decoration',
            ],
            [
                'id' => 'BK-003', 'client' => 'Ana Cruz', 'eventType' => 'Corporate Event',
                'date' => 'Sep 22, 2026', 'time' => '9:00 AM – 5:00 PM',
                'venue' => 'Nasugbu Sports Complex, Brgy. Poblacion, Nasugbu, Batangas', 'pax' => 150,
                'services' => ['decoration' => false, 'serving' => 6, 'dishwashing' => 3],
                'status' => 'Confirmed', 'totalAmount' => 18000, 'reservationFee' => 8000, 'paymentStatus' => 'Partial',
                'specialRequests' => null,
                'assignedStaff' => [
                    'decoration' => [],
                    'serving' => ['Sonny Castillo', 'Nora Ignacio', 'Manny Pascual', 'Corazon Bacani', 'Lorena Delgado', 'Arnel Ocampo'],
                    'dishwashing' => ['Perla Santiago', 'Edgar Velasquez', 'Susan Tagalog'],
                ],
                'vendorName' => 'Fiesta Complete Events', 'vendorShort' => 'Fiesta Events',
                'serviceName' => 'Serving Staff (Standard)', 'category' => 'Serving Staff',
            ],
            [
                'id' => 'BK-004', 'client' => 'Paolo Lim', 'eventType' => 'Debut',
                'date' => 'Aug 30, 2026', 'time' => '6:00 PM – 11:00 PM',
                'venue' => 'Casa del Campo Events, Brgy. Calayo, Nasugbu, Batangas', 'pax' => 80,
                'services' => ['decoration' => true, 'serving' => 3, 'dishwashing' => 2],
                'status' => 'Completed', 'totalAmount' => 9500, 'reservationFee' => 9500, 'paymentStatus' => 'Paid',
                'specialRequests' => null,
                'assignedStaff' => [
                    'decoration' => ['Rowena Bautista', 'Ivan Dela Torre', 'Pamela Ramos'],
                    'serving' => ['Ernesto Villafuerte', 'Theresa Morales', 'Danilo Aquino'],
                    'dishwashing' => ['Gina Fernandez', 'Romulo Castillo'],
                ],
                'vendorName' => 'All-in-One Events PH', 'vendorShort' => 'AIO Events',
                'serviceName' => 'Classic Decoration Package', 'category' => 'Decoration',
            ],
            [
                'id' => 'BK-005', 'client' => 'Rica Tan', 'eventType' => 'Birthday',
                'date' => 'Oct 10, 2026', 'time' => '4:00 PM – 9:00 PM',
                'venue' => 'Brgy. Bucana Multi-Purpose Hall, Nasugbu, Batangas', 'pax' => 120,
                'services' => ['decoration' => true, 'serving' => 5, 'dishwashing' => 2],
                'status' => 'Pending', 'totalAmount' => 14000, 'reservationFee' => 0, 'paymentStatus' => 'Unpaid',
                'specialRequests' => null,
                'assignedStaff' => ['decoration' => [], 'serving' => [], 'dishwashing' => []],
                'vendorName' => 'Clean Event Solutions', 'vendorShort' => 'Clean Events',
                'serviceName' => 'Dishwashing Team (Small)', 'category' => 'Dishwashing',
            ],
            [
                'id' => 'BK-006', 'client' => 'Mark Castillo', 'eventType' => 'Wedding Reception',
                'date' => 'Oct 18, 2026', 'time' => '5:00 PM – 11:00 PM',
                'venue' => 'Nasugbu Beach Resort Events Hall, Brgy. Calayo, Nasugbu, Batangas', 'pax' => 250,
                'services' => ['decoration' => true, 'serving' => 8, 'dishwashing' => 4],
                'status' => 'Pending', 'totalAmount' => 32000, 'reservationFee' => 0, 'paymentStatus' => 'Unpaid',
                'specialRequests' => null,
                'assignedStaff' => ['decoration' => [], 'serving' => [], 'dishwashing' => []],
                'vendorName' => 'Bloom & Elegance Decor', 'vendorShort' => 'Bloom Decor',
                'serviceName' => 'Full Event Package (Grand)', 'category' => 'Full Package',
            ],
            [
                'id' => 'BK-007', 'client' => 'Cecile Aguilar', 'eventType' => 'Corporate Gala',
                'date' => 'Sep 28, 2026', 'time' => '6:00 PM – 10:00 PM',
                'venue' => 'Hotel Caylabne Bay, Brgy. Reparo, Nasugbu, Batangas', 'pax' => 80,
                'services' => ['decoration' => false, 'serving' => 6, 'dishwashing' => 0],
                'status' => 'Confirmed', 'totalAmount' => 7200, 'reservationFee' => 3000, 'paymentStatus' => 'Partial',
                'specialRequests' => null,
                'assignedStaff' => [
                    'decoration' => [],
                    'serving' => ['Felisa Buenaventura', 'Sonny Castillo', 'Nora Ignacio', 'Manny Pascual', 'Corazon Bacani', 'Lorena Delgado'],
                    'dishwashing' => [],
                ],
                'vendorName' => 'Royal Serve Co.', 'vendorShort' => 'Royal Serve',
                'serviceName' => 'Serving Staff (Premium)', 'category' => 'Serving Staff',
            ],
            [
                'id' => 'BK-008', 'client' => 'Dennis Santos', 'eventType' => 'Family Reunion',
                'date' => 'Jul 20, 2026', 'time' => '10:00 AM – 5:00 PM',
                'venue' => 'Brgy. Balaytigue Covered Court, Nasugbu, Batangas', 'pax' => 90,
                'services' => ['decoration' => true, 'serving' => 0, 'dishwashing' => 0],
                'status' => 'Completed', 'totalAmount' => 12000, 'reservationFee' => 12000, 'paymentStatus' => 'Paid',
                'specialRequests' => null,
                'assignedStaff' => [
                    'decoration' => ['Rowena Bautista', 'Ivan Dela Torre', 'Pamela Ramos'],
                    'serving' => [],
                    'dishwashing' => [],
                ],
                'vendorName' => 'SparkClean Services', 'vendorShort' => 'SparkClean',
                'serviceName' => 'Classic Decoration Package', 'category' => 'Decoration',
            ],
        ];
    }

    /** Landing page service cards (App.tsx SERVICES). */
    public static function landingServices(): array
    {
        return [
            [
                'title' => 'Decoration',
                'description' => 'Balloons, tablescapes, centerpieces, and custom themes made for your event.',
                'price' => 'From ₱12,000',
                'image' => asset('images/image-2.png'),
                'featured' => false,
            ],
            [
                'title' => 'Serving Staff',
                'description' => 'Uniformed, trained staff for buffets, cocktails, and formal dinners.',
                'price' => 'From ₱750 / staff',
                'image' => 'https://images.unsplash.com/photo-1759646827844-bbbdbfd0ada2?auto=format&fit=crop&w=900&q=82',
                'featured' => false,
            ],
            [
                'title' => 'Dishwashing',
                'description' => 'Post-event cleanup with full equipment for a spotless close to your occasion.',
                'price' => 'From ₱1,500',
                'image' => 'https://images.unsplash.com/photo-1769812343266-323d6b508f24?auto=format&fit=crop&w=900&q=82',
                'featured' => false,
            ],
            [
                'title' => 'Full Package',
                'description' => 'Decoration, serving, and dishwashing combined in one coordinated service.',
                'price' => 'From ₱18,000',
                'image' => 'https://images.unsplash.com/photo-1766393524464-e5eb1b05e4c8?auto=format&fit=crop&w=900&q=82',
                'featured' => true,
            ],
        ];
    }

    /** "How it works" steps (App.tsx STEPS). */
    public const STEPS = [
        ['number' => '01', 'title' => 'Search', 'text' => 'Filter by service, date, and barangay to find the right team for your event.'],
        ['number' => '02', 'title' => 'Compare & book', 'text' => 'Review verified vendors, transparent pricing, and packages side by side.'],
        ['number' => '03', 'title' => 'Track', 'text' => 'Follow booking status, schedules, and updates from one organized dashboard.'],
        ['number' => '04', 'title' => 'Celebrate', 'text' => 'Your vendors handle setup and cleanup, so you can enjoy the day.'],
    ];

    public const REVIEWS = [
        ['quote' => "Eventlynk made our daughter's debut effortless. The setup was elegant and exactly as promised.", 'name' => 'Maria Santos', 'event' => 'Debut · Nasugbu'],
        ['quote' => 'We compared packages in minutes and booked a complete team without the usual back-and-forth.', 'name' => 'Carla Mendoza', 'event' => 'Wedding · Wawa'],
        ['quote' => 'Clear pricing, real-time tracking, and a professional crew from start to finish.', 'name' => 'Paolo Lim', 'event' => 'Corporate event · Calayo'],
    ];

    /** Browse Services marketplace (BrowseServicesPage.tsx VENDORS). */
    public const MARKET_VENDORS = [
        ['id' => 1, 'name' => 'Elegant Events & Co.', 'badge' => 'Decoration', 'rating' => 4.9, 'reviews' => 126, 'location' => 'Nasugbu, Batangas', 'barangay' => 'Poblacion', 'desc' => 'Beautiful and creative setups — balloon arches, floral centerpieces, and custom tarp designs for any occasion.', 'tags' => ['Weddings', 'Birthdays', 'Corporate'], 'price' => '₱15,000', 'priceValue' => 15000, 'unit' => '/ event', 'image' => 'https://images.unsplash.com/photo-1529636798458-92182e662485?w=600&h=400&fit=crop&auto=format'],
        ['id' => 2, 'name' => 'Prime Serve Staffing', 'badge' => 'Serving Staff', 'rating' => 4.8, 'reviews' => 88, 'location' => 'Nasugbu, Batangas', 'barangay' => 'Wawa', 'desc' => 'Courteous, uniformed, and professionally trained serving staff who make every guest feel valued.', 'tags' => ['Weddings', 'Corporate', 'Private Parties'], 'price' => '₱2,500', 'priceValue' => 2500, 'unit' => '/ staff', 'image' => 'https://images.unsplash.com/photo-1719573019827-d06944a0c2be?w=600&h=400&fit=crop&auto=format'],
        ['id' => 3, 'name' => 'Clean Event Solutions', 'badge' => 'Dishwashing', 'rating' => 4.7, 'reviews' => 64, 'location' => 'Nasugbu, Batangas', 'barangay' => 'Bucana', 'desc' => 'Fully-equipped dishwashing teams that ensure spotless clean-up before you leave the venue.', 'tags' => ['Weddings', 'Parties', 'Corporate'], 'price' => '₱1,800', 'priceValue' => 1800, 'unit' => '/ event', 'image' => 'https://images.unsplash.com/photo-1576842546422-60562b9242ae?w=600&h=400&fit=crop&auto=format'],
        ['id' => 4, 'name' => 'All-in-One Events PH', 'badge' => 'Full Package', 'rating' => 4.9, 'reviews' => 152, 'location' => 'Nasugbu, Batangas', 'barangay' => 'Calayo', 'desc' => "Eventlynk's top-rated full-service vendor — decoration, serving staff, and dishwashing bundled in one seamless booking.", 'tags' => ['Weddings', 'Birthdays', 'Corporate'], 'price' => '₱25,000', 'priceValue' => 25000, 'unit' => '/ event', 'image' => 'https://images.unsplash.com/photo-1712068534065-f56c36e21759?w=600&h=400&fit=crop&auto=format'],
        ['id' => 5, 'name' => 'Bloom & Elegance Decor', 'badge' => 'Decoration', 'rating' => 4.8, 'reviews' => 97, 'location' => 'Nasugbu, Batangas', 'barangay' => 'Poblacion', 'desc' => 'Floral specialists with a signature romantic aesthetic — perfect for debuts, weddings, and garden parties.', 'tags' => ['Debuts', 'Weddings', 'Garden Parties'], 'price' => '₱12,000', 'priceValue' => 12000, 'unit' => '/ event', 'image' => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=600&h=400&fit=crop&auto=format'],
        ['id' => 6, 'name' => 'Royal Serve Co.', 'badge' => 'Serving Staff', 'rating' => 4.9, 'reviews' => 114, 'location' => 'Nasugbu, Batangas', 'barangay' => 'Malapad na Parang', 'desc' => 'Premium serving staff trained in fine dining etiquette, perfect for high-end corporate and social events.', 'tags' => ['Corporate', 'Galas', 'Weddings'], 'price' => '₱3,000', 'priceValue' => 3000, 'unit' => '/ staff', 'image' => 'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=600&h=400&fit=crop&auto=format'],
        ['id' => 7, 'name' => 'SparkClean Services', 'badge' => 'Dishwashing', 'rating' => 4.6, 'reviews' => 48, 'location' => 'Nasugbu, Batangas', 'barangay' => 'Balaytigue', 'desc' => 'Fast, thorough, and affordable dishwashing team with industrial-grade equipment for large-scale events.', 'tags' => ['Corporate', 'Large Events', 'Universities'], 'price' => '₱1,500', 'priceValue' => 1500, 'unit' => '/ event', 'image' => 'https://images.unsplash.com/photo-1585664811087-47f65abbad64?w=600&h=400&fit=crop&auto=format'],
        ['id' => 8, 'name' => 'Fiesta Complete Events', 'badge' => 'Full Package', 'rating' => 4.7, 'reviews' => 89, 'location' => 'Nasugbu, Batangas', 'barangay' => 'Reparo', 'desc' => 'Affordable full-package event services for celebrations in South Luzon — family-oriented and budget-friendly.', 'tags' => ['Birthdays', 'Baptisms', 'Reunions'], 'price' => '₱18,000', 'priceValue' => 18000, 'unit' => '/ event', 'image' => 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=600&h=400&fit=crop&auto=format'],
    ];

    public const SERVICE_TYPES = ['Decoration', 'Serving Staff', 'Dishwashing', 'Full Package'];

    public const EVENT_FILTERS = ['Debuts', 'Weddings', 'Corporate', 'Birthdays'];
}

<?php

namespace App\Support;

/**
 * View data for the four dashboards, ported from the constants and derived
 * values at the top of each React dashboard component.
 */
class DashboardData
{
    public static function customer(): array
    {
        $bookings = array_map(fn ($b) => [
            'id' => $b['id'],
            'service' => $b['vendorName'],
            'vendor' => $b['vendorShort'],
            'date' => $b['date'],
            'time' => $b['time'],
            'guests' => $b['pax'],
            'amount' => '₱'.number_format($b['totalAmount']),
            'status' => $b['status'],
            'category' => $b['category'],
        ], MockData::bookings());

        $upcoming = array_values(array_filter($bookings, fn ($b) => in_array($b['status'], ['Confirmed', 'Pending'], true)));
        $completed = array_values(array_filter($bookings, fn ($b) => $b['status'] === 'Completed'));

        $notifications = [
            ['icon' => '✅', 'text' => 'Your booking BK-001 (Elegant Events & Co.) has been confirmed for Sep 15, 2026.', 'time' => '2 hours ago', 'unread' => true],
            ['icon' => '✅', 'text' => 'Your booking BK-002 (Prime Serve Staffing) has been confirmed for Oct 2, 2026.', 'time' => '4 hours ago', 'unread' => true],
            ['icon' => '💬', 'text' => "Fiesta Complete Events sent a message: 'We have received your booking request for BK-003. Kindly prepare the reservation fee.'", 'time' => '6 hours ago', 'unread' => true],
            ['icon' => '⭐', 'text' => 'Reminder: You have 4 completed bookings awaiting your review. Share your experience!', 'time' => '1 day ago', 'unread' => true],
            ['icon' => '📦', 'text' => 'New service available: SparkClean Services added a Premium Dishwashing Package for large events (200+ pax).', 'time' => '2 days ago', 'unread' => false],
            ['icon' => '💸', 'text' => 'Reservation fee of ₱5,000 for BK-001 has been recorded and confirmed.', 'time' => '2 days ago', 'unread' => false],
            ['icon' => '🎉', 'text' => 'Special offer from Bloom & Elegance Decor: 15% off for bookings made this October!', 'time' => '3 days ago', 'unread' => false],
            ['icon' => '📅', 'text' => 'Upcoming event reminder: BK-001 (Elegant Events & Co.) is in 12 days. Coordinate with your vendor now.', 'time' => '4 days ago', 'unread' => false],
        ];

        $favorites = [
            ['name' => 'Elegant Events & Co.', 'category' => 'Decoration', 'rating' => 4.9, 'location' => 'Nasugbu, Batangas', 'price' => '₱12,000/event', 'icon' => '🌸'],
            ['name' => 'Prime Serve Staffing', 'category' => 'Serving Staff', 'rating' => 4.8, 'location' => 'Nasugbu, Batangas', 'price' => '₱750/staff', 'icon' => '🍽'],
            ['name' => 'All-in-One Events PH', 'category' => 'Full Package', 'rating' => 4.9, 'location' => 'Nasugbu, Batangas', 'price' => '₱18,000/event', 'icon' => '🎁'],
            ['name' => 'Royal Serve Co.', 'category' => 'Serving Staff', 'rating' => 4.9, 'location' => 'Nasugbu, Batangas', 'price' => '₱1,200/staff', 'icon' => '🍽'],
            ['name' => 'Bloom & Elegance Decor', 'category' => 'Decoration', 'rating' => 4.8, 'location' => 'Nasugbu, Batangas', 'price' => '₱12,000/event', 'icon' => '🌸'],
            ['name' => 'SparkClean Services', 'category' => 'Dishwashing', 'rating' => 4.6, 'location' => 'Nasugbu, Batangas', 'price' => '₱500/staff', 'icon' => '🫧'],
        ];

        $unread = count(array_filter($notifications, fn ($n) => $n['unread']));

        // Booking Status tracker: every active booking plus the first completed one.
        $tracked = array_merge(
            array_values(array_filter($bookings, fn ($b) => $b['status'] !== 'Completed')),
            array_slice($completed, 0, 1),
        );

        return [
            'navItems' => [
                ['id' => 'overview', 'label' => 'Overview', 'icon' => '⊞'],
                ['id' => 'bookings', 'label' => 'My Bookings', 'icon' => '📅'],
                ['id' => 'status', 'label' => 'Booking Status', 'icon' => '📍'],
                ['id' => 'reviews', 'label' => 'Ratings & Reviews', 'icon' => '⭐'],
                ['id' => 'favorites', 'label' => 'Favorites', 'icon' => '♡'],
                ['id' => 'notifications', 'label' => 'Notifications', 'icon' => '🔔', 'badge' => ['count' => $unread, 'bg' => '#c9a84c', 'color' => '#1a110a', 'key' => 'unread']],
                ['id' => 'profile', 'label' => 'Profile Settings', 'icon' => '👤'],
                ['id' => 'help', 'label' => 'Help & Support', 'icon' => '❓'],
            ],
            'bookings' => $bookings,
            'upcoming' => $upcoming,
            'completed' => $completed,
            'tracked' => $tracked,
            'notifications' => $notifications,
            'unread' => $unread,
            'favorites' => $favorites,
            'stats' => [
                ['label' => 'Total Bookings', 'value' => count($bookings), 'icon' => '📅', 'color' => '#c9a84c'],
                ['label' => 'Upcoming Events', 'value' => count($upcoming), 'icon' => '🎉', 'color' => '#4a6fdc'],
                ['label' => 'Completed', 'value' => count($completed), 'icon' => '✅', 'color' => '#2e7d32'],
                ['label' => 'Saved Vendors', 'value' => count($favorites), 'icon' => '♥', 'color' => '#e53935'],
            ],
            'faqs' => [
                ['q' => 'How do I book a service on Eventlynk?', 'a' => "Go to Browse Services, select a vendor, and click 'Book Now'. Fill in your event details (date, time, venue, number of guests), select the services you need, review the pricing summary, and pay the reservation fee to confirm your booking."],
                ['q' => 'What is the reservation fee and when is the full payment due?', 'a' => 'The reservation fee (typically 30–40% of the total) secures your date and vendor. The remaining balance is settled on the day of your event before services begin. All fee amounts are clearly shown before you confirm.'],
                ['q' => 'Can I cancel or reschedule my booking?', 'a' => 'Bookings can be rescheduled up to 7 days before the event at no extra charge. Cancellations made less than 48 hours before the event may forfeit the reservation fee. Please contact your vendor or our support team to reschedule.'],
                ['q' => "What if the vendor doesn't show up?", 'a' => "In the rare event of a no-show, contact our support team immediately. We will either arrange an alternative vendor from our network or issue a full refund of all payments made. You are protected by Eventlynk's vendor guarantee."],
                ['q' => 'How do I know a vendor is trustworthy?', 'a' => 'All vendors on Eventlynk are verified through a background check process before listing. You can also view their ratings, reviews from past clients, and booking history on their profile. Only vendors with 4.0+ average ratings remain listed.'],
                ['q' => 'Can I book multiple services from different vendors?', 'a' => 'Yes! You can book Decoration from one vendor and Serving Staff from another in separate bookings. Alternatively, you can book a Full Package from a single vendor for convenience and potential bundled savings.'],
                ['q' => 'How do I contact my vendor after booking?', 'a' => "After your booking is confirmed, a direct messaging channel opens between you and your vendor inside your dashboard. You can also find the vendor's contact number in your booking confirmation email."],
                ['q' => 'Is my payment information safe?', 'a' => "All payments are processed through Eventlynk's secure payment gateway with SSL encryption. We never store your card details on our servers. Refunds are processed within 3–5 business days."],
            ],
        ];
    }

    public static function vendor(): array
    {
        $shared = MockData::bookings();

        $bookings = array_map(fn ($b) => [
            'id' => $b['id'],
            'customer' => $b['client'],
            'service' => $b['serviceName'],
            'date' => $b['date'],
            'guests' => $b['pax'],
            'amount' => '₱'.number_format($b['totalAmount']),
            'status' => $b['status'],
            'eventType' => $b['eventType'],
        ], $shared);

        $services = [
            ['id' => 'SVC-001', 'name' => 'Classic Decoration Package', 'category' => 'Decoration', 'price' => '₱12,000/event', 'bookings' => 34, 'rating' => '4.9', 'status' => 'Active', 'desc' => 'Balloon arches, tablecloths, and centerpieces for events up to 100 pax.'],
            ['id' => 'SVC-002', 'name' => 'Premium Floral Setup', 'category' => 'Decoration', 'price' => '₱20,000/event', 'bookings' => 18, 'rating' => '4.8', 'status' => 'Active', 'desc' => 'High-end floral arrangements, fairy lights, and custom entrance designs.'],
            ['id' => 'SVC-003', 'name' => 'Serving Staff (Standard)', 'category' => 'Serving Staff', 'price' => '₱750/staff', 'bookings' => 52, 'rating' => '4.9', 'status' => 'Active', 'desc' => 'Uniformed serving staff trained in buffet and cocktail service.'],
            // JS renders 5.0 as "5"
            ['id' => 'SVC-004', 'name' => 'Serving Staff (Premium)', 'category' => 'Serving Staff', 'price' => '₱1,200/staff', 'bookings' => 21, 'rating' => '5', 'status' => 'Active', 'desc' => 'Fine dining-trained staff for galas, corporate events, and weddings.'],
            ['id' => 'SVC-005', 'name' => 'Dishwashing Team (Small)', 'category' => 'Dishwashing', 'price' => '₱500/staff', 'bookings' => 29, 'rating' => '4.7', 'status' => 'Active', 'desc' => '2–4 dishwashing staff for events up to 150 pax with basic equipment.'],
            ['id' => 'SVC-006', 'name' => 'Dishwashing Team (Large)', 'category' => 'Dishwashing', 'price' => '₱450/staff', 'bookings' => 14, 'rating' => '4.6', 'status' => 'Active', 'desc' => 'Full dishwashing team with industrial-grade equipment for 200+ pax events.'],
            ['id' => 'SVC-007', 'name' => 'Full Event Package (Standard)', 'category' => 'Full Package', 'price' => '₱18,000/event', 'bookings' => 41, 'rating' => '4.9', 'status' => 'Active', 'desc' => 'Decoration + 4 serving staff + dishwashing — ideal for 80–120 pax events.'],
            ['id' => 'SVC-008', 'name' => 'Full Event Package (Grand)', 'category' => 'Full Package', 'price' => '₱32,000/event', 'bookings' => 9, 'rating' => '5', 'status' => 'Paused', 'desc' => 'Premium decoration, 10 serving staff, full dishwashing for 200+ pax.'],
        ];

        $monthly = [
            ['month' => 'Jan', 'amount' => 68000], ['month' => 'Feb', 'amount' => 82000],
            ['month' => 'Mar', 'amount' => 75000], ['month' => 'Apr', 'amount' => 120000],
            ['month' => 'May', 'amount' => 95000], ['month' => 'Jun', 'amount' => 110000],
            ['month' => 'Jul', 'amount' => 88000], ['month' => 'Aug', 'amount' => 130000],
            ['month' => 'Sep', 'amount' => 165000],
        ];

        $totalEarnings = array_sum(array_map(fn ($b) => $b['totalAmount'], array_filter($shared, fn ($b) => $b['status'] === 'Completed')));
        $activeBookings = count(array_filter($bookings, fn ($b) => in_array($b['status'], ['Confirmed', 'Pending'], true)));

        return [
            'navItems' => [
                ['id' => 'overview', 'label' => 'Dashboard', 'icon' => '⊞'],
                ['id' => 'browse', 'label' => 'Browse Services', 'icon' => '🔍'],
                ['id' => 'services', 'label' => 'My Services', 'icon' => '📦'],
                ['id' => 'bookings', 'label' => 'Bookings', 'icon' => '📅'],
                ['id' => 'staff', 'label' => 'Staff Assignment', 'icon' => '👥'],
                ['id' => 'inventory', 'label' => 'Inventory', 'icon' => '🗃'],
                ['id' => 'earnings', 'label' => 'Earnings', 'icon' => '💰'],
                ['id' => 'availability', 'label' => 'Availability', 'icon' => '🗓'],
                ['id' => 'analytics', 'label' => 'Analytics', 'icon' => '📊'],
                ['id' => 'profile', 'label' => 'Profile', 'icon' => '👤'],
                ['id' => 'settings', 'label' => 'Settings', 'icon' => '⚙'],
            ],
            'services' => $services,
            'bookings' => $bookings,
            'activeBookingList' => array_values(array_filter($bookings, fn ($b) => in_array($b['status'], ['Confirmed', 'Pending'], true))),
            'monthly' => $monthly,
            'maxEarning' => max(array_column($monthly, 'amount')),
            'stats' => [
                ['label' => 'Total Services', 'value' => count($services), 'icon' => '📦', 'color' => '#4a6fdc'],
                ['label' => 'Active Bookings', 'value' => $activeBookings, 'icon' => '📅', 'color' => '#c9a84c'],
                ['label' => 'Total Earnings', 'value' => '₱'.round($totalEarnings / 1000).'K', 'icon' => '💰', 'color' => '#2e7d32'],
                ['label' => 'Avg. Rating', 'value' => '4.9', 'icon' => '⭐', 'color' => '#e53935'],
            ],
            'staff' => [
                ['id' => 'S01', 'name' => 'Carla Mendoza', 'role' => 'Head Server', 'available' => true],
                ['id' => 'S02', 'name' => 'Rico Santos', 'role' => 'Sous Chef', 'available' => true],
                ['id' => 'S03', 'name' => 'Liza Cruz', 'role' => 'Waitstaff', 'available' => false],
                ['id' => 'S04', 'name' => 'Marco Reyes', 'role' => 'Waitstaff', 'available' => true],
                ['id' => 'S05', 'name' => 'Ana Villanueva', 'role' => 'Kitchen Aide', 'available' => true],
            ],
            'staffAssignments' => ['BK-001' => ['S01', 'S02'], 'BK-002' => [], 'BK-003' => ['S04']],
            'inventory' => [
                ['id' => 'I01', 'name' => 'Chafing Dish (Large)', 'category' => 'Equipment', 'owned' => 20, 'available' => 14, 'inUse' => 6, 'condition' => 'Good'],
                ['id' => 'I02', 'name' => 'Serving Trays (Set)', 'category' => 'Equipment', 'owned' => 40, 'available' => 28, 'inUse' => 12, 'condition' => 'Good'],
                ['id' => 'I03', 'name' => 'Table Skirting', 'category' => 'Linen', 'owned' => 15, 'available' => 10, 'inUse' => 5, 'condition' => 'Fair'],
                ['id' => 'I04', 'name' => 'Food Warmers', 'category' => 'Equipment', 'owned' => 10, 'available' => 7, 'inUse' => 3, 'condition' => 'Good'],
            ],
            'payments' => [
                ['id' => 'BK-042', 'date' => 'Aug 22, 2026', 'gross' => 45000, 'status' => 'Released'],
                ['id' => 'BK-041', 'date' => 'Aug 15, 2026', 'gross' => 38000, 'status' => 'Released'],
                ['id' => 'BK-040', 'date' => 'Aug 10, 2026', 'gross' => 52000, 'status' => 'Processing'],
                ['id' => 'BK-039', 'date' => 'Aug 5, 2026', 'gross' => 29000, 'status' => 'Pending'],
            ],
            'bookedDays' => [5, 10, 14, 18, 24, 28],
            'blockedDays' => [1, 2, 3, 4, 20, 21],
            'barangays' => MockData::BARANGAYS,
            'vendors' => MockData::MARKET_VENDORS,
            'serviceTypes' => MockData::SERVICE_TYPES,
            'eventFilters' => MockData::EVENT_FILTERS,
        ];
    }

    public static function admin(): array
    {
        $bookings = MockData::bookings();
        $staff = MockData::PLATFORM_STAFF;

        $usedStaff = [];
        foreach (array_filter($bookings, fn ($b) => $b['status'] === 'Confirmed') as $b) {
            foreach (array_merge($b['assignedStaff']['decoration'], $b['assignedStaff']['serving'], $b['assignedStaff']['dishwashing']) as $name) {
                $usedStaff[$name] = true;
            }
        }

        $upcoming = array_values(array_filter($bookings, fn ($b) => in_array($b['status'], ['Confirmed', 'Pending'], true)));

        $assignmentRows = [];
        foreach ($upcoming as $b) {
            foreach ([
                ['Decoration', 'decoration', $b['services']['decoration'] ? 3 : 0],
                ['Serving Staff', 'serving', $b['services']['serving']],
                ['Dishwashing', 'dishwashing', $b['services']['dishwashing']],
            ] as [$service, $key, $needed]) {
                if ($needed > 0) {
                    $assignmentRows[] = [
                        'bookingId' => $b['id'], 'client' => $b['client'], 'date' => $b['date'],
                        'service' => $service, 'needed' => $needed, 'assigned' => $b['assignedStaff'][$key],
                    ];
                }
            }
        }

        return [
            'navItems' => [
                ['id' => 'overview', 'label' => 'Dashboard', 'icon' => '⊞'],
                ['id' => 'bookings', 'label' => 'Bookings', 'icon' => '📋'],
                ['id' => 'calendar', 'label' => 'Calendar', 'icon' => '📅'],
                ['id' => 'staff', 'label' => 'Staff Assignment', 'icon' => '👥'],
                ['id' => 'inventory', 'label' => 'Inventory', 'icon' => '📦'],
                ['id' => 'invoices', 'label' => 'Invoices', 'icon' => '🧾'],
                ['id' => 'settings', 'label' => 'Settings', 'icon' => '⚙'],
            ],
            'bookings' => $bookings,
            'upcoming' => $upcoming,
            'kpis' => [
                ['label' => 'Total Bookings', 'value' => count($bookings), 'icon' => '📋', 'color' => '#4a6fdc'],
                ['label' => 'Pending', 'value' => count(array_filter($bookings, fn ($b) => $b['status'] === 'Pending')), 'icon' => '⏳', 'color' => '#a07a2a'],
                ['label' => 'Confirmed', 'value' => count(array_filter($bookings, fn ($b) => $b['status'] === 'Confirmed')), 'icon' => '✅', 'color' => '#2e7d32'],
                ['label' => 'Reservation Fees', 'value' => '₱'.number_format(array_sum(array_column($bookings, 'reservationFee'))), 'icon' => '💰', 'color' => '#c9a84c'],
                ['label' => 'Remaining Balance', 'value' => '₱'.number_format(array_sum(array_map(fn ($b) => $b['totalAmount'] - $b['reservationFee'], array_filter($bookings, fn ($b) => $b['status'] !== 'Cancelled')))), 'icon' => '📊', 'color' => '#e53935'],
            ],
            'inventoryAlerts' => [
                ['item' => 'Centerpieces', 'available' => 2, 'total' => 15, 'level' => 'low'],
                ['item' => 'Dish Racks', 'available' => 2, 'total' => 8, 'level' => 'low'],
                ['item' => 'Water Dispensers', 'available' => 2, 'total' => 6, 'level' => 'medium'],
                ['item' => 'Flower Arches', 'available' => 2, 'total' => 4, 'level' => 'medium'],
            ],
            'decorInventory' => [
                ['item' => 'Tarps (8x10 ft)', 'owned' => 20, 'available' => 14, 'inUse' => 6, 'condition' => 'Good'],
                ['item' => 'Balloons (pack)', 'owned' => 50, 'available' => 30, 'inUse' => 20, 'condition' => 'Good'],
                ['item' => 'Tablecloths (round)', 'owned' => 40, 'available' => 22, 'inUse' => 18, 'condition' => 'Good'],
                ['item' => 'Centerpieces (sets)', 'owned' => 15, 'available' => 9, 'inUse' => 6, 'condition' => 'Fair'],
                ['item' => 'Fairy Lights (rolls)', 'owned' => 30, 'available' => 18, 'inUse' => 12, 'condition' => 'Good'],
                ['item' => 'Flower Arches', 'owned' => 4, 'available' => 2, 'inUse' => 2, 'condition' => 'Good'],
            ],
            'equipmentInventory' => [
                ['item' => 'Chafing Dishes', 'owned' => 24, 'available' => 16, 'inUse' => 8, 'condition' => 'Good'],
                ['item' => 'Serving Trays (large)', 'owned' => 40, 'available' => 28, 'inUse' => 12, 'condition' => 'Good'],
                ['item' => 'Serving Spoons/Ladles (set)', 'owned' => 30, 'available' => 20, 'inUse' => 10, 'condition' => 'Good'],
                ['item' => 'Wash Basins', 'owned' => 10, 'available' => 7, 'inUse' => 3, 'condition' => 'Good'],
                ['item' => 'Dish Racks', 'owned' => 8, 'available' => 5, 'inUse' => 3, 'condition' => 'Fair'],
                ['item' => 'Water Dispensers', 'owned' => 6, 'available' => 4, 'inUse' => 2, 'condition' => 'Good'],
            ],
            'calendarGrid' => MockData::SEP_GRID,
            'calendarEvents' => [
                15 => [['label' => 'BK-001 · Maria Santos', 'color' => '#2e7d32']],
                22 => [['label' => 'BK-003 · Ana Cruz', 'color' => '#4a6fdc']],
                28 => [['label' => 'BK-007 · Cecile Aguilar', 'color' => '#c9a84c']],
            ],
            'assignmentRows' => $assignmentRows,
            'staff' => $staff,
            'usedStaff' => $usedStaff,
        ];
    }

    public static function superAdmin(): array
    {
        $vendors = [
            ['id' => 'V001', 'name' => 'Blossom Decor Studio', 'owner' => 'Maricel Santos', 'category' => 'Decoration', 'location' => 'Nasugbu, Batangas', 'email' => 'blossom@email.com', 'phone' => '0917-111-2222', 'joinedDate' => 'Jan 15, 2025', 'status' => 'Approved', 'totalBookings' => 42, 'revenue' => 210000, 'commission' => 21000, 'rating' => 4.8],
            ['id' => 'V002', 'name' => 'Elite Serving Team', 'owner' => 'Joel Reyes', 'category' => 'Serving Staff', 'location' => 'Nasugbu, Batangas', 'email' => 'elite@email.com', 'phone' => '0918-333-4444', 'joinedDate' => 'Feb 3, 2025', 'status' => 'Approved', 'totalBookings' => 67, 'revenue' => 335000, 'commission' => 33500, 'rating' => 4.6],
            ['id' => 'V003', 'name' => 'Cuisine Catering PH', 'owner' => 'Ana Vizcarra', 'category' => 'Catering', 'location' => 'Nasugbu, Batangas', 'email' => 'cuisine@email.com', 'phone' => '0919-555-6666', 'joinedDate' => 'Mar 10, 2025', 'status' => 'Pending', 'totalBookings' => 0, 'revenue' => 0, 'commission' => 0, 'rating' => 0],
            ['id' => 'V004', 'name' => 'Capture Moments PH', 'owner' => 'Pedro Cruz', 'category' => 'Photography', 'location' => 'Nasugbu, Batangas', 'email' => 'capture@email.com', 'phone' => '0920-777-8888', 'joinedDate' => 'Mar 22, 2025', 'status' => 'Pending', 'totalBookings' => 0, 'revenue' => 0, 'commission' => 0, 'rating' => 0],
            ['id' => 'V005', 'name' => 'Sound & Lights Pro', 'owner' => 'Carmen Dela Cruz', 'category' => 'Audio/Lights', 'location' => 'Nasugbu, Batangas', 'email' => 'sound@email.com', 'phone' => '0921-999-0000', 'joinedDate' => 'Apr 1, 2025', 'status' => 'Approved', 'totalBookings' => 28, 'revenue' => 140000, 'commission' => 14000, 'rating' => 4.9],
            ['id' => 'V006', 'name' => 'Clean Sweep Crew', 'owner' => 'Ramon Garcia', 'category' => 'Dishwashing', 'location' => 'Nasugbu, Batangas', 'email' => 'clean@email.com', 'phone' => '0922-123-4567', 'joinedDate' => 'Apr 15, 2025', 'status' => 'Rejected', 'totalBookings' => 0, 'revenue' => 0, 'commission' => 0, 'rating' => 0],
        ];

        $categories = [
            ['id' => 'C01', 'name' => 'Decoration', 'icon' => '🌸', 'vendors' => 8, 'bookings' => 124, 'commission' => 10, 'status' => 'Active'],
            ['id' => 'C02', 'name' => 'Serving Staff', 'icon' => '🍽', 'vendors' => 5, 'bookings' => 98, 'commission' => 10, 'status' => 'Active'],
            ['id' => 'C03', 'name' => 'Dishwashing', 'icon' => '🫧', 'vendors' => 3, 'bookings' => 76, 'commission' => 8, 'status' => 'Active'],
            ['id' => 'C04', 'name' => 'Catering', 'icon' => '🥘', 'vendors' => 6, 'bookings' => 55, 'commission' => 12, 'status' => 'Active'],
            ['id' => 'C05', 'name' => 'Photography', 'icon' => '📸', 'vendors' => 4, 'bookings' => 43, 'commission' => 12, 'status' => 'Active'],
            ['id' => 'C06', 'name' => 'Audio/Lights', 'icon' => '🎵', 'vendors' => 3, 'bookings' => 30, 'commission' => 10, 'status' => 'Active'],
            ['id' => 'C07', 'name' => 'Full Package', 'icon' => '🎁', 'vendors' => 2, 'bookings' => 50, 'commission' => 10, 'status' => 'Active'],
        ];

        $disputes = [
            ['id' => 'DSP-001', 'type' => 'Customer', 'subject' => 'No-show vendor on event day', 'bookingId' => 'BK-1042', 'raisedBy' => 'Liza Marcos', 'against' => 'Sound & Lights Pro', 'date' => 'Aug 28, 2026', 'status' => 'Open', 'priority' => 'High'],
            ['id' => 'DSP-002', 'type' => 'Vendor', 'subject' => 'Customer refused to pay remaining balance', 'bookingId' => 'BK-0988', 'raisedBy' => 'Blossom Decor Studio', 'against' => 'Cathy Reyes', 'date' => 'Aug 25, 2026', 'status' => 'In Review', 'priority' => 'Medium'],
            ['id' => 'DSP-003', 'type' => 'Customer', 'subject' => 'Service quality did not match listing', 'bookingId' => 'BK-0954', 'raisedBy' => 'Mark Tan', 'against' => 'Elite Serving Team', 'date' => 'Aug 20, 2026', 'status' => 'Resolved', 'priority' => 'Low'],
            ['id' => 'DSP-004', 'type' => 'Customer', 'subject' => 'Incorrect billing amount charged', 'bookingId' => 'BK-0921', 'raisedBy' => 'Rosa dela Paz', 'against' => 'Cuisine Catering PH', 'date' => 'Aug 18, 2026', 'status' => 'Closed', 'priority' => 'Low'],
        ];

        $monthly = [
            ['month' => 'Mar', 'bookings' => 28, 'revenue' => 140000, 'commission' => 14000],
            ['month' => 'Apr', 'bookings' => 45, 'revenue' => 225000, 'commission' => 22500],
            ['month' => 'May', 'bookings' => 62, 'revenue' => 310000, 'commission' => 31000],
            ['month' => 'Jun', 'bookings' => 54, 'revenue' => 270000, 'commission' => 27000],
            ['month' => 'Jul', 'bookings' => 78, 'revenue' => 390000, 'commission' => 39000],
            ['month' => 'Aug', 'bookings' => 91, 'revenue' => 455000, 'commission' => 45500],
        ];

        $pending = array_values(array_filter($vendors, fn ($v) => $v['status'] === 'Pending'));
        $approved = array_values(array_filter($vendors, fn ($v) => $v['status'] === 'Approved'));
        $openDisputes = count(array_filter($disputes, fn ($d) => $d['status'] === 'Open'));
        $totalCommission = array_sum(array_column($vendors, 'commission'));
        $totalRevenue = array_sum(array_column($vendors, 'revenue'));

        $active = array_values(array_filter($categories, fn ($c) => $c['status'] === 'Active'));
        $topCategories = $active;
        usort($topCategories, fn ($a, $b) => $b['bookings'] <=> $a['bookings']);
        $totalCategoryBookings = array_sum(array_column($categories, 'bookings'));

        $topVendors = $approved;
        usort($topVendors, fn ($a, $b) => $b['revenue'] <=> $a['revenue']);

        return [
            'navItems' => [
                ['id' => 'overview', 'label' => 'Overview', 'icon' => '⬛'],
                ['id' => 'vendors', 'label' => 'Vendors', 'icon' => '🏪', 'badge' => ['count' => count($pending), 'bg' => '#c9a84c', 'color' => '#1a110a', 'key' => 'pending']],
                ['id' => 'categories', 'label' => 'Categories', 'icon' => '🗂'],
                ['id' => 'commission', 'label' => 'Commission', 'icon' => '₱'],
                ['id' => 'analytics', 'label' => 'Analytics', 'icon' => '📊'],
                ['id' => 'disputes', 'label' => 'Disputes', 'icon' => '⚖', 'badge' => ['count' => $openDisputes, 'bg' => '#e53935', 'color' => '#fff', 'key' => 'disputes']],
                ['id' => 'settings', 'label' => 'Settings', 'icon' => '⚙'],
            ],
            'vendors' => $vendors,
            'pending' => $pending,
            'approved' => $approved,
            'categories' => $categories,
            'activeCategories' => $active,
            'topCategories' => array_slice($topCategories, 0, 5),
            'categoryShare' => array_map(fn ($c) => (int) round($c['bookings'] / $totalCategoryBookings * 100), $active),
            'disputes' => $disputes,
            'openDisputes' => $openDisputes,
            'monthly' => $monthly,
            'maxMonthly' => max(array_column($monthly, 'revenue')),
            'totalCommission' => $totalCommission,
            'totalRevenue' => $totalRevenue,
            'topVendors' => $topVendors,
            'overviewStats' => [
                ['label' => 'Total Vendors', 'value' => count($approved), 'sub' => count($pending).' pending approval', 'icon' => '🏪', 'color' => '#4a6fdc'],
                ['label' => 'Platform Revenue', 'value' => '₱'.round($totalRevenue / 1000).'K', 'sub' => 'All-time gross bookings', 'icon' => '💰', 'color' => '#2e7d32'],
                ['label' => 'Commission Earned', 'value' => '₱'.round($totalCommission / 1000).'K', 'sub' => 'Platform share to date', 'icon' => '₱', 'color' => '#c9a84c'],
                ['label' => 'Open Disputes', 'value' => $openDisputes, 'sub' => 'Requires attention', 'icon' => '⚖', 'color' => $openDisputes > 0 ? '#c62828' : '#2e7d32'],
            ],
            'analyticsStats' => [
                ['label' => 'Total Bookings', 'value' => '426', 'change' => '+18%', 'icon' => '📅'],
                ['label' => 'Platform Revenue', 'value' => '₱'.round($totalRevenue / 1000).'K', 'change' => '+24%', 'icon' => '💰'],
                ['label' => 'Active Vendors', 'value' => count($approved), 'change' => '+3', 'icon' => '🏪'],
                ['label' => 'Avg. Booking Value', 'value' => '₱8,200', 'change' => '+5%', 'icon' => '📈'],
            ],
        ];
    }
}

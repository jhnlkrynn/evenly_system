<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Vendor;

final class DatabaseDashboardData
{
    public static function customer(User $customer): array
    {
        $models = $customer->bookings()
            ->with(['vendor', 'service.category', 'staff', 'payments', 'review'])
            ->latest('event_date')
            ->latest('id')
            ->get();

        $bookings = $models->map(fn (Booking $booking): array => [
            'id' => $booking->booking_code,
            'service' => $booking->service?->name ?? 'Event service',
            'vendor' => $booking->vendor->business_name,
            'date' => $booking->event_date->format('M j, Y'),
            'time' => $booking->timeRange(),
            'guests' => $booking->pax,
            'amount' => '₱'.number_format((float) $booking->total_amount),
            'status' => $booking->status,
            'category' => $booking->service?->category?->name ?? 'Event service',
            'booking' => $booking,
        ])->all();

        $upcoming = array_values(array_filter($bookings, fn (array $booking): bool => in_array($booking['status'], ['Confirmed', 'Pending'], true)));
        $completed = array_values(array_filter($bookings, fn (array $booking): bool => $booking['status'] === 'Completed'));
        $notifications = $models->take(8)->map(fn (Booking $booking): array => [
            'icon' => $booking->status === 'Confirmed' ? '✅' : ($booking->status === 'Completed' ? '🎉' : '📅'),
            'text' => sprintf('Booking %s with %s is %s.', $booking->booking_code, $booking->vendor->business_name, strtolower($booking->status)),
            'time' => $booking->created_at->diffForHumans(),
            'unread' => $booking->status === 'Pending',
        ])->values()->all();
        $tracked = array_merge(
            array_values(array_filter($bookings, fn (array $booking): bool => $booking['status'] !== 'Completed')),
            array_slice($completed, 0, 1),
        );

        return [
            'navItems' => [
                ['id' => 'overview', 'label' => 'Overview', 'icon' => '⊞'],
                ['id' => 'bookings', 'label' => 'My Bookings', 'icon' => '📅'],
                ['id' => 'status', 'label' => 'Booking Status', 'icon' => '📍'],
                ['id' => 'reviews', 'label' => 'Ratings & Reviews', 'icon' => '⭐'],
                ['id' => 'favorites', 'label' => 'Favorites', 'icon' => '♡'],
                ['id' => 'notifications', 'label' => 'Notifications', 'icon' => '🔔', 'badge' => ['count' => collect($notifications)->where('unread', true)->count(), 'bg' => '#c9a84c', 'color' => '#1a110a', 'key' => 'unread']],
                ['id' => 'profile', 'label' => 'Profile Settings', 'icon' => '👤'],
                ['id' => 'help', 'label' => 'Help & Support', 'icon' => '❓'],
            ],
            'bookings' => $bookings,
            'upcoming' => $upcoming,
            'completed' => $completed,
            'pendingReviews' => collect($completed)->filter(fn (array $booking): bool => ! $booking['booking']->review)->count(),
            'tracked' => $tracked,
            'notifications' => $notifications,
            'unread' => collect($notifications)->where('unread', true)->count(),
            'favorites' => [],
            'stats' => [
                ['label' => 'Total Bookings', 'value' => count($bookings), 'icon' => '📅', 'color' => '#c9a84c'],
                ['label' => 'Upcoming Events', 'value' => count($upcoming), 'icon' => '🎉', 'color' => '#4a6fdc'],
                ['label' => 'Completed', 'value' => count($completed), 'icon' => '✅', 'color' => '#2e7d32'],
                ['label' => 'Saved Vendors', 'value' => 0, 'icon' => '♥', 'color' => '#e53935'],
            ],
            'faqs' => [
                ['q' => 'How do I book a service on Eventlynk?', 'a' => "Go to Browse Services, select a vendor, and click 'Book Now'. Fill in your event details, review the pricing summary, and submit your booking."],
                ['q' => 'What is the reservation fee?', 'a' => 'The reservation fee secures your date and vendor. The remaining balance is shown in your booking and can be recorded through the demo payment page.'],
                ['q' => 'Can I cancel my booking?', 'a' => 'Pending bookings can be cancelled from the booking details panel.'],
            ],
        ];
    }

    public static function vendor(Vendor $vendor): array
    {
        $bookingModels = $vendor->bookings()
            ->with(['customer', 'service.category', 'staff', 'payments'])
            ->latest('event_date')
            ->latest('id')
            ->get();
        $bookings = $bookingModels->map(fn (Booking $booking): array => [
            'id' => $booking->booking_code,
            'customer' => $booking->customer->name,
            'service' => $booking->service?->name ?? 'Event service',
            'date' => $booking->event_date->format('M j, Y'),
            'guests' => $booking->pax,
            'amount' => '₱'.number_format((float) $booking->total_amount),
            'status' => $booking->status,
            'eventType' => $booking->event_type,
            'booking' => $booking,
        ])->all();
        $serviceModels = $vendor->services()->with('category')->withCount('bookings')->withAvg('vendorReviews', 'rating')->latest()->get();
        $services = $serviceModels->map(fn ($service): array => [
            'id' => $service->id,
            'name' => $service->name,
            'category' => $service->category->name,
            'price' => '₱'.number_format((float) $service->price).'/'.$service->price_unit,
            'bookings' => $service->bookings_count,
            'rating' => $service->vendor_reviews_avg_rating ? number_format($service->vendor_reviews_avg_rating, 1) : '—',
            'status' => $service->is_active ? 'Active' : 'Paused',
            'desc' => $service->description,
            'service' => $service,
        ])->all();
        $monthly = collect(range(8, 0))->map(function (int $monthsAgo) use ($bookingModels): array {
            $month = now()->startOfMonth()->subMonths($monthsAgo);

            return [
                'month' => $month->format('M'),
                'amount' => (float) $bookingModels
                    ->where('status', 'Completed')
                    ->filter(fn (Booking $booking): bool => $booking->event_date->isSameMonth($month))
                    ->sum('total_amount'),
            ];
        })->all();
        $statusCounts = collect(Booking::STATUSES)->mapWithKeys(fn (string $status): array => [$status => $bookingModels->where('status', $status)->count()]);
        $categoryCounts = $bookingModels->groupBy(fn (Booking $booking): string => $booking->service?->category?->name ?? 'Other')->map->count();

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
            'activeBookingList' => array_values(array_filter($bookings, fn (array $booking): bool => in_array($booking['status'], ['Confirmed', 'Pending'], true))),
            'monthly' => $monthly,
            'maxEarning' => max(1, max(array_column($monthly, 'amount'))),
            'chartData' => [
                'monthly' => ['labels' => array_column($monthly, 'month'), 'values' => array_column($monthly, 'amount')],
                'statuses' => ['labels' => $statusCounts->keys()->values(), 'values' => $statusCounts->values()],
                'categories' => ['labels' => $categoryCounts->keys()->values(), 'values' => $categoryCounts->values()],
            ],
            'stats' => [
                ['label' => 'Total Services', 'value' => count($services), 'icon' => '📦', 'color' => '#4a6fdc'],
                ['label' => 'Active Bookings', 'value' => $bookingModels->whereIn('status', ['Confirmed', 'Pending'])->count(), 'icon' => '📅', 'color' => '#c9a84c'],
                ['label' => 'Total Earnings', 'value' => '₱'.number_format((float) $bookingModels->where('status', 'Completed')->sum('total_amount')), 'icon' => '💰', 'color' => '#2e7d32'],
                ['label' => 'Avg. Rating', 'value' => $vendor->reviews()->avg('rating') ? number_format($vendor->reviews()->avg('rating'), 1) : '—', 'icon' => '⭐', 'color' => '#e53935'],
            ],
            'staff' => $vendor->staff()->latest()->get()->map(fn ($member): array => ['id' => $member->id, 'name' => $member->full_name, 'role' => $member->role, 'available' => $member->is_active, 'staff' => $member])->all(),
            'staffAssignments' => $bookingModels->mapWithKeys(fn (Booking $booking): array => [$booking->booking_code => $booking->staff->pluck('id')->all()])->all(),
            'inventory' => [],
            'payments' => $bookingModels->where('status', 'Completed')->take(10)->map(fn (Booking $booking): array => ['id' => $booking->booking_code, 'date' => $booking->event_date->format('M j, Y'), 'gross' => (float) $booking->total_amount, 'status' => 'Released'])->values()->all(),
            'bookedDays' => $bookingModels->whereIn('status', ['Pending', 'Confirmed'])->filter(fn (Booking $booking): bool => $booking->event_date->isSameMonth(now()))->map(fn (Booking $booking): int => $booking->event_date->day)->all(),
            'blockedDays' => [],
            'categories' => ServiceCategory::orderBy('name')->get(),
            'calendarBookings' => $bookingModels->map(fn (Booking $booking): array => [
                'date' => $booking->event_date->toDateString(),
                'code' => $booking->booking_code,
                'status' => $booking->status,
                'customer' => $booking->customer->name,
            ])->values()->all(),
        ];
    }
}

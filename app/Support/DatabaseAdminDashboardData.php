<?php

namespace App\Support;

use App\Models\ActivityLog;
use App\Models\Barangay;
use App\Models\Booking;
use App\Models\PlatformSetting;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Carbon;

final class DatabaseAdminDashboardData
{
    public static function admin(User $actor): array
    {
        $bookings = Booking::query()->with(['customer', 'vendor.user', 'service.category', 'staff', 'payments'])
            ->latest('event_date')->get()->map(fn (Booking $b) => self::booking($b))->all();
        $vendors = Vendor::query()->with(['user', 'services.category'])->withCount(['bookings', 'services'])->latest()->get();
        $services = Service::query()->with(['vendor', 'category'])->withCount('bookings')->latest()->get();
        $users = User::query()->latest()->get();
        $categories = ServiceCategory::query()->withCount(['services', 'services as bookings_count' => fn ($q) => $q->join('bookings', 'services.id', '=', 'bookings.service_id')])->get();

        $monthly = collect(range(5, 0))->map(function (int $offset) {
            $date = now()->subMonths($offset);
            $query = Booking::query()->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->where('status', '!=', 'Cancelled');

            return ['month' => $date->format('M'), 'bookings' => (clone $query)->count(), 'revenue' => (float) (clone $query)->sum('total_amount')];
        })->values()->all();

        $categoryChart = ServiceCategory::query()->withCount(['services as booking_count' => fn ($q) => $q->join('bookings', 'services.id', '=', 'bookings.service_id')->where('bookings.status', '!=', 'Cancelled')])->get();
        $topVendors = Vendor::query()->with('user')->withSum(['bookings as revenue' => fn ($q) => $q->where('status', '!=', 'Cancelled')], 'total_amount')->withCount(['bookings as completed_bookings' => fn ($q) => $q->where('status', 'Completed')])->orderByDesc('revenue')->limit(5)->get();

        $pending = $vendors->filter(fn (Vendor $v) => ! $v->is_verified)->map(fn (Vendor $v) => self::vendor($v))->values()->all();
        $bookingRows = $bookings;
        $upcoming = collect($bookings)->filter(fn ($b) => in_array($b['status'], ['Pending', 'Confirmed'], true))->take(6)->values()->all();

        return [
            'navItems' => [
                ['id' => 'overview', 'label' => 'Overview', 'icon' => '⊞'], ['id' => 'bookings', 'label' => 'Bookings', 'icon' => '📅'], ['id' => 'vendors', 'label' => 'Vendors', 'icon' => '🏪'], ['id' => 'users', 'label' => 'Users', 'icon' => '👥'], ['id' => 'services-admin', 'label' => 'Services', 'icon' => '📦'],
                ['id' => 'calendar', 'label' => 'Calendar', 'icon' => '🗓'], ['id' => 'staff', 'label' => 'Staff', 'icon' => '👥'],
                ['id' => 'inventory', 'label' => 'Inventory', 'icon' => '🗃'], ['id' => 'invoices', 'label' => 'Invoices', 'icon' => '🧾'],
                ['id' => 'reports', 'label' => 'Reports', 'icon' => '📊'], ['id' => 'settings', 'label' => 'Settings', 'icon' => '⚙'],
            ],
            'bookings' => $bookingRows, 'upcoming' => $upcoming, 'bookingState' => collect($bookingRows)->mapWithKeys(fn ($b) => [$b['id'] => ['total' => $b['totalAmount'], 'paid' => $b['amountPaid'], 'status' => $b['status'], 'payment' => $b['paymentStatus']]])->all(),
            'kpis' => [['label' => 'Total Bookings', 'value' => count($bookings), 'icon' => '📅', 'color' => '#c9a84c'], ['label' => 'Revenue', 'value' => '₱'.number_format(Booking::where('status', '!=', 'Cancelled')->sum('total_amount')), 'icon' => '💰', 'color' => '#2e7d32'], ['label' => 'Vendors', 'value' => $vendors->count(), 'icon' => '🏪', 'color' => '#4a6fdc'], ['label' => 'Users', 'value' => $users->count(), 'icon' => '👥', 'color' => '#e53935'], ['label' => 'Pending', 'value' => count($pending), 'icon' => '⏳', 'color' => '#e65100']],
            'staff' => [], 'assignmentRows' => [], 'calendarEvents' => collect($bookings)->map(fn ($b) => ['date' => $b['date'], 'label' => $b['eventType'].' · '.$b['client'], 'status' => $b['status']])->all(),
            'calendarGrid' => [], 'usedStaff' => [], 'inventoryAlerts' => [], 'decorInventory' => [], 'equipmentInventory' => [],
            'vendors' => $vendors->map(fn (Vendor $v) => self::vendor($v))->all(), 'pending' => $pending, 'users' => $users->map(fn (User $u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => ucfirst($u->role), 'status' => $u->is_active ? 'Active' : 'Inactive'])->all(),
            'services' => $services->map(fn (Service $s) => ['id' => $s->id, 'name' => $s->name, 'category' => $s->category?->name ?? '—', 'price' => '₱'.number_format((float) $s->price), 'bookings' => $s->bookings_count, 'rating' => '—', 'status' => $s->is_active ? 'Active' : 'Inactive', 'desc' => $s->description ?? '', 'vendor' => $s->vendor?->business_name ?? '—'])->all(),
            'chartData' => ['monthly' => $monthly, 'categories' => $categoryChart->map(fn ($c) => ['label' => $c->name, 'value' => $c->booking_count])->values(), 'topVendors' => $topVendors->map(fn ($v) => ['label' => $v->business_name, 'value' => (float) ($v->revenue ?? 0)])->values()],
            'monthly' => collect($monthly)->map(fn ($m) => ['month' => $m['month'], 'revenue' => $m['revenue'], 'commission' => $m['revenue'] * .1])->all(), 'maxMonthly' => max(1, collect($monthly)->max('revenue')), 'categoryShare' => [], 'openDisputes' => 0,
        ];
    }

    public static function superAdmin(User $actor): array
    {
        $data = self::admin($actor);
        $data['navItems'] = [['id' => 'overview', 'label' => 'Overview', 'icon' => '⊞'], ['id' => 'vendors', 'label' => 'Vendors', 'icon' => '🏪'], ['id' => 'categories', 'label' => 'Categories', 'icon' => '🗂'], ['id' => 'barangays', 'label' => 'Barangays', 'icon' => '📍'], ['id' => 'admins', 'label' => 'Admins', 'icon' => '🛡'], ['id' => 'activity', 'label' => 'Activity Logs', 'icon' => '🧾'], ['id' => 'commission', 'label' => 'Commission', 'icon' => '₱'], ['id' => 'analytics', 'label' => 'Analytics', 'icon' => '📊'], ['id' => 'disputes', 'label' => 'Disputes', 'icon' => '⚠'], ['id' => 'settings', 'label' => 'Settings', 'icon' => '⚙']];
        $data['categories'] = ServiceCategory::query()->withCount('services')->get()->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'icon' => $c->icon ?? '🗂', 'vendors' => Vendor::whereHas('services', fn ($q) => $q->where('service_category_id', $c->id))->count(), 'bookings' => Booking::whereHas('service', fn ($q) => $q->where('service_category_id', $c->id))->count(), 'commission' => 10, 'status' => 'Active'])->all();
        $data['admins'] = User::whereIn('role', ['admin', 'superadmin'])->latest()->get()->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => ucfirst($u->role), 'status' => $u->is_active ? 'Active' : 'Inactive'])->all();
        $data['barangays'] = Barangay::query()->orderBy('name')->get()->all();
        $logs = ActivityLog::with('user')->when(request('user'), fn ($q, $value) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$value}%")))->when(request('action'), fn ($q, $value) => $q->where('action', 'like', "%{$value}%"))->when(request('date'), fn ($q, $value) => $q->whereDate('created_at', $value))->latest()->limit(100)->get();
        $data['activityLogs'] = $logs->map(fn ($l) => ['id' => $l->id, 'user' => $l->user?->name ?? 'System', 'action' => $l->action, 'description' => $l->description, 'date' => $l->created_at->format('M d, Y h:i A')])->all();
        $data['monthly'] = collect($data['monthly'])->map(fn ($m) => ['month' => $m['month'], 'bookings' => $m['bookings'] ?? 0, 'revenue' => $m['revenue'], 'commission' => $m['commission']])->all();
        $data['approved'] = collect($data['vendors'])->where('status', 'Approved')->values()->all();
        $data['topVendors'] = collect($data['approved'])->sortByDesc('revenue')->values()->all();
        $data['activeCategories'] = collect($data['categories'])->where('status', 'Active')->values()->all();
        $data['topCategories'] = collect($data['categories'])->sortByDesc('bookings')->take(5)->values()->all();
        $totalCategoryBookings = max(1, collect($data['categories'])->sum('bookings'));
        $data['categoryShare'] = collect($data['categories'])->map(fn ($c) => (int) round($c['bookings'] / $totalCategoryBookings * 100))->all();
        $data['disputes'] = [];
        $data['totalCommission'] = collect($data['vendors'])->sum(fn ($v) => 0);
        $data['totalRevenue'] = collect($data['vendors'])->sum(fn ($v) => 0);
        $data['overviewStats'] = [['label' => 'Total Vendors', 'value' => count($data['approved']), 'sub' => count($data['pending']).' pending approval', 'icon' => '🏪', 'color' => '#4a6fdc'], ['label' => 'Platform Revenue', 'value' => '₱'.number_format(Booking::where('status', '!=', 'Cancelled')->sum('total_amount')), 'sub' => 'All-time gross bookings', 'icon' => '💰', 'color' => '#2e7d32'], ['label' => 'Commission Earned', 'value' => '₱'.number_format(Booking::where('status', '!=', 'Cancelled')->sum('total_amount') * .1), 'sub' => 'Platform share to date', 'icon' => '₱', 'color' => '#c9a84c'], ['label' => 'Open Disputes', 'value' => 0, 'sub' => 'Requires attention', 'icon' => '⚖', 'color' => '#2e7d32']];
        $data['analyticsStats'] = [['label' => 'Total Bookings', 'value' => count($data['bookings']), 'change' => '', 'icon' => '📅'], ['label' => 'Platform Revenue', 'value' => '₱'.number_format(Booking::sum('total_amount')), 'change' => '', 'icon' => '💰'], ['label' => 'Active Vendors', 'value' => count($data['approved']), 'change' => '', 'icon' => '🏪'], ['label' => 'Avg. Booking Value', 'value' => '₱'.number_format(Booking::avg('total_amount') ?: 0), 'change' => '', 'icon' => '📈']];
        $settings = PlatformSetting::query()->pluck('value', 'key')->all();
        $data['platformSettings'] = [
            'platform_name' => $settings['platform_name'] ?? 'Eventlynk',
            'support_email' => $settings['support_email'] ?? 'support@eventlynk.com',
            'operating_locations' => $settings['operating_locations'] ?? 'Nasugbu, Lian, Batangas',
            'default_currency' => $settings['default_currency'] ?? 'PHP (₱)',
            'reservation_fee_rate' => $settings['reservation_fee_rate'] ?? '30',
        ];

        return $data;
    }

    private static function booking(Booking $b): array
    {
        return ['id' => $b->booking_code, 'client' => $b->customer?->name ?? '—', 'serviceName' => $b->service?->name ?? '—', 'date' => Carbon::parse($b->event_date)->format('M d, Y'), 'pax' => $b->pax, 'totalAmount' => (float) $b->total_amount, 'reservationFee' => (float) $b->reservation_fee, 'amountPaid' => (float) $b->amount_paid, 'status' => $b->status, 'paymentStatus' => $b->payment_status, 'paymentMethod' => $b->payments->last()?->method ?? '—', 'paid' => (float) $b->amount_paid, 'assignedStaff' => $b->staff->pluck('name')->join(', ') ?: 'Unassigned', 'eventType' => $b->event_type, 'venue' => $b->venue, 'category' => $b->service?->category?->name ?? '—', 'services' => ['decoration' => $b->needs_decoration, 'serving' => $b->serving_count, 'dishwashing' => $b->dishwashing_count]];
    }

    private static function vendor(Vendor $v): array
    {
        $revenue = (float) $v->bookings()->where('status', '!=', 'Cancelled')->sum('total_amount');

        return ['id' => $v->id, 'name' => $v->business_name, 'owner' => $v->user?->name ?? '—', 'category' => $v->services->first()?->category?->name ?? '—', 'location' => $v->locationLabel(), 'status' => $v->is_verified ? 'Approved' : 'Pending', 'bookings' => $v->bookings_count ?? 0, 'totalBookings' => $v->bookings_count ?? 0, 'rating' => 0, 'joined' => $v->created_at?->format('M Y'), 'joinedDate' => $v->created_at?->format('M d, Y'), 'email' => $v->user?->email ?? '—', 'phone' => $v->user?->phone ?? '—', 'user_id' => $v->user_id, 'revenue' => $revenue, 'commission' => $revenue * .1];
    }
}

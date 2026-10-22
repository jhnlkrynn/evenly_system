@extends('layouts.dashboard')

@section('title', 'Vendor Portal · Eventlynk')
@section('dashboard', 'vendor')
@section('shell_style', 'flex: 1; min-width: 0; display: flex; flex-direction: column; overflow: hidden;')
@section('main_class', 'vendor-main app-dashboard-main')

@php
    $user = auth()->user();
    // Every vendor account has a vendors row (created at sign-up or by the seeder).
    $business = $user->vendor ?? new \App\Models\Vendor(['business_name' => $user->name]);
    $card = 'background: #fff; border-radius: 12px; border: 1px solid #e8dfd0; overflow: hidden;';
    $cardTitle = "font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #1a110a; margin: 0;";
    $th = 'text-align: left; font-weight: 600; color: #9c8f7a;';
    $td = 'padding: 13px 16px; font-size: 13px;';
    $label = 'display: block; font-size: 12px; font-weight: 600; color: #6b5a47; margin-bottom: 6px;';
    $input = 'width: 100%; padding: 10px 12px; border: 1px solid #ddd5c8; border-radius: 8px; font-size: 13px; color: #1a110a; outline: none; background: #faf8f4; box-sizing: border-box;';
    $cancelBtn = 'flex: 1; padding: 11px; border-radius: 8px; border: 1px solid #e8dfd0; background: #faf8f4; color: #3d2510; font-size: 13px; font-weight: 500; cursor: pointer;';
    $saveBtn = 'flex: 2; padding: 11px; border-radius: 8px; border: none; background: #1a110a; color: #f5f0e8; font-size: 13px; font-weight: 600; cursor: pointer;';
    $modalTitle = "font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #1a110a; margin: 0;";
    $closeX = 'background: none; border: none; font-size: 20px; cursor: pointer; color: #6b5a47;';
    $settingsCard = 'background: #fff; border-radius: 14px; border: 1px solid #e8dfd0; padding: 24px 28px; margin-bottom: 20px;';
    $staffById = collect($staff)->keyBy('id');
@endphp

@section('sidebar')
<x-dashboard.sidebar variant="portal" :items="$navItems" extra-class="vendor-sidebar" open-classes="vendor-sidebar-open open">
    <x-slot:brand>
        <img src="{{ asset('images/eventlynk-logo.png') }}" alt="Eventlynk" style="width: 40px; height: 40px; object-fit: contain;">
        <span style="font-family: 'Playfair Display', serif; color: #c9a84c; font-size: 16px; font-weight: 700; white-space: nowrap;">Eventlynk</span>
    </x-slot:brand>
    <x-slot:profile>
        <div data-sidebar-open-only style="padding: 20px 16px; border-bottom: 1px solid #c9a84c22; text-align: center;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: #3d2510; border: 2px solid #c9a84c; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; color: #c9a84c; margin: 0 auto 10px;">{{ $user->initials() }}</div>
            <div style="color: #f5f0e8; font-size: 14px; font-weight: 600;">{{ $business->short_name ?? $business->business_name }}</div>
            <div style="color: #9c8f7a; font-size: 12px; margin-top: 3px;">{{ $user->email }}</div>
            <span style="display: inline-block; margin-top: 8px; padding: 3px 10px; background: #c9a84c22; border: 1px solid #c9a84c44; border-radius: 20px; font-size: 11px; color: #c9a84c;">⭐ Top Rated Vendor</span>
        </div>
    </x-slot:profile>
</x-dashboard.sidebar>
@endsection

@section('header')
<header class="vendor-header app-dashboard-header" style="background: #fff; border-bottom: 1px solid #e8dfd0; padding: 0 28px; height: 64px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
    <div>
        <h2 data-dashboard-title style="font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #1a110a; margin: 0;">Dashboard</h2>
        <p style="font-size: 12px; color: #9c8f7a; margin: 0;">Vendor Portal · {{ $business->business_name }}</p>
    </div>
    <div style="display: flex; align-items: center; gap: 14px;">
        <button type="button" class="vendor-add-service" data-modal-open="vendor-add-service" style="padding: 8px 20px; border-radius: 8px; background: #c9a84c; border: none; color: #1a110a; font-size: 13px; font-weight: 600; cursor: pointer;">+ Add Service</button>
        <button type="button" aria-label="Notifications" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #6b5a47;">🔔</button>
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #3d2510; border: 2px solid #c9a84c; display: flex; align-items: center; justify-content: center; color: #c9a84c; font-size: 13px; font-weight: 700; cursor: pointer;">{{ $user->initials() }}</div>
    </div>
</header>
@endsection

@section('panels')
@if (! $business->is_verified)
    <div style="background: #fff8e1; border: 1px solid #f9a825; border-radius: 10px; padding: 13px 18px; margin-bottom: 20px; color: #8a6418;">
        <strong>Pending verification.</strong> Your business profile is saved, but customers will not see your services until Eventlynk verifies your vendor account.
    </div>
@endif
<script type="application/json" data-vendor-data>@json(['charts' => $chartData, 'calendar' => $calendarBookings])</script>
{{-- ── BROWSE SERVICES (embedded marketplace; main padding drops to 0 on this tab) ── --}}
<div data-panel="browse" data-main-padding="0" hidden>
    @include('services.partials.browser', ['embedded' => true])
</div>

{{-- ── OVERVIEW ── --}}
<div data-panel="overview">
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px;">
        @foreach ($stats as $stat)
            <x-stat-card variant="tile" :label="$stat['label']" :value="$stat['value']" :icon="$stat['icon']" :color="$stat['color']" value-size="1.75rem" />
        @endforeach
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div style="{{ $card }}">
            <div style="padding: 18px 20px; border-bottom: 1px solid #e8dfd0; display: flex; justify-content: space-between;">
                <h3 style="{{ $cardTitle }}">Recent Bookings</h3>
                <button type="button" data-tab-link="bookings" style="background: none; border: none; color: #c9a84c; font-size: 13px; cursor: pointer;">View all →</button>
            </div>
            @foreach (array_slice($bookings, 0, 4) as $i => $b)
                <div style="padding: 14px 20px; border-bottom: {{ $i < 3 ? '1px solid #f0ebe2' : 'none' }}; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 600; font-size: 13px; color: #1a110a; margin-bottom: 2px;">{{ $b['customer'] }}</div>
                        <div style="font-size: 12px; color: #9c8f7a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $b['service'] }} · {{ $b['date'] }}</div>
                    </div>
                    <div style="text-align: right; flex-shrink: 0;">
                        <div style="font-weight: 600; font-size: 13px; color: #1a110a;">{{ $b['amount'] }}</div>
                        <x-status-badge :status="$b['status']" padding="2px 8px" :size="11" />
                    </div>
                </div>
            @endforeach
        </div>

        <div style="background: #fff; border-radius: 12px; border: 1px solid #e8dfd0; padding: 20px;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #1a110a; margin-bottom: 20px;">Monthly Earnings</h3>
            <canvas data-chart="monthly-earnings" height="160"></canvas>
        </div>
        <div style="{{ $card }}; padding: 20px;"><h3 style="{{ $cardTitle }}">Bookings by Status</h3><canvas data-chart="booking-status" height="180"></canvas></div>
        <div style="{{ $card }}; padding: 20px;"><h3 style="{{ $cardTitle }}">Bookings by Category</h3><canvas data-chart="booking-category" height="180"></canvas></div>
    </div>
</div>

{{-- ── MY SERVICES ── --}}
<div data-panel="services" hidden>
    <div style="display: flex; justify-content: flex-end; margin-bottom: 16px;">
        <button type="button" data-modal-open="vendor-add-service" style="padding: 10px 24px; border-radius: 8px; background: #1a110a; color: #f5f0e8; border: none; font-size: 14px; font-weight: 600; cursor: pointer;">+ Add New Service</button>
    </div>
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px;">
        @foreach ($services as $s)
            <div data-service="{{ $s['id'] }}" style="background: #fff; border-radius: 12px; border: 1px solid #e8dfd0; padding: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <span style="font-size: 11px; color: #9c8f7a;">{{ $s['id'] }}</span>
                    <x-status-badge :status="$s['status']" padding="3px 10px" data-service-status />
                </div>
                <h3 data-service-name style="font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #1a110a; margin-bottom: 6px;">{{ $s['name'] }}</h3>
                <div style="font-size: 13px; color: #9c8f7a; margin-bottom: 12px;">{{ $s['category'] }}</div>
                <div style="display: flex; gap: 20px; margin-bottom: 16px;">
                    <div>
                        <div data-service-price style="font-size: 18px; font-weight: 700; color: #c9a84c;">{{ $s['price'] }}</div>
                        <div style="font-size: 11px; color: #9c8f7a;">Price</div>
                    </div>
                    <div>
                        <div style="font-size: 18px; font-weight: 700; color: #1a110a;">{{ $s['bookings'] }}</div>
                        <div style="font-size: 11px; color: #9c8f7a;">Bookings</div>
                    </div>
                    <div>
                        <div style="font-size: 18px; font-weight: 700; color: #1a110a;">{{ $s['rating'] }} ⭐</div>
                        <div style="font-size: 11px; color: #9c8f7a;">Rating</div>
                    </div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" data-modal-open="edit-service-{{ $s['id'] }}" style="flex: 1; padding: 9px; border-radius: 8px; border: 1px solid #c9a84c; background: transparent; color: #c9a84c; font-size: 13px; font-weight: 500; cursor: pointer;">Edit</button>
                    <form method="POST" action="{{ route('vendor.services.toggle', $s['service']) }}" style="flex: 1;">@csrf @method('PATCH')<button type="submit" style="width: 100%; padding: 9px; border-radius: 8px; background: #1a110a; color: #f5f0e8; border: none; font-size: 13px; font-weight: 500; cursor: pointer;">{{ $s['status'] === 'Active' ? 'Pause' : 'Activate' }}</button></form>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ── BOOKINGS ── --}}
<div data-panel="bookings" hidden style="{{ $card }}">
    <div data-filter-group="vendor-bookings" style="padding: 18px 20px; border-bottom: 1px solid #e8dfd0; display: flex; gap: 10px;">
        @foreach (['All', 'Confirmed', 'Pending', 'Completed'] as $filter)
            <button type="button" class="dash-pill {{ $filter === 'All' ? 'active' : '' }}" data-filter-value="{{ $filter }}">{{ $filter }}</button>
        @endforeach
    </div>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #faf8f4;">
                @foreach (['Booking ID', 'Customer', 'Service', 'Date', 'Guests', 'Amount', 'Status', 'Action'] as $heading)
                    <th style="padding: 12px 16px; {{ $th }} font-size: 12px;">{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($bookings as $b)
                <tr data-filter-item="vendor-bookings" data-filter-key="{{ $b['status'] }}" style="border-top: 1px solid #f0ebe2;">
                    <td style="{{ $td }} color: #9c8f7a;">{{ $b['id'] }}</td>
                    <td style="padding: 13px 16px; font-weight: 600; font-size: 13px; color: #1a110a;">{{ $b['customer'] }}</td>
                    <td style="{{ $td }} color: #3d2510;">{{ $b['service'] }}</td>
                    <td style="{{ $td }} color: #3d2510;">{{ $b['date'] }}</td>
                    <td style="{{ $td }} color: #3d2510;">{{ $b['guests'] }}</td>
                    <td style="padding: 13px 16px; font-weight: 600; font-size: 13px; color: #1a110a;">{{ $b['amount'] }}</td>
                    <td style="padding: 13px 16px;"><x-status-badge :status="$b['status']" /></td>
                    <td style="padding: 13px 16px;">
                        <button type="button" data-modal-open="vendor-booking-{{ $b['id'] }}" style="padding: 6px 14px; border-radius: 6px; border: 1px solid #e8dfd0; background: transparent; color: #3d2510; font-size: 12px; cursor: pointer;">View</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ── STAFF ASSIGNMENT ── --}}
<div data-panel="staff" hidden>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div style="{{ $card }}">
            <div style="padding: 18px 20px; border-bottom: 1px solid #e8dfd0;">
                <div style="display: flex; justify-content: space-between; align-items: center;"><h3 style="{{ $cardTitle }}">Staff Roster</h3><button type="button" data-modal-open="vendor-add-staff" style="background: #1a110a; color: #c9a84c; border: 0; padding: 7px 12px; border-radius: 7px;">+ Add staff</button></div>
            </div>
            @foreach ($staff as $member)
                <div style="display: flex; align-items: center; gap: 14px; padding: 14px 20px; border-bottom: 1px solid #f0ebe2;">
                    <div style="width: 38px; height: 38px; border-radius: 50%; background: #3d2510; display: flex; align-items: center; justify-content: center; color: #c9a84c; font-weight: 700; font-size: 14px; flex-shrink: 0;">{{ collect(explode(' ', $member['name']))->map(fn ($n) => $n[0])->join('') }}</div>
                    <div style="flex: 1;">
                        <div style="font-size: 13px; font-weight: 600; color: #1a110a;">{{ $member['name'] }}</div>
                        <div style="font-size: 11px; color: #9c8f7a;">{{ $member['role'] }}</div>
                    </div>
                    <span style="padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: {{ $member['available'] ? '#e8f5e9' : '#fce4ec' }}; color: {{ $member['available'] ? '#2e7d32' : '#c62828' }};">{{ $member['available'] ? 'Active' : 'Inactive' }}</span>
                    <button type="button" data-modal-open="edit-staff-{{ $member['id'] }}" style="border: 0; background: transparent; color: #c9a84c;">Edit</button>
                </div>
            @endforeach
        </div>
        <div style="display: flex; flex-direction: column; gap: 14px;">
            @foreach ($activeBookingList as $b)
                @php($assigned = $staffAssignments[$b['id']] ?? [])
                <div data-staff-booking="{{ $b['id'] }}" style="background: #fff; border-radius: 12px; border: 1px solid #e8dfd0; padding: 18px 20px;">
                    <div style="font-weight: 700; font-size: 14px; color: #1a110a; margin-bottom: 2px;">{{ $b['id'] }} — {{ $b['customer'] }}</div>
                    <div style="font-size: 12px; color: #9c8f7a; margin-bottom: 12px;">{{ $b['service'] }} · {{ $b['date'] }}</div>
                    <div style="font-size: 12px; font-weight: 600; color: #6b5a47; margin-bottom: 8px;">Assign Staff:</div>
                    <form method="POST" action="{{ route('vendor.bookings.staff', $b['booking']) }}"><div style="display: flex; flex-wrap: wrap; gap: 6px;">
                        @foreach ($staff as $member)
                            @php($isAssigned = in_array($member['id'], $assigned, true))
                            <label style="padding: 5px 12px; border-radius: 20px; border: 1.5px solid #e8dfd0; background: #faf8f4; color: #6b5a47; font-size: 12px;"><input type="checkbox" name="staff_ids[]" value="{{ $member['id'] }}" @checked($isAssigned) @disabled(! $member['available'])> {{ $member['name'] }}</label>
                        @endforeach
                    </div>@csrf<button type="submit" style="margin-top: 12px; padding: 8px 14px; border: 0; border-radius: 7px; background: #1a110a; color: #c9a84c;">Save assignment</button></form>
                    <div data-staff-count @if (count($assigned) === 0) hidden @endif style="margin-top: 10px; font-size: 12px; color: #2e7d32;">{{ count($assigned) }} staff assigned</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── INVENTORY ── --}}
<div data-panel="inventory" hidden>
    <div style="display: flex; justify-content: flex-end; margin-bottom: 16px;">
        <button type="button" data-modal-open="vendor-add-inventory" style="padding: 9px 20px; border-radius: 8px; border: none; background: #1a110a; color: #c9a84c; font-size: 13px; font-weight: 600; cursor: pointer;">+ Add Item</button>
    </div>
    <div style="{{ $card }}">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #faf8f4;">
                    @foreach (['Item', 'Category', 'Owned', 'Available', 'In Use', 'Condition', 'Action'] as $heading)
                        <th style="padding: 12px 16px; {{ $th }} font-size: 11px; text-transform: uppercase;">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($inventory as $item)
                    <tr data-inventory-row style="border-top: 1px solid #f0ebe2;">
                        <td style="{{ $td }} font-weight: 600; color: #1a110a;">{{ $item['name'] }}</td>
                        <td style="{{ $td }} color: #3d2510;">{{ $item['category'] }}</td>
                        <td style="{{ $td }} font-weight: 700; color: #1a110a;">{{ $item['owned'] }}</td>
                        <td style="{{ $td }} color: #2e7d32; font-weight: 600;">{{ $item['available'] }}</td>
                        <td style="{{ $td }} color: #c9a84c; font-weight: 600;">{{ $item['inUse'] }}</td>
                        <td style="padding: 13px 16px;">
                            <span style="padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: {{ $item['condition'] === 'Good' ? '#e8f5e9' : '#fff8e1' }}; color: {{ $item['condition'] === 'Good' ? '#2e7d32' : '#e65100' }};">{{ $item['condition'] }}</span>
                        </td>
                        <td style="padding: 13px 16px;">
                            <button type="button" data-remove-row="{{ $item['name'] }} removed" style="padding: 5px 12px; border-radius: 6px; border: 1px solid #fce4ec; background: transparent; color: #c62828; font-size: 12px; cursor: pointer;">Remove</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ── EARNINGS ── --}}
<div data-panel="earnings" hidden>
    <div style="background: #fff8e1; border: 1px solid #f9a825; border-radius: 10px; padding: 12px 18px; margin-bottom: 20px; font-size: 13px; color: #6d4c41; display: flex; gap: 10px; align-items: center;">
        <span>ℹ</span>
        <span>
            Eventlynk charges a <strong>10% platform commission</strong>
            on all completed bookings. Your payout is gross revenue minus
            commission.
        </span>
    </div>
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px;">
        @foreach ([
            ['Gross Revenue', '₱567,000', '💰', '#1a110a'],
            ['Platform Commission (10%)', '₱56,700', '🏛', '#e65100'],
            ['Your Net Payout', '₱510,300', '✅', '#2e7d32'],
            ['Pending Payout', '₱290,000', '⏳', '#c9a84c'],
        ] as [$statLabel, $statValue, $statIcon, $statColor])
            <x-stat-card variant="simple" :label="$statLabel" :value="$statValue" :icon="$statIcon" :value-color="$statColor" />
        @endforeach
    </div>
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e8dfd0; padding: 24px; margin-bottom: 20px;">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #1a110a; margin-bottom: 20px; margin-top: 0px;">Monthly Earnings &amp; Commission Deduction</h3>
        <div style="display: flex; align-items: flex-end; gap: 12px; height: 180px;">
            @foreach ($monthly as $e)
                @php($barHeight = $e['amount'] / $maxEarning * 150)
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 6px; height: 100%; justify-content: flex-end;">
                    <div style="font-size: 11px; color: #6b5a47;">₱{{ round($e['amount'] / 1000) }}K</div>
                    <div style="width: 100%; display: flex; gap: 2px; align-items: flex-end;">
                        <div style="flex: 9; background: #c9a84c; border-radius: 3px 3px 0 0; height: {{ round($barHeight, 3) }}px; opacity: {{ $e['month'] === 'Jun' ? 0.4 : 1 }};"></div>
                        <div style="flex: 1; background: #e65100; border-radius: 3px 3px 0 0; height: {{ round($barHeight * 0.1, 3) }}px; opacity: {{ $e['month'] === 'Jun' ? 0.4 : 1 }};"></div>
                    </div>
                    <div style="font-size: 11px; color: #6b5a47;">{{ $e['month'] }}</div>
                </div>
            @endforeach
        </div>
        <div style="display: flex; gap: 18px; margin-top: 12px;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 12px; height: 12px; background: #c9a84c; border-radius: 2px;"></div>
                <span style="font-size: 12px; color: #6b5a47;">Net Payout</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <div style="width: 12px; height: 12px; background: #e65100; border-radius: 2px;"></div>
                <span style="font-size: 12px; color: #6b5a47;">Platform Commission</span>
            </div>
        </div>
    </div>
    <div style="{{ $card }}">
        <div style="padding: 18px 22px; border-bottom: 1px solid #e8dfd0;">
            <h3 style="{{ $cardTitle }}">Payment History</h3>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #faf8f4;">
                    @foreach (['Booking', 'Date', 'Gross', 'Commission', 'Payout', 'Status'] as $heading)
                        <th style="padding: 10px 16px; {{ $th }} font-size: 11px; text-transform: uppercase;">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $row)
                    @php($commission = $row['gross'] * 0.1)
                    @php([$payBg, $payColor] = ['Released' => ['#e8f5e9', '#2e7d32'], 'Processing' => ['#fff8e1', '#e65100']][$row['status']] ?? ['#f5f5f5', '#777'])
                    <tr style="border-top: 1px solid #f0ebe2;">
                        <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #1a110a;">{{ $row['id'] }}</td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #6b5a47;">{{ $row['date'] }}</td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #1a110a; font-weight: 600;">₱{{ number_format($row['gross']) }}</td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #e65100; font-weight: 500;">-₱{{ number_format($commission) }}</td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #2e7d32; font-weight: 700;">₱{{ number_format($row['gross'] - $commission) }}</td>
                        <td style="padding: 12px 16px;">
                            <span style="padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: {{ $payBg }}; color: {{ $payColor }};">{{ $row['status'] }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ── AVAILABILITY ── --}}
<div data-panel="availability" hidden style="max-width: 700px;" data-vendor-calendar>
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e8dfd0; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 12px;"><button type="button" data-calendar-prev class="btn btn-sm btn-outline-secondary">‹</button><h3 data-calendar-label style="font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #1a110a; margin: 0;"></h3><button type="button" data-calendar-next class="btn btn-sm btn-outline-secondary">›</button></div>
            <div style="display: flex; gap: 16px; font-size: 12px;">
                @foreach ([['#e6f4ea', '#2e7d32', 'Available'], ['#c9a84c22', '#a07a2a', 'Booked'], ['#fce4ec', '#c62828', 'Blocked']] as [$bg, $color, $legend])
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <div style="width: 12px; height: 12px; border-radius: 3px; background: {{ $bg }};"></div>
                        <span style="color: {{ $color }};">{{ $legend }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; margin-bottom: 12px;">
            @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $weekday)
                <div style="text-align: center; font-size: 12px; font-weight: 600; color: #9c8f7a; padding: 6px 0px;">{{ $weekday }}</div>
            @endforeach
        </div>
        <div data-calendar-grid style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px;"></div>
        <p style="font-size: 12px; color: #9c8f7a; margin: 14px 0px 0px;">Real bookings are read-only. Use the arrows to view any month.</p>
    </div>
</div>

{{-- ── ANALYTICS ── --}}
<div data-panel="analytics" hidden>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
        @foreach ([['Profile Views', '1,248', '👁'], ['Inquiry Rate', '34%', '💬'], ['Booking Rate', '18%', '📅']] as [$statLabel, $statValue, $statIcon])
            <x-stat-card variant="inline" :label="$statLabel" :value="$statValue" :icon="$statIcon" />
        @endforeach
    </div>
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e8dfd0; padding: 24px;">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #1a110a; margin-bottom: 16px;">Top Services by Bookings</h3>
        @foreach ($services as $s)
            <div style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="font-size: 13px; font-weight: 500; color: #1a110a;">{{ $s['name'] }}</span>
                    <span style="font-size: 13px; color: #c9a84c; font-weight: 600;">{{ $s['bookings'] }} bookings</span>
                </div>
                <div style="height: 8px; background: #f5f0e8; border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: {{ $s['bookings'] / 40 * 100 }}%; background: linear-gradient(90deg, #c9a84c, #e8cc7a); border-radius: 4px;"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ── PROFILE ── --}}
<div data-panel="profile" hidden style="max-width: 640px;">
    <div style="background: #fff; border-radius: 12px; border: 1px solid #e8dfd0; padding: 28px;">
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 28px; padding-bottom: 24px; border-bottom: 1px solid #e8dfd0;">
            <div style="width: 72px; height: 72px; border-radius: 50%; background: #3d2510; border: 3px solid #c9a84c; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; color: #c9a84c;">{{ $user->initials() }}</div>
            <div>
                <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #1a110a; margin-bottom: 4px;">{{ $business->business_name }}</h3>
                <div style="font-size: 13px; color: #9c8f7a;">Vendor · 8+ years on Eventlynk · ⭐ 4.9</div>
            </div>
        </div>
        <form method="POST" action="{{ route('vendor.profile.update', $business) }}" enctype="multipart/form-data">@csrf @method('PATCH')
        @foreach ([['Business Name', 'business_name', $business->business_name], ['Short name', 'short_name', $business->short_name], ['Phone', 'contact_number', $business->contact_number ?? $user->phone], ['Location', 'barangay', $business->barangay]] as [$fieldLabel, $fieldName, $fieldValue])
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 13px; font-weight: 500; color: #6b5a47; margin-bottom: 7px;">{{ $fieldLabel }}</label>
                <input name="{{ $fieldName }}" value="{{ $fieldValue }}" style="width: 100%; border: 1px solid #ddd5c8; border-radius: 8px; padding: 12px 14px; font-size: 14px; color: #1a110a; outline: none; box-sizing: border-box; background: #faf8f4;">
            </div>
        @endforeach
        <div style="margin-bottom: 18px;"><label style="display:block;font-size:13px;font-weight:500;color:#6b5a47;margin-bottom:7px;">Description</label><textarea name="description" style="width:100%;min-height:90px;border:1px solid #ddd5c8;border-radius:8px;padding:12px;background:#faf8f4;">{{ $business->description }}</textarea></div>
        <div style="margin-bottom: 18px;"><label style="display:block;font-size:13px;font-weight:500;color:#6b5a47;margin-bottom:7px;">Logo</label><input type="file" name="logo" accept="image/*"></div>
        <button type="submit" style="padding: 12px 32px; border-radius: 8px; background: #1a110a; color: #f5f0e8; border: none; font-size: 14px; font-weight: 600; cursor: pointer;">Save Changes</button>
        </form>
    </div>
</div>

{{-- ── SETTINGS ── --}}
<div data-panel="settings" hidden style="max-width: 640px;">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #1a110a; margin-bottom: 24px;">Settings</h2>
    <div style="{{ $settingsCard }}">
        <h3 style="font-size: 15px; font-weight: 700; color: #1a110a; margin-bottom: 18px;">Notifications</h3>
        @foreach ([
            ['Email Notifications', 'Receive booking updates via email', true],
            ['SMS Notifications', 'Receive text alerts for new bookings', false],
            ['Booking Alerts', 'Alert when a new booking is placed', true],
            ['Auto-Confirm Bookings', 'Automatically confirm incoming bookings', false],
        ] as [$settingLabel, $settingDesc, $on])
            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 16px; margin-bottom: 16px; border-bottom: 1px solid #f0ebe2;">
                <div>
                    <div style="font-size: 14px; font-weight: 600; color: #1a110a;">{{ $settingLabel }}</div>
                    <div style="font-size: 12px; color: #9c8f7a; margin-top: 2px;">{{ $settingDesc }}</div>
                </div>
                <button type="button" role="switch" aria-checked="{{ $on ? 'true' : 'false' }}" aria-label="{{ $settingLabel }}" data-switch
                    style="width: 44px; height: 24px; border-radius: 12px; background: {{ $on ? '#c9a84c' : '#ddd5c8' }}; border: none; cursor: pointer; position: relative; transition: background 0.2s; flex-shrink: 0;">
                    <span style="position: absolute; top: 3px; left: {{ $on ? 22 : 3 }}px; width: 18px; height: 18px; border-radius: 50%; background: #fff; transition: left 0.2s; display: block;"></span>
                </button>
            </div>
        @endforeach
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 14px; font-weight: 600; color: #1a110a;">Minimum Notice Period</div>
                <div style="font-size: 12px; color: #9c8f7a; margin-top: 2px;">Days required before booking date</div>
            </div>
            <select style="padding: 6px 12px; border-radius: 8px; border: 1px solid #ddd5c8; font-size: 14px; color: #1a110a; background: #faf8f4;">
                @foreach (['1', '2', '3', '5', '7', '14'] as $days)
                    <option @selected($days === '3')>{{ $days }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div style="{{ $settingsCard }}">
        <h3 style="font-size: 15px; font-weight: 700; color: #1a110a; margin-bottom: 18px;">Payout Preferences</h3>
        @foreach ([['Bank / E-Wallet', 'BDO Unibank', 'e.g. BDO Unibank'], ['Account Number', '****-****-1234', 'e.g. ****-****-1234']] as [$fieldLabel, $fieldValue, $placeholder])
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #6b5a47; margin-bottom: 6px;">{{ $fieldLabel }}</label>
                <input value="{{ $fieldValue }}" placeholder="{{ $placeholder }}" style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #ddd5c8; font-size: 14px; color: #1a110a; background: #faf8f4; box-sizing: border-box; outline: none;">
            </div>
        @endforeach
    </div>
    <div style="{{ $settingsCard }}">
        <h3 style="font-size: 15px; font-weight: 700; color: #1a110a; margin-bottom: 4px;">Account Security</h3>
        <div style="font-size: 13px; color: #9c8f7a; margin-bottom: 18px;">Manage your password and account access.</div>
        <button type="button" data-toast-message="Password reset link sent to your email." style="padding: 10px 20px; border-radius: 8px; background: #1a110a; color: #f5f0e8; font-size: 13px; font-weight: 600; border: none; cursor: pointer;">Change Password</button>
    </div>
    <button type="button" data-toast-message="Settings saved successfully." style="padding: 13px 32px; border-radius: 10px; background: #c9a84c; color: #fff; font-size: 14px; font-weight: 700; border: none; cursor: pointer;">Save Settings</button>
</div>
@endsection

@section('modals')
@foreach ($services as $s)
    <x-dashboard.modal id="edit-service-{{ $s['id'] }}" data-edit-service="{{ $s['id'] }}" panel-style="background: #fff; border-radius: 16px; padding: 32px; width: 420px; box-shadow: 0 24px 64px rgba(0,0,0,0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px;">
            <h3 style="{{ $modalTitle }}">Edit Service</h3>
            <button type="button" data-modal-close aria-label="Close" style="{{ $closeX }}">✕</button>
        </div>
        <form method="POST" action="{{ route('vendor.services.update', $s['service']) }}" enctype="multipart/form-data">
            @csrf @method('PATCH')
            <div style="margin-bottom: 14px;"><label style="{{ $label }}">Service Name</label><input name="name" value="{{ $s['service']->name }}" style="{{ $input }}"></div>
            <div style="margin-bottom: 14px;"><label style="{{ $label }}">Category</label><select name="service_category_id" style="{{ $input }}">@foreach ($categories as $category)<option value="{{ $category->id }}" @selected($category->id === $s['service']->service_category_id)>{{ $category->name }}</option>@endforeach</select></div>
            <div style="margin-bottom: 14px;"><label style="{{ $label }}">Price</label><input name="price" type="number" min="0" step="0.01" value="{{ $s['service']->price }}" style="{{ $input }}"></div>
            <div style="margin-bottom: 14px;"><label style="{{ $label }}">Price unit</label><select name="price_unit" style="{{ $input }}"><option value="event" @selected($s['service']->price_unit === 'event')>Per event</option><option value="staff" @selected($s['service']->price_unit === 'staff')>Per staff</option></select></div>
            <div style="margin-bottom: 14px;"><label style="{{ $label }}">Description</label><textarea name="description" style="{{ $input }}">{{ $s['service']->description }}</textarea></div>
            <div style="margin-bottom: 20px;"><label style="{{ $label }}">Replace image</label><input name="image" type="file" accept="image/*" style="{{ $input }}"></div>
            <div style="display: flex; gap: 10px;"><button type="button" data-modal-close style="{{ $cancelBtn }}">Cancel</button><button type="submit" style="{{ $saveBtn }}">Save Changes</button></div>
        </form>
    </x-dashboard.modal>
@endforeach

<x-dashboard.modal id="vendor-add-staff" panel-style="background:#fff;border-radius:16px;padding:30px;width:400px;">
    <h3 style="{{ $modalTitle }}">Add staff member</h3>
    <form method="POST" action="{{ route('vendor.staff.store') }}">@csrf
        <div style="margin:14px 0;"><label style="{{ $label }}">Full name</label><input name="full_name" required style="{{ $input }}"></div>
        <div style="margin-bottom:20px;"><label style="{{ $label }}">Role</label><select name="role" style="{{ $input }}">@foreach (\App\Models\Staff::ROLES as $role)<option>{{ $role }}</option>@endforeach</select></div>
        <button type="submit" style="{{ $saveBtn }}">Add staff</button>
    </form>
</x-dashboard.modal>
@foreach ($staff as $member)
<x-dashboard.modal id="edit-staff-{{ $member['id'] }}" panel-style="background:#fff;border-radius:16px;padding:30px;width:400px;">
    <h3 style="{{ $modalTitle }}">Edit staff member</h3>
    <form method="POST" action="{{ route('vendor.staff.update', $member['staff']) }}">@csrf @method('PATCH')
        <div style="margin:14px 0;"><label style="{{ $label }}">Full name</label><input name="full_name" value="{{ $member['name'] }}" required style="{{ $input }}"></div>
        <div style="margin-bottom:20px;"><label style="{{ $label }}">Role</label><select name="role" style="{{ $input }}">@foreach (\App\Models\Staff::ROLES as $role)<option @selected($role === $member['role'])>{{ $role }}</option>@endforeach</select></div>
        <button type="submit" style="{{ $saveBtn }}">Save changes</button>
    </form>
    @if ($member['available'])
        <form method="POST" action="{{ route('vendor.staff.deactivate', $member['staff']) }}" style="margin-top:10px;">@csrf @method('PATCH')<button type="submit" style="width:100%; padding:10px; border:1px solid #c62828; color:#c62828; background:#fff; border-radius:8px;">Deactivate</button></form>
    @endif
</x-dashboard.modal>
@endforeach

<x-dashboard.modal id="vendor-add-service" panel-style="background: #fff; border-radius: 16px; padding: 32px; width: 440px; box-shadow: 0 24px 64px rgba(0,0,0,0.3);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px;"><h3 style="{{ $modalTitle }}">Add Service</h3><button type="button" data-modal-close aria-label="Close" style="{{ $closeX }}">✕</button></div>
    <form method="POST" action="{{ route('vendor.services.store') }}" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom: 14px;"><label style="{{ $label }}">Service Name</label><input name="name" required style="{{ $input }}"></div>
        <div style="margin-bottom: 14px;"><label style="{{ $label }}">Category</label><select name="service_category_id" required style="{{ $input }}">@foreach ($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div>
        <div style="display: flex; gap: 10px;"><div style="flex: 1;"><label style="{{ $label }}">Price</label><input name="price" type="number" min="0" step="0.01" required style="{{ $input }}"></div><div style="flex: 1;"><label style="{{ $label }}">Unit</label><select name="price_unit" style="{{ $input }}"><option value="event">Per event</option><option value="staff">Per staff</option></select></div></div>
        <div style="margin: 14px 0;"><label style="{{ $label }}">Description</label><textarea name="description" style="{{ $input }}"></textarea></div>
        <div style="margin-bottom: 20px;"><label style="{{ $label }}">Image</label><input name="image" type="file" accept="image/*" style="{{ $input }}"></div>
        <button type="submit" style="{{ $saveBtn }}">Add Service</button>
    </form>
</x-dashboard.modal>

<x-dashboard.modal id="vendor-add-inventory" panel-style="background: #fff; border-radius: 16px; padding: 32px; width: 400px; box-shadow: 0 24px 64px rgba(0,0,0,0.3);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px;">
        <h3 style="{{ $modalTitle }}">Add Inventory Item</h3>
        <button type="button" data-modal-close aria-label="Close" style="{{ $closeX }}">✕</button>
    </div>
    @foreach ([['Item Name', 'text', 'e.g. Chafing Dish'], ['Category', 'text', 'Equipment / Linen / Decor'], ['Total Owned', 'number', '1'], ['Condition', 'text', 'Good / Fair / Poor']] as [$fieldLabel, $type, $placeholder])
        <div style="margin-bottom: 14px;">
            <label style="{{ $label }}">{{ $fieldLabel }}</label>
            <input type="{{ $type }}" placeholder="{{ $placeholder }}" style="{{ $input }}">
        </div>
    @endforeach
    <div style="display: flex; gap: 10px; margin-top: 6px;">
        <button type="button" data-modal-close style="{{ $cancelBtn }}">Cancel</button>
        <button type="button" data-modal-close data-toast-message="Item added to inventory" style="{{ $saveBtn }}">Add Item</button>
    </div>
</x-dashboard.modal>

@foreach ($bookings as $b)
    <x-dashboard.modal id="vendor-booking-{{ $b['id'] }}" panel-style="background: #fff; border-radius: 16px; padding: 32px; width: 440px; box-shadow: 0 24px 64px rgba(0,0,0,0.3); border: 1px solid #e8dfd0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="{{ $modalTitle }}">Booking Details</h3>
            <button type="button" data-modal-close aria-label="Close" style="{{ $closeX }}">✕</button>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 18px;">
            <span style="font-size: 13px; color: #9c8f7a; font-weight: 500;">{{ $b['id'] }}</span>
            <x-status-badge :status="$b['status']" padding="4px 12px" :weight="700" />
        </div>
        @foreach ([['Customer', $b['customer']], ['Service', $b['service']], ['Date', $b['date']], ['Guests', $b['guests'].' pax'], ['Amount', $b['amount']]] as [$rowLabel, $rowValue])
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0ebe2;">
                <span style="font-size: 13px; color: #6b5a47;">{{ $rowLabel }}</span>
                <span style="font-size: 13px; color: #1a110a; font-weight: 600;">{{ $rowValue }}</span>
            </div>
        @endforeach
        <div style="margin-top: 14px; display: flex; gap: 8px;">
            <a href="{{ route('bookings.slip', $b['booking']) }}" style="flex: 1; padding: 8px; border-radius: 7px; background: #faf8f4; border: 1px solid #e8dfd0; color: #3d2510; text-align: center; font-size: 12px;">Slip PDF</a>
            <a href="{{ route('bookings.invoice', $b['booking']) }}" style="flex: 1; padding: 8px; border-radius: 7px; background: #faf8f4; border: 1px solid #e8dfd0; color: #3d2510; text-align: center; font-size: 12px;">Invoice PDF</a>
        </div>
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <button type="button" data-modal-close style="{{ $cancelBtn }}">Close</button>
            @if ($b['status'] === 'Pending')
                <form method="POST" action="{{ route('vendor.bookings.status', $b['booking']) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="Confirmed"><button type="submit" style="flex: 2; padding: 11px; border-radius: 8px; border: none; background: #c9a84c; color: #1a110a; font-size: 13px; font-weight: 700; cursor: pointer;">✓ Confirm Booking</button></form>
                <form method="POST" action="{{ route('vendor.bookings.status', $b['booking']) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="Cancelled"><button type="submit" style="flex: 1; padding: 11px; border-radius: 8px; border: 1px solid #c62828; background: transparent; color: #c62828;">Cancel</button></form>
            @elseif ($b['status'] === 'Confirmed')
                <form method="POST" action="{{ route('vendor.bookings.status', $b['booking']) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="Completed"><button type="submit" style="flex: 2; padding: 11px; border-radius: 8px; border: none; background: #2e7d32; color: #fff; font-weight: 700;">✓ Mark Completed</button></form>
                <form method="POST" action="{{ route('vendor.bookings.status', $b['booking']) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="Cancelled"><button type="submit" style="flex: 1; padding: 11px; border-radius: 8px; border: 1px solid #c62828; background: transparent; color: #c62828;">Cancel</button></form>
            @endif
        </div>
    </x-dashboard.modal>
@endforeach
@endsection

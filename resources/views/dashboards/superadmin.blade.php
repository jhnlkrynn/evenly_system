@extends('layouts.dashboard')

@section('title', 'Super Admin · Eventlynk')
@section('dashboard', 'superadmin')
@section('shell_style', 'flex: 1; display: flex; flex-direction: column; min-width: 0;')
@section('main_style', 'flex: 1; padding: 28px; overflow-y: auto;')

@php
    $user = auth()->user();
    $card = 'background: #fff; border-radius: 12px; border: 1px solid #e8dfd0;';
    $cardTitle = "font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #1a110a;";
    $th = 'text-align: left; font-size: 11px; font-weight: 600; color: #9c8f7a; text-transform: uppercase;';
    $label = 'display: block; font-size: 12px; font-weight: 600; color: #6b5a47; margin-bottom: 6px;';
    $input = 'width: 100%; padding: 10px 12px; border: 1px solid #ddd5c8; border-radius: 8px; font-size: 13px; color: #1a110a; outline: none; background: #faf8f4; box-sizing: border-box;';
    $cancelBtn = 'flex: 1; padding: 11px; border-radius: 8px; border: 1px solid #e8dfd0; background: #faf8f4; color: #3d2510; font-size: 13px; font-weight: 500; cursor: pointer;';
    $saveBtn = 'flex: 2; padding: 11px; border-radius: 8px; border: none; background: #1a110a; color: #f5f0e8; font-size: 13px; font-weight: 600; cursor: pointer;';
    $modalTitle = "font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #1a110a; margin: 0;";
    $closeX = 'background: none; border: none; font-size: 20px; cursor: pointer; color: #6b5a47;';
    $modalPanel = 'background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 24px 64px rgba(0,0,0,0.3);';
    $categoryColors = ['#c9a84c', '#3d2510', '#1a110a', '#e8cc7a', '#6b5a47', '#9c8f7a'];
@endphp

@section('sidebar')
<x-dashboard.sidebar variant="super" :items="$navItems" logout-label="Sign Out">
    <x-slot:brand>
        <img src="{{ asset('images/eventlynk-logo.png') }}" alt="Eventlynk" style="width: 38px; height: 38px; object-fit: contain;">
        <div>
            <span style="font-family: 'Playfair Display', serif; color: #c9a84c; font-size: 15px; font-weight: 700; display: block; white-space: nowrap;">Eventlynk</span>
            <span style="font-size: 10px; color: #c9a84c88; white-space: nowrap;">Super Admin</span>
        </div>
    </x-slot:brand>
    <x-slot:profile>
        @if (count($pending) > 0)
            <div data-sidebar-open-only style="margin: 12px 14px; padding: 10px 12px; background: #c9a84c18; border: 1px solid #c9a84c44; border-radius: 8px;">
                <div style="font-size: 11px; color: #c9a84c; font-weight: 700;">{{ count($pending) }} PENDING APPROVAL</div>
                <div style="font-size: 11px; color: #e8dfd0aa; margin-top: 2px;">Vendors awaiting review</div>
            </div>
        @endif
    </x-slot:profile>
</x-dashboard.sidebar>
@endsection

@section('header')
<header class="app-dashboard-header" style="background: #fff; border-bottom: 1px solid #e8dfd0; padding: 0 28px; height: 64px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 50;">
    <div>
        <h1 data-dashboard-title style="font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #1a110a; margin: 0;">Overview</h1>
        <p style="font-size: 12px; color: #9c8f7a; margin: 0;">Link Tech · Platform Administration</p>
    </div>
    <div style="display: flex; align-items: center; gap: 16px;">
        @if ($openDisputes > 0)
            <button type="button" data-tab-link="disputes" style="padding: 6px 14px; border-radius: 6px; background: #fce4ec; border: none; color: #c62828; font-size: 12px; font-weight: 600; cursor: pointer;">⚠ {{ $openDisputes }} Open {{ $openDisputes === 1 ? 'Dispute' : 'Disputes' }}</button>
        @endif
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #1a110a; display: flex; align-items: center; justify-content: center; color: #c9a84c; font-weight: 700; font-size: 14px;">{{ $user->initials() }}</div>
    </div>
</header>
@endsection

@section('panels')
{{-- ── OVERVIEW ── --}}
<div data-panel="overview">
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
        @foreach ($overviewStats as $stat)
            <x-stat-card variant="header" :label="$stat['label']" :value="$stat['value']" :icon="$stat['icon']" :color="$stat['color']" :value-color="$stat['color']" :sub="$stat['sub']" letter-spacing="0.5px" />
        @endforeach
    </div>

    @if (count($pending) > 0)
        <div style="background: #fff8e1; border: 1px solid #f9a825; border-radius: 12px; padding: 16px 22px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-weight: 700; color: #e65100; font-size: 14px;">⏳ {{ count($pending) }} Vendor{{ count($pending) > 1 ? 's' : '' }} Awaiting Approval</div>
                <div style="font-size: 13px; color: #6d4c41; margin-top: 3px;">{{ collect($pending)->pluck('name')->join(', ') }}</div>
            </div>
            <button type="button" data-tab-link="vendors" style="padding: 8px 18px; border-radius: 8px; background: #e65100; border: none; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;">Review Now</button>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;">
        <div style="{{ $card }} padding: 22px 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="{{ $cardTitle }} margin: 0;">Monthly Revenue &amp; Commission</h3>
                <span style="font-size: 12px; color: #9c8f7a;">Mar – Aug 2026</span>
            </div>
            <div style="display: flex; align-items: flex-end; gap: 8px; height: 140px;">
                @foreach ($monthly as $m)
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; height: 100%; justify-content: flex-end;">
                        <div style="font-size: 11px; color: #9c8f7a;">₱{{ round($m['revenue'] / 1000) }}K</div>
                        <div style="width: 100%; display: flex; gap: 2px; align-items: flex-end;">
                            <div style="flex: 1; background: #c9a84c; border-radius: 3px 3px 0 0; height: {{ round($m['revenue'] / $maxMonthly * 110, 3) }}px;"></div>
                            <div style="flex: 1; background: #1a110a; border-radius: 3px 3px 0 0; height: {{ round($m['commission'] / $maxMonthly * 110, 3) }}px;"></div>
                        </div>
                        <div style="font-size: 11px; color: #6b5a47;">{{ $m['month'] }}</div>
                    </div>
                @endforeach
            </div>
            <div style="display: flex; gap: 18px; margin-top: 12px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 12px; height: 12px; background: #c9a84c; border-radius: 2px;"></div>
                    <span style="font-size: 12px; color: #6b5a47;">Gross Revenue</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 12px; height: 12px; background: #1a110a; border-radius: 2px;"></div>
                    <span style="font-size: 12px; color: #6b5a47;">Commission</span>
                </div>
            </div>
        </div>

        <div style="{{ $card }} padding: 22px 24px;">
            <h3 style="{{ $cardTitle }} margin-bottom: 16px; margin-top: 0px;">Top Categories</h3>
            @foreach ($topCategories as $i => $cat)
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <span style="font-size: 18px;">{{ $cat['icon'] }}</span>
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span style="font-size: 12px; font-weight: 600; color: #1a110a;">{{ $cat['name'] }}</span>
                            <span style="font-size: 11px; color: #9c8f7a;">{{ $cat['bookings'] }} bookings</span>
                        </div>
                        <div style="height: 4px; background: #f0ebe2; border-radius: 2px;">
                            <div style="height: 4px; background: {{ $i === 0 ? '#c9a84c' : '#3d2510' }}; border-radius: 2px; width: {{ round($cat['bookings'] / $categories[0]['bookings'] * 100, 4) }}%;"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div style="{{ $card }} padding: 22px 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="{{ $cardTitle }} margin: 0;">Recently Joined Vendors</h3>
            <button type="button" data-tab-link="vendors" style="background: none; border: none; color: #c9a84c; font-size: 13px; font-weight: 500; cursor: pointer;">View all →</button>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #faf8f4;">
                    @foreach (['Vendor', 'Category', 'Location', 'Joined', 'Status', 'Action'] as $heading)
                        <th style="padding: 10px 14px; {{ $th }}">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach (array_slice($vendors, 0, 4) as $v)
                    <tr style="border-top: 1px solid #f0ebe2;">
                        <td style="padding: 12px 14px;">
                            <div style="font-weight: 600; font-size: 13px; color: #1a110a;">{{ $v['name'] }}</div>
                            <div style="font-size: 11px; color: #9c8f7a;">{{ $v['owner'] }}</div>
                        </td>
                        <td style="padding: 12px 14px; font-size: 13px; color: #3d2510;">{{ $v['category'] }}</td>
                        <td style="padding: 12px 14px; font-size: 13px; color: #3d2510;">{{ $v['location'] }}</td>
                        <td style="padding: 12px 14px; font-size: 12px; color: #9c8f7a;">{{ $v['joinedDate'] }}</td>
                        <td style="padding: 12px 14px;"><x-status-badge palette="platform" :status="$v['status']" padding="3px 10px" :size="11" data-vendor-status="{{ $v['id'] }}" /></td>
                        <td style="padding: 12px 14px;">
                            <button type="button" data-tab-link="vendors" data-modal-open="vendor-{{ $v['id'] }}" style="padding: 5px 12px; border-radius: 6px; border: 1px solid #c9a84c; background: transparent; color: #c9a84c; font-size: 12px; font-weight: 600; cursor: pointer;">View</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ── VENDORS ── --}}
<div data-panel="vendors" hidden>
    <div data-filter-group="vendors" style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
        @foreach (['All', 'Approved', 'Pending', 'Rejected', 'Suspended'] as $filter)
            <button type="button" class="dash-pill {{ $filter === 'All' ? 'active' : '' }}" data-filter-value="{{ $filter }}">{{ $filter }}
                @if ($filter === 'Pending' && count($pending) > 0)
                    <span style="background: #e65100; color: #fff; border-radius: 8px; padding: 0 5px; font-size: 11px; margin-left: 4px;">{{ count($pending) }}</span>
                @endif
            </button>
        @endforeach
    </div>

    <div style="{{ $card }} overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #faf8f4;">
                    @foreach (['Vendor', 'Category', 'Bookings', 'Revenue', 'Commission', 'Rating', 'Status', 'Action'] as $heading)
                        <th style="padding: 12px 14px; {{ $th }}">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($vendors as $v)
                    <tr data-filter-item="vendors" data-filter-key="{{ $v['status'] }}" data-vendor-row="{{ $v['id'] }}" data-modal-open="vendor-{{ $v['id'] }}" style="border-top: 1px solid #f0ebe2; cursor: pointer;">
                        <td style="padding: 13px 14px;">
                            <div style="font-weight: 600; font-size: 13px; color: #1a110a;">{{ $v['name'] }}</div>
                            <div style="font-size: 11px; color: #9c8f7a;">{{ $v['owner'] }} · {{ $v['email'] }}</div>
                        </td>
                        <td style="padding: 13px 14px; font-size: 13px; color: #3d2510;">{{ $v['category'] }}</td>
                        <td style="padding: 13px 14px; font-size: 13px; color: #3d2510;">{{ $v['totalBookings'] }}</td>
                        <td style="padding: 13px 14px; font-size: 13px; color: #1a110a; font-weight: 600;">₱{{ number_format($v['revenue']) }}</td>
                        <td style="padding: 13px 14px; font-size: 13px; color: #2e7d32; font-weight: 600;">₱{{ number_format($v['commission']) }}</td>
                        <td style="padding: 13px 14px; font-size: 13px; color: #c9a84c;">{{ $v['rating'] > 0 ? '★ '.$v['rating'] : '—' }}</td>
                        <td style="padding: 13px 14px;"><x-status-badge palette="platform" :status="$v['status']" padding="3px 10px" :size="11" data-vendor-status="{{ $v['id'] }}" /></td>
                        <td style="padding: 13px 14px;" data-stop-propagation>
                            <div data-vendor-actions="{{ $v['id'] }}">
                                <div data-when-vendor="Pending" @if ($v['status'] !== 'Pending') hidden @endif style="display: flex; gap: 6px;">
                                    <button type="button" data-vendor-set="Approved" style="padding: 5px 10px; border-radius: 6px; border: none; background: #2e7d32; color: #fff; font-size: 11px; font-weight: 600; cursor: pointer;">✓ Approve</button>
                                    <button type="button" data-vendor-set="Rejected" style="padding: 5px 10px; border-radius: 6px; border: none; background: #c62828; color: #fff; font-size: 11px; font-weight: 600; cursor: pointer;">✕ Reject</button>
                                </div>
                                <button type="button" data-when-vendor="Approved" data-vendor-set="Suspended" @if ($v['status'] !== 'Approved') hidden @endif style="padding: 5px 12px; border-radius: 6px; border: 1px solid #e8dfd0; background: transparent; color: #6b5a47; font-size: 11px; cursor: pointer;">Suspend</button>
                                <button type="button" data-when-vendor="Suspended" data-vendor-set="Approved" @if ($v['status'] !== 'Suspended') hidden @endif style="padding: 5px 12px; border-radius: 6px; border: 1px solid #c9a84c; background: transparent; color: #c9a84c; font-size: 11px; cursor: pointer;">Reinstate</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ── CATEGORIES ── --}}
<div data-panel="categories" hidden>
    <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
        <button type="button" data-modal-open="add-category" style="padding: 9px 20px; border-radius: 8px; border: none; background: #1a110a; color: #c9a84c; font-size: 13px; font-weight: 600; cursor: pointer;">+ Add Category</button>
    </div>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
        @foreach ($categories as $cat)
            <div data-category="{{ $cat['id'] }}" style="{{ $card }} padding: 22px 24px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div data-category-icon style="width: 44px; height: 44px; border-radius: 10px; background: #faf8f4; border: 1px solid #e8dfd0; display: flex; align-items: center; justify-content: center; font-size: 22px;">{{ $cat['icon'] }}</div>
                        <div>
                            <div data-category-name style="font-family: 'Playfair Display', serif; font-weight: 700; font-size: 15px; color: #1a110a;">{{ $cat['name'] }}</div>
                            <x-status-badge palette="platform" :status="$cat['status']" padding="2px 8px" :size="11" :radius="10" data-category-status />
                        </div>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 16px;">
                    @foreach ([['Vendors', $cat['vendors']], ['Bookings', $cat['bookings']], ['Commission', $cat['commission'].'%']] as [$statLabel, $statValue])
                        <div style="text-align: center; padding: 10px 6px; background: #faf8f4; border-radius: 8px; border: 1px solid #e8dfd0;">
                            <div @if ($statLabel === 'Commission') data-category-rate @endif style="font-size: 16px; font-weight: 700; color: #1a110a;">{{ $statValue }}</div>
                            <div style="font-size: 10px; color: #9c8f7a; margin-top: 2px;">{{ $statLabel }}</div>
                        </div>
                    @endforeach
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="button" data-toggle-category style="flex: 1; padding: 8px; border-radius: 6px; border: 1px solid #e8dfd0; background: transparent; color: #6b5a47; font-size: 12px; font-weight: 500; cursor: pointer;">Deactivate</button>
                    <button type="button" data-modal-open="edit-category-{{ $cat['id'] }}" style="flex: 1; padding: 8px; border-radius: 6px; border: 1px solid #c9a84c; background: transparent; color: #c9a84c; font-size: 12px; font-weight: 500; cursor: pointer;">Edit</button>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ── COMMISSION ── --}}
<div data-panel="commission" hidden>
    <div style="{{ $card }} padding: 22px 24px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
            @foreach ([
                ['Total Commission Earned', '₱'.number_format($totalCommission), '₱', '#c9a84c'],
                ['This Month (Aug)', '₱45,500', '📅', '#4a6fdc'],
                ['Active Vendor Agreements', count($approved), '🤝', '#2e7d32'],
            ] as [$statLabel, $statValue, $statIcon, $statColor])
                <x-stat-card variant="center" :label="$statLabel" :value="$statValue" :icon="$statIcon" :value-color="$statColor" />
            @endforeach
        </div>
    </div>

    <div style="{{ $card }} overflow: hidden;">
        <div style="padding: 18px 22px; border-bottom: 1px solid #e8dfd0;">
            <h3 style="{{ $cardTitle }} margin: 0;">Commission Rates by Category</h3>
            <p style="font-size: 12px; color: #9c8f7a; margin: 4px 0 0;">Click the rate to edit</p>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #faf8f4;">
                    @foreach (['Category', 'Current Rate', 'Vendors Affected', 'Total Collected', 'Action'] as $heading)
                        <th style="padding: 11px 18px; {{ $th }}">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($activeCategories as $cat)
                    <tr data-commission-row="{{ $cat['id'] }}" style="border-top: 1px solid #f0ebe2;">
                        <td style="padding: 14px 18px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 18px;">{{ $cat['icon'] }}</span>
                                <span style="font-size: 14px; font-weight: 600; color: #1a110a;">{{ $cat['name'] }}</span>
                            </div>
                        </td>
                        <td style="padding: 14px 18px;">
                            <div data-commission-editor hidden style="display: flex; align-items: center; gap: 8px;">
                                <input type="number" min="0" max="100" step="0.5" value="{{ $cat['commission'] }}" data-commission-input aria-label="Commission rate for {{ $cat['name'] }}" style="width: 70px; padding: 5px 8px; border: 1px solid #c9a84c; border-radius: 6px; font-size: 13px; outline: none;">
                                <span style="font-size: 13px;">%</span>
                                <button type="button" data-commission-save style="padding: 4px 10px; border-radius: 5px; border: none; background: #1a110a; color: #c9a84c; font-size: 12px; cursor: pointer;">Save</button>
                                <button type="button" data-commission-cancel aria-label="Cancel" style="padding: 4px 8px; border-radius: 5px; border: 1px solid #e8dfd0; background: transparent; color: #6b5a47; font-size: 12px; cursor: pointer;">✕</button>
                            </div>
                            <button type="button" data-commission-edit style="padding: 5px 14px; border-radius: 20px; border: 1px solid #c9a84c33; background: #c9a84c12; color: #c9a84c; font-size: 14px; font-weight: 700; cursor: pointer;"><span data-category-rate-value>{{ $cat['commission'] }}</span>%</button>
                        </td>
                        <td style="padding: 14px 18px; font-size: 13px; color: #3d2510;">{{ $cat['vendors'] }} vendors</td>
                        <td style="padding: 14px 18px; font-size: 13px; font-weight: 600; color: #2e7d32;">₱{{ number_format($cat['bookings'] * 2000 * ($cat['commission'] / 100)) }}</td>
                        <td style="padding: 14px 18px;">
                            <button type="button" style="padding: 5px 12px; border-radius: 6px; border: 1px solid #e8dfd0; background: transparent; color: #6b5a47; font-size: 12px; cursor: pointer;">View Log</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="{{ $card }} margin-top: 20px; overflow: hidden;">
        <div style="padding: 18px 22px; border-bottom: 1px solid #e8dfd0;">
            <h3 style="{{ $cardTitle }} margin: 0;">Vendor Earnings &amp; Commission Breakdown</h3>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #faf8f4;">
                    @foreach (['Vendor', 'Category', 'Gross Revenue', 'Commission (10%)', 'Vendor Payout', 'Bookings'] as $heading)
                        <th style="padding: 11px 16px; {{ $th }}">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($approved as $v)
                    <tr style="border-top: 1px solid #f0ebe2;">
                        <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #1a110a;">{{ $v['name'] }}</td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #3d2510;">{{ $v['category'] }}</td>
                        <td style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #1a110a;">₱{{ number_format($v['revenue']) }}</td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #c9a84c; font-weight: 600;">₱{{ number_format($v['commission']) }}</td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #2e7d32; font-weight: 600;">₱{{ number_format($v['revenue'] - $v['commission']) }}</td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #3d2510;">{{ $v['totalBookings'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ── ANALYTICS ── --}}
<div data-panel="analytics" hidden>
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
        @foreach ($analyticsStats as $stat)
            <x-stat-card variant="header" :label="$stat['label']" :value="$stat['value']" :icon="$stat['icon']" :icon-box="false" :sub="'↑ '.$stat['change'].' vs last period'" sub-color="#2e7d32" />
        @endforeach
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <div style="{{ $card }} padding: 22px 24px;">
            <h3 style="{{ $cardTitle }} margin-bottom: 18px; margin-top: 0px;">Monthly Bookings Trend</h3>
            <div style="display: flex; align-items: flex-end; gap: 10px; height: 120px;">
                @foreach ($monthly as $m)
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; height: 100%; justify-content: flex-end;">
                        <div style="font-size: 11px; color: #9c8f7a;">{{ $m['bookings'] }}</div>
                        <div style="width: 100%; background: #c9a84c; border-radius: 3px 3px 0 0; height: {{ round($m['bookings'] / 91 * 100, 3) }}px;"></div>
                        <div style="font-size: 11px; color: #6b5a47;">{{ $m['month'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="{{ $card }} padding: 22px 24px;">
            <h3 style="{{ $cardTitle }} margin-bottom: 16px; margin-top: 0px;">Category Distribution</h3>
            @foreach ($activeCategories as $i => $cat)
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <span style="font-size: 16px;">{{ $cat['icon'] }}</span>
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span style="font-size: 12px; font-weight: 500; color: #1a110a;">{{ $cat['name'] }}</span>
                            <span style="font-size: 11px; color: #9c8f7a;">{{ $categoryShare[$i] }}%</span>
                        </div>
                        <div style="height: 6px; background: #f0ebe2; border-radius: 3px;">
                            <div style="height: 6px; background: {{ $categoryColors[$i % count($categoryColors)] }}; border-radius: 3px; width: {{ $categoryShare[$i] }}%;"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div style="{{ $card }} padding: 22px 24px;">
        <h3 style="{{ $cardTitle }} margin-bottom: 16px; margin-top: 0px;">Top Performing Vendors</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #faf8f4;">
                    @foreach (['Rank', 'Vendor', 'Category', 'Bookings', 'Revenue', 'Rating'] as $heading)
                        <th style="padding: 10px 14px; {{ $th }}">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($topVendors as $i => $v)
                    <tr style="border-top: 1px solid #f0ebe2;">
                        <td style="padding: 12px 14px;">
                            <div style="width: 28px; height: 28px; border-radius: 50%; background: {{ $i === 0 ? '#c9a84c' : ($i === 1 ? '#e8dfd0' : '#f5f0e8') }}; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: {{ $i === 0 ? '#1a110a' : '#6b5a47' }};">{{ $i + 1 }}</div>
                        </td>
                        <td style="padding: 12px 14px; font-size: 13px; font-weight: 600; color: #1a110a;">{{ $v['name'] }}</td>
                        <td style="padding: 12px 14px; font-size: 13px; color: #3d2510;">{{ $v['category'] }}</td>
                        <td style="padding: 12px 14px; font-size: 13px; color: #3d2510;">{{ $v['totalBookings'] }}</td>
                        <td style="padding: 12px 14px; font-size: 13px; font-weight: 600; color: #2e7d32;">₱{{ number_format($v['revenue']) }}</td>
                        <td style="padding: 12px 14px; font-size: 14px; color: #c9a84c;">{{ $v['rating'] > 0 ? '★ '.$v['rating'] : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ── DISPUTES ── --}}
<div data-panel="disputes" hidden>
    <div data-filter-group="disputes" style="display: flex; gap: 10px; margin-bottom: 20px;">
        @foreach (['All', 'Open', 'In Review', 'Resolved', 'Closed'] as $filter)
            <button type="button" class="dash-pill {{ $filter === 'All' ? 'active' : '' }}" data-filter-value="{{ $filter }}">{{ $filter }}</button>
        @endforeach
    </div>

    <div style="display: flex; flex-direction: column; gap: 14px;">
        @foreach ($disputes as $d)
            <div class="dash-hover-shadow" data-filter-item="disputes" data-filter-key="{{ $d['status'] }}" data-dispute="{{ $d['id'] }}" data-modal-open="dispute-{{ $d['id'] }}" style="{{ $card }} padding: 20px 24px; cursor: pointer; transition: box-shadow 0.15s;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                            <span style="font-size: 12px; font-weight: 700; color: #9c8f7a;">{{ $d['id'] }}</span>
                            <x-status-badge palette="platform" :status="$d['priority']" padding="2px 8px" :size="11" :radius="10" />
                            <x-status-badge palette="platform" :status="$d['status']" padding="2px 8px" :size="11" :radius="10" data-dispute-status="{{ $d['id'] }}" />
                            <span style="padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; background: {{ $d['type'] === 'Customer' ? '#e3f2fd' : '#f3e5f5' }}; color: {{ $d['type'] === 'Customer' ? '#1565c0' : '#6a1b9a' }};">{{ $d['type'] }} Complaint</span>
                        </div>
                        <div style="font-size: 15px; font-weight: 700; color: #1a110a; margin-bottom: 4px;">{{ $d['subject'] }}</div>
                        <div style="font-size: 13px; color: #6b5a47;">
                            <span style="color: #9c8f7a;">Booking:</span> {{ $d['bookingId'] }} · <span style="color: #9c8f7a;">By:</span> {{ $d['raisedBy'] }} · <span style="color: #9c8f7a;">Against:</span> {{ $d['against'] }}
                        </div>
                    </div>
                    <div style="font-size: 12px; color: #9c8f7a; flex-shrink: 0; margin-left: 16px;">{{ $d['date'] }}</div>
                </div>
                <div data-dispute-actions data-stop-propagation @if (! in_array($d['status'], ['Open', 'In Review'], true)) hidden @endif style="display: flex; gap: 8px; margin-top: 14px;">
                    <button type="button" data-dispute-set="In Review" @if ($d['status'] !== 'Open') hidden @endif style="padding: 7px 16px; border-radius: 7px; border: none; background: #1a110a; color: #c9a84c; font-size: 12px; font-weight: 600; cursor: pointer;">Begin Review</button>
                    <button type="button" data-dispute-set="Resolved" style="padding: 7px 16px; border-radius: 7px; border: 1px solid #2e7d32; background: transparent; color: #2e7d32; font-size: 12px; font-weight: 600; cursor: pointer;">Mark Resolved</button>
                    <button type="button" data-dispute-set="Closed" style="padding: 7px 16px; border-radius: 7px; border: 1px solid #e8dfd0; background: transparent; color: #6b5a47; font-size: 12px; font-weight: 600; cursor: pointer;">Close</button>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ── SETTINGS ── --}}
<div data-panel="settings" hidden style="max-width: 640px;">
    <div style="{{ $card }} padding: 28px 32px; margin-bottom: 20px;">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 17px; font-weight: 700; color: #1a110a; margin-bottom: 20px; margin-top: 0px;">Platform Settings</h3>
        @foreach ([['Platform Name', 'Eventlynk'], ['Support Email', 'support@eventlynk.com'], ['Operating Locations', 'Nasugbu, Lian, Batangas'], ['Default Currency', 'PHP (₱)'], ['Reservation Fee Rate', '30% of total']] as [$settingLabel, $settingValue])
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 0; border-bottom: 1px solid #f0ebe2;">
                <span style="font-size: 13px; color: #6b5a47; font-weight: 500;">{{ $settingLabel }}</span>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 13px; color: #1a110a; font-weight: 600;">{{ $settingValue }}</span>
                    <button type="button" style="padding: 4px 10px; border-radius: 5px; border: 1px solid #c9a84c44; background: transparent; color: #c9a84c; font-size: 11px; cursor: pointer;">Edit</button>
                </div>
            </div>
        @endforeach
        <button type="button" data-toast-message="Settings saved" style="margin-top: 22px; padding: 11px 28px; border-radius: 8px; border: none; background: #1a110a; color: #f5f0e8; font-size: 13px; font-weight: 600; cursor: pointer;">Save Changes</button>
    </div>
</div>
@endsection

@section('modals')
{{-- Vendor profile --}}
@foreach ($vendors as $v)
    <x-dashboard.modal id="vendor-{{ $v['id'] }}" data-vendor-modal="{{ $v['id'] }}" panel-style="{{ $modalPanel }} width: 480px; max-height: 85vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="{{ $modalTitle }}">Vendor Profile</h3>
            <button type="button" data-modal-close aria-label="Close" style="{{ $closeX }}">✕</button>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <div style="font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #1a110a;">{{ $v['name'] }}</div>
                <div style="font-size: 13px; color: #6b5a47; margin-top: 2px;">{{ $v['id'] }} · {{ $v['category'] }}</div>
            </div>
            <x-status-badge palette="platform" :status="$v['status']" padding="5px 14px" :weight="700" data-vendor-status="{{ $v['id'] }}" />
        </div>
        @foreach ([
            ['Owner', $v['owner']],
            ['Email', $v['email']],
            ['Phone', $v['phone']],
            ['Location', $v['location']],
            ['Joined', $v['joinedDate']],
            ['Total Bookings', (string) $v['totalBookings']],
            ['Gross Revenue', '₱'.number_format($v['revenue'])],
            ['Commission Paid', '₱'.number_format($v['commission'])],
            ['Rating', $v['rating'] > 0 ? '★ '.$v['rating'].' / 5.0' : 'No reviews yet'],
        ] as [$rowLabel, $rowValue])
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0ebe2;">
                <span style="font-size: 13px; color: #6b5a47;">{{ $rowLabel }}</span>
                <span style="font-size: 13px; color: #1a110a; font-weight: 600;">{{ $rowValue }}</span>
            </div>
        @endforeach
        <div style="display: flex; gap: 10px; margin-top: 20px;" data-vendor-actions="{{ $v['id'] }}">
            <button type="button" data-when-vendor="Pending" data-vendor-set="Approved" data-modal-close @if ($v['status'] !== 'Pending') hidden @endif style="flex: 1; padding: 11px; border-radius: 8px; border: none; background: #2e7d32; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;">✓ Approve Vendor</button>
            <button type="button" data-when-vendor="Pending" data-vendor-set="Rejected" data-modal-close @if ($v['status'] !== 'Pending') hidden @endif style="flex: 1; padding: 11px; border-radius: 8px; border: none; background: #c62828; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;">✕ Reject Vendor</button>
            <button type="button" data-when-vendor="Approved" data-vendor-set="Suspended" data-modal-close @if ($v['status'] !== 'Approved') hidden @endif style="flex: 1; padding: 11px; border-radius: 8px; border: 1px solid #c62828; background: transparent; color: #c62828; font-size: 13px; font-weight: 600; cursor: pointer;">Suspend Vendor</button>
            <button type="button" data-when-vendor="Suspended" data-vendor-set="Approved" data-modal-close @if ($v['status'] !== 'Suspended') hidden @endif style="flex: 1; padding: 11px; border-radius: 8px; border: none; background: #2e7d32; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;">Reinstate Vendor</button>
            <button type="button" data-modal-close style="{{ $cancelBtn }}">Close</button>
        </div>
    </x-dashboard.modal>
@endforeach

{{-- Edit category --}}
@foreach ($categories as $cat)
    <x-dashboard.modal id="edit-category-{{ $cat['id'] }}" data-edit-category="{{ $cat['id'] }}" panel-style="{{ $modalPanel }} width: 420px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px;">
            <h3 style="{{ $modalTitle }}">Edit Category</h3>
            <button type="button" data-modal-close aria-label="Close" style="{{ $closeX }}">✕</button>
        </div>
        <div style="margin-bottom: 14px;">
            <label style="{{ $label }}">Category Name</label>
            <input value="{{ $cat['name'] }}" data-edit-name style="{{ $input }}">
        </div>
        <div style="margin-bottom: 14px;">
            <label style="{{ $label }}">Icon Emoji</label>
            <input value="{{ $cat['icon'] }}" data-edit-icon style="{{ $input }}">
        </div>
        <div style="margin-bottom: 20px;">
            <label style="{{ $label }}">Commission Rate (%)</label>
            <input type="number" min="0" max="100" value="{{ $cat['commission'] }}" data-edit-rate style="{{ $input }}">
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="button" data-modal-close style="{{ $cancelBtn }}">Cancel</button>
            <button type="button" data-save-category style="{{ $saveBtn }}">Save Changes</button>
        </div>
    </x-dashboard.modal>
@endforeach

{{-- Add category --}}
<x-dashboard.modal id="add-category" panel-style="{{ $modalPanel }} width: 420px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px;">
        <h3 style="{{ $modalTitle }}">Add New Category</h3>
        <button type="button" data-modal-close aria-label="Close" style="{{ $closeX }}">✕</button>
    </div>
    @foreach ([['Category Name', 'text', 'e.g. Flowers & Bouquets'], ['Icon Emoji', 'text', 'e.g. 💐'], ['Commission Rate (%)', 'number', '10']] as [$fieldLabel, $type, $placeholder])
        <div style="margin-bottom: 16px;">
            <label style="{{ $label }}">{{ $fieldLabel }}</label>
            <input type="{{ $type }}" placeholder="{{ $placeholder }}" style="{{ $input }}">
        </div>
    @endforeach
    <div style="display: flex; gap: 10px; margin-top: 8px;">
        <button type="button" data-modal-close style="{{ $cancelBtn }}">Cancel</button>
        <button type="button" data-modal-close data-toast-message="Category added successfully" style="{{ $saveBtn }}">Add Category</button>
    </div>
</x-dashboard.modal>

{{-- Dispute detail --}}
@foreach ($disputes as $d)
    <x-dashboard.modal id="dispute-{{ $d['id'] }}" panel-style="{{ $modalPanel }} width: 480px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="{{ $modalTitle }}">Dispute Detail</h3>
            <button type="button" data-modal-close aria-label="Close" style="{{ $closeX }}">✕</button>
        </div>
        <div style="display: flex; gap: 8px; margin-bottom: 16px;">
            <x-status-badge palette="platform" :status="$d['priority']" :label="$d['priority'].' Priority'" padding="4px 10px" :radius="10" />
            <x-status-badge palette="platform" :status="$d['status']" padding="4px 10px" :radius="10" data-dispute-status="{{ $d['id'] }}" />
        </div>
        <div style="font-size: 16px; font-weight: 700; color: #1a110a; margin-bottom: 16px;">{{ $d['subject'] }}</div>
        @foreach ([['Dispute ID', $d['id']], ['Booking', $d['bookingId']], ['Raised By', $d['raisedBy']], ['Against', $d['against']], ['Date Filed', $d['date']]] as [$rowLabel, $rowValue])
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0ebe2;">
                <span style="font-size: 13px; color: #6b5a47;">{{ $rowLabel }}</span>
                <span style="font-size: 13px; color: #1a110a; font-weight: 600;">{{ $rowValue }}</span>
            </div>
        @endforeach
        <div style="margin-top: 16px; margin-bottom: 16px;">
            <label style="{{ $label }}">Resolution Notes</label>
            <textarea placeholder="Add your resolution notes here..." style="{{ $input }} min-height: 80px; resize: vertical;"></textarea>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="button" data-modal-close style="{{ $cancelBtn }}">Close</button>
            <button type="button" data-dispute-id="{{ $d['id'] }}" data-dispute-set="Resolved" data-modal-close style="{{ $saveBtn }}">Mark as Resolved</button>
        </div>
    </x-dashboard.modal>
@endforeach
@endsection

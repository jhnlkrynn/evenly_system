@extends('layouts.dashboard')

@section('title', 'Admin Panel · Eventlynk')
@section('dashboard', 'admin')
@section('main_style', 'flex: 1; overflow-y: auto; padding: 24px;')

@php
    $user = auth()->user();
    $card = 'background: #fff; border-radius: 10px; border: 1px solid #e8dfd0; overflow: hidden;';
    $cardHead = 'padding: 16px 18px; border-bottom: 1px solid #e8dfd0;';
    $cardTitle = "font-family: 'Playfair Display', serif; font-size: 15px; font-weight: 700; color: #1a110a; margin: 0;";
    $th = 'text-align: left; font-size: 11px; font-weight: 600; color: #9c8f7a;';
    $label = 'display: block; font-size: 12px; font-weight: 600; color: #6b5a47; margin-bottom: 6px;';
    $cancelBtn = 'flex: 1; padding: 11px; border-radius: 7px; border: 1px solid #e8dfd0; background: #faf8f4; color: #3d2510; font-size: 13px; font-weight: 500; cursor: pointer;';
    $saveBtn = 'flex: 2; padding: 11px; border-radius: 7px; border: none; background: #1a110a; color: #f5f0e8; font-size: 13px; font-weight: 600; cursor: pointer;';
    $closeX = 'background: none; border: none; font-size: 18px; cursor: pointer; color: #6b5a47;';
    $serviceList = fn ($b) => collect([
        $b['services']['decoration'] ? 'Decor' : null,
        $b['services']['serving'] > 0 ? 'Serving ×'.$b['services']['serving'] : null,
        $b['services']['dishwashing'] > 0 ? 'Dish ×'.$b['services']['dishwashing'] : null,
    ])->filter()->join(', ');
    // Payment/status state the page scripts update when a payment is recorded.
    $bookingState = collect($bookings)->mapWithKeys(fn ($b) => [$b['id'] => [
        'total' => $b['totalAmount'],
        'paid' => $b['reservationFee'],
        'status' => $b['status'],
        'payment' => $b['paymentStatus'],
    ]]);
@endphp

@section('sidebar')
<x-dashboard.sidebar variant="admin" :items="$navItems">
    <x-slot:brand>
        <img src="{{ asset('images/eventlynk-logo.png') }}" alt="Eventlynk" style="width: 36px; height: 36px; object-fit: contain;">
        <div>
            <div style="font-family: 'Playfair Display', serif; color: #c9a84c; font-size: 14px; font-weight: 700; white-space: nowrap;">Eventlynk</div>
            <div style="font-size: 10px; color: #9c8f7a; white-space: nowrap;">Admin Panel</div>
        </div>
    </x-slot:brand>
    <x-slot:profile>
        <div data-sidebar-open-only style="padding: 14px 14px; border-bottom: 1px solid #c9a84c22; text-align: center;">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: #c9a84c22; border: 2px solid #c9a84c; display: flex; align-items: center; justify-content: center; font-size: 18px; margin: 0 auto 8px;">🛡</div>
            <div style="color: #f5f0e8; font-size: 13px; font-weight: 600;">{{ $user->name }}</div>
            <div style="color: #9c8f7a; font-size: 11px; margin-top: 2px;">{{ $user->email }}</div>
        </div>
    </x-slot:profile>
</x-dashboard.sidebar>
@endsection

@section('header')
<header class="app-dashboard-header" style="background: #fff; border-bottom: 1px solid #e8dfd0; padding: 0 24px; height: 60px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
    <div>
        <h2 data-dashboard-title style="font-family: 'Playfair Display', serif; font-size: 17px; font-weight: 700; color: #1a110a; margin: 0;">Dashboard</h2>
        <p style="font-size: 11px; color: #9c8f7a; margin: 0;">Event Services Management</p>
    </div>
    <div style="display: flex; align-items: center; gap: 12px;">
        <button type="button" data-show-on-tab="bookings" data-modal-open="admin-create-booking" hidden style="padding: 8px 18px; border-radius: 7px; background: #1a110a; color: #f5f0e8; border: none; font-size: 13px; font-weight: 600; cursor: pointer;">+ New Booking</button>
        <button type="button" aria-label="Notifications" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #6b5a47;">🔔</button>
        <div style="width: 34px; height: 34px; border-radius: 50%; background: #1a110a; border: 2px solid #c9a84c; display: flex; align-items: center; justify-content: center; color: #c9a84c; font-size: 16px;">🛡</div>
    </div>
</header>
@endsection

@section('panels')
<script type="application/json" data-admin-bookings>{!! json_encode($bookingState, JSON_HEX_TAG) !!}</script>

{{-- ── OVERVIEW ── --}}
<div data-panel="overview">
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 24px;">
        @foreach ($kpis as $kpi)
            <x-stat-card variant="stack" :label="$kpi['label']" :value="$kpi['value']" :icon="$kpi['icon']" :color="$kpi['color']" />
        @endforeach
    </div>

    <div style="display: grid; grid-template-columns: 1fr 320px; gap: 18px; margin-bottom: 18px;">
        <div style="{{ $card }}">
            <div style="{{ $cardHead }} display: flex; justify-content: space-between;">
                <h3 style="{{ $cardTitle }}">Upcoming Events</h3>
                <button type="button" data-tab-link="bookings" style="background: none; border: none; color: #c9a84c; font-size: 12px; cursor: pointer;">View all →</button>
            </div>
            @foreach ($upcoming as $i => $b)
                @php($dateParts = explode(' ', $b['date']))
                <div style="padding: 14px 18px; border-bottom: {{ $i < count($upcoming) - 1 ? '1px solid #f0ebe2' : 'none' }}; display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 8px; background: #f5f0e8; display: flex; flex-direction: column; align-items: center; justify-content: center; flex-shrink: 0;">
                        <div style="font-size: 14px; font-weight: 700; color: #1a110a; line-height: 1;">{{ str_replace(',', '', $dateParts[1]) }}</div>
                        <div style="font-size: 10px; color: #9c8f7a; text-transform: uppercase;">{{ $dateParts[0] }}</div>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; font-size: 13px; color: #1a110a;">{{ $b['client'] }} — {{ $b['eventType'] }}</div>
                        <div style="font-size: 12px; color: #6b5a47;">📍 {{ $b['venue'] }} · {{ $b['pax'] }} pax</div>
                    </div>
                    <x-status-badge :status="$b['status']" padding="3px 9px" :size="11" data-booking-status="{{ $b['id'] }}" />
                </div>
            @endforeach
        </div>

        <div style="{{ $card }}">
            <div style="{{ $cardHead }}">
                <h3 style="{{ $cardTitle }}">Inventory Alerts</h3>
            </div>
            <div style="padding: 14px 18px;">
                @foreach ($inventoryAlerts as $alert)
                    <div style="margin-bottom: 14px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span style="font-size: 13px; color: #1a110a;">{{ $alert['item'] }}</span>
                            <span style="font-size: 12px; color: {{ $alert['level'] === 'low' ? '#c62828' : '#a07a2a' }}; font-weight: 600;">{{ $alert['available'] }}/{{ $alert['total'] }} left</span>
                        </div>
                        <div style="height: 6px; background: #f5f0e8; border-radius: 3px;">
                            <div style="height: 100%; width: {{ round($alert['available'] / $alert['total'] * 100, 4) }}%; background: {{ $alert['level'] === 'low' ? '#e53935' : '#c9a84c' }}; border-radius: 3px;"></div>
                        </div>
                    </div>
                @endforeach
                <button type="button" data-tab-link="inventory" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #e8dfd0; background: #faf8f4; color: #3d2510; font-size: 12px; cursor: pointer; margin-top: 4px;">View Full Inventory →</button>
            </div>
        </div>
    </div>

    <div style="{{ $card }}">
        <div style="{{ $cardHead }}">
            <h3 style="{{ $cardTitle }}">Payment Overview</h3>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #faf8f4;">
                    @foreach (['Booking', 'Client', 'Event', 'Date', 'Total', 'Reservation', 'Balance', 'Payment'] as $heading)
                        <th style="padding: 10px 14px; {{ $th }}">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach (collect($bookings)->where('status', '!=', 'Cancelled') as $b)
                    <tr style="border-top: 1px solid #f0ebe2;">
                        <td style="padding: 11px 14px; font-size: 12px; color: #9c8f7a;">{{ $b['id'] }}</td>
                        <td style="padding: 11px 14px; font-size: 13px; font-weight: 600; color: #1a110a;">{{ $b['client'] }}</td>
                        <td style="padding: 11px 14px; font-size: 13px; color: #3d2510;">{{ $b['eventType'] }}</td>
                        <td style="padding: 11px 14px; font-size: 13px; color: #6b5a47;">{{ $b['date'] }}</td>
                        <td style="padding: 11px 14px; font-size: 13px; font-weight: 600;">₱{{ number_format($b['totalAmount']) }}</td>
                        <td style="padding: 11px 14px; font-size: 13px; color: #2e7d32; font-weight: 600;">₱<span data-fee="{{ $b['id'] }}">{{ number_format($b['reservationFee']) }}</span></td>
                        <td style="padding: 11px 14px; font-size: 13px; color: #e53935; font-weight: 600;">₱<span data-balance="{{ $b['id'] }}">{{ number_format($b['totalAmount'] - $b['reservationFee']) }}</span></td>
                        <td style="padding: 11px 14px;"><x-status-badge palette="payment" :status="$b['paymentStatus']" padding="3px 9px" :size="11" data-booking-payment="{{ $b['id'] }}" /></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ── BOOKINGS (list) ── --}}
<div data-panel="bookings" hidden>
    <div class="dash-pills--admin" data-filter-group="admin-bookings" style="display: flex; gap: 8px; margin-bottom: 16px;">
        @foreach (['All', 'Pending', 'Confirmed', 'Completed', 'Cancelled'] as $filter)
            <button type="button" class="dash-pill {{ $filter === 'All' ? 'active' : '' }}" data-filter-value="{{ $filter }}">{{ $filter }}</button>
        @endforeach
    </div>
    <div style="{{ $card }}">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #faf8f4;">
                    @foreach (['#', 'Client', 'Event', 'Date', 'Venue', 'Pax', 'Services', 'Status', 'Payment', 'Action'] as $heading)
                        <th style="padding: 11px 14px; {{ $th }} white-space: nowrap;">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $b)
                    <tr data-filter-item="admin-bookings" data-filter-key="{{ $b['status'] }}" data-filter-status-of="{{ $b['id'] }}" style="border-top: 1px solid #f0ebe2;">
                        <td style="padding: 12px 14px; font-size: 12px; color: #9c8f7a; font-weight: 600;">{{ $b['id'] }}</td>
                        <td style="padding: 12px 14px; font-size: 13px; font-weight: 600; color: #1a110a;">{{ $b['client'] }}</td>
                        <td style="padding: 12px 14px; font-size: 13px; color: #3d2510;">{{ $b['eventType'] }}</td>
                        <td style="padding: 12px 14px; font-size: 13px; color: #6b5a47; white-space: nowrap;">{{ $b['date'] }}</td>
                        <td style="padding: 12px 14px; font-size: 12px; color: #6b5a47; max-width: 140px;">{{ $b['venue'] }}</td>
                        <td style="padding: 12px 14px; font-size: 13px; font-weight: 600; color: #1a110a; text-align: center;">{{ $b['pax'] }}</td>
                        <td style="padding: 12px 14px; font-size: 12px; color: #3d2510;">{{ $serviceList($b) }}</td>
                        <td style="padding: 12px 14px;"><x-status-badge :status="$b['status']" padding="3px 9px" :size="11" data-booking-status="{{ $b['id'] }}" /></td>
                        <td style="padding: 12px 14px;"><x-status-badge palette="payment" :status="$b['paymentStatus']" padding="3px 9px" :size="11" data-booking-payment="{{ $b['id'] }}" /></td>
                        <td style="padding: 12px 14px;">
                            <div style="display: flex; gap: 5px;">
                                <button type="button" data-show-detail="{{ $b['id'] }}" style="padding: 5px 11px; border-radius: 5px; border: 1px solid #e8dfd0; background: transparent; color: #3d2510; font-size: 11px; cursor: pointer;">View</button>
                                <button type="button" data-modal-open="invoice-{{ $b['id'] }}" style="padding: 5px 11px; border-radius: 5px; border: none; background: #f5f0e8; color: #1a110a; font-size: 11px; cursor: pointer;">Invoice</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ── BOOKING DETAIL (one per booking, shown from the list) ── --}}
@foreach ($bookings as $b)
    <div data-panel="bookings" data-detail="{{ $b['id'] }}" hidden>
        <button type="button" data-hide-detail style="background: none; border: none; color: #c9a84c; font-size: 13px; cursor: pointer; margin-bottom: 16px; display: flex; align-items: center; gap: 6px;">← Back to Bookings</button>
        <div style="display: grid; grid-template-columns: 1fr 340px; gap: 18px;">
            <div>
                <div style="background: #fff; border-radius: 10px; border: 1px solid #e8dfd0; padding: 22px; margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px;">
                        <div>
                            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; font-weight: 700; color: #1a110a; margin: 0;">Booking {{ $b['id'] }}</h2>
                            <div style="font-size: 12px; color: #9c8f7a; margin-top: 4px;">{{ $b['client'] }} · {{ $b['eventType'] }}</div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <x-status-badge :status="$b['status']" padding="5px 12px" data-booking-status="{{ $b['id'] }}" />
                            <x-status-badge palette="payment" :status="$b['paymentStatus']" padding="5px 12px" data-booking-payment="{{ $b['id'] }}" />
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                        @foreach ([['Client', $b['client']], ['Event Type', $b['eventType']], ['Event Date', $b['date']], ['Time', $b['time']], ['Venue', $b['venue']], ['No. of Guests', $b['pax'].' pax']] as [$fieldLabel, $fieldValue])
                            <div>
                                <div style="font-size: 11px; color: #9c8f7a; margin-bottom: 3px; text-transform: uppercase; letter-spacing: 0.06em;">{{ $fieldLabel }}</div>
                                <div style="font-size: 13px; font-weight: 600; color: #1a110a;">{{ $fieldValue }}</div>
                            </div>
                        @endforeach
                    </div>
                    @if ($b['specialRequests'])
                        <div style="margin-top: 14px; padding: 12px 14px; background: #fffdf7; border-radius: 7px; border: 1px solid #c9a84c33;">
                            <div style="font-size: 11px; color: #9c8f7a; margin-bottom: 4px;">SPECIAL REQUESTS</div>
                            <div style="font-size: 13px; color: #3d2510;">{{ $b['specialRequests'] }}</div>
                        </div>
                    @endif
                </div>

                <div style="background: #fff; border-radius: 10px; border: 1px solid #e8dfd0; padding: 22px; margin-bottom: 16px;">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 15px; font-weight: 700; color: #1a110a; margin-bottom: 14px;">Services Booked</h3>
                    @foreach ([
                        ['Decoration', $b['services']['decoration'], $b['services']['decoration'] ? 'Full decoration setup' : null],
                        ['Serving Staff', $b['services']['serving'] > 0, $b['services']['serving'] > 0 ? $b['services']['serving'].' staff' : null],
                        ['Dishwashing', $b['services']['dishwashing'] > 0, $b['services']['dishwashing'] > 0 ? $b['services']['dishwashing'].' staff' : null],
                    ] as [$serviceLabel, $checked, $detail])
                        <div style="display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f0ebe2;">
                            <div style="width: 22px; height: 22px; border-radius: 5px; border: 2px solid {{ $checked ? '#2e7d32' : '#e8dfd0' }}; background: {{ $checked ? '#e6f4ea' : '#faf8f4' }}; display: flex; align-items: center; justify-content: center; font-size: 13px; color: #2e7d32;">{{ $checked ? '✓' : '' }}</div>
                            <span style="font-size: 14px; font-weight: 500; color: {{ $checked ? '#1a110a' : '#9c8f7a' }};">{{ $serviceLabel }}</span>
                            @if ($detail)
                                <span style="font-size: 12px; color: #6b5a47; margin-left: auto;">{{ $detail }}</span>
                            @endif
                        </div>
                    @endforeach
                    <div style="margin-top: 14px;">
                        @include('dashboards.partials.price-calc', ['services' => $b['services']])
                    </div>
                </div>

                <div style="background: #fff; border-radius: 10px; border: 1px solid #e8dfd0; padding: 22px;">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 15px; font-weight: 700; color: #1a110a; margin-bottom: 14px;">Staff Assignment</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #faf8f4;">
                                @foreach (['Service', 'Staff Needed', 'Assigned Staff', 'Status'] as $heading)
                                    <th style="padding: 9px 12px; {{ $th }}">{{ $heading }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ([
                                ['Decoration', 'decoration', $b['services']['decoration'] ? 3 : 0],
                                ['Serving Staff', 'serving', $b['services']['serving']],
                                ['Dishwashing', 'dishwashing', $b['services']['dishwashing']],
                            ] as [$serviceLabel, $key, $needed])
                                @continue($needed === 0)
                                @php($assigned = $b['assignedStaff'][$key])
                                @php($complete = count($assigned) >= $needed)
                                <tr style="border-top: 1px solid #f0ebe2;">
                                    <td style="padding: 11px 12px; font-size: 13px; font-weight: 500; color: #1a110a;">{{ $serviceLabel }}</td>
                                    <td style="padding: 11px 12px; font-size: 13px; color: #3d2510;">{{ $needed }} staff</td>
                                    <td style="padding: 11px 12px; font-size: 13px; color: #3d2510;">
                                        @if (count($assigned) > 0)
                                            {{ implode(', ', $assigned) }}
                                        @else
                                            <span style="color: #9c8f7a; font-style: italic;">Not yet assigned</span>
                                        @endif
                                    </td>
                                    <td style="padding: 11px 12px;">
                                        <span style="padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 600; background: {{ $complete ? '#e6f4ea' : '#fff8e8' }}; color: {{ $complete ? '#2e7d32' : '#a07a2a' }};">{{ $complete ? 'Complete' : 'Incomplete' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="background: #fff; border-radius: 10px; border: 1px solid #e8dfd0; padding: 18px;">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 14px; font-weight: 700; color: #1a110a; margin-bottom: 12px;">Actions</h3>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <button type="button" data-set-status="Confirmed" data-when-status="Pending" @if ($b['status'] !== 'Pending') hidden @endif style="padding: 10px; border-radius: 7px; border: none; background: #1a110a; color: #f5f0e8; font-size: 13px; font-weight: 600; cursor: pointer;">✓ Confirm Booking</button>
                        <button type="button" data-set-status="Completed" data-when-status="Confirmed" @if ($b['status'] !== 'Confirmed') hidden @endif style="padding: 10px; border-radius: 7px; border: none; background: #2e7d32; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer;">✓ Mark as Completed</button>
                        <button type="button" data-modal-open="invoice-{{ $b['id'] }}" style="padding: 10px; border-radius: 7px; border: 1px solid #e8dfd0; background: #faf8f4; color: #1a110a; font-size: 13px; font-weight: 500; cursor: pointer;">🧾 View Invoice</button>
                        <button type="button" data-tab-link="staff" style="padding: 10px; border-radius: 7px; border: 1px solid #e8dfd0; background: #faf8f4; color: #1a110a; font-size: 13px; font-weight: 500; cursor: pointer;">👥 Assign Staff</button>
                        <button type="button" data-set-status="Cancelled" data-confirm="Cancel booking {{ $b['id'] }}?" data-when-status="Pending Confirmed" @if (in_array($b['status'], ['Cancelled', 'Completed'], true)) hidden @endif style="padding: 10px; border-radius: 7px; border: 1px solid #fce4ec; background: #fce4ec; color: #c62828; font-size: 13px; font-weight: 500; cursor: pointer;">✕ Cancel Booking</button>
                    </div>
                </div>

                <div style="background: #fff; border-radius: 10px; border: 1px solid #e8dfd0; padding: 18px;">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 14px; font-weight: 700; color: #1a110a; margin-bottom: 12px;">Payment</h3>
                    <div style="font-size: 13px; color: #3d2510;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #9c8f7a;">Total Amount</span>
                            <span style="font-weight: 600;">₱{{ number_format($b['totalAmount']) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #9c8f7a;">Reservation Fee</span>
                            <span style="font-weight: 600;">₱<span data-fee="{{ $b['id'] }}">{{ number_format($b['reservationFee']) }}</span></span>
                        </div>
                        <div style="border-top: 1px solid #e8dfd0; padding-top: 8px; display: flex; justify-content: space-between;">
                            <span style="color: #e53935; font-weight: 600;">Remaining Balance</span>
                            <span style="color: #e53935; font-weight: 700;">₱<span data-balance="{{ $b['id'] }}">{{ number_format($b['totalAmount'] - $b['reservationFee']) }}</span></span>
                        </div>
                    </div>
                    <div style="margin-top: 10px;">
                        <x-status-badge palette="payment" :status="$b['paymentStatus']" padding="4px 12px" data-booking-payment="{{ $b['id'] }}" />
                    </div>
                    <button type="button" data-modal-open="payment-{{ $b['id'] }}" style="width: 100%; margin-top: 12px; padding: 9px; border-radius: 7px; border: 1px solid #c9a84c; background: transparent; color: #c9a84c; font-size: 12px; font-weight: 600; cursor: pointer;">💳 Record Payment</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- ── CALENDAR ── --}}
<div data-panel="calendar" hidden style="display: grid; grid-template-columns: 1fr 300px; gap: 18px;">
    <div style="background: #fff; border-radius: 10px; border: 1px solid #e8dfd0; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; font-weight: 700; color: #1a110a; margin: 0;">September 2026</h2>
            <div style="display: flex; gap: 8px;">
                <button type="button" style="padding: 6px 14px; border-radius: 6px; border: 1px solid #e8dfd0; background: #fff; cursor: pointer; color: #3d2510; font-size: 13px;">‹ Aug</button>
                <button type="button" style="padding: 6px 14px; border-radius: 6px; border: 1px solid #e8dfd0; background: #fff; cursor: pointer; color: #3d2510; font-size: 13px;">Oct ›</button>
            </div>
        </div>
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px;">
            @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $weekday)
                <div style="text-align: center; padding: 8px 4px; font-size: 12px; font-weight: 600; color: #9c8f7a;">{{ $weekday }}</div>
            @endforeach
            @foreach (collect($calendarGrid)->flatten() as $day)
                <div style="min-height: 72px; padding: 6px; border: 1px solid #f0ebe2; border-radius: 6px; background: {{ $day === 15 ? '#fffdf7' : '#fff' }}; position: relative;">
                    @if ($day)
                        <div style="font-size: 13px; font-weight: {{ $day === 15 ? 700 : 400 }}; color: {{ $day === 15 ? '#c9a84c' : '#1a110a' }}; margin-bottom: 4px;">{{ $day }}</div>
                        @foreach ($calendarEvents[$day] ?? [] as $event)
                            <div style="font-size: 10px; padding: 2px 5px; border-radius: 3px; background: {{ $event['color'] }}; color: #fff; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $event['label'] }}</div>
                        @endforeach
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div>
        <div style="background: #fff; border-radius: 10px; border: 1px solid #e8dfd0; padding: 18px;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 14px; font-weight: 700; color: #1a110a; margin-bottom: 14px;">Events This Month</h3>
            @foreach (collect($bookings)->where('status', '!=', 'Cancelled') as $b)
                <div style="padding: 12px 0; border-bottom: 1px solid #f0ebe2;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="font-weight: 600; font-size: 13px; color: #1a110a;">{{ $b['client'] }}</span>
                        <x-status-badge :status="$b['status']" padding="2px 8px" :size="11" :radius="12" data-booking-status="{{ $b['id'] }}" />
                    </div>
                    <div style="font-size: 12px; color: #6b5a47; margin-bottom: 2px;">📅 {{ $b['date'] }} · {{ $b['time'] }}</div>
                    <div style="font-size: 12px; color: #6b5a47; margin-bottom: 2px;">📍 {{ $b['venue'] }}</div>
                    <div style="font-size: 12px; color: #9c8f7a;">👥 {{ $b['pax'] }} pax · {{ $b['eventType'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── STAFF ASSIGNMENT ── --}}
<div data-panel="staff" hidden>
    <div style="display: grid; grid-template-columns: 1fr 280px; gap: 18px;">
        <div>
            <div style="{{ $card }} margin-bottom: 16px;">
                <div style="{{ $cardHead }}">
                    <h3 style="{{ $cardTitle }}">Staff Assignments by Booking</h3>
                    <p style="font-size: 12px; color: #9c8f7a; margin: 4px 0 0;">
                        Staff assigned to confirmed bookings — double-booking is
                        flagged in red.
                    </p>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #faf8f4;">
                            @foreach (['Booking', 'Date', 'Service', 'Needed', 'Assigned', 'Status', 'Action'] as $heading)
                                <th style="padding: 10px 14px; {{ $th }}">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assignmentRows as $row)
                            @php($complete = count($row['assigned']) >= $row['needed'])
                            <tr style="border-top: 1px solid #f0ebe2;">
                                <td style="padding: 11px 14px;">
                                    <div style="font-size: 12px; font-weight: 600; color: #9c8f7a;">{{ $row['bookingId'] }}</div>
                                    <div style="font-size: 12px; color: #1a110a;">{{ $row['client'] }}</div>
                                </td>
                                <td style="padding: 11px 14px; font-size: 12px; color: #6b5a47;">{{ $row['date'] }}</td>
                                <td style="padding: 11px 14px; font-size: 13px; font-weight: 500; color: #1a110a;">{{ $row['service'] }}</td>
                                <td style="padding: 11px 14px; font-size: 13px; color: #3d2510; text-align: center;">{{ $row['needed'] }}</td>
                                <td style="padding: 11px 14px; font-size: 12px; color: #3d2510;">
                                    @if (count($row['assigned']) > 0)
                                        {{ implode(', ', $row['assigned']) }}
                                    @else
                                        <span style="color: #9c8f7a; font-style: italic;">Not assigned</span>
                                    @endif
                                </td>
                                <td style="padding: 11px 14px;">
                                    <span style="padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 600; background: {{ $complete ? '#e6f4ea' : '#fff8e8' }}; color: {{ $complete ? '#2e7d32' : '#a07a2a' }};">{{ $complete ? '✓ Complete' : count($row['assigned']).'/'.$row['needed'].' Assigned' }}</span>
                                </td>
                                <td style="padding: 11px 14px;">
                                    {{-- Every staff member is already on a confirmed booking, so the prototype always reports none free. --}}
                                    <button type="button" @disabled($complete) data-toast-message="No available staff for this service" style="padding: 5px 12px; border-radius: 6px; border: 1px solid #c9a84c; background: transparent; color: #c9a84c; font-size: 11px; font-weight: 600; cursor: {{ $complete ? 'default' : 'pointer' }}; opacity: {{ $complete ? 0.4 : 1 }};">+ Assign</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div style="{{ $card }}">
            <div style="{{ $cardHead }}">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 14px; font-weight: 700; color: #1a110a; margin: 0;">Staff Roster</h3>
            </div>
            <div style="padding: 12px;">
                @foreach (['Decoration', 'Serving', 'Dishwashing'] as $role)
                    <div style="margin-bottom: 16px;">
                        <div style="font-size: 11px; color: #9c8f7a; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 6px;">{{ $role }}</div>
                        @foreach (collect($staff)->where('role', $role) as $member)
                            @php($busy = isset($usedStaff[$member['name']]))
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; border-radius: 6px; background: {{ $busy ? '#fff8e8' : '#f9f7f4' }}; margin-bottom: 4px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 28px; height: 28px; border-radius: 50%; background: {{ $busy ? '#c9a84c22' : '#f0ebe2' }}; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #6b5a47;">{{ collect(explode(' ', $member['name']))->map(fn ($w) => $w[0])->join('') }}</div>
                                    <span style="font-size: 13px; color: #1a110a;">{{ $member['name'] }}</span>
                                </div>
                                <span style="font-size: 11px; padding: 2px 7px; border-radius: 10px; background: {{ $busy ? '#fff8e8' : '#e6f4ea' }}; color: {{ $busy ? '#a07a2a' : '#2e7d32' }}; font-weight: 600;">{{ $busy ? 'Assigned' : 'Free' }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── INVENTORY ── --}}
<div data-panel="inventory" hidden>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
        @foreach ([['decor', '🌸 Decor Inventory', $decorInventory], ['equipment', '🍽 Equipment Inventory', $equipmentInventory]] as [$kind, $title, $items])
            <div style="{{ $card }}">
                <div style="{{ $cardHead }} display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="{{ $cardTitle }}">{{ $title }}</h3>
                    <button type="button" data-modal-open="admin-add-{{ $kind }}" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #c9a84c; background: transparent; color: #c9a84c; font-size: 11px; cursor: pointer;">+ Add Item</button>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #faf8f4;">
                            @foreach (['Item', 'Owned', 'Available', 'In Use', 'Condition'] as $heading)
                                <th style="padding: 9px 12px; {{ $th }}">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr style="border-top: 1px solid #f0ebe2;">
                                <td style="padding: 11px 12px; font-size: 13px; font-weight: 500; color: #1a110a;">{{ $item['item'] }}</td>
                                <td style="padding: 11px 12px; font-size: 13px; color: #3d2510; text-align: center;">{{ $item['owned'] }}</td>
                                <td style="padding: 11px 12px; text-align: center;">
                                    <span style="font-size: 13px; font-weight: 600; color: {{ $item['available'] <= 3 ? '#c62828' : '#2e7d32' }};">{{ $item['available'] }}</span>
                                </td>
                                <td style="padding: 11px 12px; font-size: 13px; color: #6b5a47; text-align: center;">{{ $item['inUse'] }}</td>
                                <td style="padding: 11px 12px;">
                                    <span style="padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; background: {{ $item['condition'] === 'Good' ? '#e6f4ea' : '#fff8e8' }}; color: {{ $item['condition'] === 'Good' ? '#2e7d32' : '#a07a2a' }};">{{ $item['condition'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</div>

{{-- ── INVOICES ── --}}
<div data-panel="invoices" hidden style="{{ $card }}">
    <div style="{{ $cardHead }}">
        <h3 style="{{ $cardTitle }}">All Invoices / Booking Slips</h3>
    </div>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #faf8f4;">
                @foreach (['#', 'Client', 'Event', 'Date', 'Services', 'Total', 'Paid', 'Balance', 'Action'] as $heading)
                    <th style="padding: 10px 14px; {{ $th }}">{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($bookings as $b)
                <tr style="border-top: 1px solid #f0ebe2;">
                    <td style="padding: 12px 14px; font-size: 12px; color: #9c8f7a; font-weight: 600;">{{ $b['id'] }}</td>
                    <td style="padding: 12px 14px; font-size: 13px; font-weight: 600; color: #1a110a;">{{ $b['client'] }}</td>
                    <td style="padding: 12px 14px; font-size: 13px; color: #3d2510;">{{ $b['eventType'] }}</td>
                    <td style="padding: 12px 14px; font-size: 13px; color: #6b5a47;">{{ $b['date'] }}</td>
                    <td style="padding: 12px 14px; font-size: 12px; color: #3d2510;">{{ $serviceList($b) }}</td>
                    <td style="padding: 12px 14px; font-size: 13px; font-weight: 600;">₱{{ number_format($b['totalAmount']) }}</td>
                    <td style="padding: 12px 14px; font-size: 13px; color: #2e7d32; font-weight: 600;">₱<span data-fee="{{ $b['id'] }}">{{ number_format($b['reservationFee']) }}</span></td>
                    <td style="padding: 12px 14px; font-size: 13px; color: #e53935; font-weight: 600;">₱<span data-balance="{{ $b['id'] }}">{{ number_format($b['totalAmount'] - $b['reservationFee']) }}</span></td>
                    <td style="padding: 12px 14px;">
                        <div style="display: flex; gap: 5px; align-items: center;">
                            <button type="button" data-modal-open="invoice-{{ $b['id'] }}" style="padding: 5px 11px; border-radius: 5px; border: 1px solid #e8dfd0; background: #faf8f4; color: #1a110a; font-size: 11px; cursor: pointer;">View</button>
                            <x-status-badge palette="payment" :status="$b['paymentStatus']" padding="3px 9px" :size="11" data-booking-payment="{{ $b['id'] }}" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ── SETTINGS ── --}}
<div data-panel="settings" hidden style="background: #fff; border-radius: 10px; border: 1px solid #e8dfd0; padding: 40px; text-align: center;">
    <div style="font-size: 48px; margin-bottom: 16px;">⚙</div>
    <h3 style="font-family: 'Playfair Display', serif; font-size: 20px; color: #1a110a; margin-bottom: 8px;">Business Settings</h3>
    <p style="color: #6b5a47; font-size: 14px;">
        Configure service rates, staff information, payment terms, and
        notification preferences.
    </p>
</div>
@endsection

@section('modals')
{{-- Invoice / booking slip --}}
@foreach ($bookings as $b)
    @php($pay = \App\Support\MockData::palette('payment', $b['paymentStatus']))
    <x-dashboard.modal id="invoice-{{ $b['id'] }}" overlay="sheet" data-hides-on-tab="invoices" panel-style="background: #fff; border-radius: 12px; padding: 36px; max-width: 480px; width: 100%; max-height: 90vh; overflow-y: auto; position: relative;">
        <button type="button" data-modal-close aria-label="Close" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 18px; cursor: pointer; color: #9c8f7a;">✕</button>

        <div style="text-align: center; margin-bottom: 24px;">
            <div style="font-family: 'Playfair Display', serif; font-size: 1.2rem; font-weight: 700; color: #1a110a; text-transform: uppercase; letter-spacing: 0.1em;">Eventlynk</div>
            <div style="font-size: 13px; font-weight: 700; color: #c9a84c; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.12em;">Booking Slip</div>
            <div style="height: 1px; background: linear-gradient(90deg, transparent, #c9a84c66, transparent); margin: 16px 0;"></div>
        </div>

        <div style="margin-bottom: 20px;">
            @foreach ([['Booking #', $b['id']], ['Customer', $b['client']], ['Event', $b['eventType']], ['Date', $b['date']], ['Time', $b['time']], ['Venue', $b['venue']], ['Guests', $b['pax'].' pax']] as [$rowLabel, $rowValue])
                <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dotted #e8dfd0;">
                    <span style="font-size: 13px; color: #9c8f7a;">{{ $rowLabel }}</span>
                    <span style="font-size: 13px; font-weight: 600; color: #1a110a;">{{ $rowValue }}</span>
                </div>
            @endforeach
        </div>

        <div style="font-size: 12px; font-weight: 700; color: #9c8f7a; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px;">Services</div>
        @include('dashboards.partials.price-calc', ['services' => $b['services']])

        <div style="height: 1px; background: #e8dfd0; margin: 16px 0;"></div>

        <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
            <span style="font-size: 13px; color: #2e7d32; font-weight: 500;">Reservation Fee Paid</span>
            <span style="font-size: 13px; font-weight: 700; color: #2e7d32;">₱<span data-fee="{{ $b['id'] }}">{{ number_format($b['reservationFee']) }}</span></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 16px;">
            <span style="font-size: 13px; color: #e53935; font-weight: 500;">Remaining Balance</span>
            <span style="font-size: 13px; font-weight: 700; color: #e53935;">₱<span data-balance="{{ $b['id'] }}">{{ number_format($b['totalAmount'] - $b['reservationFee']) }}</span></span>
        </div>

        <div data-pay-box="{{ $b['id'] }}" style="text-align: center; padding: 10px; border-radius: 8px; background: {{ $pay['bg'] }}; margin-bottom: 20px;">
            <span data-pay-text="{{ $b['id'] }}" style="font-size: 14px; font-weight: 700; color: {{ $pay['color'] }};">Payment Status: {{ strtoupper($b['paymentStatus']) }}</span>
        </div>

        <button type="button" data-print style="width: 100%; padding: 11px; border-radius: 7px; background: #1a110a; color: #f5f0e8; border: none; font-size: 13px; font-weight: 600; cursor: pointer;">🖨 Print / Download</button>
    </x-dashboard.modal>
@endforeach

{{-- Create booking --}}
<x-dashboard.modal id="admin-create-booking" overlay="sheet" panel-style="background: #fff; border-radius: 12px; padding: 32px; max-width: 560px; width: 100%; max-height: 90vh; overflow-y: auto; position: relative;">
    <button type="button" data-modal-close aria-label="Close" style="position: absolute; top: 14px; right: 14px; background: none; border: none; font-size: 18px; cursor: pointer; color: #9c8f7a;">✕</button>
    <h2 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; font-weight: 700; color: #1a110a; margin-bottom: 20px;">Create New Booking</h2>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        @foreach ([
            ['Customer Name', 'text', 'e.g. Maria Santos'],
            ['Contact Number', 'tel', 'e.g. +63 917 123 4567'],
            ['Event Type', 'text', 'e.g. Birthday, Wedding'],
            ['Event Date', 'date', ''],
            ['Start Time', 'time', ''],
            ['End Time', 'time', ''],
            ['Venue / Location', 'text', 'e.g. Laguna, Sta. Rosa'],
            ['No. of Guests (Pax)', 'number', 'e.g. 100'],
        ] as [$fieldLabel, $type, $placeholder])
            <div style="grid-column: {{ $fieldLabel === 'Venue / Location' ? '1 / -1' : 'auto' }};">
                <label style="display: block; font-size: 12px; font-weight: 500; color: #6b5a47; margin-bottom: 6px;">{{ $fieldLabel }}</label>
                <input type="{{ $type }}" placeholder="{{ $placeholder }}" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd5c8; border-radius: 7px; font-size: 13px; color: #1a110a; outline: none; background: #faf8f4; box-sizing: border-box;">
            </div>
        @endforeach
    </div>

    <div style="margin-top: 16px; margin-bottom: 14px;">
        <div style="font-size: 12px; font-weight: 500; color: #6b5a47; margin-bottom: 10px;">Services</div>
        @foreach ([['🌸 Decoration', false], ['🍽 Serving Staff', true], ['🫧 Dishwashing', true]] as [$service, $hasCount])
            <div style="display: flex; align-items: center; gap: 12px; padding: 10px 12px; border: 1px solid #e8dfd0; border-radius: 7px; background: #faf8f4; margin-bottom: 8px;">
                <input type="checkbox" style="width: 16px; height: 16px; cursor: pointer;">
                <span style="font-size: 13px; color: #1a110a; flex: 1;">{{ $service }}</span>
                @if ($hasCount)
                    <input type="number" min="1" placeholder="0" style="width: 60px; padding: 5px 8px; border: 1px solid #ddd5c8; border-radius: 5px; font-size: 13px; color: #1a110a; outline: none;">
                @endif
            </div>
        @endforeach
    </div>

    <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 12px; font-weight: 500; color: #6b5a47; margin-bottom: 6px;">Special Requests</label>
        <textarea placeholder="Any special instructions, theme, or notes..." style="width: 100%; padding: 10px 12px; border: 1px solid #ddd5c8; border-radius: 7px; font-size: 13px; color: #1a110a; outline: none; background: #faf8f4; min-height: 72px; resize: vertical; box-sizing: border-box;"></textarea>
    </div>

    <div style="display: flex; gap: 10px;">
        <button type="button" data-modal-close style="{{ $cancelBtn }}">Cancel</button>
        <button type="button" data-modal-close style="{{ $saveBtn }}">Save Booking</button>
    </div>
</x-dashboard.modal>

{{-- Record payment --}}
@foreach ($bookings as $b)
    <x-dashboard.modal id="payment-{{ $b['id'] }}" overlay="dark" data-payment-modal="{{ $b['id'] }}" panel-style="background: #fff; border-radius: 12px; padding: 28px; width: 360px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 17px; font-weight: 700; color: #1a110a; margin: 0;">Record Payment</h3>
            <button type="button" data-modal-close aria-label="Close" style="{{ $closeX }}">✕</button>
        </div>
        <div style="padding: 12px 14px; background: #faf8f4; border-radius: 8px; margin-bottom: 16px; border: 1px solid #e8dfd0;">
            <div style="font-size: 12px; color: #6b5a47; margin-bottom: 4px;">Booking: {{ $b['id'] }} — {{ $b['client'] }}</div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; color: #1a110a; margin-bottom: 2px;">
                <span>Total Amount</span>
                <span style="font-weight: 700;">₱{{ number_format($b['totalAmount']) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; color: #1a110a; margin-bottom: 2px;">
                <span>Amount Paid</span>
                <span style="color: #2d7a2d; font-weight: 600;">₱<span data-fee="{{ $b['id'] }}">{{ number_format($b['reservationFee']) }}</span></span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 13px; color: #1a110a;">
                <span>Remaining Balance</span>
                <span style="color: #c84b4b; font-weight: 600;">₱<span data-balance="{{ $b['id'] }}">{{ number_format($b['totalAmount'] - $b['reservationFee']) }}</span></span>
            </div>
        </div>
        <div style="margin-bottom: 16px;">
            <label style="{{ $label }}">Amount to Record (₱)</label>
            <input type="number" value="0" min="0" max="{{ $b['totalAmount'] - $b['reservationFee'] }}" data-payment-amount style="width: 100%; padding: 10px 12px; border: 1px solid #ddd5c8; border-radius: 7px; font-size: 14px; color: #1a110a; outline: none; background: #faf8f4; box-sizing: border-box;">
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="button" data-modal-close style="{{ $cancelBtn }}">Cancel</button>
            <button type="button" data-record-payment="{{ $b['id'] }}" style="{{ $saveBtn }}">Confirm Payment</button>
        </div>
    </x-dashboard.modal>
@endforeach

{{-- Add inventory item (décor / equipment) --}}
@foreach (['decor' => 'Décor', 'equipment' => 'Equipment'] as $kind => $kindLabel)
    <x-dashboard.modal id="admin-add-{{ $kind }}" overlay="dark" panel-style="background: #fff; border-radius: 12px; padding: 28px; width: 380px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 17px; font-weight: 700; color: #1a110a; margin: 0;">Add {{ $kindLabel }} Item</h3>
            <button type="button" data-modal-close aria-label="Close" style="{{ $closeX }}">✕</button>
        </div>
        @foreach ([['Item Name', 'text', 'e.g. Round Table Centerpiece'], ['Category', 'text', 'e.g. Centerpiece / Linens'], ['Total Owned', 'number', '0'], ['Condition', 'text', 'Good / Fair / Poor']] as [$fieldLabel, $type, $placeholder])
            <div style="margin-bottom: 13px;">
                <label style="display: block; font-size: 12px; font-weight: 600; color: #6b5a47; margin-bottom: 5px;">{{ $fieldLabel }}</label>
                <input type="{{ $type }}" placeholder="{{ $placeholder }}" style="width: 100%; padding: 9px 12px; border: 1px solid #ddd5c8; border-radius: 7px; font-size: 13px; color: #1a110a; outline: none; background: #faf8f4; box-sizing: border-box;">
            </div>
        @endforeach
        <div style="display: flex; gap: 10px; margin-top: 4px;">
            <button type="button" data-modal-close style="{{ $cancelBtn }}">Cancel</button>
            <button type="button" data-modal-close data-toast-message="Item added to {{ $kindLabel }} inventory" style="{{ $saveBtn }}">Add Item</button>
        </div>
    </x-dashboard.modal>
@endforeach
@endsection

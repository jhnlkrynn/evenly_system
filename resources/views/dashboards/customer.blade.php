@extends('layouts.dashboard')

@section('title', 'My Dashboard · Eventlynk')
@section('dashboard', 'customer')

@php
    $user = auth()->user();
    $card = 'background: #fff; border-radius: 12px; border: 1px solid #e8dfd0; overflow: hidden;';
    $cardHead = 'padding: 18px 20px; border-bottom: 1px solid #e8dfd0; display: flex; justify-content: space-between; align-items: center;';
    $cardTitle = "font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: #1a110a; margin: 0;";
    $linkBtn = 'background: none; border: none; color: #c9a84c; font-size: 13px; font-weight: 500; cursor: pointer;';
    $th = 'text-align: left; font-size: 12px; font-weight: 600; color: #9c8f7a;';
@endphp

@section('sidebar')
<x-dashboard.sidebar variant="portal" :items="$navItems">
    <x-slot:brand>
        <img src="{{ asset('images/eventlynk-logo.png') }}" alt="Eventlynk" style="width: 40px; height: 40px; object-fit: contain;">
        <span style="font-family: 'Playfair Display', serif; color: #c9a84c; font-size: 16px; font-weight: 700; white-space: nowrap;">Eventlynk</span>
    </x-slot:brand>
    <x-slot:profile>
        <div data-sidebar-open-only style="padding: 20px 16px; border-bottom: 1px solid #c9a84c22; text-align: center;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: #3d2510; border: 2px solid #c9a84c; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; color: #c9a84c; margin: 0 auto 10px;">{{ $user->initials() }}</div>
            <div style="color: #f5f0e8; font-size: 14px; font-weight: 600;">{{ $user->name }}</div>
            <div style="color: #9c8f7a; font-size: 12px; margin-top: 3px;">{{ $user->email }}</div>
            <span style="display: inline-block; margin-top: 8px; padding: 3px 10px; background: #c9a84c22; border: 1px solid #c9a84c44; border-radius: 20px; font-size: 11px; color: #c9a84c;">Customer</span>
        </div>
    </x-slot:profile>
</x-dashboard.sidebar>
@endsection

@section('header')
<header class="app-dashboard-header" style="background: #fff; border-bottom: 1px solid #e8dfd0; padding: 0 28px; height: 64px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
    <div>
        <h2 data-dashboard-title style="font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #1a110a; margin: 0;">Overview</h2>
        <p style="font-size: 12px; color: #9c8f7a; margin: 0;">Welcome back, {{ strtok($user->name, ' ') }}!</p>
    </div>
    <div style="display: flex; align-items: center; gap: 16px;">
        <button type="button" data-href="{{ route('services.index') }}" style="padding: 8px 20px; border-radius: 8px; background: #c9a84c; border: none; color: #1a110a; font-size: 13px; font-weight: 600; cursor: pointer;">+ Book a Service</button>
        <div style="position: relative;">
            <button type="button" data-tab-link="notifications" aria-label="Notifications" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #6b5a47;">🔔</button>
            <span data-badge="unread" style="position: absolute; top: 0; right: 0; width: 16px; height: 16px; background: #c9a84c; border-radius: 50%; font-size: 10px; font-weight: 700; color: #1a110a; display: flex; align-items: center; justify-content: center;">{{ $unread }}</span>
        </div>
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #3d2510; border: 2px solid #c9a84c; display: flex; align-items: center; justify-content: center; color: #c9a84c; font-size: 13px; font-weight: 700; cursor: pointer;">{{ $user->initials() }}</div>
    </div>
</header>
@endsection

@section('panels')
{{-- ── OVERVIEW ── --}}
<div data-panel="overview">
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px;">
        @foreach ($stats as $stat)
            <x-stat-card variant="tile" :label="$stat['label']" :value="$stat['value']" :icon="$stat['icon']" :color="$stat['color']" value-size="1.8rem" />
        @endforeach
    </div>

    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 20px;">
        <div style="{{ $card }}">
            <div style="{{ $cardHead }}">
                <h3 style="{{ $cardTitle }}">Upcoming Bookings</h3>
                <button type="button" data-tab-link="bookings" style="{{ $linkBtn }}">View all →</button>
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #faf8f4;">
                        @foreach (['Service', 'Date', 'Guests', 'Amount', 'Status'] as $heading)
                            <th style="padding: 11px 16px; {{ $th }} white-space: nowrap;">{{ $heading }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($upcoming as $b)
                        <tr style="border-top: 1px solid #f0ebe2;">
                            <td style="padding: 13px 16px;">
                                <div style="font-weight: 600; font-size: 13px; color: #1a110a;">{{ $b['service'] }}</div>
                                <div style="font-size: 11px; color: #9c8f7a;">{{ $b['id'] }} · {{ $b['category'] }}</div>
                            </td>
                            <td style="padding: 13px 16px; font-size: 13px; color: #3d2510;">
                                <div>{{ $b['date'] }}</div>
                                <div style="font-size: 11px; color: #9c8f7a;">{{ $b['time'] }}</div>
                            </td>
                            <td style="padding: 13px 16px; font-size: 13px; color: #3d2510;">{{ $b['guests'] }} pax</td>
                            <td style="padding: 13px 16px; font-size: 13px; font-weight: 600; color: #1a110a;">{{ $b['amount'] }}</td>
                            <td style="padding: 13px 16px;"><x-status-badge :status="$b['status']" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="{{ $card }}">
            <div style="{{ $cardHead }}">
                <h3 style="{{ $cardTitle }}">Notifications</h3>
                <button type="button" data-tab-link="notifications" style="{{ $linkBtn }}">View all →</button>
            </div>
            <div>
                @foreach (array_slice($notifications, 0, 4) as $i => $n)
                    <div data-notification="{{ $i }}" style="padding: 14px 16px; border-bottom: {{ $i < 3 ? '1px solid #f0ebe2' : 'none' }}; background: {{ $n['unread'] ? '#fffdf7' : '#fff' }}; display: flex; gap: 12px; align-items: flex-start;">
                        @if ($n['unread'])
                            <div data-unread-dot style="width: 7px; height: 7px; border-radius: 50%; background: #c9a84c; margin-top: 5px; flex-shrink: 0;"></div>
                        @else
                            <div style="width: 7px; flex-shrink: 0;"></div>
                        @endif
                        <div>
                            <p style="font-size: 12px; color: #3d2510; line-height: 1.5; margin: 0; margin-bottom: 4px;">{{ $n['text'] }}</p>
                            <span style="font-size: 11px; color: #9c8f7a;">{{ $n['time'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── MY BOOKINGS ── --}}
<div data-panel="bookings" hidden>
    <div class="dash-pills--customer" data-filter-group="customer-bookings" style="display: flex; gap: 10px; margin-bottom: 20px;">
        @foreach (['All', 'Confirmed', 'Pending', 'Completed', 'Cancelled'] as $filter)
            <button type="button" class="dash-pill {{ $filter === 'All' ? 'active' : '' }}" data-filter-value="{{ $filter }}">{{ $filter }}</button>
        @endforeach
    </div>
    <div style="{{ $card }}">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #faf8f4;">
                    @foreach (['Booking ID', 'Service', 'Date & Time', 'Guests', 'Amount', 'Status', 'Action'] as $heading)
                        <th style="padding: 13px 16px; {{ $th }}">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $b)
                    <tr data-filter-item="customer-bookings" data-filter-key="{{ $b['status'] }}" style="border-top: 1px solid #f0ebe2;">
                        <td style="padding: 14px 16px; font-size: 13px; color: #9c8f7a; font-weight: 500;">{{ $b['id'] }}</td>
                        <td style="padding: 14px 16px;">
                            <div style="font-weight: 600; font-size: 13px; color: #1a110a;">{{ $b['service'] }}</div>
                            <div style="font-size: 11px; color: #9c8f7a;">{{ $b['category'] }}</div>
                        </td>
                        <td style="padding: 14px 16px;">
                            <div style="font-size: 13px; color: #3d2510;">{{ $b['date'] }}</div>
                            <div style="font-size: 11px; color: #9c8f7a;">{{ $b['time'] }}</div>
                        </td>
                        <td style="padding: 14px 16px; font-size: 13px; color: #3d2510;">{{ $b['guests'] }} pax</td>
                        <td style="padding: 14px 16px; font-size: 13px; font-weight: 600; color: #1a110a;">{{ $b['amount'] }}</td>
                        <td style="padding: 14px 16px;"><x-status-badge :status="$b['status']" /></td>
                        <td style="padding: 14px 16px;">
                            <button type="button" data-modal-open="booking-{{ $b['id'] }}" style="padding: 6px 14px; border-radius: 6px; border: 1px solid #c9a84c; background: transparent; color: #c9a84c; font-size: 12px; font-weight: 600; cursor: pointer;">View</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ── NOTIFICATIONS ── --}}
<div data-panel="notifications" hidden style="max-width: 700px;">
    <div style="{{ $card }}">
        <div style="padding: 18px 20px; border-bottom: 1px solid #e8dfd0; display: flex; justify-content: space-between;">
            <span style="font-weight: 600; font-size: 14px; color: #1a110a;">All Notifications</span>
            <button type="button" data-mark-all-read style="background: none; border: none; color: #c9a84c; font-size: 13px; cursor: pointer;">Mark all as read</button>
        </div>
        @foreach ($notifications as $i => $n)
            <div data-notification="{{ $i }}" style="padding: 18px 20px; border-bottom: {{ $i < count($notifications) - 1 ? '1px solid #f0ebe2' : 'none' }}; background: {{ $n['unread'] ? '#fffdf7' : '#fff' }}; display: flex; gap: 14px; align-items: flex-start;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #f5f0e8; border: 1px solid #e8dfd0; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">{{ $n['icon'] }}</div>
                <div style="flex: 1;">
                    <p style="font-size: 14px; color: #1a110a; margin: 0 0 6px; line-height: 1.5;">{{ $n['text'] }}</p>
                    <span style="font-size: 12px; color: #9c8f7a;">{{ $n['time'] }}</span>
                </div>
                @if ($n['unread'])
                    <div data-unread-dot style="width: 8px; height: 8px; border-radius: 50%; background: #c9a84c; margin-top: 6px; flex-shrink: 0;"></div>
                @endif
            </div>
        @endforeach
    </div>
</div>

{{-- ── BOOKING STATUS TRACKER ── --}}
<div data-panel="status" hidden style="max-width: 720px;">
    <p style="font-size: 13px; color: #6b5a47; margin-bottom: 20px;">
        Live status of your active bookings. Updates are reflected in
        real time.
    </p>
    @php($steps = ['Submitted', 'Under Review', 'Confirmed', 'In Progress', 'Completed'])
    @foreach ($tracked as $b)
        @php($stepIdx = ['Pending' => 1, 'Confirmed' => 2, 'Completed' => 4][$b['status']] ?? 0)
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e8dfd0; padding: 22px 24px; margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px;">
                <div>
                    <div style="font-weight: 700; font-size: 15px; color: #1a110a; margin-bottom: 2px;">{{ $b['service'] }}</div>
                    <div style="font-size: 12px; color: #9c8f7a;">{{ $b['id'] }} · {{ $b['date'] }} · {{ $b['guests'] }} pax</div>
                </div>
                <x-status-badge :status="$b['status']" padding="4px 12px" :weight="700" />
            </div>
            <div style="display: flex; align-items: center; gap: 0px;">
                @foreach ($steps as $i => $step)
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; position: relative;">
                        @if ($i < count($steps) - 1)
                            <div style="position: absolute; top: 11px; left: 50%; width: 100%; height: 2px; background: {{ $i < $stepIdx ? '#c9a84c' : '#e8dfd0' }};"></div>
                        @endif
                        <div style="width: 24px; height: 24px; border-radius: 50%; background: {{ $i <= $stepIdx ? '#c9a84c' : '#e8dfd0' }}; border: 2px solid {{ $i <= $stepIdx ? '#c9a84c' : '#ddd5c8' }}; display: flex; align-items: center; justify-content: center; z-index: 1; font-size: 12px; color: {{ $i <= $stepIdx ? '#1a110a' : '#9c8f7a' }}; font-weight: 700;">{{ $i < $stepIdx ? '✓' : ($i === $stepIdx ? '●' : '') }}</div>
                        <div style="font-size: 10px; color: {{ $i <= $stepIdx ? '#c9a84c' : '#9c8f7a' }}; font-weight: {{ $i === $stepIdx ? 700 : 400 }}; margin-top: 6px; text-align: center; white-space: nowrap;">{{ $step }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

{{-- ── RATINGS & REVIEWS ── --}}
<div data-panel="reviews" hidden style="max-width: 720px;">
    <div style="background: #fff8e1; border: 1px solid #f9a825; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; font-size: 13px; color: #e65100;">
        ⭐ You have <strong>{{ $pendingReviews }} completed bookings</strong> pending review.
        Help future customers by sharing your experience!
    </div>
    @foreach ($completed as $b)
        <div data-review-card style="background: #fff; border-radius: 12px; border: 1px solid #e8dfd0; padding: 22px 24px; margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                <div>
                    <div style="font-weight: 700; font-size: 15px; color: #1a110a; margin-bottom: 2px;">{{ $b['service'] }}</div>
                    <div style="font-size: 12px; color: #9c8f7a;">{{ $b['id'] }} · {{ $b['date'] }} · {{ $b['category'] }}</div>
                </div>
                <span data-review-done hidden style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: #e8f5e9; color: #2e7d32;">✓ Reviewed</span>
            </div>
            @php($booking = $b['booking'])
            @if (! $booking->review)
                <form method="POST" action="{{ route('customer.bookings.reviews.store', $booking) }}" data-review-form>
                    @csrf
                <div style="margin-bottom: 12px;">
                    <div style="font-size: 13px; font-weight: 600; color: #6b5a47; margin-bottom: 8px;">Your Rating</div>
                    <input type="hidden" name="rating" data-review-rating>
                    <div style="display: flex; gap: 4px;">
                        @for ($star = 1; $star <= 5; $star++)
                            <button type="button" class="review-star" data-star="{{ $star }}" aria-label="{{ $star }} star{{ $star > 1 ? 's' : '' }}">★</button>
                        @endfor
                    </div>
                </div>
                <div style="margin-bottom: 14px;">
                    <div style="font-size: 13px; font-weight: 600; color: #6b5a47; margin-bottom: 8px;">Your Review</div>
                    <textarea name="comment" data-review-text placeholder="Share your experience with this vendor..." style="width: 100%; padding: 10px 12px; border: 1px solid #ddd5c8; border-radius: 8px; font-size: 13px; color: #1a110a; outline: none; background: #faf8f4; min-height: 80px; resize: vertical; box-sizing: border-box;"></textarea>
                </div>
                    <button type="submit" data-review-submit style="padding: 9px 24px; border-radius: 8px; border: none; background: #1a110a; color: #f5f0e8; font-size: 13px; font-weight: 600; cursor: pointer;">Submit Review</button>
                </form>
            @else
                <div style="padding: 14px 16px; background: #faf8f4; border-radius: 8px;">
                    <div style="color: #c9a84c; font-size: 20px; margin-bottom: 6px; letter-spacing: 2px;">{{ str_repeat('★', $booking->review->rating) }}</div>
                    <p style="font-size: 13px; color: #3d2510; margin: 0; font-style: italic;">{{ $booking->review->comment ?: 'No written review.' }}</p>
                </div>
            @endif
            <div data-review-result hidden style="padding: 14px 16px; background: #faf8f4; border-radius: 8px;">
                <div data-review-stars style="color: #c9a84c; font-size: 20px; margin-bottom: 6px; letter-spacing: 2px;"></div>
                <p data-review-quote style="font-size: 13px; color: #3d2510; margin: 0; font-style: italic;"></p>
            </div>
        </div>
    @endforeach
</div>

{{-- ── FAVORITES ── --}}
<div data-panel="favorites" hidden style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px;">
    @foreach ($favorites as $f)
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e8dfd0; padding: 20px; position: relative;">
            <button type="button" data-wishlist aria-label="Remove from favorites" style="position: absolute; top: 14px; right: 14px; background: none; border: none; color: #e53935; font-size: 20px; cursor: pointer;">♥</button>
            <div style="width: 48px; height: 48px; border-radius: 10px; background: #f5f0e8; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 12px;">{{ $f['icon'] }}</div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 15px; font-weight: 700; color: #1a110a; margin-bottom: 4px;">{{ $f['name'] }}</h3>
            <div style="font-size: 12px; color: #9c8f7a; margin-bottom: 8px;">{{ $f['category'] }} · 📍 {{ $f['location'] }}</div>
            <div style="color: #c9a84c; font-size: 13px; margin-bottom: 12px;">{{ str_repeat('★', (int) floor($f['rating'])) }} <span style="color: #6b5a47;">{{ $f['rating'] }}</span></div>
            <div style="font-weight: 700; color: #1a110a; font-size: 15px; margin-bottom: 14px;">{{ $f['price'] }}</div>
            <button type="button" data-href="{{ route('services.index') }}" style="width: 100%; padding: 9px; border-radius: 8px; background: #1a110a; color: #f5f0e8; border: none; font-size: 13px; font-weight: 600; cursor: pointer;">Book Now</button>
        </div>
    @endforeach
</div>

{{-- ── PROFILE ── --}}
<div data-panel="profile" hidden style="max-width: 640px;">
    <form method="POST" action="{{ route('profile.update') }}" style="background: #fff; border-radius: 12px; border: 1px solid #e8dfd0; padding: 28px;">
        @csrf
        @method('PUT')
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 28px; padding-bottom: 24px; border-bottom: 1px solid #e8dfd0;">
            <div style="width: 72px; height: 72px; border-radius: 50%; background: #3d2510; border: 3px solid #c9a84c; display: flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 700; color: #c9a84c;">{{ $user->initials() }}</div>
            <div>
                <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #1a110a; margin-bottom: 4px;">{{ $user->name }}</h3>
                <div style="font-size: 13px; color: #9c8f7a;">Customer · Member since {{ $user->created_at->format('F Y') }}</div>
            </div>
            <span style="margin-left: auto; color: #9c8f7a; font-size: 12px;">Profile photo</span>
        </div>
        @foreach ([['Full Name', 'name', $user->name], ['Email Address', null, $user->email], ['Phone Number', 'phone', $user->phone]] as [$label, $name, $value])
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 500; color: #6b5a47; margin-bottom: 8px;">{{ $label }}</label>
                <input @if ($name) name="{{ $name }}" @else disabled @endif value="{{ $value }}" style="width: 100%; border: 1px solid #ddd5c8; border-radius: 8px; padding: 12px 14px; font-size: 14px; color: #1a110a; outline: none; box-sizing: border-box; background: #faf8f4;">
            </div>
        @endforeach
        <div style="display: flex; align-items: center; gap: 14px;">
            <button type="submit" style="padding: 12px 32px; border-radius: 8px; background: #1a110a; color: #f5f0e8; border: none; font-size: 14px; font-weight: 600; cursor: pointer;">Save Changes</button>
        </div>
    </form>
</div>

{{-- ── HELP & SUPPORT ── --}}
<div data-panel="help" hidden style="max-width: 720px;">
    <div style="background: #1a110a; border-radius: 12px; padding: 24px 28px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
        <div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #f5f0e8; margin: 0 0 6px;">Need help? We're here 24/7.</h3>
            <p style="color: #e8dfd099; font-size: 13px; margin: 0;">
                Reach us by email, phone, or live chat — we typically
                respond within 1 hour.
            </p>
        </div>
        <div style="display: flex; gap: 10px; flex-shrink: 0;">
            <button type="button" style="padding: 10px 20px; border-radius: 8px; background: #c9a84c; border: none; color: #1a110a; font-size: 13px; font-weight: 700; cursor: pointer;">💬 Live Chat</button>
            <button type="button" style="padding: 10px 20px; border-radius: 8px; border: 1px solid #c9a84c55; background: transparent; color: #c9a84c; font-size: 13px; font-weight: 500; cursor: pointer;">📧 Email Us</button>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 24px;">
        @foreach ([['📧', 'Email', 'support@eventlynk.com'], ['📞', 'Phone', '+63 2 8888-EVNT'], ['🕐', 'Hours', '24/7 support available']] as [$icon, $label, $value])
            <div style="background: #fff; border-radius: 10px; border: 1px solid #e8dfd0; padding: 18px; text-align: center;">
                <div style="font-size: 24px; margin-bottom: 8px;">{{ $icon }}</div>
                <div style="font-size: 12px; color: #9c8f7a; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px;">{{ $label }}</div>
                <div style="font-size: 13px; color: #1a110a; font-weight: 600;">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div style="{{ $card }}">
        <div style="padding: 18px 22px; border-bottom: 1px solid #e8dfd0;">
            <h3 style="{{ $cardTitle }}">Frequently Asked Questions</h3>
        </div>
        @foreach ($faqs as $faq)
            <div data-faq style="border-bottom: 1px solid #f0ebe2;">
                <button type="button" data-faq-toggle aria-expanded="false" style="width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 16px 22px; background: none; border: none; cursor: pointer; text-align: left; gap: 12px;">
                    <span style="font-size: 14px; font-weight: 600; color: #1a110a;">{{ $faq['q'] }}</span>
                    <span data-faq-icon style="color: #c9a84c; font-size: 18px; flex-shrink: 0; transition: transform 0.2s; display: inline-block; transform: none;">+</span>
                </button>
                <div data-faq-answer hidden style="padding: 0 22px 16px; font-size: 14px; color: #6b5a47; line-height: 1.7;">{{ $faq['a'] }}</div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('modals')
@foreach ($bookings as $b)
    @php($modalBadge = ['Confirmed' => ['#e8f5e9', '#2e7d32'], 'Pending' => ['#fff8e1', '#e65100']][$b['status']] ?? ['#f5f5f5', '#555'])
    <x-dashboard.modal id="booking-{{ $b['id'] }}" panel-style="background: #fff; border-radius: 16px; padding: 32px; width: 440px; box-shadow: 0 24px 64px rgba(0,0,0,0.3); border: 1px solid #e8dfd0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #1a110a; margin: 0;">Booking Details</h3>
            <button type="button" data-modal-close aria-label="Close" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #6b5a47;">✕</button>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <span style="font-size: 13px; color: #6b5a47; font-weight: 500;">{{ $b['id'] }}</span>
            <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: {{ $modalBadge[0] }}; color: {{ $modalBadge[1] }};">{{ $b['status'] }}</span>
        </div>
        @foreach ([['Service', $b['service']], ['Vendor', $b['vendor']], ['Category', $b['category']], ['Date', $b['date']], ['Time', $b['time']], ['Guests', $b['guests'].' pax'], ['Amount', $b['amount']], ['Paid', '₱'.number_format((float) $b['booking']->amount_paid, 2)], ['Balance', '₱'.number_format($b['booking']->balance(), 2)]] as [$label, $value])
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0ebe2;">
                <span style="font-size: 13px; color: #6b5a47;">{{ $label }}</span>
                <span style="font-size: 13px; color: #1a110a; font-weight: 600;">{{ $value }}</span>
            </div>
        @endforeach
        <div style="margin-top: 20px; font-size: 12px; color: #6b5a47;">
            <strong>Assigned staff</strong>
            <div style="margin: 6px 0 12px;">{{ $b['booking']->staff->pluck('full_name')->join(', ') ?: 'Not yet assigned' }}</div>
            <strong>Payment history</strong>
            @forelse ($b['booking']->payments->sortByDesc('created_at') as $payment)
                <div style="display: flex; justify-content: space-between; padding: 5px 0;"><span>{{ $payment->type }} · {{ $payment->method }}</span><span>₱{{ number_format((float) $payment->amount, 2) }}</span></div>
            @empty
                <div style="margin-top: 6px;">No payments recorded yet.</div>
            @endforelse
        </div>
        <div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" data-modal-close style="flex: 1; padding: 11px; border-radius: 8px; border: 1px solid #e8dfd0; background: #faf8f4; color: #3d2510; font-size: 13px; font-weight: 500; cursor: pointer;">Close</button>
            @if ($b['booking']->paymentOptions() !== [])
                <a href="{{ route('customer.payments.create', $b['booking']) }}" style="flex: 1; padding: 11px; border-radius: 8px; background: #c9a84c; color: #1a110a; font-size: 13px; font-weight: 700; text-align: center;">Pay balance</a>
            @endif
            <a href="{{ route('bookings.slip', $b['booking']) }}" style="flex: 1; padding: 11px; border-radius: 8px; background: #1a110a; color: #f5f0e8; font-size: 13px; text-align: center;">Slip PDF</a>
            <a href="{{ route('bookings.invoice', $b['booking']) }}" style="flex: 1; padding: 11px; border-radius: 8px; background: #1a110a; color: #f5f0e8; font-size: 13px; text-align: center;">Invoice PDF</a>
            <button type="button" data-href="{{ route('services.index') }}" style="flex: 2; padding: 11px; border-radius: 8px; border: none; background: #1a110a; color: #f5f0e8; font-size: 13px; font-weight: 600; cursor: pointer;">Book Again</button>
            @if ($b['status'] === 'Pending')
                <button type="button" data-modal-open="cancel-booking-{{ $b['id'] }}" style="flex: 1 0 100%; padding: 9px; border-radius: 8px; border: 1px solid #c62828; background: transparent; color: #c62828; font-size: 12px; cursor: pointer;">Cancel pending booking</button>
            @endif
        </div>
    </x-dashboard.modal>
    @if ($b['status'] === 'Pending')
        <x-dashboard.modal id="cancel-booking-{{ $b['id'] }}" panel-style="background: #fff; border-radius: 16px; padding: 28px; width: 400px; box-shadow: 0 24px 64px rgba(0,0,0,0.3);">
            <h3 style="font-family: 'Playfair Display', serif; color: #1a110a;">Cancel booking?</h3>
            <p style="color: #6b5a47; font-size: 13px;">This will release the vendor's date for {{ $b['id'] }}. This action cannot be undone.</p>
            <form method="POST" action="{{ route('customer.bookings.cancel', $b['booking']) }}" style="display: flex; gap: 10px;">
                @csrf
                <input type="hidden" name="confirmation" value="1">
                <button type="button" data-modal-close style="flex: 1; padding: 10px; border: 1px solid #e8dfd0; background: #faf8f4; border-radius: 8px;">Keep booking</button>
                <button type="submit" style="flex: 1; padding: 10px; border: none; background: #c62828; color: #fff; border-radius: 8px;">Yes, cancel</button>
            </form>
        </x-dashboard.modal>
    @endif
@endforeach
@endsection

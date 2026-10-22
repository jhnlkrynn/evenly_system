@extends('layouts.base')

@section('title', 'Book '.$service->name.' · Eventlynk')

{{--
    Three-stage booking flow (BookingPage.tsx design) posting to BookingController@store.
    resources/js/eventlynk/booking.js keeps the steps, calendar, estimate and the hidden
    inputs below in sync; StoreBookingRequest validates everything again on the server.
    Step wrappers use display: contents so their sections stay direct children of the
    .booking-main-column grid.
--}}

@section('body')
<main class="booking-page" data-booking
    data-initial="{{ json_encode($initial) }}"
    data-initial-step="{{ $initialStep }}"
    data-pricing="{{ json_encode($pricing) }}"
    data-booked-url="{{ route('vendors.booked-dates', $vendor) }}"
    data-today="{{ now()->toDateString() }}">
    @include('bookings.partials.header')

    <form class="booking-shell" method="POST" action="{{ route('customer.bookings.store') }}" data-booking-form novalidate>
        @csrf
        <input type="hidden" name="service_id" value="{{ $service->id }}">
        <input type="hidden" name="event_type" data-field="occasion">
        <input type="hidden" name="event_date" data-field="date">
        <input type="hidden" name="pax" data-field="guests">
        <input type="hidden" name="needs_decoration" data-field="decoration">
        <input type="hidden" name="serving_count" data-field="serving">
        <input type="hidden" name="dishwashing_count" data-field="dishwashing">
        <input type="hidden" name="theme" data-field="theme">

        <p class="booking-eyebrow">Book an event service · {{ $vendor->business_name }}</p>
        <h1 data-step-title
            data-title-1="Tell us about your event"
            data-title-2="Choose your services"
            data-title-3="Review and confirm">Tell us about your event</h1>

        <div class="booking-progress">
            @foreach ([[1, 'Event details'], [2, 'Services'], [3, 'Review & confirm']] as [$number, $label])
                <button type="button" class="{{ $number === 1 ? 'active' : '' }}" data-progress="{{ $number }}" @disabled($number > 1)>
                    <i></i>
                    <span data-progress-label>Step {{ $number }}</span>
                    <strong>{{ $label }}</strong>
                </button>
            @endforeach
        </div>

        @if ($errors->any())
            <div class="auth-message error booking-errors" role="alert">
                @foreach ($errors->all() as $message)
                    <div>{{ $message }}</div>
                @endforeach
            </div>
        @endif

        <div class="booking-layout">
            <div class="booking-main-column">
                {{-- STEP 1 · Event details --}}
                <div data-step="1" style="display: contents;">
                    <section class="booking-card contact-card">
                        <div>
                            <strong>Selected service</strong>
                            <span>{{ $service->name }} · {{ $vendor->business_name }}</span>
                        </div>
                        <button type="button" data-href="{{ route('services.show', $service) }}">Change</button>
                    </section>

                    <section class="booking-card occasion-card">
                        <div class="booking-card-heading">
                            <h2>What’s the occasion?</h2>
                            <p>Helps vendors suggest the right setup.</p>
                        </div>
                        <div class="choice-pills">
                            @foreach ($eventTypes as $type)
                                <button type="button" data-occasion="{{ $type }}">{{ $type }}</button>
                            @endforeach
                        </div>
                    </section>

                    <section class="booking-card date-card">
                        <div class="booking-card-heading">
                            <h2>When is it?</h2>
                            <p>Dates {{ $vendor->short_name ?? $vendor->business_name }} is already booked are greyed out with a crossed-out number.</p>
                        </div>
                        <div class="date-grid">
                            <div class="calendar-panel">
                                <div class="calendar-title">
                                    <button type="button" data-month-step="-1" aria-label="Previous month">‹</button>
                                    <strong data-month-label></strong>
                                    <button type="button" data-month-step="1" aria-label="Next month">›</button>
                                </div>
                                <div class="calendar-grid" data-calendar role="grid" aria-label="Event date"></div>
                                <div class="calendar-legend">
                                    <span><i class="free"></i>Available</span>
                                    <span><i></i>Booked</span>
                                </div>
                            </div>
                            <div class="time-panel">
                                <div class="selected-date">
                                    <span>Selected date</span>
                                    <strong data-bind="dateLong">Pick a date</strong>
                                    <small data-date-status>Choose a date on the calendar</small>
                                </div>
                                <div class="time-fields">
                                    <label>
                                        Starts
                                        <input type="time" name="start_time" data-input="startTime" required>
                                    </label>
                                    <label>
                                        Ends
                                        <input type="time" name="end_time" data-input="endTime" required>
                                    </label>
                                </div>
                                <p>Setup crews arrive before the selected start time.</p>
                            </div>
                        </div>
                    </section>

                    <section class="booking-card venue-card">
                        <div class="booking-card-heading">
                            <h2>Where, and how many guests?</h2>
                        </div>
                        <label class="booking-field">
                            <span>Venue name or address</span>
                            <input name="venue" data-input="venue" maxlength="255" placeholder="e.g. Casa del Campo Events Hall" required>
                        </label>
                        <label class="booking-field">
                            <span>Venue barangay</span>
                            <x-searchable-select :options="$barangays->all()" :value="$initial['barangay']" name="barangay" placeholder="Select barangay..." all-label="Select barangay..." data-venue-select />
                            <small>Within service area · travel fee may apply</small>
                        </label>
                        <div class="guest-control">
                            <span>Number of guests</span>
                            <div>
                                <button type="button" data-guests-step="-10">−</button>
                                <strong data-bind="guests">100</strong>
                                <button type="button" data-guests-step="10">+</button>
                                @foreach ([50, 100, 150, 200] as $amount)
                                    <button type="button" data-guests-preset="{{ $amount }}">{{ $amount }}{{ $amount === 200 ? '+' : '' }}</button>
                                @endforeach
                            </div>
                            <small>We use this to suggest how many staff you’ll need.</small>
                        </div>
                    </section>

                    <section class="booking-card contact-card">
                        <div>
                            <strong>Contact for this booking</strong>
                            <span>{{ auth()->user()->name }} · {{ auth()->user()->phone ?? 'Add a mobile number' }}</span>
                        </div>
                        <button type="button" data-href="{{ route('profile.edit') }}">Change</button>
                    </section>
                    <p class="booking-step-error" data-step-error="1" role="alert" hidden></p>
                    <div class="booking-actions end">
                        <button type="button" class="primary" data-go-step="2">Continue to services →</button>
                    </div>
                </div>

                {{-- STEP 2 · Services --}}
                <div data-step="2" style="display: contents;" hidden>
                    <section class="package-banner">
                        <div>
                            <strong>Booking all three? Get the Full Package</strong>
                            <span>Bundle services and keep one coordinated team.</span>
                        </div>
                        <button type="button" data-full-package>Switch to package</button>
                    </section>

                    <section class="service-choice" data-service-card="decoration">
                        <div class="service-choice-head">
                            <label>
                                <input type="checkbox" data-service-toggle="decoration">
                                <span>
                                    <strong>Decoration</strong>
                                    <small>Full setup — tarps, balloons, tablecloths, and centerpieces.</small>
                                </span>
                            </label>
                            <div>
                                <strong>₱{{ number_format($pricing['decoration']) }}</strong>
                                <small>flat rate</small>
                            </div>
                        </div>
                        <div class="theme-row" data-service-body>
                            <span>Theme color</span>
                            <div>
                                @foreach ($themes as $color)
                                    <button type="button" data-theme="{{ $color }}">{{ $color }}</button>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    @foreach ([
                        ['key' => 'serving', 'title' => 'Serving staff', 'description' => 'Uniformed, trained staff for buffets, cocktails, and plated dinners.'],
                        ['key' => 'dishwashing', 'title' => 'Dishwashing', 'description' => 'Post-event crew with complete equipment.'],
                    ] as $item)
                        <section class="service-choice" data-service-card="{{ $item['key'] }}">
                            <div class="service-choice-head">
                                <label>
                                    <input type="checkbox" data-service-toggle="{{ $item['key'] }}">
                                    <span>
                                        <strong>{{ $item['title'] }}</strong>
                                        <small>{{ $item['description'] }}</small>
                                    </span>
                                </label>
                                <div>
                                    <strong>₱{{ number_format($pricing[$item['key']]) }}</strong>
                                    <small>per person</small>
                                </div>
                            </div>
                            <div class="quantity-row" data-service-body>
                                <div>
                                    <span>How many staff?</span>
                                    <small>Suggested for <data data-bind="guests">100</data> guests: <data data-bind="{{ $item['key'] }}Suggested"></data></small>
                                </div>
                                <div class="quantity-stepper">
                                    <button type="button" data-qty-step="-1" data-qty-key="{{ $item['key'] }}" aria-label="Fewer {{ strtolower($item['title']) }}">−</button>
                                    <strong data-bind="{{ $item['key'] }}"></strong>
                                    <button type="button" data-qty-step="1" data-qty-key="{{ $item['key'] }}" aria-label="More {{ strtolower($item['title']) }}">+</button>
                                </div>
                                <strong>₱<data data-bind="{{ $item['key'] }}Cost"></data></strong>
                            </div>
                        </section>
                    @endforeach

                    <section class="booking-card notes-card">
                        <label>
                            Anything vendors should know? <span>(optional)</span>
                            <textarea name="special_requests" data-input="notes" maxlength="1000" placeholder="Theme details, stage backdrop, venue notes..."></textarea>
                        </label>
                    </section>
                    <p class="booking-step-error" data-step-error="2" role="alert" hidden></p>
                    <div class="booking-actions">
                        <button type="button" class="secondary" data-go-step="1">← Back</button>
                        <button type="button" class="primary" data-go-step="3" data-requires-services>Review booking →</button>
                    </div>
                </div>

                {{-- STEP 3 · Review & confirm --}}
                <div data-step="3" style="display: contents;" hidden>
                    <section class="booking-card review-card">
                        <div class="review-title">
                            <h2>Event</h2>
                            <button type="button" data-go-step="1">Edit</button>
                        </div>
                        <dl class="event-review">
                            <div>
                                <dt>Occasion</dt>
                                <dd data-bind="eventLabel"></dd>
                            </div>
                            <div>
                                <dt>Date &amp; time</dt>
                                <dd><data data-bind="dateLong"></data> · <data data-bind="timeRange"></data></dd>
                            </div>
                            <div>
                                <dt>Venue</dt>
                                <dd data-bind="fullVenue"></dd>
                            </div>
                            <div>
                                <dt>Vendor</dt>
                                <dd>{{ $vendor->business_name }} · {{ $service->name }}</dd>
                            </div>
                        </dl>
                    </section>
                    <section class="booking-card review-card">
                        <div class="review-title">
                            <h2>Services</h2>
                            <button type="button" data-go-step="2">Edit</button>
                        </div>
                        <div class="service-review">
                            <div data-review-line="decoration">
                                <span>
                                    <strong>Decoration</strong>
                                    <small><data data-bind="theme"></data> theme · flat rate</small>
                                </span>
                                <strong>₱<data data-bind="decorationCost"></data></strong>
                            </div>
                            <div data-review-line="serving">
                                <span>
                                    <strong>Serving staff</strong>
                                    <small><data data-bind="serving"></data> × ₱{{ number_format($pricing['serving']) }}</small>
                                </span>
                                <strong>₱<data data-bind="servingCost"></data></strong>
                            </div>
                            <div data-review-line="dishwashing">
                                <span>
                                    <strong>Dishwashing</strong>
                                    <small><data data-bind="dishwashing"></data> × ₱{{ number_format($pricing['dishwashing']) }}</small>
                                </span>
                                <strong>₱<data data-bind="dishwashingCost"></data></strong>
                            </div>
                            <p data-review-notes><strong>Note to vendor:</strong> <data data-bind="notes"></data></p>
                        </div>
                    </section>
                    <section class="booking-card payment-card">
                        <div class="booking-card-heading">
                            <h2>How you’ll pay the reservation fee</h2>
                            <p>You’ll pay the 30% fee on the next screen (demo payment). Settle the balance with your vendor on or before the event.</p>
                        </div>
                        <div class="payment-grid">
                            @foreach ([['GCash', 'Demo e-wallet payment'], ['Maya', 'Demo e-wallet payment'], ['Cash', 'Pay the vendor in person']] as [$name, $detail])
                                <label>
                                    <input type="radio" name="payment_method" value="{{ $name }}" data-payment>
                                    <span>
                                        <strong>{{ $name }}</strong>
                                        <small>{{ $detail }}</small>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </section>
                    <section class="booking-card terms-card">
                        <p>
                            <strong>Cancellation:</strong> Free cancellation until 7
                            days before the event. After that, the reservation fee is
                            non-refundable.
                        </p>
                        <label>
                            <input type="checkbox" name="terms" value="1" data-terms>
                            I’ve checked the details above and agree to the booking terms.
                        </label>
                    </section>
                    <div class="booking-actions">
                        <button type="button" class="secondary" data-go-step="2">← Back</button>
                        <button type="submit" class="pay-button" data-submit-booking disabled>Confirm booking · ₱<data data-bind="total"></data></button>
                    </div>
                </div>
            </div>

            {{-- Sidebars: one per step so :only-child matches the design's layout --}}
            <aside class="booking-sidebar" data-sidebar-step="1">
                <section class="booking-summary-dark">
                    <h2>Your booking</h2>
                    <dl>
                        <div>
                            <dt>Vendor</dt>
                            <dd>{{ $vendor->short_name ?? $vendor->business_name }}</dd>
                        </div>
                        <div>
                            <dt>Event</dt>
                            <dd data-bind="occasion"></dd>
                        </div>
                        <div>
                            <dt>Date</dt>
                            <dd><data data-bind="dateShort"></data> · <data data-bind="timeRange"></data></dd>
                        </div>
                        <div>
                            <dt>Venue</dt>
                            <dd data-bind="fullVenue"></dd>
                        </div>
                        <div>
                            <dt>Guests</dt>
                            <dd data-bind="guests"></dd>
                        </div>
                    </dl>
                    <p>Pick services next to see your estimate.</p>
                </section>
                <section class="booking-side-card rates-card">
                    <h3>Starting rates</h3>
                    <dl>
                        <div>
                            <dt>Decoration</dt>
                            <dd>₱{{ number_format($pricing['decoration']) }} flat</dd>
                        </div>
                        <div>
                            <dt>Serving staff</dt>
                            <dd>₱{{ number_format($pricing['serving']) }} / person</dd>
                        </div>
                        <div>
                            <dt>Dishwashing</dt>
                            <dd>₱{{ number_format($pricing['dishwashing']) }} / person</dd>
                        </div>
                    </dl>
                    <p>A 30% reservation fee secures your date and is deducted from your total.</p>
                </section>
            </aside>

            <aside class="booking-sidebar" data-sidebar-step="2" hidden>
                <section class="booking-summary-dark estimate-card">
                    <h2>Estimate</h2>
                    <p class="estimate-meta" data-bind="estimateMeta"></p>
                    <dl>
                        <div data-estimate-line="decoration">
                            <dt>Decoration</dt>
                            <dd>₱<data data-bind="decorationCost"></data></dd>
                        </div>
                        <div data-estimate-line="serving">
                            <dt>Serving staff</dt>
                            <dd>₱<data data-bind="servingCost"></data></dd>
                        </div>
                        <div data-estimate-line="dishwashing">
                            <dt>Dishwashing</dt>
                            <dd>₱<data data-bind="dishwashingCost"></data></dd>
                        </div>
                    </dl>
                    <div class="estimate-total">
                        <span>Total</span>
                        <strong>₱<data data-bind="total"></data></strong>
                    </div>
                    <p>Due after you confirm: 30% reservation fee. The balance is paid to the vendor on the event day.</p>
                </section>
            </aside>

            <aside class="booking-sidebar" data-sidebar-step="3" hidden>
                <section class="booking-summary-dark payment-summary">
                    <h2>Payment summary</h2>
                    <dl>
                        <div>
                            <dt>Services total</dt>
                            <dd>₱<data data-bind="total"></data></dd>
                        </div>
                        <div>
                            <dt>Travel fee</dt>
                            <dd>₱0</dd>
                        </div>
                    </dl>
                    <div class="estimate-total">
                        <span>Reservation fee</span>
                        <strong>₱<data data-bind="reservationFee"></data></strong>
                    </div>
                    <p>Balance on event day: ₱<data data-bind="balance"></data></p>
                </section>
                <section class="booking-side-card next-card">
                    <h3>What happens next</h3>
                    <ol>
                        <li>Pay the reservation fee to secure your date.</li>
                        <li>{{ $vendor->short_name ?? $vendor->business_name }} confirms within 24 hours.</li>
                        <li>Download your booking slip and invoice anytime.</li>
                    </ol>
                </section>
            </aside>
        </div>
    </form>
    @include('bookings.partials.footer')
</main>
@endsection

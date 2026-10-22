@extends('layouts.base')

@section('title', 'Book an event service · Eventlynk')

{{--
    Three-stage booking flow (BookingPage.tsx). All three steps are rendered
    and resources/js/eventlynk/booking.js switches between them and keeps the
    totals, labels and summaries in sync. Step wrappers use display: contents
    so their sections stay direct children of the .booking-main-column grid.
--}}

@section('body')
<main class="booking-page" data-booking>
    @include('bookings.partials.header')

    <div class="booking-shell">
        <p class="booking-eyebrow">Book an event service</p>
        <h1 data-step-title
            data-title-1="Tell us about your event"
            data-title-2="Choose your services"
            data-title-3="Review and confirm">Tell us about your event</h1>

        <div class="booking-progress">
            @foreach ([[1, 'Event details'], [2, 'Services'], [3, 'Review & pay']] as [$number, $label])
                <button type="button" class="{{ $number === 1 ? 'active' : '' }}" data-progress="{{ $number }}" @disabled($number > 1)>
                    <i></i>
                    <span data-progress-label>Step {{ $number }}</span>
                    <strong>{{ $label }}</strong>
                </button>
            @endforeach
        </div>

        <div class="booking-layout">
            <div class="booking-main-column">
                {{-- STEP 1 · Event details --}}
                <div data-step="1" style="display: contents;">
                    <section class="booking-card occasion-card">
                        <div class="booking-card-heading">
                            <h2>What’s the occasion?</h2>
                            <p>Helps vendors suggest the right setup.</p>
                        </div>
                        <div class="choice-pills">
                            @foreach ($eventTypes as $type)
                                <button type="button" class="{{ $type === 'Debut' ? 'selected' : '' }}" data-occasion="{{ $type }}">{{ $type }}</button>
                            @endforeach
                        </div>
                    </section>

                    <section class="booking-card date-card">
                        <div class="booking-card-heading">
                            <h2>When is it?</h2>
                            <p>Availability dots show how many vendors are still free that day.</p>
                        </div>
                        <div class="date-grid">
                            <div class="calendar-panel">
                                <div class="calendar-title">
                                    <button type="button" aria-label="Previous month">‹</button>
                                    <strong>December 2026</strong>
                                    <button type="button" aria-label="Next month">›</button>
                                </div>
                                <div class="calendar-grid">
                                    @foreach (['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'] as $weekday)
                                        <span class="weekday">{{ $weekday }}</span>
                                    @endforeach
                                    <span></span>
                                    <span></span>
                                    @for ($day = 1; $day <= 31; $day++)
                                        <button type="button" class="{{ $day === 12 ? 'selected' : '' }}" data-day="{{ $day }}">
                                            <span>{{ $day }}</span>
                                            <i class="{{ in_array($day, [13, 18, 31], true) ? 'filling' : 'free' }}"></i>
                                        </button>
                                    @endfor
                                </div>
                                <div class="calendar-legend">
                                    <span><i class="free"></i>Many free</span>
                                    <span><i class="filling"></i>Filling up</span>
                                    <span><i></i>Fully booked</span>
                                </div>
                            </div>
                            <div class="time-panel">
                                <div class="selected-date">
                                    <span>Selected date</span>
                                    <strong>Sat, <data data-bind="date">12</data> Dec 2026</strong>
                                    <small>[14] vendors available</small>
                                </div>
                                <div class="time-fields">
                                    <label>
                                        Starts
                                        <input type="time" value="18:00" data-input="startTime">
                                    </label>
                                    <label>
                                        Ends
                                        <input type="time" value="23:00" data-input="endTime">
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
                            <span>Venue barangay</span>
                            <x-searchable-select :options="$barangays" value="Bucana" placeholder="Select barangay..." all-label="Select barangay..." data-venue-select />
                            <small>Within service area · travel fee may apply</small>
                        </label>
                        <div class="guest-control">
                            <span>Number of guests</span>
                            <div>
                                <button type="button" data-guests-step="-10">−</button>
                                <strong data-bind="guests">100</strong>
                                <button type="button" data-guests-step="10">+</button>
                                @foreach ([50, 100, 150, 200] as $amount)
                                    <button type="button" class="{{ $amount === 100 ? 'active' : '' }}" data-guests-preset="{{ $amount }}">{{ $amount }}{{ $amount === 200 ? '+' : '' }}</button>
                                @endforeach
                            </div>
                            <small>We use this to suggest how many staff you’ll need.</small>
                        </div>
                    </section>

                    <section class="booking-card contact-card">
                        <div>
                            <strong>Contact for this booking</strong>
                            <span><data data-bind="customerName">Naicel Apolona</data> · <data data-bind="contactNumber">+63 906 407 4540</data></span>
                        </div>
                        <button type="button" data-change-contact>Change</button>
                    </section>
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

                    <section class="service-choice selected" data-service-card="decoration">
                        <div class="service-choice-head">
                            <label>
                                <input type="checkbox" checked data-service-toggle="decoration">
                                <span>
                                    <strong>Decoration</strong>
                                    <small>Full setup — tarps, balloons, tablecloths, and centerpieces.</small>
                                </span>
                            </label>
                            <div>
                                <strong>₱5,000</strong>
                                <small>flat rate</small>
                            </div>
                        </div>
                        <div class="theme-row" data-service-body>
                            <span>Theme color</span>
                            <div>
                                @foreach (['Gold & ivory', 'Blush', 'Sage', 'Custom...'] as $color)
                                    <button type="button" class="{{ $color === 'Gold & ivory' ? 'active' : '' }}" data-theme="{{ $color }}">{{ $color }}</button>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    @foreach ([
                        ['key' => 'serving', 'title' => 'Serving staff', 'description' => 'Uniformed, trained staff for buffets, cocktails, and plated dinners.', 'rate' => '₱750', 'value' => 8, 'suggested' => 8],
                        ['key' => 'dishwashing', 'title' => 'Dishwashing', 'description' => 'Post-event crew with complete equipment.', 'rate' => '₱500', 'value' => 4, 'suggested' => 4],
                    ] as $service)
                        <section class="service-choice selected" data-service-card="{{ $service['key'] }}">
                            <div class="service-choice-head">
                                <label>
                                    <input type="checkbox" checked data-service-toggle="{{ $service['key'] }}">
                                    <span>
                                        <strong>{{ $service['title'] }}</strong>
                                        <small>{{ $service['description'] }}</small>
                                    </span>
                                </label>
                                <div>
                                    <strong>{{ $service['rate'] }}</strong>
                                    <small>per person</small>
                                </div>
                            </div>
                            <div class="quantity-row" data-service-body>
                                <div>
                                    <span>How many staff?</span>
                                    <small>Suggested for <data data-bind="guests">100</data> guests: {{ $service['suggested'] }}</small>
                                </div>
                                <div class="quantity-stepper">
                                    <button type="button" data-qty-step="-1" data-qty-key="{{ $service['key'] }}">−</button>
                                    <strong data-bind="{{ $service['key'] }}">{{ $service['value'] }}</strong>
                                    <button type="button" data-qty-step="1" data-qty-key="{{ $service['key'] }}">+</button>
                                </div>
                                <strong>₱<data data-bind="{{ $service['key'] }}Cost">{{ number_format($service['key'] === 'serving' ? 6000 : 2000) }}</data></strong>
                            </div>
                        </section>
                    @endforeach

                    <section class="booking-card notes-card">
                        <label>
                            Anything vendors should know? <span>(optional)</span>
                            <textarea data-input="notes" placeholder="Theme details, stage backdrop, venue notes...">18 roses setup, stage backdrop.</textarea>
                        </label>
                    </section>
                    <div class="booking-actions">
                        <button type="button" class="secondary" data-go-step="1">← Back</button>
                        <button type="button" class="primary" data-go-step="3" data-requires-services>Review booking →</button>
                    </div>
                </div>

                {{-- STEP 3 · Review & pay --}}
                <div data-step="3" style="display: contents;" hidden>
                    <section class="booking-card review-card">
                        <div class="review-title">
                            <h2>Event</h2>
                            <button type="button" data-go-step="1">Edit</button>
                        </div>
                        <dl class="event-review">
                            <div>
                                <dt>Occasion</dt>
                                <dd data-bind="eventLabel">Debut · 100 guests</dd>
                            </div>
                            <div>
                                <dt>Date &amp; time</dt>
                                <dd>Sat, <data data-bind="date">12</data> Dec 2026 · <data data-bind="startTime">18:00</data>–<data data-bind="endTime">23:00</data></dd>
                            </div>
                            <div>
                                <dt>Venue</dt>
                                <dd data-bind="fullVenue">Bucana, Nasugbu, Batangas</dd>
                            </div>
                            <div>
                                <dt>Contact</dt>
                                <dd><data data-bind="customerName">Naicel Apolona</data> · <data data-bind="contactNumber">+63 906 407 4540</data></dd>
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
                                    <small><data data-bind="theme">Gold &amp; ivory</data> theme · flat rate</small>
                                </span>
                                <strong>₱<data data-bind="decorationCost">5,000</data></strong>
                            </div>
                            <div data-review-line="serving">
                                <span>
                                    <strong>Serving staff</strong>
                                    <small><data data-bind="serving">8</data> × ₱750</small>
                                </span>
                                <strong>₱<data data-bind="servingCost">6,000</data></strong>
                            </div>
                            <div data-review-line="dishwashing">
                                <span>
                                    <strong>Dishwashing</strong>
                                    <small><data data-bind="dishwashing">4</data> × ₱500</small>
                                </span>
                                <strong>₱<data data-bind="dishwashingCost">2,000</data></strong>
                            </div>
                            <p data-review-notes><strong>Note to vendor:</strong> <data data-bind="notes">18 roses setup, stage backdrop.</data></p>
                        </div>
                    </section>
                    <section class="booking-card payment-card">
                        <div class="booking-card-heading">
                            <h2>Pay the reservation fee</h2>
                            <p>Settle the remaining balance with your vendor on or before the event.</p>
                        </div>
                        <div class="payment-grid">
                            @foreach ([['GCash', 'Instant confirmation'], ['Maya', 'Instant confirmation'], ['Debit / credit card', 'Visa or Mastercard'], ['Bank transfer', 'Confirmed in 1–2 business days']] as [$name, $detail])
                                <label class="{{ $name === 'GCash' ? 'selected' : '' }}">
                                    <input type="radio" name="payment" value="{{ $name }}" @checked($name === 'GCash') data-payment>
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
                            <input type="checkbox" data-terms>
                            I’ve checked the details above and agree to the booking terms.
                        </label>
                    </section>
                    <div class="booking-actions">
                        <button type="button" class="secondary" data-go-step="2">← Back</button>
                        <button type="button" class="pay-button" data-submit-booking disabled>Pay ₱<data data-bind="reservationFee">3,900</data> &amp; confirm booking</button>
                    </div>
                </div>
            </div>

            {{-- Sidebars: one per step so :only-child matches the React layout --}}
            <aside class="booking-sidebar" data-sidebar-step="1">
                <section class="booking-summary-dark">
                    <h2>Your booking</h2>
                    <dl>
                        <div>
                            <dt>Event</dt>
                            <dd data-bind="occasion">Debut</dd>
                        </div>
                        <div>
                            <dt>Date</dt>
                            <dd>Sat, <data data-bind="date">12</data> Dec · 6–11 PM</dd>
                        </div>
                        <div>
                            <dt>Venue</dt>
                            <dd data-bind="fullVenue">Bucana, Nasugbu, Batangas</dd>
                        </div>
                        <div>
                            <dt>Guests</dt>
                            <dd data-bind="guests">100</dd>
                        </div>
                    </dl>
                    <p>Pick services next to see your estimate.</p>
                </section>
                <section class="booking-side-card rates-card">
                    <h3>Starting rates</h3>
                    <dl>
                        <div>
                            <dt>Decoration</dt>
                            <dd>₱5,000 flat</dd>
                        </div>
                        <div>
                            <dt>Serving staff</dt>
                            <dd>₱750 / person</dd>
                        </div>
                        <div>
                            <dt>Dishwashing</dt>
                            <dd>₱500 / person</dd>
                        </div>
                    </dl>
                    <p>A 30% reservation fee secures your date and is deducted from your total.</p>
                </section>
            </aside>

            <aside class="booking-sidebar" data-sidebar-step="2" hidden>
                <section class="booking-summary-dark estimate-card">
                    <h2>Estimate</h2>
                    <p class="estimate-meta">Debut · Sat, 12 Dec · 100 guests</p>
                    <dl>
                        <div data-estimate-line="decoration">
                            <dt>Decoration</dt>
                            <dd>₱<data data-bind="decorationCost">5,000</data></dd>
                        </div>
                        <div data-estimate-line="serving">
                            <dt>Serving staff</dt>
                            <dd>₱<data data-bind="servingCost">6,000</data></dd>
                        </div>
                        <div data-estimate-line="dishwashing">
                            <dt>Dishwashing</dt>
                            <dd>₱<data data-bind="dishwashingCost">2,000</data></dd>
                        </div>
                    </dl>
                    <div class="estimate-total">
                        <span>Total</span>
                        <strong>₱<data data-bind="total">13,000</data></strong>
                    </div>
                    <p>Due today: 30% reservation fee. The balance is paid to the vendor on the event day.</p>
                </section>
            </aside>

            <aside class="booking-sidebar" data-sidebar-step="3" hidden>
                <section class="booking-summary-dark payment-summary">
                    <h2>Payment summary</h2>
                    <dl>
                        <div>
                            <dt>Services total</dt>
                            <dd>₱<data data-bind="total">13,000</data></dd>
                        </div>
                        <div>
                            <dt>Travel fee</dt>
                            <dd>₱0</dd>
                        </div>
                    </dl>
                    <div class="estimate-total">
                        <span>Due today</span>
                        <strong>₱<data data-bind="reservationFee">3,900</data></strong>
                    </div>
                    <p>Balance on event day: ₱<data data-bind="balance">9,100</data></p>
                </section>
                <section class="booking-side-card next-card">
                    <h3>What happens next</h3>
                    <ol>
                        <li>Your vendor confirms within 24 hours.</li>
                        <li>You can chat and track status from your dashboard.</li>
                        <li>If no vendor accepts, your fee is refunded in full.</li>
                    </ol>
                </section>
            </aside>
        </div>
    </div>
    @include('bookings.partials.footer')
</main>

<main class="booking-page booking-success" data-booking-success hidden>
    @include('bookings.partials.header')
    <section class="success-panel">
        <div class="success-mark">✓</div>
        <p class="booking-eyebrow">Booking confirmed</p>
        <h1>Your event is in motion.</h1>
        <p>
            We received your reservation fee and sent booking BK-009 to matching
            vendors in Nasugbu.
        </p>
        <dl>
            <div>
                <dt>Event</dt>
                <dd data-bind="eventLabel">Debut · 100 guests</dd>
            </div>
            <div>
                <dt>Date</dt>
                <dd>Sat, 12 Dec 2026</dd>
            </div>
            <div>
                <dt>Venue</dt>
                <dd data-bind="fullVenue">Bucana, Nasugbu, Batangas</dd>
            </div>
            <div>
                <dt>Paid today</dt>
                <dd>₱<data data-bind="reservationFee">3,900</data></dd>
            </div>
        </dl>
        <button type="button" data-href="{{ route('home') }}">Return home</button>
    </section>
</main>
@endsection

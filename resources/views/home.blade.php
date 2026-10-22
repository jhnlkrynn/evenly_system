@extends('layouts.landing')

@section('content')
    <section class="landing-hero">
        <div class="landing-container hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Event services · Nasugbu, Batangas</p>
                <h1>Create events.<br><em>Create memories.</em></h1>
                <p class="hero-intro">
                    Book decoration, serving staff, dishwashing, and complete event
                    packages from verified local vendors. Compare options, choose a
                    date, and track every detail in one place.
                </p>

                <form class="hero-search" action="{{ route('services.index') }}" method="GET">
                    <label>
                        <span>Service</span>
                        <input name="q" placeholder="Decoration, catering...">
                    </label>
                    <label>
                        <span>Location</span>
                        <select name="location">
                            <option value="">Any barangay</option>
                            <option>Poblacion</option>
                            <option>Wawa</option>
                            <option>Calayo</option>
                            <option>Bucana</option>
                        </select>
                    </label>
                    <label>
                        <span>Event date</span>
                        <input type="date" name="date">
                    </label>
                    <button type="submit">Search</button>
                </form>
                <div class="popular-row">
                    <span>Popular</span>
                    @foreach (['Debut', 'Wedding', 'Corporate gala', 'Birthday'] as $tag)
                        <button type="button" data-href="{{ route('services.index', ['q' => $tag]) }}">{{ $tag }}</button>
                    @endforeach
                </div>
            </div>

            <div class="hero-visual" aria-label="Elegant Eventlynk event setup">
                <img src="{{ asset('images/image-2.png') }}" alt="Elegant neutral wedding decoration setup">
                <x-booking-card
                    title="Full Package"
                    meta="Sat, 12 Dec · Calayo, Nasugbu"
                    note="Vendor confirmed · Setup team assigned"
                />
            </div>
        </div>
    </section>

    <section class="stats-strip" aria-label="Eventlynk platform statistics">
        <div class="landing-container stats-grid">
            @foreach ([['500+', 'Verified vendors'], ['10K+', 'Events booked'], ['98%', 'Customer satisfaction'], ['24h', 'Average response time']] as [$value, $label])
                <div>
                    <strong>{{ $value }}</strong>
                    <span>{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="landing-section services-section">
        <div class="landing-container">
            <div class="section-heading split-heading">
                <div>
                    <p class="eyebrow">Our services</p>
                    <h2>Everything your event<br>needs, booked in one place</h2>
                </div>
                <button type="button" class="arrow-link" data-href="{{ route('services.index') }}">Browse all services <span>→</span></button>
            </div>
            <div class="service-grid">
                @foreach ($services as $service)
                    <x-service-card :service="$service" />
                @endforeach
            </div>
        </div>
    </section>

    <section class="landing-section steps-section" id="how-it-works">
        <div class="landing-container">
            <div class="section-heading centered">
                <p class="eyebrow">How it works</p>
                <h2>From idea to event day in four steps</h2>
            </div>
            <div class="steps-grid">
                @foreach ($steps as $step)
                    <article class="{{ $loop->last ? 'dark' : '' }}">
                        <strong>{{ $step['number'] }}</strong>
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="landing-section audience-section">
        <div class="landing-container audience-grid">
            <article>
                <p class="eyebrow">For customers</p>
                <h2>Plan with confidence</h2>
                <ul>
                    <li>On-demand booking with transparent pricing</li>
                    <li>Verified local vendors and real customer reviews</li>
                    <li>Real-time booking status and event updates</li>
                </ul>
                <button type="button" class="dark-button" data-href="{{ route('services.index') }}">Book a service</button>
            </article>
            <article class="vendor-panel">
                <p class="eyebrow">For vendors</p>
                <h2>Grow your event business</h2>
                <ul>
                    <li>Reach customers actively planning events</li>
                    <li>Commission-based analytics and clear earnings</li>
                    <li>Schedule, service, and booking management</li>
                </ul>
                <button type="button" class="gold-button" data-href="{{ route('register', ['role' => 'vendor']) }}">Become a vendor</button>
            </article>
        </div>
    </section>

    <section class="landing-section reviews-section">
        <div class="landing-container">
            <div class="section-heading">
                <p class="eyebrow">Reviews</p>
                <h2>Loved by hosts across the Philippines</h2>
            </div>
            <div class="review-grid">
                @foreach ($reviews as $review)
                    <article>
                        <div class="stars">★★★★★</div>
                        <blockquote>“{{ $review['quote'] }}”</blockquote>
                        <div class="reviewer">
                            <span>{{ collect(explode(' ', $review['name']))->map(fn ($part) => $part[0])->join('') }}</span>
                            <p>
                                <strong>{{ $review['name'] }}</strong>
                                <small>{{ $review['event'] }}</small>
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="landing-section cta-section">
        <div class="landing-container">
            <div class="cta-panel">
                <h2>Ready to plan <em>your perfect event?</em></h2>
                <p>Create a free account and get quotes from verified vendors today.</p>
                <div>
                    <button type="button" class="gold-button" data-href="{{ route('register') }}">Get started — it’s free</button>
                    <button type="button" class="outline-button" data-href="{{ route('register', ['role' => 'vendor']) }}">Become a vendor</button>
                </div>
            </div>
        </div>
    </section>
@endsection

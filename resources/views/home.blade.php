@extends('layouts.landing')

@section('content')
    <section class="landing-hero" data-scroll-parallax>
        <div class="landing-container hero-grid">
            <div class="hero-copy" data-scroll-reveal="fade-up">
                <p class="eyebrow">Event services · Nasugbu, Batangas</p>
                <h1>Create events.<br><em>Create memories.</em></h1>
                <p class="hero-intro">
                    Book decoration, serving staff, dishwashing, and complete event
                    packages from verified local vendors. Compare options, choose a
                    date, and track every detail in one place.
                </p>

                <form class="hero-search" action="{{ route('services.index') }}" method="GET" data-clean-get>
                    <label>
                        <span>Service</span>
                        <input name="search" list="hero-categories" placeholder="Decoration, catering..." maxlength="100">
                        <datalist id="hero-categories">
                            @foreach ($categories as $category)
                                <option value="{{ $category->name }}"></option>
                            @endforeach
                        </datalist>
                    </label>
                    <label>
                        <span>Location</span>
                        <select name="barangay">
                            <option value="">Any barangay</option>
                            @foreach ($barangays as $barangay)
                                <option>{{ $barangay }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Event date</span>
                        <input type="date" name="date" min="{{ now()->toDateString() }}">
                    </label>
                    <button type="submit">Search</button>
                </form>
                <div class="popular-row">
                    <span>Popular</span>
                    @foreach (['Debut' => 'debut', 'Wedding' => 'wedding', 'Corporate gala' => 'corporate', 'Birthday' => 'birthday'] as $tag => $event)
                        <button type="button" data-href="{{ route('services.index', ['events' => [$event]]) }}">{{ $tag }}</button>
                    @endforeach
                </div>
            </div>

            <div class="hero-visual" data-scroll-reveal="fade-left" aria-label="Elegant Eventlynk event setup">
                <img src="{{ asset('images/image-2.png') }}" alt="Elegant neutral wedding decoration setup">
                <x-booking-card :booking="$latestBooking" />
            </div>
        </div>
    </section>

    <section class="stats-strip" aria-label="Eventlynk platform statistics">
        <div class="landing-container stats-grid">
            @foreach ([
                [number_format($stats['vendors']), 'Verified vendors'],
                [number_format($stats['completed']), 'Events completed'],
                [$stats['rating'] ? number_format($stats['rating'], 1).' ★' : '—', 'Average rating'],
                ['24h', 'Average response time'],
            ] as [$value, $label])
                <div data-scroll-reveal="fade-up">
                    <strong>{{ $value }}</strong>
                    <span>{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="landing-section services-section">
        <div class="landing-container">
            <div class="section-heading split-heading" data-scroll-reveal="fade-up">
                <div>
                    <p class="eyebrow">Our services</p>
                    <h2>Everything your event<br>needs, booked in one place</h2>
                </div>
                <button type="button" class="arrow-link" data-href="{{ route('services.index') }}">Browse all services <span>→</span></button>
            </div>
            <div class="service-grid" data-scroll-reveal-group>
                @foreach ($featured as ['category' => $category, 'service' => $service])
                    <x-service-card data-scroll-reveal="fade-up" :service="[
                        'title' => $category->name,
                        'description' => \App\Models\ServiceCategory::SHOWCASE[$category->name]['description'],
                        'price' => 'From '.$service->formattedPrice().($service->price_unit === 'staff' ? ' / staff' : ''),
                        'image' => $category->imageUrl(),
                        'highlight' => \App\Models\ServiceCategory::SHOWCASE[$category->name]['highlight'] ?? null,
                        'url' => route('services.index', ['categories' => [$category->id]]),
                    ]" />
                @endforeach
            </div>
        </div>
    </section>

    <section class="landing-section steps-section" id="how-it-works">
        <div class="landing-container">
            <div class="section-heading centered" data-scroll-reveal="fade-up">
                <p class="eyebrow">How it works</p>
                <h2>From idea to event day in four steps</h2>
            </div>
            <div class="steps-grid" data-scroll-reveal-group>
                @foreach ($steps as $step)
                    <article class="{{ $loop->last ? 'dark' : '' }}" data-scroll-reveal="fade-up">
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
            <article data-scroll-reveal="fade-right">
                <p class="eyebrow">For customers</p>
                <h2>Plan with confidence</h2>
                <ul>
                    <li>On-demand booking with transparent pricing</li>
                    <li>Verified local vendors and real customer reviews</li>
                    <li>Real-time booking status and event updates</li>
                </ul>
                <button type="button" class="dark-button" data-href="{{ route('services.index') }}">Book a service</button>
            </article>
            <article class="vendor-panel" data-scroll-reveal="fade-left">
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
            <div class="section-heading" data-scroll-reveal="fade-up">
                <p class="eyebrow">Reviews</p>
                <h2>Loved by hosts across the Philippines</h2>
            </div>
            <div class="review-grid" data-scroll-reveal-group>
                @forelse ($reviews as $review)
                    <article data-scroll-reveal="fade-up">
                        <div class="stars" aria-label="{{ $review->rating }} out of 5 stars">{{ str_repeat('★', $review->rating) }}</div>
                        <blockquote>“{{ $review->comment }}”</blockquote>
                        <div class="reviewer">
                            <span>{{ $review->customer->initials() }}</span>
                            <p>
                                <strong>{{ $review->customer->name }}</strong>
                                <small>{{ $review->booking->event_type }} · {{ $review->booking->barangay ?? 'Nasugbu' }}</small>
                            </p>
                        </div>
                    </article>
                @empty
                    <p class="modal-copy">Reviews from completed events will appear here.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="landing-section cta-section">
        <div class="landing-container">
            <div class="cta-panel" data-scroll-reveal="zoom-in">
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

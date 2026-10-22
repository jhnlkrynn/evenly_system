{{--
    Browse Services marketplace (BrowseServicesPage.tsx).
    Rendered standalone at /services and embedded in the vendor dashboard.
    Filtering, sorting and chips are handled by resources/js/eventlynk/browse.js.
--}}
@php
    $embedded = $embedded ?? false;
    $query = request('q', '');
    $location = request('location', '');
    $date = request('date') ?: '2026-12-12';
    $parsed = \Illuminate\Support\Carbon::parse($date);
    $dateShort = $parsed->format('M j');
    $dateLong = $parsed->format('D, M j');
    $vendors = collect($vendors)->sortByDesc('reviews')->values();
@endphp

<div class="browse-clean-page {{ $embedded ? 'embedded' : '' }}" data-browse>
    @unless ($embedded)
        <header class="browse-clean-header">
            <div class="browse-clean-nav">
                <x-brand class="browse-brand" />
                <nav>
                    <button type="button" data-href="{{ route('home') }}">Home</button>
                    <button type="button" class="active">Browse Services</button>
                    <button type="button" data-open-modal="browse-how">How It Works</button>
                    <button type="button" data-open-modal="browse-about">About Us</button>
                </nav>
                <div>
                    @auth
                        <x-user-menu />
                    @else
                        <button type="button" class="browse-login" data-href="{{ route('login') }}">Log in</button>
                        <button type="button" class="browse-signup" data-href="{{ route('register') }}">Sign up</button>
                    @endauth
                </div>
            </div>
        </header>
    @endunless

    <section class="browse-search-hero">
        <div class="browse-clean-container">
            <h1>Browse event services</h1>
            <p>Compare verified vendors, then check if they’re free on your date.</p>
            <div class="browse-search-bar">
                <label>
                    <span>What</span>
                    <input value="{{ $query }}" data-filter-query placeholder="Decoration, serving staff, vendor name...">
                </label>
                <label class="location-search">
                    <span>Where</span>
                    <x-searchable-select :options="$barangays" :value="$location" placeholder="Nasugbu, Batangas" all-label="All of Nasugbu, Batangas" data-filter-location />
                </label>
                <label>
                    <span>When</span>
                    <input type="date" value="{{ $date }}" data-filter-date>
                </label>
                <button type="button" data-close-filters>Search</button>
            </div>
        </div>
    </section>

    <section class="browse-results-section">
        <div class="browse-clean-container browse-results-layout">
            <button type="button" class="mobile-filter-button" data-toggle-filters>Show filters</button>
            <aside class="browse-filter-panel" data-filter-panel>
                <div class="filter-heading">
                    <h2>Filters</h2>
                    <button type="button" data-clear-filters>Clear all</button>
                </div>
                <section class="filter-group">
                    <h3>Service</h3>
                    @foreach ($serviceTypes as $type)
                        <label class="filter-check">
                            <input type="checkbox" value="{{ $type }}" data-filter-service>
                            <span>{{ $type }}</span>
                            <small>{{ $vendors->where('badge', $type)->count() }}</small>
                        </label>
                    @endforeach
                </section>
                <section class="filter-group">
                    <h3>Budget</h3>
                    <div class="budget-fields">
                        <label>
                            <span>Min</span>
                            <input type="number" value="0" min="0" data-filter-min>
                        </label>
                        <i>–</i>
                        <label>
                            <span>Max</span>
                            <input type="number" value="30000" min="0" data-filter-max>
                        </label>
                    </div>
                </section>
                <section class="filter-group">
                    <h3>Rating</h3>
                    <div class="rating-pills">
                        @foreach ([[0, 'Any'], [4.5, '4.5+'], [4.8, '4.8+']] as [$value, $label])
                            <button type="button" class="{{ $value === 0 ? 'active' : '' }}" data-filter-rating="{{ $value }}">{{ $label }}</button>
                        @endforeach
                    </div>
                </section>
                <section class="filter-group">
                    <h3>Good for</h3>
                    @foreach ($eventFilters as $event)
                        <label class="filter-check">
                            <input type="checkbox" value="{{ $event }}" data-filter-event>
                            <span>{{ $event }}</span>
                        </label>
                    @endforeach
                </section>
                <label class="availability-toggle">
                    <span>Only show vendors free on <span data-date-short>{{ $dateShort }}</span></span>
                    <input type="checkbox" checked data-filter-available>
                </label>
                <p class="filter-note">Results update as you choose — no apply button needed.</p>
            </aside>

            <div class="browse-results-main">
                <div class="results-toolbar">
                    <div>
                        <strong data-result-label>{{ $vendors->count() }} vendors</strong><span> in Nasugbu, Batangas · free on <span data-date-long>{{ $dateLong }}</span></span>
                    </div>
                    <label>
                        Sort by
                        <select data-filter-sort>
                            <option>Recommended</option>
                            <option>Highest rated</option>
                            <option>Price: low to high</option>
                            <option>Price: high to low</option>
                        </select>
                    </label>
                </div>
                <div class="active-filter-chips" data-filter-chips>
                    <button type="button" data-chip="available">Available on date ×</button>
                </div>

                <div class="vendor-market-grid" data-vendor-grid>
                    @foreach ($vendors as $vendor)
                        <x-vendor-card :vendor="$vendor" :date-label="$dateShort" :book-url="route('bookings.create')" />
                    @endforeach
                </div>
                <div class="browse-empty" data-browse-empty hidden>
                    <h2>No vendors match those filters</h2>
                    <p>Try widening your budget or clearing one of the selected services.</p>
                    <button type="button" data-clear-filters>Clear filters</button>
                </div>
                <div class="browse-results-end">
                    <span>Showing <span data-result-count>{{ $vendors->count() }}</span> of {{ $vendors->count() }} vendors</span>
                    <button type="button" data-href="{{ route('bookings.create') }}">Can’t find what you need? Tell us about your event →</button>
                </div>
            </div>
        </div>
    </section>

    @unless ($embedded)
        <section class="browse-trust-strip">
            <div class="browse-clean-container">
                <span>Verified vendors and businesses</span>
                <span>Secure online payments</span>
                <span>Support available when you need it</span>
            </div>
        </section>
        <footer class="browse-clean-footer">© 2026 Eventlynk · Terms · Privacy · Help center</footer>

        <div class="browse-modal" id="browse-how" data-modal hidden>
            <div>
                <button type="button" data-modal-close aria-label="Close">×</button>
                <p>How it works</p>
                <h2>Find, compare, and book with confidence.</h2>
                <span>Search verified vendors, compare packages, reserve your date, and track every booking from your dashboard.</span>
            </div>
        </div>
        <div class="browse-modal" id="browse-about" data-modal hidden>
            <div>
                <button type="button" data-modal-close aria-label="Close">×</button>
                <p>About Eventlynk</p>
                <h2>Local event services, linked in one place.</h2>
                <span>Eventlynk connects customers with verified event-service vendors across Nasugbu, Batangas.</span>
            </div>
        </div>
    @endunless
</div>

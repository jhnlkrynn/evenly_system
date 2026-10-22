{{--
    Browse Services marketplace (BrowseServicesPage.tsx design), backed by App\Support\ServiceSearch.
    One GET form (#browse-filters) drives the search bar, the filter panel and the sort menu;
    resources/js/eventlynk/browse.js submits it as filters change.
    Rendered at /services and inside the vendor dashboard (embedded = true).
--}}
@php
    $embedded = $embedded ?? false;
    $date = $filters['date'];
    $placeLabel = $filters['barangay'] !== '' ? $filters['barangay'].', Nasugbu' : 'Nasugbu, Batangas';
@endphp

<div class="browse-clean-page {{ $embedded ? 'embedded' : '' }}" data-browse>
    @unless ($embedded)
        @include('services.partials.header')
    @endunless

    <form id="browse-filters" method="GET" action="{{ $formAction }}" data-browse-form hidden>
        @foreach ($baseQuery as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <input type="hidden" name="rating" value="{{ $filters['rating'] ?: '' }}" data-rating-input>
        <input type="hidden" name="available" value="0">
    </form>

    <section class="browse-search-hero">
        <div class="browse-clean-container">
            <h1>Browse event services</h1>
            <p>Compare verified vendors, then check if they’re free on your date.</p>
            <div class="browse-search-bar">
                <label>
                    <span>What</span>
                    <input form="browse-filters" name="search" value="{{ $filters['search'] }}" maxlength="100" placeholder="Decoration, serving staff, vendor name...">
                </label>
                <label class="location-search">
                    <span>Where</span>
                    <x-searchable-select :options="$barangays->all()" :value="$filters['barangay']" name="barangay" form="browse-filters" placeholder="Nasugbu, Batangas" all-label="All of Nasugbu, Batangas" data-auto-submit />
                </label>
                <label>
                    <span>When</span>
                    <input form="browse-filters" type="date" name="date" value="{{ $date?->format('Y-m-d') }}" min="{{ now()->toDateString() }}" data-auto-submit>
                </label>
                <button form="browse-filters" type="submit">Search</button>
            </div>
        </div>
    </section>

    <section class="browse-results-section">
        <div class="browse-clean-container browse-results-layout">
            <button type="button" class="mobile-filter-button" data-toggle-filters>Show filters</button>
            <aside class="browse-filter-panel" data-filter-panel>
                <div class="filter-heading">
                    <h2>Filters</h2>
                    <button type="button" data-href="{{ $clearUrl }}">Clear all</button>
                </div>
                <section class="filter-group">
                    <h3>Service</h3>
                    @foreach ($categories as $category)
                        <label class="filter-check">
                            <input form="browse-filters" type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(in_array($category->id, $filters['categories'], true)) data-auto-submit>
                            <span>{{ $category->name }}</span>
                            <small>{{ $category->services_count }}</small>
                        </label>
                    @endforeach
                </section>
                <section class="filter-group">
                    <h3>Budget</h3>
                    <div class="budget-fields">
                        <label>
                            <span>Min</span>
                            <input form="browse-filters" type="number" name="min_price" value="{{ $filters['min_price'] !== null ? (int) $filters['min_price'] : '' }}" min="0" step="100" placeholder="0" data-auto-submit>
                        </label>
                        <i>–</i>
                        <label>
                            <span>Max</span>
                            <input form="browse-filters" type="number" name="max_price" value="{{ $filters['max_price'] !== null ? (int) $filters['max_price'] : '' }}" min="0" step="100" placeholder="Any" data-auto-submit>
                        </label>
                    </div>
                </section>
                <section class="filter-group">
                    <h3>Rating</h3>
                    <div class="rating-pills">
                        @foreach ($ratings as $value => $label)
                            <button type="button" class="{{ (float) $value === $filters['rating'] ? 'active' : '' }}" data-rating="{{ (string) $value === '0' ? '' : $value }}">{{ $label }}</button>
                        @endforeach
                    </div>
                </section>
                <section class="filter-group">
                    <h3>Good for</h3>
                    @foreach ($events as $key => $event)
                        <label class="filter-check">
                            <input form="browse-filters" type="checkbox" name="events[]" value="{{ $key }}" @checked(in_array($key, $filters['events'], true)) data-auto-submit>
                            <span>{{ $event['label'] }}</span>
                        </label>
                    @endforeach
                </section>
                <label class="availability-toggle">
                    <span>Only show vendors free on {{ $date ? $date->format('M j') : 'your date' }}</span>
                    <input form="browse-filters" type="checkbox" name="available" value="1" @checked($filters['available']) data-auto-submit>
                </label>
                <p class="filter-note">Results update as you choose — no apply button needed.</p>
            </aside>

            <div class="browse-results-main">
                <div class="results-toolbar">
                    <div>
                        <strong>{{ $results->total() }} {{ Str::plural('service', $results->total()) }}</strong><span> in {{ $placeLabel }} · {{ $date ? ($filters['available'] ? 'free on ' : 'on ').$date->format('D, M j') : 'any date' }}</span>
                    </div>
                    <label>
                        Sort by
                        <select form="browse-filters" name="sort" data-auto-submit data-default="recommended">
                            @foreach ($sorts as $value => $label)
                                <option value="{{ $value }}" @selected($filters['sort'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                @if ($chips)
                    <div class="active-filter-chips">
                        @foreach ($chips as $chip)
                            <button type="button" data-href="{{ $chip['url'] }}" aria-label="Remove filter {{ $chip['label'] }}">{{ $chip['label'] }} ×</button>
                        @endforeach
                    </div>
                @endif

                @if ($results->isNotEmpty())
                    <div class="vendor-market-grid">
                        @foreach ($results as $service)
                            <x-service-result-card :service="$service" :date="$date" :booked="$bookedVendorIds->contains($service->vendor_id)" />
                        @endforeach
                    </div>
                @else
                    <div class="browse-empty">
                        <h2>No services match those filters</h2>
                        <p>Try another date, widening your budget, or removing one of the filters above.</p>
                        <button type="button" data-href="{{ $clearUrl }}">Clear filters</button>
                    </div>
                @endif

                <div class="browse-results-end">
                    @if ($results->total())
                        <span>Showing {{ $results->firstItem() }}–{{ $results->lastItem() }} of {{ $results->total() }} {{ Str::plural('service', $results->total()) }}</span>
                    @endif
                    @if ($results->hasPages())
                        <nav class="browse-pagination" aria-label="Results pages">{{ $results->links('pagination.eventlynk') }}</nav>
                    @endif
                    <button type="button" data-open-modal="browse-how">Can’t find what you need? See how booking works →</button>
                </div>
            </div>
        </div>
    </section>

    @unless ($embedded)
        @include('services.partials.footer')
    @endunless
</div>

@extends('layouts.base')

@section('title', $vendor->business_name.' · Eventlynk')

@section('body')
<div class="browse-clean-page">
    @include('services.partials.header')

    <section class="browse-search-hero detail-hero">
        <div class="browse-clean-container">
            <nav class="detail-crumbs" aria-label="Breadcrumb">
                <a href="{{ route('services.index') }}">Browse services</a>
                <span>/</span>
                <span>Vendors</span>
            </nav>
            <h1>{{ $vendor->business_name }}</h1>
            <div class="detail-hero-meta">
                <span class="detail-badge">✓ Verified</span>
                @if ($rating)
                    <span><x-star-rating :value="$rating" /> <strong>{{ number_format($rating, 1) }}</strong> ({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</span>
                @else
                    <span>No reviews yet</span>
                @endif
                <span>{{ $vendor->locationLabel() }}</span>
            </div>
        </div>
    </section>

    <section class="browse-results-section">
        <div class="browse-clean-container">
            <div class="detail-layout">
                <div class="detail-main">
                    <article class="detail-card">
                        <h2>About {{ $vendor->short_name ?? $vendor->business_name }}</h2>
                        <p>{{ $vendor->description ?: 'This vendor hasn’t written an introduction yet.' }}</p>
                        <dl class="detail-facts">
                            <div>
                                <dt>Services listed</dt>
                                <dd>{{ $services->count() }}</dd>
                            </div>
                            <div>
                                <dt>Events completed</dt>
                                <dd>{{ $completedBookings }}</dd>
                            </div>
                            <div>
                                <dt>Average rating</dt>
                                <dd>{{ $rating ? number_format($rating, 1).' / 5' : '—' }}</dd>
                            </div>
                        </dl>
                    </article>
                </div>
                <aside class="detail-sidebar">
                    @include('vendors.partials.card', ['vendor' => $vendor, 'hideProfileLink' => true])
                </aside>
            </div>

            <div class="detail-section-title">
                <h2>Services</h2>
                <span>{{ $services->count() }} {{ Str::plural('listing', $services->count()) }}</span>
            </div>
            @if ($services->isNotEmpty())
                <div class="vendor-market-grid">
                    @foreach ($services as $service)
                        <x-service-result-card :service="$service" />
                    @endforeach
                </div>
            @else
                <div class="detail-empty">This vendor has no active services right now.</div>
            @endif

            <div class="detail-page-end">
                @include('services.partials.reviews')
            </div>
        </div>
    </section>

    @include('services.partials.footer')
</div>
@endsection

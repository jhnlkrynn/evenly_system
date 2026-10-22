@extends('layouts.base')

@section('title', $service->name.' · '.$vendor->business_name.' · Eventlynk')

@section('body')
<div class="browse-clean-page">
    @include('services.partials.header')

    <section class="browse-search-hero detail-hero">
        <div class="browse-clean-container">
            <nav class="detail-crumbs" aria-label="Breadcrumb">
                <a href="{{ route('services.index') }}">Browse services</a>
                <span>/</span>
                <a href="{{ route('services.index', ['categories' => [$service->service_category_id]]) }}">{{ $service->category->name }}</a>
            </nav>
            <h1>{{ $service->name }}</h1>
            <div class="detail-hero-meta">
                <span>by <a href="{{ route('vendors.show', $vendor) }}" style="color: #c69a3a; font-weight: 700;">{{ $vendor->business_name }}</a></span>
                @if ($rating)
                    <span><x-star-rating :value="$rating" /> <strong>{{ number_format($rating, 1) }}</strong> ({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</span>
                @endif
                <span class="detail-badge">{{ $service->category->icon }} {{ $service->category->name }}</span>
                <span>{{ $vendor->locationLabel() }}</span>
            </div>
        </div>
    </section>

    <section class="browse-results-section">
        <div class="browse-clean-container">
            <div class="detail-layout">
                <div class="detail-main">
                    <div class="detail-photo">
                        <img src="{{ $service->imageUrl() }}" alt="{{ $service->name }} by {{ $vendor->business_name }}">
                    </div>
                    <article class="detail-card">
                        <h2>About this service</h2>
                        <p>{{ $service->description ?: 'The vendor hasn’t added a description yet.' }}</p>
                        <dl class="detail-facts">
                            <div>
                                <dt>Category</dt>
                                <dd>{{ $service->category->name }}</dd>
                            </div>
                            <div>
                                <dt>Pricing</dt>
                                <dd>{{ $service->formattedPrice() }} / {{ $service->price_unit }}</dd>
                            </div>
                            <div>
                                <dt>Based in</dt>
                                <dd>{{ $vendor->barangay ?? 'Nasugbu' }}</dd>
                            </div>
                        </dl>
                    </article>
                </div>

                <aside class="detail-sidebar">
                    <div class="detail-price-card">
                        <span>Starting at</span>
                        <strong>{{ $service->formattedPrice() }}</strong>
                        <span>per {{ $service->price_unit }}</span>
                        <p>A 30% reservation fee secures your date and is deducted from your total. The balance is paid to the vendor on the event day.</p>
                        <button type="button" data-href="{{ route('customer.bookings.create', ['service' => $service->id]) }}">Book this service</button>
                    </div>
                    @include('vendors.partials.card', ['vendor' => $vendor])
                </aside>
            </div>

            @if ($otherServices->isNotEmpty())
                <div class="detail-section-title">
                    <h2>More from {{ $vendor->short_name ?? $vendor->business_name }}</h2>
                    <a href="{{ route('vendors.show', $vendor) }}" style="color: #87641c; font-size: 10px; font-weight: 700;">View vendor profile →</a>
                </div>
                <div class="vendor-market-grid">
                    @foreach ($otherServices as $other)
                        <x-service-result-card :service="$other" />
                    @endforeach
                </div>
            @endif

            <div class="detail-page-end">
                @include('services.partials.reviews')
            </div>
        </div>
    </section>

    @include('services.partials.footer')
</div>
@endsection

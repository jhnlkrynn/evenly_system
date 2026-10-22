{{--
    Marketplace card for one service (BrowseServicesPage.tsx card design).
    Expects vendor + category loaded and the withVendorRatings() columns.
--}}
@props(['service', 'date' => null, 'booked' => false])

@php($rating = $service->vendor_reviews_avg_rating)

<article class="vendor-market-card">
    <a class="vendor-market-image" href="{{ route('services.show', $service) }}" tabindex="-1">
        <img src="{{ $service->imageUrl() }}" alt="{{ $service->name }} by {{ $service->vendor->business_name }}" loading="lazy">
        <span>{{ $service->category->name }}</span>
    </a>
    <button type="button" class="vendor-market-wishlist" data-wishlist aria-label="Add to favorites">♡</button>
    <div class="vendor-market-body">
        <h2><a href="{{ route('vendors.show', $service->vendor) }}">{{ $service->vendor->business_name }}</a> <small>Verified</small></h2>
        <div class="vendor-rating">
            @if ($rating)
                <strong>★ {{ number_format($rating, 1) }}</strong>
                <span>({{ $service->vendor_reviews_count }} {{ Str::plural('review', $service->vendor_reviews_count) }})</span>
            @else
                <span>No reviews yet</span>
            @endif
        </div>
        <p class="vendor-location">
            <a class="vendor-service-name" href="{{ route('services.show', $service) }}">{{ $service->name }}</a>
            · {{ $service->vendor->locationLabel() }}
        </p>
        <p class="vendor-description">{{ Str::limit($service->description, 140) }}</p>
        <p class="vendor-availability {{ $booked ? 'limited' : '' }}">
            <i></i>{{ $date ? ($booked ? 'Booked' : 'Free').' on '.$date->format('M j') : 'Taking bookings' }}
        </p>
        <div class="vendor-card-footer">
            <div>
                <span>from</span>
                <strong>{{ $service->formattedPrice() }}</strong>
                <small>/ {{ $service->price_unit }}</small>
            </div>
            <button type="button" data-href="{{ route('services.show', $service) }}">Book</button>
        </div>
    </div>
</article>

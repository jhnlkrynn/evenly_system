{{-- Marketplace vendor card (BrowseServicesPage.tsx). Filtering/sorting reads the data-* attributes. --}}
@props(['vendor', 'dateLabel', 'bookUrl'])

@php($limited = in_array($vendor['id'], [3, 8], true))

<article class="vendor-market-card"
    data-vendor-card
    data-id="{{ $vendor['id'] }}"
    data-search="{{ strtolower($vendor['name'].' '.$vendor['badge'].' '.$vendor['desc']) }}"
    data-badge="{{ $vendor['badge'] }}"
    data-barangay="{{ $vendor['barangay'] }}"
    data-tags="{{ strtolower(implode('|', $vendor['tags'])) }}"
    data-price="{{ $vendor['priceValue'] }}"
    data-rating="{{ $vendor['rating'] }}"
    data-reviews="{{ $vendor['reviews'] }}">
    <div class="vendor-market-image">
        <img src="{{ $vendor['image'] }}" alt="{{ $vendor['name'] }} service">
        <span>{{ $vendor['badge'] }}</span>
        <button type="button" data-wishlist aria-label="Add to favorites">♡</button>
    </div>
    <div class="vendor-market-body">
        <h2>{{ $vendor['name'] }} <small>Verified</small></h2>
        <div class="vendor-rating">
            <strong>★ {{ $vendor['rating'] }}</strong>
            <span>({{ $vendor['reviews'] }} reviews)</span>
        </div>
        <p class="vendor-location">{{ $vendor['barangay'] }}, {{ $vendor['location'] }}</p>
        <p class="vendor-description">{{ $vendor['desc'] }}</p>
        <p class="vendor-availability {{ $limited ? 'limited' : '' }}">
            <i></i>{{ $limited ? ($vendor['id'] === 8 ? '1 slot left' : '2 slots left') : 'Free' }} on <span data-date-short>{{ $dateLabel }}</span>
        </p>
        <div class="vendor-card-footer">
            <div>
                <span>from</span>
                <strong>{{ $vendor['price'] }}</strong>
                <small>{{ $vendor['unit'] }}</small>
            </div>
            <button type="button" data-href="{{ $bookUrl }}">Book</button>
        </div>
    </div>
</article>

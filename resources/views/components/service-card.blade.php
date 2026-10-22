{{-- Homepage category card: title, description, "From ₱…" price, photo, link to the filtered Browse page. --}}
@props(['service'])

<article class="service-card {{ $service['highlight'] ?? false ? 'featured' : '' }}">
    <div class="service-image">
        <img src="{{ $service['image'] }}" alt="{{ $service['title'] }} service">
        @if ($service['highlight'] ?? false)
            <span>{{ $service['highlight'] }}</span>
        @endif
    </div>
    <div class="service-body">
        <h3>{{ $service['title'] }}</h3>
        <p>{{ $service['description'] }}</p>
        <div>
            <strong>{{ $service['price'] }}</strong>
            <button type="button" data-href="{{ $service['url'] }}">Book now →</button>
        </div>
    </div>
</article>

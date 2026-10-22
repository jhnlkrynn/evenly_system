@props(['service'])

<article class="service-card {{ $service['featured'] ? 'featured' : '' }}">
    <div class="service-image">
        <img src="{{ $service['image'] }}" alt="{{ $service['title'] }} service">
        @if ($service['featured'])
            <span>Best value</span>
        @endif
    </div>
    <div class="service-body">
        <h3>{{ $service['title'] }}</h3>
        <p>{{ $service['description'] }}</p>
        <div>
            <strong>{{ $service['price'] }}</strong>
            <button type="button" data-href="{{ route('services.index') }}">Book now →</button>
        </div>
    </div>
</article>

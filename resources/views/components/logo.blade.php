@props(['compact' => false])

<div {{ $attributes->merge(['class' => 'landing-logo']) }}>
    <img src="{{ asset('images/eventlynk-logo.png') }}" alt="Eventlynk Event Services">
    @unless ($compact)
        <span>Eventlynk</span>
    @endunless
</div>

{{-- Logo + wordmark button used in the auth, browse and booking headers. --}}
@props(['href' => route('home')])

<button type="button" data-href="{{ $href }}" {{ $attributes }}>
    <img src="{{ asset('images/eventlynk-logo.png') }}" alt="">
    <span>Eventlynk</span>
</button>

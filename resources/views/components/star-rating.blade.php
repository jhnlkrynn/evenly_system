{{-- Five gold stars, filled to the nearest whole star. --}}
@props(['value' => 0])

@php($filled = (int) round((float) $value))

<span {{ $attributes->merge(['class' => 'star-rating']) }} role="img" aria-label="{{ number_format((float) $value, 1) }} out of 5 stars">{{ str_repeat('★', $filled) }}<span class="star-off">{{ str_repeat('★', 5 - $filled) }}</span></span>

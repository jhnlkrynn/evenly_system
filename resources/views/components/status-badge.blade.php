{{--
    Coloured status pill used across the dashboards.
    palette: booking | payment | platform (see App\Support\MockData::PALETTES)
    The sizing props mirror the slightly different pills in each React dashboard.
--}}
@props([
    'status',
    'palette' => 'booking',
    'label' => null,
    'padding' => '4px 10px',
    'size' => 12,
    'weight' => 600,
    'radius' => 20,
])

@php($colors = \App\Support\MockData::palette($palette, $status))

<span data-status-badge="{{ $palette }}" data-status="{{ $status }}" {{ $attributes }}
    style="padding: {{ $padding }}; border-radius: {{ $radius }}px; font-size: {{ $size }}px; font-weight: {{ $weight }}; background: {{ $colors['bg'] }}; color: {{ $colors['color'] }};">{{ $label ?? $status }}</span>

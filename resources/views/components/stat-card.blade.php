{{--
    KPI card used across the dashboards. Each variant reproduces one of the
    card layouts from the React dashboards:
      tile    — icon box left, value + label right   (customer / vendor overview)
      stack   — icon box on top, value, label         (admin overview)
      header  — uppercase label + icon, value, sub    (super admin overview / analytics)
      simple  — emoji, value, label                   (vendor earnings)
      inline  — big emoji beside value + label        (vendor analytics)
      center  — centred on a cream tile               (super admin commission)
--}}
@props([
    'variant' => 'tile',
    'label',
    'value',
    'icon' => '',
    'color' => '#c9a84c',
    'valueColor' => '#1a110a',
    'valueSize' => '1.8rem',
    'sub' => null,
    'subColor' => '#9c8f7a',
    'iconBox' => true,
    'letterSpacing' => null,
])

@switch($variant)
    @case('tile')
        <div style="background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e8dfd0; display: flex; align-items: flex-start; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: {{ $color }}18; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">{{ $icon }}</div>
            <div>
                <div style="font-family: 'Playfair Display', serif; font-size: {{ $valueSize }}; font-weight: 700; color: #1a110a; line-height: 1;">{{ $value }}</div>
                <div style="font-size: 13px; color: #6b5a47; margin-top: 4px;">{{ $label }}</div>
            </div>
        </div>
        @break

    @case('stack')
        <div style="background: #fff; border-radius: 10px; padding: 16px; border: 1px solid #e8dfd0;">
            <div style="width: 38px; height: 38px; border-radius: 9px; background: {{ $color }}18; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 10px;">{{ $icon }}</div>
            <div style="font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 700; color: #1a110a; line-height: 1;">{{ $value }}</div>
            <div style="font-size: 12px; color: #6b5a47; margin-top: 4px;">{{ $label }}</div>
        </div>
        @break

    @case('header')
        <div style="background: #fff; border-radius: 12px; border: 1px solid #e8dfd0; padding: 20px 22px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                <span style="font-size: 11px; font-weight: 600; color: #9c8f7a; text-transform: uppercase;{{ $letterSpacing ? ' letter-spacing: '.$letterSpacing.';' : '' }}">{{ $label }}</span>
                @if ($iconBox)
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: {{ $color }}18; display: flex; align-items: center; justify-content: center; font-size: 16px;">{{ $icon }}</div>
                @else
                    <span style="font-size: 18px;">{{ $icon }}</span>
                @endif
            </div>
            <div style="font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 700; color: {{ $valueColor }};">{{ $value }}</div>
            <div style="font-size: {{ $iconBox ? 11 : 12 }}px; color: {{ $subColor }}; margin-top: 4px;">{{ $sub }}</div>
        </div>
        @break

    @case('simple')
        <div style="background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e8dfd0;">
            <div style="font-size: 22px; margin-bottom: 8px;">{{ $icon }}</div>
            <div style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; color: {{ $valueColor }};">{{ $value }}</div>
            <div style="font-size: 11px; color: #6b5a47; margin-top: 4px;">{{ $label }}</div>
        </div>
        @break

    @case('inline')
        <div style="background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e8dfd0; display: flex; align-items: center; gap: 14px;">
            <div style="font-size: 28px;">{{ $icon }}</div>
            <div>
                <div style="font-family: 'Playfair Display', serif; font-size: 1.6rem; font-weight: 700; color: #1a110a;">{{ $value }}</div>
                <div style="font-size: 13px; color: #6b5a47;">{{ $label }}</div>
            </div>
        </div>
        @break

    @case('center')
        <div style="padding: 18px 20px; background: #faf8f4; border-radius: 10px; border: 1px solid #e8dfd0; text-align: center;">
            <div style="font-size: 24px; margin-bottom: 8px;">{{ $icon }}</div>
            <div style="font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: {{ $valueColor }};">{{ $value }}</div>
            <div style="font-size: 12px; color: #9c8f7a; margin-top: 4px;">{{ $label }}</div>
        </div>
        @break
@endswitch

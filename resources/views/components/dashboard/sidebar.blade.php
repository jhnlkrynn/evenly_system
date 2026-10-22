{{--
    Collapsible dashboard sidebar.
    variant: portal (customer/vendor) | admin | super — sizes come from the
    .dash-sidebar--{variant} custom properties in eventlynk.css.
    items: [['id', 'label', 'icon', 'badge' => ['count', 'bg', 'color', 'key']]]
    Elements marked data-sidebar-open-only disappear when the sidebar is collapsed.
--}}
@props([
    'variant' => 'portal',
    'items',
    'active' => 'overview',
    'openClasses' => 'open',
    'extraClass' => '',
    'logoutLabel' => 'Log Out',
])

@php
    $headerPadding = $variant === 'portal' ? '20px 16px' : '18px 14px';
    $togglerStyle = $variant === 'portal'
        ? 'font-size: 18px; padding: 4px;'
        : 'font-size: 16px; padding: 4px; flex-shrink: 0;';
    $navPadding = ['portal' => '12px 8px', 'admin' => '10px 6px', 'super' => '8px 8px'][$variant];
    $footerPadding = ['portal' => '12px 8px', 'admin' => '10px 6px', 'super' => '14px 10px'][$variant];
    [$openArrow, $closedArrow] = $variant === 'super' ? ['◀', '▶'] : ['◂', '▸'];
@endphp

<aside class="{{ trim($extraClass.' app-dashboard-sidebar dash-sidebar--'.$variant.' '.$openClasses) }}" data-sidebar data-open-classes="{{ $openClasses }}">
    <div style="padding: {{ $headerPadding }}; border-bottom: 1px solid #c9a84c22; display: flex; align-items: center; justify-content: space-between;">
        <button type="button" data-sidebar-open-only data-href="{{ route('home') }}" style="background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 10px;">
            {{ $brand }}
        </button>
        <button type="button" data-sidebar-toggle aria-label="Toggle sidebar" style="background: none; border: none; color: #c9a84c; cursor: pointer; {{ $togglerStyle }}">
            <span data-sidebar-open-only>{{ $openArrow }}</span><span data-sidebar-closed-only>{{ $closedArrow }}</span>
        </button>
    </div>

    {{ $profile ?? '' }}

    <nav style="flex: 1; padding: {{ $navPadding }}; overflow-y: auto;">
        @foreach ($items as $item)
            <button type="button" class="dash-nav-btn {{ $item['id'] === $active ? 'active' : '' }}" data-tab="{{ $item['id'] }}" data-tab-label="{{ $item['label'] }}">
                <span class="dash-nav-icon">{{ $item['icon'] }}</span>
                <span data-sidebar-open-only style="white-space: nowrap;">{{ $item['label'] }}</span>
                @if (! empty($item['badge']) && $item['badge']['count'] > 0)
                    <span data-sidebar-open-only data-badge="{{ $item['badge']['key'] }}" style="margin-left: auto; background: {{ $item['badge']['bg'] }}; color: {{ $item['badge']['color'] }}; border-radius: 10px; padding: 1px 7px; font-size: 11px; font-weight: 700;">{{ $item['badge']['count'] }}</span>
                @endif
            </button>
        @endforeach
    </nav>

    <div style="padding: {{ $footerPadding }}; border-top: 1px solid #c9a84c22;">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            @if ($variant === 'super')
                <button type="submit" class="dash-super-logout">
                    <span>🚪</span>
                    <span data-sidebar-open-only>{{ $logoutLabel }}</span>
                </button>
            @else
                <button type="submit" class="dash-logout-btn">
                    <span style="font-size: var(--nav-icon);">🚪</span>
                    <span data-sidebar-open-only>{{ $logoutLabel }}</span>
                </button>
            @endif
        </form>
    </div>
</aside>

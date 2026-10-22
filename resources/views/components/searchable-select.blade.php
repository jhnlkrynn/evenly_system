{{-- Type-to-filter dropdown (SearchableSelect.tsx). Behaviour lives in resources/js/eventlynk/searchable-select.js. --}}
@props([
    'options',
    'value' => '',
    'name' => null,
    'form' => null,
    'placeholder' => 'Search...',
    'allLabel' => 'All',
])

<div data-searchable-select data-placeholder="{{ $placeholder }}" data-all-label="{{ $allLabel }}" {{ $attributes->merge(['style' => 'position: relative;']) }}>
    @if ($name)
        <input type="hidden" name="{{ $name }}" value="{{ $value }}" @if ($form) form="{{ $form }}" @endif data-select-value>
    @endif
    <button type="button" data-select-trigger aria-haspopup="listbox" aria-expanded="false"
        style="width: 100%; padding: 10px 32px 10px 12px; border: 1.5px solid #e8dfd0; border-radius: 8px; background: #fff; font-size: 13px; color: {{ $value ? '#1a110a' : '#9c8f7a' }}; text-align: left; cursor: pointer; position: relative;">{{ $value ?: $placeholder }}<span data-select-caret style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%) rotate(0deg); font-size: 11px; color: #9c8f7a; transition: transform 0.15s;">▼</span>
    </button>

    <div data-select-menu hidden style="position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #fff; border: 1.5px solid #e8dfd0; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.12); z-index: 999; overflow: hidden;">
        <div style="padding: 8px 8px 4px;">
            <input type="text" data-select-search placeholder="Type to search..." aria-label="Search options"
                style="width: 100%; padding: 7px 10px; border: 1.5px solid #e8dfd0; border-radius: 6px; font-size: 13px; outline: none; box-sizing: border-box;">
        </div>
        <div role="listbox" style="max-height: 220px; overflow-y: auto;">
            <div role="option" class="searchable-option {{ $value ? '' : 'is-selected' }}" data-select-option="">{{ $allLabel }}</div>
            <div data-select-empty hidden style="padding: 9px 12px; font-size: 13px; color: #9c8f7a;">No results</div>
            @foreach ($options as $option)
                <div role="option" class="searchable-option {{ $value === $option ? 'is-selected' : '' }}" data-select-option="{{ $option }}">{{ $option }}</div>
            @endforeach
        </div>
    </div>
</div>

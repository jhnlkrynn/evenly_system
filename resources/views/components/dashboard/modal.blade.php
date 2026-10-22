{{--
    Dashboard overlay + dialog. Opened by any [data-modal-open="{id}"] button,
    closed by clicking the backdrop or a [data-modal-close] element.
    overlay: dim (0.55, z 300) | dark (0.6, z 300) | sheet (0.6, z 200, padded)
--}}
@props(['id', 'overlay' => 'dim', 'panelStyle'])

@php
    $overlayStyle = [
        'dim' => 'position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 300; display: flex; align-items: center; justify-content: center;',
        'dark' => 'position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 300; display: flex; align-items: center; justify-content: center;',
        'sheet' => 'position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 200; display: flex; align-items: center; justify-content: center; padding: 24px;',
    ][$overlay];
@endphp

<div id="{{ $id }}" data-modal hidden {{ $attributes }} style="{{ $overlayStyle }}">
    <div role="dialog" aria-modal="true" style="{{ $panelStyle }}">
        {{ $slot }}
    </div>
</div>

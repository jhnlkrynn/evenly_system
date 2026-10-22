{{-- Two-up role picker (RoleSelector.tsx). Selection is handled by resources/js/eventlynk/auth.js. --}}
@props(['roles', 'value' => null, 'name' => 'role'])

<div data-role-selector style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" data-role-value>
    @foreach ($roles as $role)
        <button type="button" class="role-option {{ $value === $role['id'] ? 'is-selected' : '' }}" data-role-option="{{ $role['id'] }}" aria-pressed="{{ $value === $role['id'] ? 'true' : 'false' }}">
            <div style="font-size: 18px; margin-bottom: 3px;">{{ $role['icon'] }}</div>
            <div class="role-option-label">{{ $role['label'] }}</div>
            <div style="font-size: 11px; color: #9c8f7a; margin-top: 1px;">{{ $role['desc'] }}</div>
        </button>
    @endforeach
</div>

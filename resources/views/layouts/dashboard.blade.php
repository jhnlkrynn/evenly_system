@extends('layouts.base')

{{--
    Sidebar dashboard shell shared by the customer, vendor, admin and
    super admin portals. Child views provide:
      sidebar  — <x-dashboard.sidebar>
      header   — the white top bar (title element carries data-dashboard-title)
      panels   — one top-level element per tab, marked data-panel="{id}"
      modals   — <x-dashboard.modal> overlays (must stay direct children of
                 .app-dashboard for the mobile rules in eventlynk.css)
    Tab switching, modals, filters and toasts: resources/js/eventlynk/dashboard.js
--}}

@section('body')
<div class="app-dashboard" data-dashboard="@yield('dashboard')"
    style="display: flex; min-height: 100vh; background: #f5f0e8; font-family: 'Inter', sans-serif;">
    @yield('sidebar')

    <div class="app-dashboard-shell" style="@yield('shell_style', 'flex: 1; display: flex; flex-direction: column; overflow: hidden;')">
        @yield('header')

        <main class="@yield('main_class', 'app-dashboard-main')" data-dashboard-main style="@yield('main_style', 'flex: 1; overflow-y: auto; padding: 28px;')">
            @yield('panels')
        </main>
    </div>

    @yield('modals')

    @include('notifications.dropdown')

    <script type="application/json" data-palettes>@json(\App\Support\MockData::PALETTES)</script>

    <div data-toast hidden
        style="position: fixed; bottom: 28px; left: 50%; transform: translateX(-50%); background: #1a110a; color: #f5f0e8; padding: 12px 24px; border-radius: 8px; font-size: 13px; font-weight: 500; z-index: 400; box-shadow: 0 8px 32px rgba(0,0,0,0.4); border: 1px solid #c9a84c; display: flex; align-items: center; gap: 10px; white-space: nowrap;">
        <span style="color: #c9a84c; font-size: 16px;">✓</span>
        <span data-toast-text></span>
    </div>
</div>
@endsection

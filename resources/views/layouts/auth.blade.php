@extends('layouts.base')

@section('body')
<main class="auth-page @yield('page_class')">
    @yield('story')

    <section class="auth-form-panel @yield('panel_class')">
        @yield('form')
    </section>
</main>
@endsection

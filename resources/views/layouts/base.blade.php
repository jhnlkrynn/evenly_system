<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Eventlynk')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/eventlynk-logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/css/eventlynk.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body>
    @yield('body')
    @stack('scripts')
</body>
</html>

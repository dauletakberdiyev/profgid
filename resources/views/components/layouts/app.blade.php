<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">

    <title>{{ $title ?? 'Profgid' }}</title>

    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

    <link rel="stylesheet" href="{{ asset('assets/fonts/atyp/stylesheet.css') }}">

    <link rel="stylesheet" href="{{ asset('build/assets/app-CpbGTD6R.css') }}">

    @livewireStyles

</head>

<body>

    <livewire:parts.header />

    {{ $slot }}

    <livewire:parts.footer />

    @livewireScripts

    <script src="{{ asset('build/assets/app-B6G8EThm.js') }}"></script>

</body>

</html>

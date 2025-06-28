<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Profgid' }}</title>

    <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->

    <link rel="stylesheet" href="{{ asset('assets/fonts/atyp/stylesheet.css') }}">
    <link rel="stylesheet" href="{{ asset('build/assets/app-Cpn0XG2O.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('build/assets/app-D8uUZLEr.css') }}"> -->

    @livewireStyles

</head>

<body>

    <livewire:parts.header />

    {{ $slot }}

    <livewire:parts.footer />

    @livewireScripts

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js" integrity="sha512-01CJ9/g7e8cUmY0DFTMcUw/ikS799FHiOA0eyHsUWfOetgbx/t6oV4otQ5zXKQyIrQGTHSmRVPIgrgLcZi/WMA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    {{-- <script src="https://www.npmjs.com/package/html2canvas-pro"></script> --}}

    <script src="{{ asset('build/assets/app-BlukfNVj.js') }}"></script>

    <script>
        // document.getElementById("exportByImage").click(function () {
        //     alert('ewrferf')
        // })

    </script>

</body>

</html>

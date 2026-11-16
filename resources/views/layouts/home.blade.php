<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.quiet-transitions')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'SMMS HRMS'))</title>

    {{-- Fonts are served from this site, so pages do not depend on an outside font service. --}}
    <link rel="preload" href="{{ asset('fonts/instrument-sans-variable.woff2') }}" as="font" type="font/woff2" crossorigin>
    <style>
        @font-face {
            font-family: 'Marcellus';
            src: url("{{ asset('fonts/marcellus-400.woff2') }}") format('woff2');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Instrument Sans';
            src: url("{{ asset('fonts/instrument-sans-variable.woff2') }}") format('woff2');
            font-weight: 400 700;
            font-style: normal;
            font-display: swap;
        }
    </style>

    @yield('styles')
</head>
<body>

    {{-- Main page content --}}
    @yield('content')

    {{-- JavaScript files --}}
    @yield('scripts')

</body>
</html>

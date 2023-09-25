<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('site_title') | {{ config('app.name') }}</title>

        <!-- CSS and JS -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    </head>

    <body class="d-flex flex-column vh-100">
        @yield('dataTables_config')

        @include('layouts.nav-main')

        @yield('content')

        @include('layouts.footer-main')

        @include('inc.bs-tooltip')
    </body>
</html>

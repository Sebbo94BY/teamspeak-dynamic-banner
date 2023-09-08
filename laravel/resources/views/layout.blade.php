<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('site_title')</title>
    @include('layouts.head-css')
    @include('layouts.head-js')
</head>
<body class="d-flex flex-column vh-100">

@yield('dataTables_config')

@include('layouts.nav-main')

@yield('content')

@include('layouts.footer-main')
</body>
</html>

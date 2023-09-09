<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('site_title') | {{config('app.name')}}</title>
    @include('layouts.head')
</head>
<body class="d-flex flex-column vh-100">

@yield('dataTables_config')

@include('layouts.nav-main')

@yield('content')

@include('layouts.footer-main')
</body>
</html>

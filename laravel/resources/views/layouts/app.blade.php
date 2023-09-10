<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @if (config('matomo.enabled'))
    <!-- Matomo -->
    <script>
        var _paq = window._paq = window._paq || [];
        /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
        _paq.push(["setDoNotTrack", true]);
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        _paq.push(['enableHeartBeatTimer'], "{{ config('matomo.heartbeat_timer') }}");
        (function() {
            var u="{{ config('matomo.base_url') }}";
            _paq.push(['setTrackerUrl', u+'/matomo.php']);
            _paq.push(['setSiteId', "{{ config('matomo.site_id') }}"]);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
        })();
    </script>
    <noscript><p><img src="{{ config('matomo.base_url') }}/matomo.php?idsite={{ config('matomo.site_id') }}&amp;rec=1" style="border:0;" alt="" /></p></noscript>
    @endif
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                        </li>

                        @can('view instances')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('instances') }}">{{ __('Instances') }}</a>
                        </li>
                        @endcan

                        @can('view templates')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('templates') }}">{{ __('Templates') }}</a>
                        </li>
                        @endcan

                        @can('view banners')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('banners') }}">{{ __('Banners') }}</a>
                        </li>
                        @endcan
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                        @else
                            @canany(['view users', 'view roles', 'view fonts', 'view system status', 'view php info'])
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Administration
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @can('view users')
                                    <a class="dropdown-item" href="{{ route('administration.users') }}">
                                        Users
                                    </a>
                                    @endcan

                                    @can('view roles')
                                    <a class="dropdown-item" href="{{ route('administration.roles') }}">
                                        Roles
                                    </a>
                                    @endcan

                                    @can('view fonts')
                                    <a class="dropdown-item" href="{{ route('administration.fonts') }}">
                                        Fonts
                                    </a>
                                    @endcan

                                    @can('view system status')
                                    <a class="dropdown-item" href="{{ route('administration.systemstatus') }}">
                                        System Status
                                    </a>
                                    @endcan

                                    @can('view php info')
                                    <a class="dropdown-item" href="{{ route('administration.phpinfo') }}">
                                        PHP Info
                                    </a>
                                    @endcan
                                </div>
                            </li>
                            @endcanany

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        {{ __('Profile') }}
                                    </a>

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <main class="py-4">
        @yield('content')
    </main>
</body>
</html>

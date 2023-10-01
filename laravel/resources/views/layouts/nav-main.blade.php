@guest()
<nav class="navbar navbar-dark bg-dark navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{Route('dashboard')}}">{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        @if (str_starts_with(Route::currentRouteName(), 'setup.'))
            <div class="collapse navbar-collapse justify-content-end text-bg-dark">
                <ul class="navbar-nav my-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle
                           {{ (str_starts_with(Route::currentRouteName(), 'administration.')) ? 'active' : '' }}"
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                           {{ __("views/layouts/nav-main.change_language") }}
                        </a>
                        <ul class="dropdown-menu">
                            @foreach (\App\Models\Localization::get() as $localization)
                            <li><a class="dropdown-item @if (Request::route('locale') == $localization->locale) active @endif" href="{{ route(Route::currentRouteName(), ['locale' => $localization->locale]) }}">{{ $localization->language_name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </div>
        @else
            <div class="collapse navbar-collapse justify-content-end text-bg-dark">
                <ul class="navbar-nav my-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ (request()->is('login') ? 'active' : '') }}" href="{{Route('login')}}">{{ __("views/layouts/nav-main.login") }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ (str_starts_with(Route::currentRouteName(), 'password.')) ? 'active' : '' }} " href="{{ route('password.request') }}">{{ __("views/layouts/nav-main.forgot_password") }}</a>
                    </li>
                </ul>
            </div>
        @endif
    </div>
</nav>
@endguest
@auth()
<nav class="navbar navbar-dark bg-dark navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{Route('dashboard')}}">{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ (str_starts_with(Route::currentRouteName(), 'dashboard')) ? 'active' : '' }}" aria-current="page" href="{{Route('dashboard')}}">{{ __("views/layouts/nav-main.dashboard") }}</a>
                </li>
                @can('view instances')
                    <li class="nav-item">
                        <a class="nav-link {{ (str_starts_with(Route::currentRouteName(), 'instance')) ? 'active' : '' }}" aria-current="page" href="{{ Route('instances') }}">{{ __("views/layouts/nav-main.instances") }}</a>
                    </li>
                @endcan
                @can('view templates')
                    <li class="nav-item">
                        <a class="nav-link {{ (str_starts_with(Route::currentRouteName(), 'template')) ? 'active' : '' }}" aria-current="page" href="{{ Route('templates') }}">{{ __("views/layouts/nav-main.templates") }}</a>
                    </li>
                @endcan
                @can('view banners')
                    <li class="nav-item">
                        <a class="nav-link {{ (str_starts_with(Route::currentRouteName(), 'banner')) ? 'active' : '' }}" aria-current="page" href="{{ Route('banners') }}">{{ __("views/layouts/nav-main.banners") }}</a>
                    </li>
                @endcan
            </ul>
        </div>
        <div class="collapse navbar-collapse justify-content-end text-bg-dark">
            @canany(['view users', 'view roles','view fonts','view system status','view php info'])
                <ul class="navbar-nav my-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle
                           {{ (str_starts_with(Route::currentRouteName(), 'administration.')) ? 'active' : '' }}"
                           href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                           {{ __("views/layouts/nav-main.administration") }}
                        </a>
                        <ul class="dropdown-menu">
                            @can('view users')
                            <li><a class="dropdown-item {{ (Route::currentRouteName() == 'administration.users') ? 'active' : '' }}" href="{{Route('administration.users')}}">{{ __("views/layouts/nav-main.administration_users") }}</a></li>
                            @endcan
                            @can('view roles')
                            <li><a class="dropdown-item {{ (Route::currentRouteName() == 'administration.roles') ? 'active' : '' }}" href="{{ route('administration.roles') }}">{{ __("views/layouts/nav-main.administration_roles") }}</a></li>
                            @endcan
                            @can('view fonts')
                            <li><a class="dropdown-item {{ (Route::currentRouteName() == 'administration.fonts') ? 'active' : '' }}" href="{{ route('administration.fonts') }}">{{ __("views/layouts/nav-main.administration_fonts") }}</a></li>
                            @endcan
                            @can('view system status')
                            <li><a class="dropdown-item {{ (Route::currentRouteName() == 'administration.systemstatus') ? 'active' : '' }}" href="{{ route('administration.systemstatus') }}">{{ __("views/layouts/nav-main.administration_systemstatus") }}</a></li>
                            @endcan
                            @can('view php info')
                            <li><a class="dropdown-item {{ (Route::currentRouteName() == 'administration.phpinfo') ? 'active' : '' }}" href="{{ route('administration.phpinfo') }}">{{ __("views/layouts/nav-main.administration_phpinfo") }}</a></li>
                            @endcan
                        </ul>
                    </li>
                </ul>
            @endcanany
            <ul class="navbar-nav my-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle
                       {{ (str_starts_with(Route::currentRouteName(), 'profile.')) ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {!! trans_choice("views/layouts/nav-main.greet_user", Carbon\Carbon::now(Request::header('X-Timezone'))->format('H'), ['username' => Auth::user()->name]) !!}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ (Route::currentRouteName() == 'profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">{{ __("views/layouts/nav-main.profile") }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __("views/layouts/nav-main.logout") }}</a></li>
                    </ul>
                </li>
            </ul>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</nav>
@endauth

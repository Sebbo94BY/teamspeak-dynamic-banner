@guest()
<nav class="navbar navbar-dark bg-dark navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{Route('dashboard')}}">Dynamic Banner</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        @if(!str_starts_with(Route::currentRouteName(), 'setup.'))
            <div class="collapse navbar-collapse justify-content-end text-bg-dark">
                <ul class="navbar-nav my-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ (request()->is('login') ? 'active' : '') }}" href="{{Route('login')}}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ (str_starts_with(Route::currentRouteName(), 'password.')) ? 'active' : '' }} " href="{{ route('password.request') }}">Forgot Password</a>
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
        <a class="navbar-brand" href="{{Route('dashboard')}}">Dynamic Banner</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ (str_starts_with(Route::currentRouteName(), 'dashboard')) ? 'active' : '' }}" aria-current="page" href="{{Route('dashboard')}}">Dashboard</a>
                </li>
                @can('view instances')
                    <li class="nav-item">
                        <a class="nav-link {{ (str_starts_with(Route::currentRouteName(), 'instance')) ? 'active' : '' }}" aria-current="page" href="{{ Route('instances') }}">Instances</a>
                    </li>
                @endcan
                @can('view templates')
                    <li class="nav-item">
                        <a class="nav-link {{ (str_starts_with(Route::currentRouteName(), 'template')) ? 'active' : '' }}" aria-current="page" href="{{ Route('templates') }}">Templates</a>
                    </li>
                @endcan
                @can('view banners')
                    <li class="nav-item">
                        <a class="nav-link {{ (str_starts_with(Route::currentRouteName(), 'banner')) ? 'active' : '' }}" aria-current="page" href="{{ Route('banners') }}">Banners</a>
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
                            Administration
                        </a>
                        <ul class="dropdown-menu">
                            @can('view users')
                            <li><a class="dropdown-item {{ (Route::currentRouteName() == 'administration.users') ? 'active' : '' }}" href="{{Route('administration.users')}}">User</a></li>
                            @endcan
                            @can('view roles')
                            <li><a class="dropdown-item {{ (Route::currentRouteName() == 'administration.roles') ? 'active' : '' }}" href="{{ route('administration.roles') }}">Roles</a></li>
                            @endcan
                            @can('view fonts')
                            <li><a class="dropdown-item {{ (Route::currentRouteName() == 'administration.fonts') ? 'active' : '' }}" href="{{ route('administration.fonts') }}">Fonts</a></li>
                            @endcan
                            @can('view system status')
                            <li><a class="dropdown-item {{ (Route::currentRouteName() == 'administration.systemstatus') ? 'active' : '' }}" href="{{ route('administration.systemstatus') }}">System Status</a></li>
                            @endcan
                            @can('view php info')
                            <li><a class="dropdown-item {{ (Route::currentRouteName() == 'administration.phpinfo') ? 'active' : '' }}" href="{{ route('administration.phpinfo') }}">PHP Info</a></li>
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
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ (Route::currentRouteName() == 'profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">Profil</a></li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
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

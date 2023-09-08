@guest()
<nav class="navbar navbar-dark bg-dark navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{Route('dashboard')}}">Dynamic Banner</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end text-bg-dark">
            <ul class="navbar-nav my-auto">
                <li class="nav-item">
                    <a class="nav-link @yield('nav_link_active_login')" href="{{Route('login')}}">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @yield('nav_link_active_password_reset')" href="{{ route('password.request') }}">Forgot Password</a>
                </li>
            </ul>
        </div>
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
                    <a class="nav-link @yield('nav_link_active_dashboard')" aria-current="page" href="{{Route('dashboard')}}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @yield('nav_link_active_instances')" aria-current="page" href="{{ Route('instances') }}">Instances</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @yield('nav_link_active_templates')" aria-current="page" href="{{ Route('templates') }}">Templates</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @yield('nav_link_active_banners')" aria-current="page" href="{{ Route('banners') }}">Banners</a>
                </li>
            </ul>
        </div>
        <div class="collapse navbar-collapse justify-content-end text-bg-dark">
            <ul class="navbar-nav my-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle @yield('nav_link_active_user') @yield('nav_link_active_roles') @yield('nav_link_active_fonts') @yield('nav_link_active_system_status') @yield('nav_link_active_php_info')" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Administration
                    </a>
                    <ul class="dropdown-menu">
                        @can('view users')
                        <li><a class="dropdown-item @yield('nav_link_active_user')" href="{{Route('administration.users')}}">User</a></li>
                        @endcan
                        @can('view roles')
                        <li><a class="dropdown-item @yield('nav_link_active_roles')" href="{{ route('administration.roles') }}">Roles</a></li>
                        @endcan
                        @can('view fonts')
                        <li><a class="dropdown-item @yield('nav_link_active_fonts')" href="{{ route('administration.fonts') }}">Fonts</a></li>
                        @endcan
                        @can('view system status')
                        <li><a class="dropdown-item @yield('nav_link_active_system_status')" href="{{ route('administration.systemstatus') }}">System Status</a></li>
                        @endcan
                        @can('view php info')
                        <li><a class="dropdown-item @yield('nav_link_active_php_info')" href="{{ route('administration.phpinfo') }}">PHP Info</a></li>
                        @endcan
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav my-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle @yield('nav_link_active_edit_profile')" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{\Illuminate\Support\Facades\Auth::user()->name}}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item @yield('nav_link_active_edit_profile')" href="{{ route('profile.edit') }}">Profil</a></li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
@endauth
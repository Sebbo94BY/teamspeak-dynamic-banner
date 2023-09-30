@extends('layout')

@section('site_title')
    {{ __('views/setup/installer/requirements.installer_requirements') }}
@endsection

@section('content')
    <div class="container mt-3">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="fw-bold fs-3">{{ __('views/setup/installer/requirements.installer_requirements') }}</h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-12">
                @if ($system_status_danger_count == 0)
                    <div class="mb-3">
                        <a href="{{ route('setup.installer.user', ['locale' => Request::route('locale')]) }}" class="btn btn-primary">{{ __('views/setup/installer/requirements.next_button') }}</a>
                    </div>
                @else
                    <div class="mb-3">
                        <a href="{{ route(Route::currentRouteName(), ['locale' => Request::route('locale')]) }}" class="btn btn-primary">{{ __('views/setup/installer/requirements.refresh_button') }}</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@include('inc.system.systemstatus')

@endsection

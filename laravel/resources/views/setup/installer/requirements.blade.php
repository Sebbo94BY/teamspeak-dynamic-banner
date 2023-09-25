@extends('layout')

@section('site_title')
    Requirements
@endsection

@section('content')
    <div class="container mt-3">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="fw-bold fs-3">Setup: Requirements</h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-12">
                @if ($system_status_danger_count == 0)
                    <div class="mb-3">
                        <a href="{{ route('setup.installer.user') }}" class="btn btn-primary">Next</a>
                    </div>
                @else
                    <div class="mb-3">
                        <a href="{{ route(Route::currentRouteName()) }}" class="btn btn-primary">Refresh</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@include('inc.system.systemstatus')
@endsection

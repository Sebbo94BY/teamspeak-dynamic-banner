@extends('layout')

@section('site_title')
    Dashboard
@endsection

@section('content')
    <div class="container mt-3">
        @include('inc.standard-alerts')
        <div class="row row-cols-4 row-cols-md-3 g-2">
            <div class="col d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">{{ trans_choice('views/dashboard.instances_count_title', $instances_count, ['instances_count' => $instances_count]) }}</h5>
                        <p class="card-text">{{ __("views/dashboard.instances_count_text") }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @can('view instances')
                        <a href="{{ route('instances') }}" class="btn btn-primary">{{ __("views/dashboard.instances_button") }}</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="col d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">{{ trans_choice('views/dashboard.templates_count_title', $templates_count, ['templates_count' => $templates_count]) }}</h5>
                        <p class="card-text">{{ __("views/dashboard.templates_count_text") }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @can('view templates')
                        <a href="{{ route('templates') }}" class="btn btn-primary">{{ __("views/dashboard.templates_button") }}</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="col d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">{{ trans_choice('views/dashboard.banners_count_title', $banners_count, ['banners_count' => $banners_count]) }}</h5>
                        <p class="card-text">{{ __("views/dashboard.banners_count_text") }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @can('view banners')
                        <a href="{{ route('banners') }}" class="btn btn-primary">{{ __("views/dashboard.banners_button") }}</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

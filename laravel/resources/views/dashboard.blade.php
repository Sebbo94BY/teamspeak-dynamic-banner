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
                        <h5 class="card-title">{{ __(":instances_count ".Str::plural("Instance", $instances_count), ['instances_count' => $instances_count]) }}</h5>
                        <p class="card-text">{{ __("An instance can be seen as data source for the banners.") }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @can('view instances')
                        <a href="{{ route('instances') }}" class="btn btn-primary">{{ __("View Instances") }}</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="col d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">{{ __(":templates_count ".Str::plural("Template", $templates_count), ['templates_count' => $templates_count]) }}</h5>
                        <p class="card-text">{{ __("A template defines the design of your banner.") }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @can('view templates')
                        <a href="{{ route('templates') }}" class="btn btn-primary">{{ __("View Templates") }}</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="col d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">{{ __(":banners_count ".Str::plural("Banner", $banners_count), ['banners_count' => $banners_count]) }}</h5>
                        <p class="card-text">{{ __("A banner uses a specific instance and can use any number of templates to generate dynamic images.") }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @can('view banners')
                        <a href="{{ route('banners') }}" class="btn btn-primary">{{ __("View Banners") }}</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

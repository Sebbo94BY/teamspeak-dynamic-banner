@extends('layout')

@section('site_title')
    Dashboard | Dynamic Banner
@endsection

@section('content')
    <div class="container mt-3">
        @include('inc.standard-alerts')
        <div class="row row-cols-4 row-cols-md-3 g-2">
            <div class="col d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">{{ $instances_count }} {{ \Illuminate\Support\Str::plural("Instance", $instances_count) }}</h5>
                        <p class="card-text">An instance can be seen as data source for the banners.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @can('view instances')
                        <a href="{{ Route('instances') }}" class="btn btn-primary">Instances</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="col d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">{{ $templates_count }} {{ \Illuminate\Support\Str::plural("Template", $templates_count) }}</h5>
                        <p class="card-text">A template defines the design of your banner.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @can('view templates')
                        <a href="{{ Route('templates') }}" class="btn btn-primary">Templates</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="col d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">{{ $banners_count }} {{ \Illuminate\Support\Str::plural("Banner", $banners_count) }}</h5>
                        <p class="card-text">The actual dynamic banner configurations, which use your instances and templates.</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @can('view banners')
                        <a href="{{ Route('banners') }}" class="btn btn-primary">Banners</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

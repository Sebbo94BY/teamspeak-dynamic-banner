@extends('layouts.app')

@section('content')
<div class="container">
        @if (session('success'))
        <div class="row">
            <div class="alert alert-success" role="alert">
                {{ session('message') }}
            </div>
        </div>
        @endif
    <div class="row row-cols-4 row-cols-md-4 g-2">
        <div class="col d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <h5 class="card-title">{{ $instances_count }} {{ \Illuminate\Support\Str::plural("Instance", $instances_count) }}</h5>
                    <p class="card-text">An instance can be seen as data source for the banners.</p>
                    @can('view instances')
                    <a href="{{ route('instances') }}" class="btn btn-primary">Instances</a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="col d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <h5 class="card-title">{{ $templates_count }} {{ \Illuminate\Support\Str::plural("Template", $templates_count) }}</h5>
                    <p class="card-text">A template defines the design of your banner.</p>
                    @can('view templates')
                    <a href="{{ route('templates') }}" class="btn btn-primary">Templates</a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="col d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                <h5 class="card-title">{{ $banners_count }} {{ \Illuminate\Support\Str::plural("Banner", $banners_count) }}</h5>
                    <p class="card-text">The acual dynamic banner configurations, which use your instances and templates.</p>
                    @can('view banners')
                    <a href="{{ route('banners') }}" class="btn btn-primary">Banners</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

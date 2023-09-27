@extends('layout')

@section('site_title')
    {{ __('views/administration/systemstatus.system_status') }}
@endsection

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold fs-3">{{ __('views/administration/systemstatus.system_status') }}</h1>
        </div>
    </div>
    <hr>
</div>
@include('inc.system.systemstatus')
@endsection

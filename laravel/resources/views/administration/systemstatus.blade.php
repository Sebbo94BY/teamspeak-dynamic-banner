@extends('layout')

@section('site_title')
    Systemstatus
@endsection

@section('nav_link_active_system_status')
    active
@endsection

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold fs-3">Systemstatus</h1>
        </div>
    </div>
    <hr>
</div>
@include('inc.system.systemstatus')
@endsection

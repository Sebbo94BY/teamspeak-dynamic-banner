@extends('layout')

@section('site_title')
    PHP Info | Dynamic Banner
@endsection

@section('nav_link_active_php_info')
    active
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-12" id="phpinfo">
            {!! $style !!}
            {!! $phpinfo !!}
        </div>
    </div>
</div>
@endsection

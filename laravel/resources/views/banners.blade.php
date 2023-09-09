@extends('layout')

@section('site_title')
    Banners
@endsection

@section('nav_link_active_banners')
    active
@endsection

@section('dataTables_script')
<script>
    $(document).ready( function () {
        $('#banners').DataTable({
            "oLanguage": {
                "sLengthMenu": "_MENU_",
            },
            columnDefs:[
                {
                    orderable: false,
                    targets: 3,
                }
            ],
        });
        $('#availableVariables').DataTable({
            "oLanguage": {
                "sLengthMenu": "_MENU_",
            },
        });
    } );
</script>
@endsection

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold fs-3">Banners</h1>
        </div>
    </div>
    <hr>
</div>
@can('add banners')
<div class="container">
    <div class="row">
        <div class="col-lg-3">
            <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addBanner">
                Add Banner
            </button>
        </div>
    </div>
    <hr>
</div>
@endcan
<div class="container mt-3">
    @if ($banners->count() == 0)
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-primary" role="alert">
                There are no Banners configured! <a href="#addBanner" data-bs-toggle="modal" data-bs-target="#addBanner">Add now a new Banner</a>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped" id="banners">
                <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Instance</th>
                    <th scope="col">Templates in use</th>
                    <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($banners as $banner)
                <tr>
                    <td class="col-lg-3">{{ $banner->name }}</td>
                    <td class="col-lg-5" role="button">
                        {{ $banner->instance->virtualserver_name }}
                    </td>
                    <td class="col-lg-2">
                        <span class="badge text-bg-secondary fs-6">{{ $banner->templates->count() }}</span>
                    </td>
                    <td class="col-lg-2">
                        @can('edit banners')
                        <a href="#editBanner-{{$banner->id}}" data-bs-toggle="modal" data-bs-target="#editBanner-{{$banner->id}}"><i class="fa-solid fa-pencil text-primary me-2 fa-lg"></i></a>
                        @endcan
                        <a href="#modalAvailableVariables-{{$banner->instance->id}}" data-bs-toggle="modal" data-bs-target="#modalAvailableVariables-{{$banner->instance->id}}"><i class="fa-solid fa-square-root-variable text-primary me-2 fa-lg"></i></a>
                        @can('edit banners')
                        <a href="{{ route('banner.templates', ['banner_id' => $banner->id]) }}"><i class="fa-solid fa-image text-primary me-2 fa-lg"></i></a>
                        @endcan
                        <a href="{{ route('api.banner', ['banner_id' => base_convert($banner->id, 10, 35)]) }}"><i class="fa-solid fa-arrow-up-right-from-square text-primary me-2 fa-lg"></i></a>
                        @can('delete banners')
                        <a href="{{ route('banner.delete', ['banner_id' => $banner->id]) }}"><i class="fa fa-trash text-danger me-2 fa-lg"></i></a>
                        @endcan
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@can('add banners')
    @include('modals.banners.modal-add')
@endcan

@can('edit banners')
    @foreach($banners as $bannerEditModal)
        @include('modals.banners.modal-edit')
    @endforeach
@endcan
@foreach($instance_list as $instanceVariableModal)
    @include('modals.modal-variables')
@endforeach
@endsection

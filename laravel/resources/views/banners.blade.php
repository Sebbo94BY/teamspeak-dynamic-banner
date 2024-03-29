@extends('layout')

@section('site_title')
    {{ __('views/banners.banners') }}
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
            <h1 class="fw-bold fs-3">{{ __('views/banners.banners') }}</h1>
        </div>
    </div>
    <hr>
</div>
@can('add banners')
<div class="container">
    @if($instance_list->count() > 0)
        <div class="row">
            <div class="col-lg-3">
                <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addBanner">
                    {{ __('views/banners.add_banner') }}
                </button>
            </div>
        </div>
    <hr>
    @endif
</div>
@endcan
<div class="container mt-3">
    @include('inc.standard-alerts')
    @if ($banners->count() == 0)
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-primary" role="alert">
                @if($instance_list->count() > 0)
                    {{ __('views/banners.no_banner_added_yet') }}
                    <button class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#addBanner">{{ __('views/banners.add_banner') }}</button>
                @else
                    {{ __('views/banners.no_instances_added_yet') }}
                    <a class="btn btn-link p-0" href="{{Route('instances')}}">{{ __('views/banners.add_instance') }}</a>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped" id="banners">
                <thead>
                <tr>
                    <th scope="col">{{ __('views/banners.table_name') }}</th>
                    <th scope="col">{{ __('views/banners.table_instance') }}</th>
                    <th scope="col">{{ __('views/banners.table_templates_in_use') }}</th>
                    <th scope="col">{{ __('views/banners.table_actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($banners as $banner)
                <tr>
                    <td class="col-lg-3">{{ $banner->name }}</td>
                    <td class="col-lg-5">
                        {{ $banner->instance->virtualserver_name }}
                    </td>
                    <td class="col-lg-2">
                        <span class="badge text-bg-secondary fs-6">{{ $banner->templates->count() }}</span>
                    </td>
                    <td class="col-lg-2">
                        <div class="d-flex">
                            @can('edit banners')
                                <button class="btn btn-link px-0 me-2" type="button" data-bs-toggle="modal" data-bs-target="#editBanner-{{$banner->id}}"><i class="fa-solid fa-pencil text-primary fa-lg"></i></button>
                            @endcan
                            @can('edit banners')
                                <a href="{{ route('banner.templates', ['banner_id' => $banner->id]) }}" class="btn btn-link px-0 me-2"><i class="fa-solid fa-image text-primary fa-lg"></i></a>
                            @endcan
                                <a class="btn btn-link px-0 me-2" href="{{ route('api.banner', ['banner_id' => base_convert($banner->id, 10, 35)]) }}" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square text-primary fa-lg"></i></a>
                            @can('delete banners')
                                <button class="btn btn-link px-0 me-2" type="submit" data-bs-toggle="modal" data-bs-target="#delBanner-{{$banner->id}}"><i class="fa fa-trash text-danger fa-lg"></i></button>
                            @endcan
                        </div>
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
    @if ($instance_list->count() > 0)
        @include('modals.banners.modal-add')
    @endif
@endcan

@foreach($banners as $bannerModal)
    @can('edit banners')
        @include('modals.banners.modal-edit',['bannerEditModal'=>$bannerModal])
    @endcan
    @can('delete banners')
        @include('modals.delete-feedback.modal-delete-banner', ['bannerDeleteModal'=>$bannerModal])
    @endcan
@endforeach

@endsection

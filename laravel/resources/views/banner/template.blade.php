@extends('layout')

@section('site_title')
    Banner template
@endsection

@section('dataTables_script')
    <script>
        $(document).ready( function () {
            $('#templates').DataTable({
                "oLanguage": {
                    "sLengthMenu": "_MENU_",
                },
                "ordering":false,
            });
        } );
    </script>
@endsection
@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold fs-3">Banner template</h1>
        </div>
    </div>
    <hr>
</div>
@can('add templates')
<div class="container">
    <div class="row">
        <div class="col-lg-3">
            <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addBannerTemplate">
                Add Banner Template
            </button>
        </div>
    </div>
    <hr>
</div>
@endcan
<div class="container mt-3">
    @include('inc.standard-alerts')
    @if($banner->templates->count() == 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-primary" role="alert">
                    There are no templates configured!
                    @can('add templates')
                        <button class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#addBannerTemplate">Define a new template now</button>
                    @endcan
                </div>
            </div>
        </div>
    @else
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped" id="templates">
                <thead>
                <tr>
                    <th scope="col">Banner</th>
                    <th scope="col">Name</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($banner->templates as $banner_template)
                <tr>
                    <td class="col-lg-6 text-start">
                        <img class="img-fluid shadow-lg p-1 mb-2 bg-white rounded opacity-{{ ($banner_template->enabled) ? 100 : 50 }}" src="{{ asset($banner_template->file_path_drawed_text.'/'.$banner_template->template->filename) }}" alt="{{ $banner_template->template->alias }}">
                    </td>
                    <td class="col-lg-5">{{$banner_template->name}}</td>
                    <td class="col-lg-1 text-end">
                        <div class="d-flex">
                            @if ($banner_template->enabled)
                                <form method="post" action="{{ route('banner.template.disable', ['banner_template_id' => $banner_template->id]) }}">
                                    @method('patch')
                                    @csrf
                                    <button class="btn btn-link px-0 me-2" type="submit"><i class="fa-solid fa-toggle-on fa-lg"></i></button>
                                </form>
                            @else
                                <form method="post" action="{{ route('banner.template.enable', ['banner_template_id' => $banner_template->id]) }}">
                                    @method('patch')
                                    @csrf
                                    <button class="btn btn-link px-0 me-2" type="submit"><i class="fa-solid fa-toggle-off fa-lg"></i></button>
                                </form>
                            @endif
                            <a href="{{ route('banner.template.configuration.edit', ['banner_template_id' => $banner_template->id]) }}" class="btn btn-link px-0 me-2"><i class="fa-solid fa-gear fa-lg"></i></a>
                            <form method="post" action="{{ route('banner.template.remove', ['banner_template_id' => $banner_template->id]) }}">
                                @method('delete')
                                @csrf
                                <button class="btn btn-link px-0 me-2" type="submit"><i class="fa fa-trash text-danger fa-lg"></i></button>
                            </form>
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

@include('modals.banners.modal-add-template')
@endsection

@extends('layout')

@section('site_title')
    Templates
@endsection

@section('dataTables_script')
    <script>
        $(document).ready( function () {
            $('#templates').DataTable({
                "oLanguage": {
                    "sLengthMenu": "_MENU_",
                },
                columnDefs:[
                    {
                        orderable: false,
                        targets: 2,
                    }
                ],
            });
        } );
    </script>
@endsection

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold fs-3">Templates</h1>
        </div>
    </div>
    <hr>
</div>
@can('add templates')
<div class="container">
    <div class="row">
        <div class="col-lg-3">
            <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addTemplate">
                Add Template
            </button>
        </div>
    </div>
    <hr>
</div>
@endcan
<div class="container mt-3">
    @include('inc.standard-alerts')
    @if($templates->count() == 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-primary" role="alert">
                    No templates have been uploaded yet!
                    @can('add templates')
                    <button class="btn btn-link p-0" type="button" data-bs-toggle="modal" data-bs-target="#addTemplate">Upload a new template now.</button>
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
                    <th scope="col">Alias</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($templates as $template)
                <tr>
                    <td class="col-lg-auto">
                        <img class="img-fluid w-75" src="{{ asset($template->file_path_original.'/'.$template->filename) }}" alt="{{ $template->alias }}">
                    </td>
                    <td class="col-lg-3">
                        <p class="fw-bold">{{ $template->alias }}</p>
                        <p>File Size: {{ ceil(filesize($template->file_path_original.'/'.$template->filename) / 1024) }} KiB</p>
                        <p>File Dimensions: {{ $template->width }}x{{ $template->height }} Pixel</p>
                        <p>Last Modified: {{ date('d.m.Y', strtotime($template->updated_at)) }}</p>
                    </td>
                    <td class="col-lg-2">
                        @can('edit templates')
                            <a href="#editTemplate-{{$template->id}}" data-bs-toggle="modal" data-bs-target="#editTemplate-{{$template->id}}"><i class="fa-solid fa-pen-to-square text-primary fa-lg me-1"></i></a>
                        @endcan
                        @can('delete templates')
                            <a href="#delTemplate-{{$template->id}}" data-bs-toggle="modal" data-bs-target="#delTemplate-{{$template->id}}"><i class="fa fa-trash text-danger fa-lg me-1"></i></a>
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

@include('modals.templates.modal-add')

@foreach($templates as $templateModal)
    @can('edit templates')
        @include('modals.templates.modal-edit', ['templateModal'=>$templateModal])
    @endcan
    @can('delete templates')
        @include('modals.delete-feedback.modal-delete-template', ['templateDeleteModal'=>$templateModal])
    @endcan
@endforeach

@endsection

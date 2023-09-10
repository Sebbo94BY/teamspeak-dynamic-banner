@extends('layout')

@section('site_title')
    Fonts
@endsection

@section('dataTables_script')
    <script>
        $(document).ready( function () {
            $('#fonts').DataTable({
                "oLanguage": {
                    "sLengthMenu": "_MENU_",
                }
            });
        } );
    </script>
@endsection

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold fs-3">Fonts</h1>
        </div>
    </div>
    <hr>
</div>
@can('add fonts')
<div class="container">
    <div class="row">
        <div class="col-lg-3">
            <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addFont">
                Add Font
            </button>
        </div>
    </div>
    <hr>
</div>
@endcan
<div class="container">
    @include('inc.standard-alerts')
    @if($fonts->count() == 0)
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-primary" role="alert">
                        There are no Fonts Uploaded!
                        @can('add fonts')
                        <a href="#addFont" data-bs-toggle="modal" data-bs-target="#addFont">Add Font now</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped" id="fonts">
                <thead>
                <tr>
                    <th class="col-lg-8">Filename</th>
                    <th class="col-lg-2">Last Modified</th>
                    <th class="col-lg-2"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($fonts as $font)
                <tr>
                    <td>{{ $font->filename }}</td>
                    <td>{{ $font->updated_at }}</td>
                    <td>
                        <div class="d-flex">
                            @can('edit fonts')
                                <button class="btn btn-link px-0 me-2" type="button" data-bs-toggle="modal" data-bs-target="#editFont-{{$font->id}}"><i class="fa-solid fa-pencil text-primary fa-lg"></i></button>
                            @endcan
                            @can('delete fonts')
                                <button class="btn btn-link px-0 me-2" type="button" data-bs-toggle="modal" data-bs-target="#delFont-{{$font->id}}"><i class="fa-solid fa-trash text-danger fa-lg"></i></button>
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
@include('modals.fonts.modal-add')

@foreach($fonts as $fontForEdit)
    @include('modals.fonts.modal-edit', ['fontForEdit'=>$fontForEdit])
@endforeach

@can('delete fonts')
    @foreach($fonts as $fontDeleteModal)
        @include('modals.delete-feedback.modal-delete-font', ['fontDeleteModal'=>$fontDeleteModal])
    @endforeach
@endcan

@endsection

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

@section('nav_link_active_fonts')
    active
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
<div class="container">
    @include('inc.standard-alerts')
    @if($fonts->count() == 0)
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-primary" role="alert">
                        There are no Fonts Uploaded! <a href="#addFont" data-bs-toggle="modal" data-bs-target="#addFont">Add Font now</a>
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
                    <td class="text-end">
                        @can('edit fonts')
                            <a href="#editFont-{{$font->id}}" data-bs-toggle="modal" data-bs-target="#editFont-{{$font->id}}"><i class="fa-solid fa-pencil text-primary fa-lg me-1"></i></a>
                        @endcan
                        @can('delete fonts')
                            <a href="{{ route('administration.font.delete', ['font_id' => $font->id]) }}"><i class="fa-solid fa-trash text-danger fa-lg me-1"></i></a>
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
@include('modals.fonts.modal-add')

@foreach($fonts as $fontEdit)
    @include('modals.fonts.modal-edit')
@endforeach
@endsection

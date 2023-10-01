@extends('layout')

@section('site_title')
    {{ __('views/administration/fonts.fonts') }}
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
            <h1 class="fw-bold fs-3">{{ __('views/administration/fonts.fonts') }}</h1>
        </div>
    </div>
    <hr>
</div>
@can('add fonts')
<div class="container">
    <div class="row">
        <div class="col-lg-3">
            <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addFont">
                {{ __('views/administration/fonts.add_font_button') }}
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
                        {{ __('views/administration/fonts.no_fonts_uploaded_info') }}
                        @can('add fonts')
                            <button class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#addFont">{{ __('views/administration/fonts.add_font_button') }}</button>
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
                    <th class="col-lg-8">{{ __('views/administration/fonts.table_filename') }}</th>
                    <th class="col-lg-2">{{ __('views/administration/fonts.table_last_modified') }}</th>
                    <th class="col-lg-2"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($fonts as $font)
                <tr>
                    <td>{{ $font->filename }}</td>
                    <td>{{ Carbon\Carbon::parse($font->updated_at)->setTimezone(Request::header('X-Timezone')) }}</td>
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
    @can('edit fonts')
        @include('modals.fonts.modal-edit', ['fontForEdit'=>$fontForEdit])
    @endcan
    @can('delete fonts')
        @include('modals.delete-feedback.modal-delete-font', ['fontDeleteModal'=>$fontForEdit])
    @endcan
@endforeach

@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __('Templates') }}

                    @can('add templates')
                    <a href="{{ route('template.add') }}" class="btn btn-primary">Add</a>
                    @endcan
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if (count($templates) > 0)
                    <table id="templates" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Template</th>
                                <th>Information</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $template)
                            <tr>
                                <td>
                                    <img class="img-fluid shadow-lg p-1 mb-2 bg-white rounded" style="max-height: 200px;" src="{{ asset($template->file_path_original.'/'.$template->filename) }}" alt="{{ $template->alias }}">
                                </td>
                                <td>
                                    <p><b>{{ $template->alias }}</b></p>
                                    <p>File Size: {{ ceil(filesize($template->file_path_original.'/'.$template->filename) / 1024) }} KiB</p>
                                    <p>File Dimensions: {{ $template->width }}x{{ $template->height }} Pixel</p>
                                    <p>Last Modified: {{ $template->updated_at }}</p>
                                </td>
                                <td>
                                    @can('edit templates')
                                    <a href="{{ route('template.edit', ['template_id' => $template->id]) }}" class="btn btn-info">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    @endcan

                                    @can('delete templates')
                                    <form method="POST" action="{{ route('template.delete', ['template_id' => $template->id]) }}">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p>You do not have any templates uploaded yet. Add your first template.</p>
                    @endif
                </div>
            </div>

            <script type="module">
                $(document).ready(function () {
                    $('#templates').DataTable();
                });
            </script>
        </div>
    </div>
</div>
@endsection

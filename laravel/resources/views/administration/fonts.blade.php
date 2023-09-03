@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __('Fonts') }}

                    @can('add fonts')
                    <a href="{{ route('administration.font.add') }}" class="btn btn-primary">Add</a>
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

                    <table id="fonts" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Last Modified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fonts as $font)
                            <tr>
                                <td>{{ $font->filename }}</td>
                                <td>{{ $font->updated_at }}</td>
                                <td>
                                    @can('edit fonts')
                                    <a href="{{ route('administration.font.edit', ['font_id' => $font->id]) }}" class="btn btn-info">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    @endcan

                                    @can('delete fonts')
                                    <form method="POST" action="{{ route('administration.font.delete', ['font_id' => $font->id]) }}">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    $(document).ready(function () {
        $('#fonts').DataTable();
    });
</script>
@endsection

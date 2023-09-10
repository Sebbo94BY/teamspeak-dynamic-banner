@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __("Add a template to your banner.") }}
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
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <table id="templates" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Template</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $template)
                            <tr>
                                <td>
                                    <img class="img-fluid shadow-lg p-1 mb-2 bg-white rounded opacity-100" style="max-height: 200px;" src="{{ asset($template->file_path_original.'/'.$template->filename) }}" alt="{{ $template->alias }}">
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('banner.add.template') }}" novalidate>
                                        @csrf
                                        <input type="hidden" name="banner_id" value="{{ $banner->id }}">
                                        <input type="hidden" name="template_id" value="{{ $template->id }}">

                                        <div class="mb-3">
                                            <label for="validationName" class="form-label">Name</label>
                                            <input class="form-control" id="validationName" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Event Announcement" aria-describedby="nameHelp" required>
                                            <div id="nameHelp" class="form-text">What will this template be about? Give it a descriptive name.</div>
                                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                            <div class="invalid-feedback">{{ __("Please provide a valid name.") }}</div>
                                        </div>

                                        <span class="badge" data-bs-toggle="tooltip" data-bs-html="true"
                                            title="Add this template. Make it configurable."
                                            id="add-badge">
                                            <button type="submit" class="btn btn-success"><i class="fa-solid fa-plus"></i></button>
                                        </span>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <script type="module">
                $(document).ready(function () {
                    $('#templates').DataTable();
                });

                // Enable Bootstrap Tooltips
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            </script>
        </div>
    </div>
</div>
@endsection

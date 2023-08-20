@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __("Define the templates for this banner.") }}

                    @can('edit banners')
                    <a href="{{ route('banner.template.add', ['banner_id' => $banner->id]) }}" class="btn btn-primary">Add</a>
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
                            @foreach($banner->templates as $banner_template)
                            <tr>
                                <td>
                                    <img class="img-fluid shadow-lg p-1 mb-2 bg-white rounded opacity-{{ ($banner_template->enabled) ? 100 : 50 }}" style="max-height: 200px;" src="{{ asset($banner_template->file_path_drawed_text.'/'.$banner_template->template->filename) }}" alt="{{ $banner_template->template->alias }}">
                                </td>
                                <td>
                                    @if ($banner_template->enabled)
                                        <form method="POST" action="{{ route('banner.template.disable', ['banner_template_id' => $banner_template->id]) }}" novalidate>
                                            @method('patch')
                                            @csrf

                                            <span class="badge" data-bs-toggle="tooltip" data-bs-html="true"
                                                title="Disable this configuration."
                                                id="disable-configuration-badge">
                                                <button type="submit" class="btn btn-info"><i class="fa-solid fa-toggle-on"></i></button>
                                            </span>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('banner.template.enable', ['banner_template_id' => $banner_template->id]) }}" novalidate>
                                            @method('patch')
                                            @csrf

                                            <span class="badge" data-bs-toggle="tooltip" data-bs-html="true"
                                                title="Enable this configuration."
                                                id="disable-configuration-badge">
                                                <button type="submit" class="btn btn-dark"><i class="fa-solid fa-toggle-off"></i></button>
                                            </span>
                                        </form>
                                    @endif

                                    <span class="badge" data-bs-toggle="tooltip" data-bs-html="true"
                                        title="Edit this configuration."
                                        id="configure-badge">
                                        <a href="{{ route('banner.template.configuration.edit', ['banner_template_id' => $banner_template->id]) }}" class="btn btn-info">
                                            <i class="fa-solid fa-gear"></i>
                                        </a>
                                    </span>

                                    <form method="POST" action="{{ route('banner.template.remove', ['banner_template_id' => $banner_template->id]) }}" novalidate>
                                        @method('delete')
                                        @csrf

                                        <span class="badge" data-bs-toggle="tooltip" data-bs-html="true"
                                            title="Delete this configuration."
                                            id="delete-badge">
                                            <button type="submit" class="btn btn-danger"><i class="fa-solid fa-minus"></i></button>
                                        </span>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <script>
                        // Example starter JavaScript for disabling form submissions if there are invalid fields
                        (function () {
                        'use strict'

                        // Fetch all the forms we want to apply custom Bootstrap validation styles to
                        var forms = document.querySelectorAll('.needs-validation')

                        // Loop over them and prevent submission
                        Array.prototype.slice.call(forms)
                            .forEach(function (form) {
                            form.addEventListener('submit', function (event) {
                                if (!form.checkValidity()) {
                                event.preventDefault()
                                event.stopPropagation()
                                }

                                form.classList.add('was-validated')
                            }, false)
                            })
                        })()
                    </script>
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

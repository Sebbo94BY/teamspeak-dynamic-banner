@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ __("Add a new font.") }}
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

                    <div class="alert alert-primary" role="alert">
                        <p>This project uses <i>TrueType Fonts (TTF)</i> for writing the text on the banner images.</p>

                        <p>Installation instructions:</p>
                        <ol>
                            <li>Visit for example <a href="https://fontsource.org" target="_blank">Fontsource.org</a></li>
                            <li>Specify filters or search for specific fonts</li>
                            <li>Checkout the font previews to find a font, which you like and open the font details page</li>
                            <li>Click on the download button for this specific font</li>
                            <li>Unzip the recently downloaded ZIP file</li>
                            <li>Select the required <code>*.ttf</code> file here</li>
                            <li>Submit the form</li>
                        </ol>

                        <p>You can upload and use any TTF file - it does not have to be from Fontsource.</p>
                    </div>

                    <form method="POST" action="{{ route('administration.font.create') }}" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="validationFile" class="form-label">Font</label>
                            <input class="form-control" id="validationFile" type="file" accept=".ttf" name="file" value="{{ old('file') }}" aria-describedby="fileHelp" required>
                            <div id="fileHelp" class="form-text">The fonts file. (TTF)</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid file.") }}</div>
                        </div>

                        <div class="mb-3">
                            <a href="{{ route('administration.fonts') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>

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
        </div>
    </div>
</div>
@endsection

<div class="modal fade" id="addFont" tabindex="-1" aria-labelledby="addFontLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="addFontLabel">Add new font</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('administration.font.create') }}" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        @include('inc.fonts.font-install-instruction')
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="validationFile" class="form-label">Font</label>
                                <input class="form-control" id="validationFile" type="file" accept=".ttf"
                                       name="file" value="{{ old('file') }}"
                                       aria-describedby="validationFileHelp validationFileFeedback" required>
                                <div id="validationFileHelp" class="form-text">The fonts file. (TTF)</div>
                                <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                <div id="validationFileFeedback" class="invalid-feedback">{{ __("Please provide a valid file.") }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('inc.form-validation')

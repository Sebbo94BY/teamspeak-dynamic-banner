<div class="modal fade" id="addFont" tabindex="-1" aria-labelledby="addFontLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="addFontLabel">{{ __('views/modals/fonts/modal-add.add_new_font') }}</h1>
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
                                <label for="validationFile" class="form-label">{{ __('views/modals/fonts/modal-add.font_file') }}</label>
                                <input class="form-control" id="validationFile" type="file" accept=".ttf"
                                       name="file" value="{{ old('file') }}"
                                       aria-describedby="validationFileHelp validationFileFeedback" required>
                                <div id="validationFileHelp" class="form-text">{{ __('views/modals/fonts/modal-add.font_file_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/fonts/modal-add.form_validation_looks_good') }}</div>
                                <div id="validationFileFeedback" class="invalid-feedback">{{ __('views/modals/fonts/modal-add.file_validation_error') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/fonts/modal-add.dismiss_button') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('views/modals/fonts/modal-add.upload_button') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('inc.form-validation')

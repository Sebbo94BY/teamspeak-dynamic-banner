<div class="modal fade" id="addTemplate" tabindex="-1" aria-labelledby="addTemplateLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="addTemplateLabel">{{ __('views/modals/templates/modal-add.add_template') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('template.save') }}" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="col-lg-12">
                        <div class="mb-3 row">
                            <label for="validationAlias" class="col-lg-1 col-form-label fw-bold">{{ __('views/modals/templates/modal-add.alias') }}</label>
                            <div class="col-lg-11">
                                <input type="text" class="form-control" id="validationAlias" name="alias" value="{{ old('alias') }}"
                                       aria-describedby="validationAliasHelp validationAliasFeedback" placeholder="{{ __('views/modals/templates/modal-add.alias_placeholder') }}" required>
                                <div id="validationAliasHelp" class="form-text">{{ __('views/modals/templates/modal-add.alias_help') }}</div>
                            </div>
                            <div class="valid-feedback">{{ __('views/modals/templates/modal-add.form_validation_looks_good') }}</div>
                            <div id="validationAliasFeedback" class="invalid-feedback">{{ __('views/modals/templates/modal-add.alias_validation_error') }}</div>
                        </div>
                        <div class="mb-3 row">
                            <label for="validationFile" class="col-lg-1 col-form-label fw-bold">{{ __('views/modals/templates/modal-add.template_file') }}</label>
                            <div class="col-lg-11">
                                <input type="file" class="form-control" id="validationFile" accept="image/png, image/jpeg" name="file"
                                       aria-describedby="validationFileHelp validationFileFeedback" value="{{ old('file') }}" required>
                                <div id="validationFileHelp" class="form-text">{{ __('views/modals/templates/modal-add.template_file_help') }}</div>
                            </div>
                            <div class="valid-feedback">{{ __('views/modals/templates/modal-add.form_validation_looks_good') }}</div>
                            <div id="validationFileFeedback" class="invalid-feedback">{{ __('views/modals/templates/modal-add.template_file_validation_error') }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/templates/modal-add.dismiss_button') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('views/modals/templates/modal-add.upload_button') }}</button>
                </div>
            </form>
            @include('inc.form-validation')
        </div>
    </div>
</div>

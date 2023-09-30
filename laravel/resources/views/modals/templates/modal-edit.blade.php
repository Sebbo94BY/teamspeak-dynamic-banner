<div class="modal fade" id="editTemplate-{{$templateModal->id}}" tabindex="-1" aria-labelledby="editTemplate-{{$templateModal->id}}-Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="editTemplate-{{$templateModal->id}}-Label">{!! __('views/modals/templates/modal-edit.edit_template', ['template_alias' => $templateModal->alias]) !!}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ Route('template.update', ['template_id' => $templateModal->id]) }}" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
                @method('patch')
                @csrf
                <div class="modal-body">
                    <div class="col-lg-12">
                        <div class="mb-3 row justify-content-center">
                            <img class="img-fluid shadow-lg p-1 mb-2 bg-white rounded w-75" src="{{ asset($templateModal->file_path_original.'/'.$templateModal->filename) }}" alt="{{ $templateModal->alias }}">
                        </div>
                        <div class="mb-3 row">
                            <label for="validationAlias" class="col-lg-1 col-form-label fw-bold">{{ __('views/modals/templates/modal-edit.alias') }}</label>
                            <div class="col-lg-11">
                                <input type="text" class="form-control" id="validationAlias" name="alias" value="{{ old('alias', $templateModal->alias) }}"
                                       aria-describedby="validationAliasHelp validationAliasFeedback-{{$templateModal->id}}"
                                       placeholder="{{ __('views/modals/templates/modal-edit.alias_placeholder') }}" required>
                                <div id="validationAliasHelp" class="form-text">{{ __('views/modals/templates/modal-edit.alias_help') }}</div>
                            </div>
                            <div class="valid-feedback">{{ __('views/modals/templates/modal-edit.form_validation_looks_good') }}</div>
                            <div id="validationAliasFeedback-{{$templateModal->id}}" class="invalid-feedback">{{ __('views/modals/templates/modal-edit.alias_validation_error') }}</div>
                        </div>
                        <div class="mb-3 row">
                            <label for="validationFile" class="col-lg-1 col-form-label fw-bold">{{ __('views/modals/templates/modal-edit.template_file') }}</label>
                            <div class="col-lg-11">
                                <input type="file" class="form-control" id="validationFile" accept="image/png, image/jpeg"
                                       aria-describedby="validationFileHelp validationFileFeedback-{{$templateModal->id}}"
                                       name="file" value="{{ old('file') }}" required>
                                <div id="validationFileHelp" class="form-text">{{ __('views/modals/templates/modal-edit.template_file_help') }}</div>
                            </div>
                            <div class="valid-feedback">{{ __('views/modals/templates/modal-edit.form_validation_looks_good') }}</div>
                            <div id="validationFileFeedback-{{$templateModal->id}}" class="invalid-feedback">{{ __('views/modals/templates/modal-edit.template_file_validation_error') }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/templates/modal-edit.dismiss_button') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('views/modals/templates/modal-edit.replace_button') }}</button>
                </div>
            </form>
            @include('inc.form-validation')
        </div>
    </div>
</div>

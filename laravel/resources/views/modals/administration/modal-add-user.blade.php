<div class="modal fade" id="newUser" tabindex="-1" aria-labelledby="newUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="addTemplateLabel">{{ __('views/modals/administration/modal-add-user.name') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('administration.user.create') }}" class="row g-3 needs-validation" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label for="validationName" class="form-label fw-bold">{{ __('views/modals/administration/modal-add-user.name') }}</label>
                            <input type="text" class="form-control" id="validationName" name="name" value="{{ old('name') }}" aria-describedby="validationNameHelp validationNameFeedback"
                                placeholder="{{ __('views/modals/administration/modal-add-user.name_placeholder') }}" required>
                            <div id="validationNameHelp" class="form-text">{{ __('views/modals/administration/modal-add-user.name_help') }}</div>
                            <div class="valid-feedback">{{ __('views/modals/administration/modal-add-user.form_validation_looks_good') }}</div>
                            <div id="validationNameFeedback" class="invalid-feedback">{{ __('views/modals/administration/modal-add-user.name_validation_error') }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="validationEmail" class="form-label fw-bold">{{ __('views/modals/administration/modal-add-user.email') }}</label>
                            <input type="email" class="form-control" id="validationEmail" name="email" value="{{ old('email') }}" aria-describedby="validationEmailHelp validationEmailFeedback"
                                placeholder="{{ __('views/modals/administration/modal-add-user.email_placeholder') }}" required>
                            <div id="validationEmailHelp" class="form-text">{{ __('views/modals/administration/modal-add-user.email_help') }}</div>
                            <div class="valid-feedback">{{ __('views/modals/administration/modal-add-user.form_validation_looks_good') }}</div>
                            <div id="validationEmailFeedback" class="invalid-feedback">{{ __('views/modals/administration/modal-add-user.email_validation_error') }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="validationRoles" class="form-label fw-bold">{{ __('views/modals/administration/modal-add-user.roles') }}</label>
                            <select class="form-select" id="validationRoles" multiple name="roles[]" aria-describedby="validationRolesHelp validationRolesFeedback">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <div id="validationRolesHelp" class="form-text">{{ __('views/modals/administration/modal-add-user.roles_help') }}</div>
                            <div class="valid-feedback">{{ __('views/modals/administration/modal-add-user.form_validation_looks_good') }}</div>
                            <div id="validationRolesFeedback" class="invalid-feedback">{{ __('views/modals/administration/modal-add-user.role_validation_error') }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/administration/modal-add-user.button_dismiss') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('views/modals/administration/modal-add-user.button_add') }}</button>
                </div>
            </form>
            @include('inc.form-validation')
        </div>
    </div>
</div>

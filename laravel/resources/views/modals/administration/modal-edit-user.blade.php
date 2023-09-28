<div class="modal fade" id="editUser-{{$userEdit->id}}" tabindex="-1" aria-labelledby="editUser-{{$userEdit->id}}-Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="editUser-{{$userEdit->id}}-Label">{!! __('views/modals/administration/modal-edit-user.edit_user', ['user_name' => $userEdit->name]) !!}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('administration.user.update', ['user_id' => $userEdit->id]) }}" class="row g-3 needs-validation" novalidate>
                @method('patch')
                @csrf
                <div class="modal-body">
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label for="validationName" class="form-label fw-bold">{{ __('views/modals/administration/modal-edit-user.name') }}</label>
                            <input type="text" class="form-control" id="validationName"
                                   name="name" value="{{ old('name', $userEdit->name) }}"
                                   aria-describedby="validationNameHelp validationNameFeedback-{{$userEdit->id}}"
                                   placeholder="{{ __('views/modals/administration/modal-edit-user.name_placeholder') }}" required>
                            <div id="validationNameHelp" class="form-text">{{ __('views/modals/administration/modal-edit-user.name_help') }}</div>
                            <div class="valid-feedback">{{ __('views/modals/administration/modal-edit-user.form_validation_looks_good') }}</div>
                            <div id="validationNameFeedback-{{$userEdit->id}}" class="invalid-feedback">{{ __('views/modals/administration/modal-edit-user.name_validation_error') }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="validationEmail" class="form-label fw-bold">{{ __('views/modals/administration/modal-edit-user.email') }}</label>
                            <input type="email" class="form-control" id="validationEmail"
                                   name="email" value="{{ old('email', $userEdit->email) }}"
                                   aria-describedby="validationEmailHelp validationEmailFeedback-{{$userEdit->id}}"
                                   placeholder="{{ __('views/modals/administration/modal-edit-user.email_placeholder') }}" required>
                            <div id="validationEmailHelp" class="form-text">{{ __('views/modals/administration/modal-edit-user.email_help') }}</div>
                            <div class="valid-feedback">{{ __('views/modals/administration/modal-edit-user.form_validation_looks_good') }}</div>
                            <div id="validationEmailFeedback-{{$userEdit->id}}" class="invalid-feedback">{{ __('views/modals/administration/modal-edit-user.email_validation_error') }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="validationRoles" class="form-label fw-bold">{{ __('views/modals/administration/modal-edit-user.roles') }}</label>
                            <select class="form-select" multiple id="validationRoles" name="roles[]" aria-describedby="validationRolesHelp validationRolesFeedback-{{$userEdit->id}}">
                                @foreach ($roles as $role)
                                    @if ($role->id == $user->hasRole($role->id))
                                        <option value="{{ $role->id }}" selected>{{ $role->name }}</option>
                                    @else
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div id="validationRolesHelp" class="form-text">{{ __('views/modals/administration/modal-edit-user.roles_help') }}</div>
                            <div class="valid-feedback">{{ __('views/modals/administration/modal-edit-user.form_validation_looks_good') }}</div>
                            <div id="validationRolesFeedback-{{$userEdit->id}}" class="invalid-feedback">{{ __('views/modals/administration/modal-edit-user.role_validation_error') }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/administration/modal-edit-user.button_dismiss') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('views/modals/administration/modal-edit-user.button_update') }}</button>
                </div>
            </form>
            @include('inc.form-validation')
        </div>
    </div>
</div>

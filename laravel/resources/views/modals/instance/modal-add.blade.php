<div class="modal fade" id="modalAddInstance" tabindex="-1" aria-labelledby="modalAddInstanceLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="modalAddInstanceLabel">{{ __('views/modals/instance/modal-add.add_instance') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('instance.save') }}" class="row g-3 needs-validation" novalidate>
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationHost">{{ __('views/modals/instance/modal-add.host') }}</label>
                                <input type="text" class="form-control" id="validationHost"
                                       name="host" value="{{ old('host') }}"
                                       aria-describedby="validationHostHelp validationHostFeedback"
                                       placeholder="{{ __('views/modals/instance/modal-add.host_placeholder') }}" required>
                                <div id="validationHostHelp" class="form-text">{{ __('views/modals/instance/modal-add.host_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-add.form_validation_looks_good') }}</div>
                                <div id="validationHostFeedback" class="invalid-feedback">{{ __('views/modals/instance/modal-add.host_validation_error') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationVoicePort">{{ __('views/modals/instance/modal-add.voice_port') }}</label>
                                <input type="number" class="form-control" id="validationVoicePort"
                                       name="voice_port"
                                       aria-describedby="validationVoicePortHelp validationVoicePortFeedback"
                                       min="1" max="65535" value="{{ old('voice_port', 9987) }}"
                                       placeholder="{{ __('views/modals/instance/modal-add.voice_port_placeholder') }}" required>
                                <div id="validationVoicePortHelp" class="form-text">{{ __('views/modals/instance/modal-add.voice_port_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-add.form_validation_looks_good') }}</div>
                                <div id="validationVoicePortFeedback" class="invalid-feedback">{{ __('views/modals/instance/modal-add.voice_port_validation_error') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationServerQueryPort">{{ __('views/modals/instance/modal-add.serverquery_port') }}</label>
                                <input type="number" class="form-control" id="validationServerQueryPort"
                                       name="serverquery_port"
                                       aria-describedby="validationServerQueryPortHelp validationServerQueryPortFeedback"
                                       min="1" max="65535" value="{{ old('serverquery_port', 10022) }}"
                                       placeholder="{{ __('views/modals/instance/modal-add.serverquery_port_placeholder') }}" required>
                                <div id="validationServerQueryPortHelp" class="form-text">{{ __('views/modals/instance/modal-add.serverquery_port_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-add.form_validation_looks_good') }}</div>
                                <div id="validationServerQueryPortFeedback" class="invalid-feedback">{{ __('views/modals/instance/modal-add.serverquery_port_validation_error') }}</div>
                            </div>
                            <div class="mb-3">
                                <label for="validationServerqueryEncryption" class="form-check-label fw-bold">{{ __('views/modals/instance/modal-add.serverquery_encryption') }}</label>
                                <input class="form-check-input ms-2" id="validationServerqueryEncryption" type="checkbox" name="is_ssh"
                                       aria-describedby="validationServerqueryEncryptionHelp validationServerqueryEncryptionFeedback" @if (old('is_ssh')) checked @endif>
                                <div id="validationServerqueryEncryptionHelp" class="form-text">{{ __('views/modals/instance/modal-add.serverquery_encryption_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-add.form_validation_looks_good') }}</div>
                                <div id="validationServerqueryEncryptionFeedback" class="invalid-feedback">{{ __('views/modals/instance/modal-add.serverquery_encryption_validation_error') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationServerQueryUsername">{{ __('views/modals/instance/modal-add.serverquery_username') }}</label>
                                <input type="text" class="form-control" id="validationServerQueryUsername"
                                       name="serverquery_username"
                                       aria-describedby=""
                                       value="{{ old('serverquery_username') }}" placeholder="{{ __('views/modals/instance/modal-add.serverquery_username_placeholder') }}" required>
                                <div class="form-text">{{ __('views/modals/instance/modal-add.serverquery_username_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-add.form_validation_looks_good') }}</div>
                                <div class="invalid-feedback">{{ __('views/modals/instance/modal-add.serverquery_username_validation_error') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationServerQueryPassword">{{ __('views/modals/instance/modal-add.serverquery_password') }}</label>
                                <input type="password" class="form-control" id="validationServerQueryPassword"
                                       name="serverquery_password"
                                       aria-describedby="validationServerQueryPasswordHelp validationServerQueryPasswordFeedback"
                                       value="{{ old('serverquery_password') }}"
                                       placeholder="{{ __('views/modals/instance/modal-add.serverquery_password_placeholder') }}" required>
                                <div id="validationServerQueryPasswordHelp" class="form-text">{{ __('views/modals/instance/modal-add.serverquery_password_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-add.form_validation_looks_good') }}</div>
                                <div id="validationServerQueryPasswordFeedback" class="invalid-feedback">{{ __('views/modals/instance/modal-add.serverquery_password_validation_error') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationClientNickname">{{ __('views/modals/instance/modal-add.client_nickname') }}</label>
                                <input type="text" class="form-control" id="validationClientNickname" maxlength="30"
                                       name="client_nickname"
                                       aria-describedby="validationClientNicknameHelp validationClientNicknameFeedback"
                                       value="{{ old('client_nickname') }}"
                                       placeholder="{{ __('views/modals/instance/modal-add.client_nickname_placeholder') }}" required>
                                <div id="validationClientNicknameHelp" class="form-text">{{ __('views/modals/instance/modal-add.client_nickname_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-add.form_validation_looks_good') }}</div>
                                <div id="validationClientNicknameFeedback" class="invalid-feedback">{{ __('views/modals/instance/modal-add.client_nickname_validation_error') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/instance/modal-add.dismiss_button') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('views/modals/instance/modal-add.add_button') }}</button>
                </div>
            </form>
            @include('inc.form-validation')
        </div>
    </div>
</div>

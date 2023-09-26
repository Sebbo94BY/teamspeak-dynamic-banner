<div class="modal fade" id="modalAddInstance" tabindex="-1" aria-labelledby="modalAddInstanceLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="modalAddInstanceLabel">Add an Instance</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('instance.save') }}" class="row g-3 needs-validation" novalidate>
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationHost">Host</label>
                                <input type="text" class="form-control" id="validationHost"
                                       name="host" value="{{ old('host') }}"
                                       aria-describedby="validationHostHelp validationHostFeedback"
                                       placeholder="e.g. my.teamspeak.local or 192.168.2.87" required>
                                <div id="validationHostHelp" class="form-text">The hostname, domain or IP address of your Teamspeak server.</div>
                                <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                <div id="validationHostFeedback" class="invalid-feedback">{{ __("Please provide a valid domain or IP address.") }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationVoicePort">Voice Port</label>
                                <input type="number" class="form-control" id="validationVoicePort"
                                       name="voice_port"
                                       aria-describedby="validationVoicePortHelp validationVoicePortFeedback"
                                       min="1" max="65535" value="{{ old('voice_port', 9987) }}" required>
                                <div id="validationVoicePortHelp" class="form-text">The Voice port of the TeamSpeak server to connect at.</div>
                                <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                <div id="validationVoicePortFeedback" class="invalid-feedback">{{ __("Please provide a valid port.") }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationServerQueryPort">ServerQuery Port</label>
                                <input type="number" class="form-control" id="validationServerQueryPort"
                                       name="serverquery_port"
                                       aria-describedby="validationServerQueryPortHelp validationServerQueryPortFeedback"
                                       min="1" max="65535" value="{{ old('serverquery_port', 10022) }}" required>
                                <div id="validationServerQueryPortHelp" class="form-text">The ServerQuery port of the Teamspeak server for executing commands and gathering data</div>
                                <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                <div id="validationServerQueryPortFeedback" class="invalid-feedback">{{ __("Please provide a valid port.") }}</div>
                            </div>
                            <div class="mb-3">
                                <label for="validationServerqueryEncryption" class="form-check-label fw-bold">Enable ServerQuery Encryption (SSH)</label>
                                <input class="form-check-input ms-2" id="validationServerqueryEncryption" type="checkbox" name="is_ssh"
                                       aria-describedby="validationServerqueryEncryptionHelp validationServerqueryEncryptionFeedback" @if (old('is_ssh')) checked @endif>
                                <div id="validationServerqueryEncryptionHelp" class="form-text">When enabled, the ServerQuery connection will be established via an encrypted SSH connection. The respective ServerQuery port must be set.</div>
                                <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                <div id="validationServerqueryEncryptionFeedback" class="invalid-feedback">{{ __("You can only enable or disable this checkbox.") }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationServerQueryUsername">ServerQuery Username</label>
                                <input type="text" class="form-control" id="validationServerQueryUsername"
                                       name="serverquery_username"
                                       aria-describedby=""
                                       value="{{ old('serverquery_username') }}" placeholder="e.g. serveradmin" required>
                                <div class="form-text">The ServerQuery username for the authentication.</div>
                                <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                <div class="invalid-feedback">{{ __("Please provide a valid username.") }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationServerQueryPassword">ServerQuery Password</label>
                                <input type="password" class="form-control" id="validationServerQueryPassword"
                                       name="serverquery_password"
                                       aria-describedby="validationServerQueryPasswordHelp validationServerQueryPasswordFeedback"
                                       value="{{ old('serverquery_password') }}" required>
                                <div id="validationServerQueryPasswordHelp" class="form-text">The password of the previous defined ServerQuery username</div>
                                <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                <div id="validationServerQueryPasswordFeedback" class="invalid-feedback">{{ __("Please provide a valid password.") }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationClientNickname">Client Nickname</label>
                                <input type="text" class="form-control" id="validationClientNickname" maxlength="30"
                                       name="client_nickname"
                                       aria-describedby="validationClientNicknameHelp validationClientNicknameFeedback"
                                       value="{{ old('client_nickname') }}"
                                       placeholder="e. g. Dynamic Banner" required>
                                <div id="validationClientNicknameHelp" class="form-text">How this client should be named on your TeamSpeak server. (Maximum length: 30 characters)</div>
                                <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                <div id="validationClientNicknameFeedback" class="invalid-feedback">{{ __("Please provide a valid nickname.") }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
            @include('inc.form-validation')
        </div>
    </div>
</div>

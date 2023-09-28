<div class="modal fade" id="modalEditInstance-{{$instanceModal->id}}" tabindex="-1" aria-labelledby="modalEditInstance-{{$instanceModal->id}}-Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="modalEditInstance-{{$instanceModal->id}}-Label">{!! __('views/modals/instance/modal-edit.edit_instance', ['virtualserver_name' => $instanceModal->virtualserver_name]) !!}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('instance.update', ['instance_id' => $instance->id]) }}" class="row g-3 needs-validation" novalidate>
                @method('patch')
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationHost">{{ __('views/modals/instance/modal-edit.host') }}</label>
                                <input type="text" class="form-control" id="validationHost" name="host" value="{{ old('host', $instanceModal->host) }}"
                                       aria-describedby="validationHostHelp validationHostFeedback-{{$instanceModal->id}}"
                                       placeholder="{{ __('views/modals/instance/modal-edit.host_placeholder') }}" required>
                                <div id="validationHostHelp" class="form-text">{{ __('views/modals/instance/modal-edit.host_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-edit.form_validation_looks_good') }}</div>
                                <div id="validationHostFeedback-{{$instanceModal->id}}" class="invalid-feedback">{{ __('views/modals/instance/modal-edit.host_validation_error') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationVoicePort">{{ __('views/modals/instance/modal-edit.voice_port') }}</label>
                                <input type="number" class="form-control" id="validationVoicePort"
                                       name="voice_port" min="1" max="65535"
                                       aria-describedby="validationVoicePortHelp validationVoicePortFeedback-{{$instanceModal->id}}"
                                       value="{{ old('voice_port', $instanceModal->voice_port) }}"
                                       placeholder="{{ __('views/modals/instance/modal-edit.voice_port_placeholder') }}" required>
                                <div id="validationVoicePortHelp" class="form-text">{{ __('views/modals/instance/modal-edit.voice_port_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-edit.form_validation_looks_good') }}</div>
                                <div id="validationVoicePortFeedback-{{$instanceModal->id}}" class="invalid-feedback">{{ __('views/modals/instance/modal-edit.voice_port_validation_error') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationServerQueryPort">{{ __('views/modals/instance/modal-edit.serverquery_port') }}</label>
                                <input type="number" class="form-control" id="validationServerQueryPort" name="serverquery_port" min="1" max="65535"
                                       aria-describedby="validationServerQueryPortHelp validationServerQueryPortFeedback-{{$instanceModal->id}}"
                                       value="{{ old('serverquery_port', $instanceModal->serverquery_port) }}"
                                       placeholder="{{ __('views/modals/instance/modal-edit.serverquery_port_placeholder') }}" required>
                                <div id="validationServerQueryPortHelp" class="form-text">{{ __('views/modals/instance/modal-edit.serverquery_port_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-edit.form_validation_looks_good') }}</div>
                                <div id="validationServerQueryPortFeedback-{{$instanceModal->id}}" class="invalid-feedback">{{ __('views/modals/instance/modal-edit.serverquery_port_validation_error') }}</div>
                            </div>
                            <div class="mb-3">
                                <label for="validationServerqueryEncryption" class="form-check-label fw-bold">{{ __('views/modals/instance/modal-edit.serverquery_encryption') }}</label>
                                <input class="form-check-input ms-2" id="validationServerqueryEncryption" type="checkbox" name="is_ssh"
                                       aria-describedby="validationServerqueryEncryptionHelp validationServerqueryEncryptionFeedback-{{$instanceModal->id}}" @if (old('is_ssh', $instance->is_ssh)) checked @endif>
                                <div id="validationServerqueryEncryptionHelp" class="form-text">{{ __('views/modals/instance/modal-edit.serverquery_encryption_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-edit.form_validation_looks_good') }}</div>
                                <div id="validationServerqueryEncryptionFeedback-{{$instanceModal->id}}" class="invalid-feedback">{{ __('views/modals/instance/modal-edit.serverquery_encryption_validation_error') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationServerQueryUsername">{{ __('views/modals/instance/modal-edit.serverquery_username') }}</label>
                                <input type="text" class="form-control" id="validationServerQueryUsername" name="serverquery_username"
                                       aria-describedby="validationServerQueryUsernameHelp validationServerQueryUsernameFeedback-{{$instanceModal->id}}"
                                       value="{{ old('serverquery_username', $instanceModal->serverquery_username) }}" placeholder="{{ __('views/modals/instance/modal-edit.serverquery_username_placeholder') }}" required>
                                <div id="validationServerQueryUsernameHelp" class="form-text">{{ __('views/modals/instance/modal-edit.serverquery_username_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-edit.form_validation_looks_good') }}</div>
                                <div id="validationServerQueryUsernameFeedback-{{$instanceModal->id}}" class="invalid-feedback">{{ __('views/modals/instance/modal-edit.serverquery_username_validation_error') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationServerQueryPassword">{{ __('views/modals/instance/modal-edit.serverquery_password') }}</label>
                                <input type="password" class="form-control" id="validationServerQueryPassword"
                                       aria-describedby="validationServerQueryPasswordHelp validationServerQueryPasswordFeedback-{{$instanceModal->id}}"
                                       value="{{ old('serverquery_password', $instance->serverquery_password) }}"
                                       name="serverquery_password" placeholder="{{ __('views/modals/instance/modal-edit.serverquery_password_placeholder') }}" required>
                                <div id="validationServerQueryPasswordHelp" class="form-text">{{ __('views/modals/instance/modal-edit.serverquery_password_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-edit.form_validation_looks_good') }}</div>
                                <div id="validationServerQueryPasswordFeedback-{{$instanceModal->id}}" class="invalid-feedback">{{ __('views/modals/instance/modal-edit.serverquery_password_validation_error') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="validationClientNickname">{{ __('views/modals/instance/modal-edit.client_nickname') }}</label>
                                <input type="text" class="form-control" id="validationClientNickname" maxlength="30" name="client_nickname"
                                       aria-describedby="validationClientNicknameHelp validationClientNicknameFeedback-{{$instanceModal->id}}"
                                       value="{{ old('client_nickname', $instanceModal->client_nickname) }}"
                                       placeholder="{{ __('views/modals/instance/modal-edit.client_nickname_placeholder') }}" required>
                                <div id="validationClientNicknameHelp" class="form-text">{{ __('views/modals/instance/modal-edit.client_nickname_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-edit.form_validation_looks_good') }}</div>
                                <div id="validationClientNicknameFeedback-{{$instanceModal->id}}" class="invalid-feedback">{{ __('views/modals/instance/modal-edit.client_nickname_validation_error') }}</div>
                            </div>
                            <div class="mb-3">
                                <label for="validationDefaultChannelId" class="form-label fw-bold">{{ __('views/modals/instance/modal-edit.default_channel') }}</label>
                                <select class="form-select" name="default_channel_id" id="validationDefaultChannelId"
                                        aria-describedby="validationDefaultChannelIdHelp validationDefaultChannelIdFeedback-{{$instanceModal->id}}">
                                <option value="" @if(!isset($channel_list[$instanceModal->id]['error'])) selected @endif>Default Channel</option>
                                    @if(isset($channel_list[$instanceModal->id]['error']))
                                        <option value="{{$instanceModal->default_channel_id}}" selected>{{$instanceModal->default_channel_id}}</option>
                                    @else
                                        @foreach($channel_list[$instanceModal->id]['channel_list'] as $channel)
                                            @if (old('default_channel_id', $instanceModal->default_channel_id) == $channel->cid) "selected"
                                                <option value="{{ $channel->cid }}" selected>{{ $channel->channel_name }}</option>
                                            @else
                                                <option value="{{ $channel->cid }}">{{ $channel->channel_name }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @if(isset($channel_list[$instanceModal->id]['error']))
                                    <div id="validationDefaultChannelIdHelp" class="form-text">An error has occurred. You see here the default channel ID instead of the channel name</div>
                                @else
                                    <div id="validationDefaultChannelIdHelp" class="form-text">{{ __('views/modals/instance/modal-edit.default_channel_validation_error') }}</div>
                                @endif
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-edit.form_validation_looks_good') }}</div>
                                <div id="validationDefaultChannelIdFeedback-{{$instanceModal->id}}" class="invalid-feedback">
                                    @if(isset($channel_list[$instanceModal->id]['error']))
                                        An error has occurred. You see here the default channel ID instead of the channel name
                                    @else
                                        {{ __("Please provide a valid channel (ID).") }}
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="validationAutostart" class="form-check-label fw-bold">{{ __('views/modals/instance/modal-edit.autostart') }}</label>
                                <input class="form-check-input ms-2" id="validationAutostart" type="checkbox" name="autostart_enabled"
                                       aria-describedby="validationAutostartHelp validationAutostartFeedback-{{$instanceModal->id}}" @if (old('autostart_enabled', $instance->autostart_enabled)) checked @endif>
                                <div id="validationAutostartHelp" class="form-text">{{ __('views/modals/instance/modal-edit.autostart_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/instance/modal-edit.form_validation_looks_good') }}</div>
                                <div id="validationAutostartFeedback-{{$instanceModal->id}}" class="invalid-feedback">{{ __('views/modals/instance/modal-edit.autostart_validation_error') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/instance/modal-edit.dismiss_button') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('views/modals/instance/modal-edit.update_button') }}</button>
                </div>
            </form>
            @include('inc.form-validation')
        </div>
    </div>
</div>

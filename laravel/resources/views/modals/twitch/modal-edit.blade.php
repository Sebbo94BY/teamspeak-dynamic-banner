<div class="modal fade" id="editTwitchStreamer-{{$streamer->id}}" tabindex="-1" aria-labelledby="editTwitchStreamer-{{$streamer->id}}-Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="editTwitchStreamer-{{$streamer->id}}-Label">{!! __('views/modals/twitch/modal-edit.update_streamer', ['stream_url' => $streamer->stream_url]) !!}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('administration.twitch.update_streamer', ['twitch_streamer_id' => $streamer->id]) }}" class="row g-3 needs-validation">
                @method('patch')
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                            <label for="validationStreamUrl" class="form-label">{{ __('views/modals/twitch/modal-edit.stream_url') }}</label>
                                <input class="form-control" id="validationStreamUrl" type="url" pattern="https://www.twitch.tv/.*" name="stream_url" value="{{ old('stream_url', $streamer->stream_url) }}"
                                    placeholder="{{ __('views/modals/twitch/modal-edit.stream_url_placeholder') }}"
                                    aria-describedby="validationStreamUrlHelp validationStreamUrlFeedback" required>
                                <div id="validationStreamUrlHelp" class="form-text">{{ __('views/modals/twitch/modal-edit.stream_url_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/twitch/modal-edit.form_validation_looks_good') }}</div>
                                <div id="validationStreamUrlFeedback" class="invalid-feedback">{{ __('views/modals/twitch/modal-edit.stream_url_validation_error') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/twitch/modal-edit.dismiss_button') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('views/modals/twitch/modal-edit.update_button') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('inc.form-validation')

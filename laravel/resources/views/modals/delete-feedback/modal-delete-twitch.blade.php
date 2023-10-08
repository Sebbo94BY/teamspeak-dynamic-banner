<div class="modal fade" id="deleteTwitchStreamer-{{$streamer->id}}" tabindex="-1" aria-labelledby="deleteTwitchStreamer-{{$streamer->id}}-Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="deleteTwitchStreamer-{{$streamer->id}}-Label">{{ __('views/modals/delete-feedback/modal-delete-twitch.delete_twitch_streamer') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="m-0">{!! __('views/modals/delete-feedback/modal-delete-twitch.are_you_sure_question', ['stream_url' => $streamer->stream_url]) !!}</p>
                <p class="m-0">{{ __('views/modals/delete-feedback/modal-delete-twitch.relation_deletion_hint') }}</p>
                <p class="fw-bold text-danger m-0">{{ __('views/modals/delete-feedback/modal-delete-twitch.not_revertible_hint') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/delete-feedback/modal-delete-twitch.dismiss_button') }}</button>
                <form method="post" action="{{ route('administration.twitch.delete_streamer', ['twitch_streamer_id' => $streamer->id]) }}">
                    @method('delete')
                    @csrf
                    <button class="btn btn-danger" type="submit">{{ __('views/modals/delete-feedback/modal-delete-twitch.delete_button') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

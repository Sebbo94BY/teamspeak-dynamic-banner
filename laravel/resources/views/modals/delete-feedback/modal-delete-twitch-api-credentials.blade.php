<div class="modal fade" id="deleteTwitchApiCredentials" tabindex="-1" aria-labelledby="deleteTwitchApiCredentialsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="deleteTwitchApiCredentialsLabel">{{ __('views/modals/delete-feedback/modal-delete-twitch-api-credentials.delete_twitch_api_credentials') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="m-0">{{ __('views/modals/delete-feedback/modal-delete-twitch-api-credentials.are_you_sure_question') }}</p>
                <p class="fw-bold text-danger m-0">{{ __('views/modals/delete-feedback/modal-delete-twitch-api-credentials.not_revertible_hint') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/delete-feedback/modal-delete-twitch-api-credentials.dismiss_button') }}</button>
                <form method="post" action="{{ route('administration.twitch.delete_api_credentials') }}">
                    @method('delete')
                    @csrf
                    <button class="btn btn-danger" type="submit">{{ __('views/modals/delete-feedback/modal-delete-twitch-api-credentials.delete_button') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

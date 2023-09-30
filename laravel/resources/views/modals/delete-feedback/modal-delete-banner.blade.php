<div class="modal fade" id="delBanner-{{$bannerDeleteModal->id}}" tabindex="-1" aria-labelledby="delBanner-{{$bannerDeleteModal->id}}-Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="delBanner-{{$bannerDeleteModal->id}}-Label">{{ __('views/modals/delete-feedback/modal-delete-banner.delete_banner') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="m-0">{!! __('views/modals/delete-feedback/modal-delete-banner.are_you_sure_question', ['banner_name' => $bannerDeleteModal->name]) !!}</p>
                <p class="m-0">{{ __('views/modals/delete-feedback/modal-delete-banner.relation_deletion_hint') }}</p>
                <p class="fw-bold text-danger m-0">{{ __('views/modals/delete-feedback/modal-delete-banner.not_revertible_hint') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/delete-feedback/modal-delete-banner.dismiss_button') }}</button>
                <form method="post" action="{{ route('banner.delete', ['banner_id' => $bannerDeleteModal->id]) }}">
                    @method('delete')
                    @csrf
                    <button class="btn btn-danger" type="submit">{{ __('views/modals/delete-feedback/modal-delete-banner.delete_button') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

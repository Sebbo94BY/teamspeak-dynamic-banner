<div class="modal fade" id="delFont-{{$fontDeleteModal->id}}" tabindex="-1" aria-labelledby="delFont-{{$fontDeleteModal->id}}-Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="delFont-{{$fontDeleteModal->id}}-Label">{{ __('views/modals/delete-feedback/modal-delete-font.delete_fontfile') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="m-0">{!! __('views/modals/delete-feedback/modal-delete-font.are_you_sure_question', ['font_filename' => $fontDeleteModal->filename]) !!}</p>
                <p class="m-0">{{ __('views/modals/delete-feedback/modal-delete-font.relation_deletion_hint') }}</p>
                <p class="fw-bold text-danger m-0">{{ __('views/modals/delete-feedback/modal-delete-font.not_revertible_hint') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/delete-feedback/modal-delete-font.dismiss_button') }}</button>
                <form method="post" action="{{ route('administration.font.delete', ['font_id' => $fontDeleteModal->id]) }}">
                    @method('delete')
                    @csrf
                    <button class="btn btn-danger" type="submit">{{ __('views/modals/delete-feedback/modal-delete-font.delete_button') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

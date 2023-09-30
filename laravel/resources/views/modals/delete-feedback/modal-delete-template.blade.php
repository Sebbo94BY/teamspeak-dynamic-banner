<div class="modal fade" id="delTemplate-{{$templateDeleteModal->id}}" tabindex="-1" aria-labelledby="delTemplate-{{$templateDeleteModal->id}}-Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="delTemplate-{{$templateDeleteModal->id}}-Label">{{ __('views/modals/delete-feedback/modal-delete-template.delete_template') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="m-0">{!! __('views/modals/delete-feedback/modal-delete-template.are_you_sure_question', ['template_alias' => $templateDeleteModal->alias]) !!}</p>
                <p class="m-0">{{ __('views/modals/delete-feedback/modal-delete-template.relation_deletion_hint') }}</p>
                <p class="fw-bold text-danger m-0">{{ __('views/modals/delete-feedback/modal-delete-template.not_revertible_hint') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/delete-feedback/modal-delete-template.dismiss_button') }}</button>
                <form method="post" action="{{ route('template.delete', ['template_id' => $templateDeleteModal->id]) }}">
                    @method('delete')
                    @csrf
                    <button class="btn btn-danger" type="submit">{{ __('views/modals/delete-feedback/modal-delete-template.delete_button') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

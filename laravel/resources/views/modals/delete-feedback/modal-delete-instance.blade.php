<div class="modal fade" id="delInstance-{{$instanceDeleteModal->id}}" tabindex="-1" aria-labelledby="delInstance-{{$instanceDeleteModal->id}}-Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="delInstance-{{$instanceDeleteModal->id}}-Label">{{ __('views/modals/delete-feedback/modal-delete-instance.delete_instance') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="m-0">{!! __('views/modals/delete-feedback/modal-delete-instance.are_you_sure_question', ['virtualserver_name' => $instanceDeleteModal->virtualserver_name]) !!}</p>
                <p class="m-0">{{ __('views/modals/delete-feedback/modal-delete-instance.relation_deletion_hint') }}</p>
                <p class="fw-bold text-danger m-0">{{ __('views/modals/delete-feedback/modal-delete-instance.not_revertible_hint') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/delete-feedback/modal-delete-instance.dismiss_button') }}</button>
                <form method="post" action="{{ route('instance.delete', ['instance_id' => $instanceDeleteModal->id]) }}">
                    @method('delete')
                    @csrf
                    <button class="btn btn-danger" type="submit">{{ __('views/modals/delete-feedback/modal-delete-instance.delete_button') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delUser-{{$userDeleteModal->id}}" tabindex="-1" aria-labelledby="delUser-{{$userDeleteModal->id}}-Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="delUser-{{$userDeleteModal->id}}-Label">{{ __('views/modals/delete-feedback/modal-delete-user.delete_user') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="m-0">{!! __('views/modals/delete-feedback/modal-delete-user.are_you_sure_question', ['user_name' => $userDeleteModal->name, 'user_email' => $userDeleteModal->email]) !!}</p>
                <p class="fw-bold text-danger m-0">{{ __('views/modals/delete-feedback/modal-delete-user.not_revertible_hint') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/delete-feedback/modal-delete-user.dismiss_button') }}</button>
                <form method="post" action="{{Route('administration.user.delete', ['user_id'=>$userDeleteModal->id])}}">
                    @method('delete')
                    @csrf
                    <button class="btn btn-danger" type="submit">{{ __('views/modals/delete-feedback/modal-delete-user.delete_button') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

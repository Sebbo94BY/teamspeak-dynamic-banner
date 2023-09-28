<div class="modal fade" id="rolePermission-{{$role->id}}" tabindex="-1" aria-labelledby="rolePermission-{{$role->id}}-Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="rolePermission-{{$role->id}}-Label">{!! __('views/modals/administration/modal-view-role-permissions.role_permissions', ['role_name' => $role->name]) !!}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <ul class="list-group list-group-flush">
                        @if($role->name == 'Super Admin')
                            <li class="list-group-item">{{ __('views/modals/administration/modal-view-role-permissions.all_permissions') }}</li>
                        @else
                            @foreach($role->permissions->pluck('name') as $permission)
                                <li class="list-group-item">{{ $permission }}</li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/administration/modal-view-role-permissions.button_dismiss') }}</button>
            </div>
        </div>
    </div>
</div>

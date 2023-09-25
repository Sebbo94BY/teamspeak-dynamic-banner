<div class="modal fade" id="roleRolesAndPermission-{{$userViewRoles->id}}" tabindex="-1" aria-labelledby="roleRolesAndPermission-{{$userViewRoles->id}}-Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="roleRolesAndPermission-{{$userViewRoles->id}}-Label">Role and Permission <code>{{$userViewRoles->name}}</code></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <div class="row row-cols-1 row-cols-md-3 g-2">
                        @foreach($userViewRoles->roles as $role)
                        <div class="col d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <h5 class="card-title">{{$role->name}}</h5>
                                    <ul>
                                        @if ($role->name == "Super Admin")
                                            <li>All permissions</li>
                                        @else
                                            @foreach ($role->permissions->pluck('name') as $permission)
                                                <li>{{ $permission }}</li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('administration.roles') }}" class="btn btn-primary">View Roles</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
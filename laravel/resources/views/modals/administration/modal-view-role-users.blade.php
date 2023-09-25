<div class="modal fade" id="roleMembers-{{$role->id}}" tabindex="-1" aria-labelledby="roleMembers-{{$role->id}}-Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="roleMembers-{{$role->id}}-Label">Role Permission <code>{{$role->name}}</code></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <ul class="list-group list-group-flush">
                        @foreach ($role->users as $user)
                        <li class="list-group-item">{{ $user->name }} (<code>{{ $user->email }}</code>)</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
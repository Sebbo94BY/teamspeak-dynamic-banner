<div class="modal fade" id="roleMembers-{{$roleUsers->id}}" tabindex="-1" aria-labelledby="roleMembers-{{$roleUsers->id}}-Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="roleMembers-{{$roleUsers->id}}-Label">Role Permission <code>{{$roleUsers->name}}</code></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-lg-12">
                    <ul class="list-group list-group-flush">
                        @foreach ($roleUsers->users as $member)
                        <li class="list-group-item">{{ $member->name }} (<code>{{ $member->email }}</code>)</li>
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
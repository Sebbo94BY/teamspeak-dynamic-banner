@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Roles') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif

                    <table id="roles" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Guard Name</th>
                                <th>Members</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->guard_name }}</td>
                                <td>{{ $role->users->count() }}</td>
                                <td>
                                    <a class="btn btn-info" data-bs-toggle="modal" href="#permissions-modal-rid-{{ $role->id }}" role="button">
                                        <i class="fa-solid fa-key"></i>
                                    </a>

                                    <a class="btn btn-info" data-bs-toggle="modal" href="#members-modal-rid-{{ $role->id }}" role="button">
                                        <i class="fa-solid fa-users"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach ($roles as $role)
<div class="modal modal-lg fade" id="permissions-modal-rid-{{ $role->id }}" aria-hidden="true" aria-labelledby="permissions-modal-rid-{{ $role->id }}-label" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissions-modal-rid-{{ $role->id }}-label">Role Permissions: <code>{{ $role->name }}</code></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
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

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-lg fade" id="members-modal-rid-{{ $role->id }}" aria-hidden="true" aria-labelledby="members-modal-rid-{{ $role->id }}-label" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="members-modal-rid-{{ $role->id }}-label">Role Members: <code>{{ $role->name }}</code></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <ul>
                    @foreach ($role->users as $member)
                        <li>{{ $member->name }} (<code>{{ $member->email }}</code>)</li>
                    @endforeach
                </ul>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script type="module">
    $(document).ready(function () {
        $('#roles').DataTable();
    });
</script>
@endsection

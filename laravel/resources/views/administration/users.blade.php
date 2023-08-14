@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Users') }} <a href="{{ route('administration.user.add') }}" class="btn btn-primary">Add</a></div>

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

                    <table id="users" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Registered since</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                @php
                                    $roles = [];
                                @endphp
                                @foreach ($user->roles as $role)
                                    @php
                                        $roles[] = $role->name;
                                    @endphp
                                @endforeach
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ implode(', ', $roles) }}</td>
                                <td>{{ $user->created_at }}</td>
                                <td>
                                    <a class="btn btn-primary" data-bs-toggle="modal" href="#roles-and-permissions-modal-uid-{{ $user->id }}" role="button">
                                        <i class="fa-solid fa-key"></i>
                                    </a>

                                    @can('edit users')
                                    <a href="{{ route('administration.user.edit', ['user_id' => $user->id]) }}" class="btn btn-info">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>

                                    @if (Auth::user()->id != $user->id)
                                    <form method="POST" action="{{ route('administration.user.delete', ['user_id' => $user->id]) }}">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash-can"></i></a>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <script type="module">
                $(document).ready(function () {
                    $('#users').DataTable();
                });
            </script>
        </div>
    </div>
</div>

@foreach($users as $user)
<div class="modal modal-lg fade" id="roles-and-permissions-modal-uid-{{ $user->id }}" aria-hidden="true" aria-labelledby="roles-and-permissions-modal-uid-{{ $user->id }}-label" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roles-and-permissions-modal-uid-{{ $user->id }}-label">Roles and Permissions: <code>{{ $user->name }}</code></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                @php
                    $role_counter = 0;
                @endphp

                @foreach ($user->roles as $role)
                    @if (($role_counter % 3) == 0)
                    <div class="col-md-12 card-group">
                    @endif
                        <div class="card" style="width: 18rem;">
                            <div class="card-body">
                                <h5 class="card-title">{{ $role->name }}</h5>
                                <p class="card-text">
                                    <ul>
                                        @if ($role->name == "Super Admin")
                                            <li>All permissions</li>
                                        @else
                                            @foreach ($role->permissions->pluck('name') as $permission)
                                                <li>{{ $permission }}</li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </p>
                            </div>
                        </div>
                    @if (($role_counter % 3) == 2)
                    </div>
                    @endif

                    @php
                        ++$role_counter;
                    @endphp
                @endforeach

                @if (! in_array($role_counter % 3, [0, 2]))
                </div>
                @endif
            </div>

            <div class="modal-footer">
                @can('view roles')
                <a href="{{ route('administration.roles') }}" class="btn btn-secondary">View Roles</a>
                @endcan
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

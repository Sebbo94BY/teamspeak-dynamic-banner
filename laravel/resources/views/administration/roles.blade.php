@extends('layout')

@section('site_title')
    Administration Roles and Permission | Dynamic Banner
@endsection

@section('nav_link_active_roles')
    active
@endsection

@section('dataTables_script')
    <script>
        $(document).ready( function () {
            $('#roles').DataTable({
                "oLanguage": {
                    "sLengthMenu": "_MENU_",
                },
                columnDefs:[
                    {
                        orderable: false,
                        targets: 3,
                    }
                ],
            });
        } );
    </script>
@endsection

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold fs-3">Administration Roles and Permission</h1>
        </div>
    </div>
    <hr>
</div>
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped" id="roles">
                <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Guard Name</th>
                    <th scope="col">Members</th>
                    <th scope="col">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                <tr>
                    <td class="col-lg-2">{{ $role->name }}</td>
                    <td class="col-lg-2">{{ $role->guard_name }}</td>
                    <td class="col-lg-2">
                        <span class="badge text-bg-secondary">{{ $role->users->count() }}</span>
                    </td>
                    <td class="col-lg-2">
                        <a href="#rolePermission-{{$role->id}}" data-bs-toggle="modal" data-bs-target="#rolePermission-{{$role->id}}"><i class="fa-solid fa-key text-primary fa-lg me-1"></i></a>
                        <a href="#roleMembers-{{$role->id}}" data-bs-toggle="modal" data-bs-target="#roleMembers-{{$role->id}}"><i class="fa-solid fa-users fa-lg me-1"></i></a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($roles as $rolePermission)
    @include('modals.administration.modal-view-role-permissions')
@endforeach

@foreach($roles as $roleUsers)
    @include('modals.administration.modal-view-role-users')
@endforeach

@endsection

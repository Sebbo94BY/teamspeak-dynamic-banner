@extends('layout')

@section('site_title')
    {{ __('views/administration/roles.roles_and_permissions') }}
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
                        targets: 2,
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
            <h1 class="fw-bold fs-3">{{ __('views/administration/roles.roles_and_permissions') }}</h1>
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
                    <th scope="col">{{ __('views/administration/roles.table_name') }}</th>
                    <th scope="col">{{ __('views/administration/roles.table_members') }}</th>
                    <th scope="col">{{ __('views/administration/roles.table_actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                <tr>
                    <td class="col-lg-2">{{ $role->name }}</td>
                    <td class="col-lg-2">
                        <span class="badge text-bg-secondary">{{ $role->users->count() }}</span>
                    </td>
                    <td class="col-lg-2">
                        <div class="d-flex">
                            <button class="btn btn-link px-0 me-2" type="button" data-bs-toggle="modal" data-bs-target="#rolePermission-{{$role->id}}"><i class="fa-solid fa-key text-primary fa-lg"></i></button>
                            <button class="btn btn-link px-0 me-2" type="button" data-bs-toggle="modal" data-bs-target="#roleMembers-{{$role->id}}"><i class="fa-solid fa-users fa-lg"></i></button>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($roles as $role)
    @include('modals.administration.modal-view-role-permissions', ['role'=>$role])
    @include('modals.administration.modal-view-role-users', ['role'=>$role])
@endforeach

@endsection

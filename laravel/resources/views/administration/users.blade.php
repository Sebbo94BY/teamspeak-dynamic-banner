@extends('layout')

@section('site_title')
    {{ __('views/administration/users.users') }}
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
                        targets: 4,
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
            <h1 class="fw-bold fs-3">{{ __('views/administration/users.users') }}</h1>
        </div>
    </div>
    <hr>
</div>
@can('add users')
<div class="container">
    <div class="row">
        <div class="col-lg-3">
            <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#newUser">
                {{ __('views/administration/users.add_user_button') }}
            </button>
        </div>
    </div>
    <hr>
</div>
@endcan
<div class="container mt-3">
    @include('inc.standard-alerts')
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped" id="roles">
                <thead>
                <tr>
                    <th scope="col">{{ __('views/administration/users.table_name') }}</th>
                    <th scope="col">{{ __('views/administration/users.table_email') }}</th>
                    <th scope="col">{{ __('views/administration/users.table_roles') }}</th>
                    <th scope="col">{{ __('views/administration/users.table_registered_since') }}</th>
                    <th scope="col">{{ __('views/administration/users.table_actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="col-lg-2">
                        {{ $user->name }}
                    </td>
                    <td class="col-lg-2">
                        {{ $user->email }}
                    </td>
                    <td class="col-lg-4">
                        @foreach($user->getRoleNames() as $role)
                            @switch(true)
                                @case($role == 'Super Admin')
                                    <span class="badge text-bg-danger">{{$role}}</span>
                                    @break
                                @case(preg_match('/Admin/i', $role))
                                    <span class="badge text-bg-warning">{{$role}}</span>
                                    @break
                                @default
                                    <span class="badge text-bg-primary">{{$role}}</span>
                            @endswitch
                        @endforeach
                    </td>
                    <td class="col-lg-2">
                        {{ \Illuminate\Support\Carbon::parse($user->created_at)->format('d.m.Y') }}
                    </td>
                    <td class="col-lg-2">
                        <div class="d-flex">
                            <button class="btn btn-link px-0 me-2" type="button" data-bs-toggle="modal" data-bs-target="#roleRolesAndPermission-{{$user->id}}"><i class="fa-solid fa-key text-primary fa-lg"></i></button>
                            @can('edit users')
                                <button class="btn btn-link px-0 me-2" type="button" data-bs-toggle="modal" data-bs-target="#editUser-{{$user->id}}"><i class="fa-solid fa-pencil text-primary fa-lg"></i></button>
                            @endcan
                            @if (Auth::user()->id != $user->id)
                                @can('delete users')
                                    <button class="btn btn-link px-0 me-2" type="button" data-bs-toggle="modal" data-bs-target="#delUser-{{$user->id}}"><i class="fa fa-trash text-danger fa-lg"></i></button>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-lg-12">
            <p>
                <span class="fw-bold">{{ __('views/administration/users.legend_label') }}:</span>
                <span class="badge text-bg-danger">{{ __('views/administration/users.legend_super_admin') }}</span>
                <span class="badge text-bg-warning">{{ __('views/administration/users.legend_area_admin') }}</span>
                <span class="badge text-bg-primary">{{ __('views/administration/users.legend_limited_role') }}</span>
            </p>
        </div>
    </div>
</div>

@foreach($users as $userEdit)
    @can('edit users')
        @include('modals.administration.modal-edit-user', ['userEdit'=>$userEdit])
    @endcan
    @can('delete users')
        @if (Auth::user()->id != $userEdit->id)
            @include('modals.delete-feedback.modal-delete-user', ['userDeleteModal'=>$userEdit])
        @endif
    @endcan
    @include('modals.administration.modal-view-user-roles', ['userViewRoles'=>$userEdit])
@endforeach

@can('add users')
    @include('modals.administration.modal-add-user')
@endcan

@endsection

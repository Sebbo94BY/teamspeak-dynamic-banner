@extends('layout')

@section('site_title')
    {{ __('views/instances.instances') }}
@endsection

@section('dataTables_config')
    <script type="module">
        $(document).ready( function () {
            $('#instances').DataTable({
                "oLanguage": {
                    "sLengthMenu": "_MENU_",
                },
                columnDefs:[
                    {
                        orderable: false,
                        targets: 5,
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
            <h1 class="fw-bold fs-3">{{ __('views/instances.instances') }}</h1>
        </div>
    </div>
    <hr>
</div>
@can('add instances')
<div class="container">
    <div class="row">
        <div class="col-lg-3">
            <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAddInstance">
                {{ __('views/instances.add_instance') }}
            </button>
        </div>
    </div>
    <hr>
</div>
@endcan
<div class="container mt-3">
@include('inc.standard-alerts')
    @if ($instances->count() == 0)
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-primary" role="alert">
                {{ __('views/instances.no_instance_added_yet') }}
                @can('add instances')
                    <button class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#modalAddInstance">{{ __('views/instances.add_instance') }}</button>
                @endcan
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped" id="instances">
                <thead>
                <tr>
                    <th scope="col">{{ __('views/instances.table_status') }}</th>
                    <th scope="col">{{ __('views/instances.table_server_name') }}</th>
                    <th scope="col">{{ __('views/instances.table_host') }}</th>
                    <th scope="col">{{ __('views/instances.table_voice_port') }}</th>
                    <th scope="col">{{ __('views/instances.table_client_nickname') }}</th>
                    <th scope="col">{{ __('views/instances.table_actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($instances as $instance)
                <tr>
                    <td class="col-lg-1">
                        @if(is_null($instance->process))
                            <span class="badge text-bg-danger" data-bs-toggle="tooltip" data-bs-html="true"
                                title="{{ __('views/instances.table_status_stopped_title') }}"
                                id="status-badge-stopped">{{ __('views/instances.table_status_stopped') }}
                            </span>
                        @else
                            <span class="badge text-bg-success" data-bs-toggle="tooltip" data-bs-html="true"
                                title="{!! __('views/instances.table_status_running_title', ['process_id' => $instance->process->process_id, 'started_at' => $instance->process->created_at]) !!}"
                                id="status-badge-running">{{ __('views/instances.table_status_running') }}
                            </span>
                        @endif
                    </td>
                    <td class="col-lg-4">
                        {{ $instance->virtualserver_name }}
                    </td>
                    <td class="col-lg-3">
                        {{ $instance->host }}
                        @if($instance->is_ssh)
                            <span class="badge text-bg-success ms-2" data-bs-toggle="tooltip" data-bs-html="true"
                                title="{{ $instance->serverquery_port }} (TCP)"
                                id="instance-port-ssh-badge">SSH
                            </span>
                        @else
                            <span class="badge text-bg-warning ms-2" data-bs-toggle="tooltip" data-bs-html="true"
                                title="{{ $instance->serverquery_port }} (TCP)"
                                id="instance-port-raw-badge">RAW
                            </span>
                        @endif
                    </td>
                    <td class="col-lg-1">
                        {{ $instance->voice_port }}
                    </td>
                    <td class="col-lg-1">
                        {{ $instance->client_nickname }}
                    </td>
                    <td class="col-lg-2">
                        <div class="d-flex">
                            @if (is_null($instance->process))
                                @can('start instances')
                                    <form method="post" action="{{ route('instance.start', ['instance_id' => $instance->id]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-link px-0 me-2"><i class="fa-solid fa-play text-success fa-lg"></i></button>
                                    </form>
                                @endcan
                            @else
                                @can('stop instances')
                                    <form method="post" action="{{ route('instance.stop', ['instance_id' => $instance->id]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-link px-0 me-2"><i class="fa-solid fa-power-off text-warning fa-lg"></i></button>
                                    </form>
                                @endcan
                            @endif
                            @can('restart instances')
                                <form method="post" action="{{ route('instance.restart', ['instance_id' => $instance->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link px-0 me-2"><i class="fa-solid fa-rotate-left text-warning fa-lg"></i></button>
                                </form>
                            @endcan
                            @can('edit instances')
                                <button class="btn btn-link px-0 me-2" type="button" data-bs-toggle="modal" data-bs-target="#modalEditInstance-{{$instance->id}}"><i class="fa-solid fa-pencil text-primary fa-lg"></i></button>
                            @endcan
                            @can('delete instances')
                                <button class="btn btn-link px-0 me-2" type="button" data-bs-toggle="modal" data-bs-target="#delInstance-{{$instance->id}}"><i class="fa-solid fa-trash text-danger fa-lg"></i></button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@include('modals.instance.modal-add')

@foreach($instances as $instanceModal)
    @can('edit instances')
        @include('modals.instance.modal-edit', ['instanceModal'=>$instanceModal,'channel_list'=>$channel_list])
    @endcan
    @can('delete instances')
        @include('modals.delete-feedback.modal-delete-instance', ['instanceDeleteModal'=>$instanceModal])
    @endcan
@endforeach

@endsection

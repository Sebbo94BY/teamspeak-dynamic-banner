@extends('layout')

@section('site_title')
    Instances | Dynamic Banner
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
                <h1 class="fw-bold fs-3">Instances</h1>
            </div>
        </div>
        <hr>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAddInstance">
                    Add Instance
                </button>
            </div>
        </div>
        <hr>
        @include('inc.standard-alerts')
    </div>
    <div class="container mt-3">
        @if ($instances->count() == 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-primary" role="alert">
                    There are no instance configured! <a href="#modalAddInstance" data-bs-toggle="modal" data-bs-target="#modalAddInstance">Add now a new instance</a>
                </div>
            </div>
        </div>
        @else
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-striped" id="instances">
                    <thead>
                    <tr>
                        <th scope="col">Status</th>
                        <th scope="col">Server Name</th>
                        <th scope="col">Host</th>
                        <th scope="col">Voice Port</th>
                        <th scope="col">Client Nickname</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($instances as $instance)
                    <tr>
                        <td class="col-lg-1">
                            @if(is_null($instance->process))
                                <span class="badge text-bg-danger">Stopped</span>
                            @else
                                <span class="badge text-bg-success"
                                      data-bs-toggle="tooltip"
                                      data-bs-html="true"
                                      title="PID <b>{{ $instance->process->process_id }}</b> is active since <b>{{ $instance->process->created_at }} UTC</b>."
                                      id="status-badge">Connected
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
                            @if (is_null($instance->process))
                            @can('start instances')
                                <a href="{{ route('instance.start', ['instance_id' => $instance->id]) }}"><i class="fa-solid fa-play text-success me-2 fa-lg"></i></a>
                            @endcan
                            @else
                            @can('stop instances')
                                <a href="{{ route('instance.stop', ['instance_id' => $instance->id]) }}"><i class="fa-solid fa-power-off text-warning me-2 fa-lg"></i></a>
                            @endcan
                            @endif
                            @can('restart instances')
                                <a href="{{ route('instance.restart', ['instance_id' => $instance->id]) }}"><i class="fa-solid fa-rotate-left text-warning me-2 fa-lg"></i></a>
                            @endcan
                            @can('edit instances')
                                <a href="#modalEditInstance-{{$instance->id}}" data-bs-toggle="modal" data-bs-target="#modalEditInstance-{{$instance->id}}"><i class="fa-solid fa-pencil text-primary me-2 fa-lg"></i></a>
                            @endcan
                            @can('delete instances')
                                <a href="{{ route('instance.delete', ['instance_id' => $instance->id]) }}"><i class="fa-solid fa-trash text-danger me-2 fa-lg"></i></a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    <script type="module">
        $(document).ready(function () {
            $('#instances').DataTable();
        });

        // Enable Bootstrap Tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    </script>
    @include('modals.instance.modal-add')
    @foreach($instances as $instanceModal)
        @can('edit instances')
            @include('modals.instance.modal-edit')
        @endcan
    @endforeach
@endsection

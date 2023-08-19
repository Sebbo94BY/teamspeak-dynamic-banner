@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __('Instances') }}

                    @can('add instances')
                    <a href="{{ route('instance.add') }}" class="btn btn-primary">Add</a>
                    @endcan
                </div>

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

                    @if (session('success') == 'instance-start-successful' or session('success') == 'instance-restart-successful')
                        <script>
                            setTimeout(function() {
                                location.reload();
                            }, 5000);
                        </script>
                    @endif

                    @if (count($instances) > 0)
                    <table id="instances" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Server Name</th>
                                <th>Host</th>
                                <th>Voice Port</th>
                                <th>Client Nickname</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($instances as $instance)
                            <tr>
                                <td>
                                    @if (is_null($instance->process))
                                    <span class="badge bg-danger">Stopped</span>
                                    @else
                                    <span class="badge bg-success" data-bs-toggle="tooltip" data-bs-html="true"
                                        title="PID <b>{{ $instance->process->process_id }}</b> is active since <b>{{ $instance->process->created_at }} UTC</b>."
                                        id="status-badge">
                                        Connected
                                    </span>
                                    @endif
                                </td>
                                <td>{{ $instance->virtualserver_name }}</td>
                                <td>
                                    {{ $instance->host }}
                                    @if ($instance->is_ssh)
                                    <span class="badge bg-success" data-bs-toggle="tooltip" data-bs-html="true"
                                        title="{{ $instance->serverquery_port }} (TCP)"
                                        id="instance-port-ssh-badge">
                                        SSH
                                    </span>
                                    @else
                                    <span class="badge bg-warning" data-bs-toggle="tooltip" data-bs-html="true"
                                        title="{{ $instance->serverquery_port }} (TCP)"
                                        id="instance-port-raw-badge">
                                        RAW
                                    </span>
                                    @endif
                                </td>
                                <td>{{ $instance->voice_port }}</td>
                                <td>{{ $instance->client_nickname }}</td>
                                <td>
                                    @if (is_null($instance->process))
                                        @can('start instances')
                                        <form method="POST" action="{{ route('instance.start', ['instance_id' => $instance->id]) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                <i class="fa-solid fa-play"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    @else
                                        @can('stop instances')
                                        <form method="POST" action="{{ route('instance.stop', ['instance_id' => $instance->id]) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fa-solid fa-power-off"></i>
                                            </button>
                                        </form>
                                        @endcan

                                        @can('restart instances')
                                        <form method="POST" action="{{ route('instance.restart', ['instance_id' => $instance->id]) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fa-solid fa-arrow-rotate-left"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    @endif

                                    @can('edit instances')
                                    <a href="{{ route('instance.edit', ['instance_id' => $instance->id]) }}" class="btn btn-info">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    @endcan

                                    @can('delete instances')
                                    <form method="POST" action="{{ route('instance.delete', ['instance_id' => $instance->id]) }}">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p>You do not have any instances configured yet. Add your first instance.</p>
                    @endif
                </div>
            </div>

            <script type="module">
                $(document).ready(function () {
                    $('#instances').DataTable();
                });

                // Enable Bootstrap Tooltips
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            </script>
        </div>
    </div>
</div>
@endsection

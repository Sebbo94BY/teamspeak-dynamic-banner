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
    <div id="instance-lifecycle-progress" class="alert alert-info d-none" role="status" aria-live="polite">
        <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>
        <span id="instance-lifecycle-progress-message"></span>
    </div>
    @if (session('refresh_status_after_seconds'))
        <div id="instance-status-refresh" class="alert alert-info" role="status" aria-live="polite" data-refresh-after-seconds="{{ session('refresh_status_after_seconds') }}">
            <div class="d-flex align-items-center mb-2">
                <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>
                <span>{{ __('views/instances.status_refresh_scheduled', ['seconds' => session('refresh_status_after_seconds')]) }}</span>
            </div>
            <div class="progress" role="progressbar" aria-label="{{ __('views/instances.status_refresh_progress') }}" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
            </div>
        </div>
    @endif
    @if($attention === 'stopped')
        <div class="alert alert-warning" role="alert">{{ __('views/instances.attention_stopped_filter') }}</div>
    @endif
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
                                title="{!! __('views/instances.table_status_running_title', [
                                        'process_id' => $instance->process->process_id,
                                        'started_at' => Carbon\Carbon::parse($instance->process->created_at)->setTimezone(Request::header('X-Timezone')),
                                        'timezone' => Request::header('X-Timezone'),
                                    ]) !!}"
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
                                    <form method="post" action="{{ route('instance.start', ['instance_id' => $instance->id]) }}" class="instance-lifecycle-form" data-progress-message="{{ __('views/instances.starting_instance') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-link px-0 me-2"><i class="fa-solid fa-play text-success fa-lg"></i></button>
                                    </form>
                                @endcan
                            @else
                                @can('stop instances')
                                    <form method="post" action="{{ route('instance.stop', ['instance_id' => $instance->id]) }}" class="instance-lifecycle-form" data-progress-message="{{ __('views/instances.stopping_instance') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-link px-0 me-2"><i class="fa-solid fa-power-off text-warning fa-lg"></i></button>
                                    </form>
                                @endcan
                            @endif
                            @can('restart instances')
                                <form method="post" action="{{ route('instance.restart', ['instance_id' => $instance->id]) }}" class="instance-lifecycle-form" data-progress-message="{{ __('views/instances.restarting_instance') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link px-0 me-2"><i class="fa-solid fa-rotate-left text-warning fa-lg"></i></button>
                                </form>
                            @endcan
                            <button class="btn btn-link px-0 me-2" type="button" data-bs-toggle="modal" data-bs-target="#modalAvailableVariables-{{$instance->id}}"><i class="fa-solid fa-square-root-variable text-primary fa-lg"></i></button>
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

@if(request()->boolean('create'))
<script>
    document.addEventListener('DOMContentLoaded', () => new bootstrap.Modal('#modalAddInstance').show());
</script>
@endif

@foreach($instances as $instanceModal)
    @include('modals.modal-variables', ['instanceVariableModal' => $instanceModal])

    @can('edit instances')
        @include('modals.instance.modal-edit', ['instanceModal'=>$instanceModal,'channel_list'=>$channel_list])
    @endcan

    @can('delete instances')
        @include('modals.delete-feedback.modal-delete-instance', ['instanceDeleteModal'=>$instanceModal])
    @endcan
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const lifecycleProgress = document.querySelector('#instance-lifecycle-progress');
        const lifecycleMessage = document.querySelector('#instance-lifecycle-progress-message');

        document.querySelectorAll('.instance-lifecycle-form').forEach((form) => {
            form.addEventListener('submit', () => {
                lifecycleMessage.textContent = form.dataset.progressMessage;
                lifecycleProgress.classList.remove('d-none');

                document.querySelectorAll('.instance-lifecycle-form button[type="submit"]').forEach((button) => {
                    button.disabled = true;
                });
            });
        });

        const scheduledRefresh = document.querySelector('#instance-status-refresh');
        if (!scheduledRefresh) {
            return;
        }

        const refreshAfterSeconds = Number(scheduledRefresh.dataset.refreshAfterSeconds);
        const progressBar = scheduledRefresh.querySelector('.progress-bar');
        const startedAt = Date.now();
        const updateProgress = () => {
            const elapsedSeconds = (Date.now() - startedAt) / 1000;
            const remainingPercent = Math.max(0, 100 - (elapsedSeconds / refreshAfterSeconds * 100));
            progressBar.style.width = `${remainingPercent}%`;
            progressBar.setAttribute('aria-valuenow', String(Math.round(remainingPercent)));
        };

        const progressTimer = window.setInterval(updateProgress, 250);
        window.setTimeout(() => {
            window.clearInterval(progressTimer);
            window.location.reload();
        }, refreshAfterSeconds * 1000);
    });
</script>

@endsection

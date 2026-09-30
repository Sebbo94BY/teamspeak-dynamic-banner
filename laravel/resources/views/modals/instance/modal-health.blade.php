<div class="modal fade" id="modalInstanceHealth-{{ $instanceHealthModal->id }}" tabindex="-1" aria-labelledby="modalInstanceHealth-{{ $instanceHealthModal->id }}-Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="modalInstanceHealth-{{ $instanceHealthModal->id }}-Label">
                    {{ __('views/instances.health_modal_title', ['virtualserver_name' => $instanceHealthModal->virtualserver_name]) }}
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @php($hasUnresolvedRuntimeError = $instanceHealthModal->health_last_runtime_error_at && ! $instanceHealthModal->health_last_success_at?->greaterThan($instanceHealthModal->health_last_runtime_error_at))
                @php($healthStatusHealthy = $instanceHealth['checked'] && $instanceHealth['healthy'] && ! $hasUnresolvedRuntimeError)
                <div class="alert alert-{{ ! $instanceHealth['checked'] ? 'secondary' : ($healthStatusHealthy ? 'success' : 'danger') }}" role="alert">
                    {{ ! $instanceHealth['checked'] ? __('views/instances.health_pending_title') : ($healthStatusHealthy ? __('views/instances.health_ok_title') : __('views/instances.health_failed_title')) }}
                </div>
                @if($instanceHealthModal->health_last_checked_at)
                    <p class="small text-muted">{{ __('views/instances.health_last_checked_at', ['time' => $instanceHealthModal->health_last_checked_at->setTimezone(Request::header('X-Timezone'))]) }}</p>
                @endif
                @if($instanceHealthModal->bot_restart_scheduled_at)
                    <div class="alert alert-warning" role="alert">
                        <strong>{{ __('views/instances.bot_restart_scheduled') }}</strong><br>
                        {{ __('views/instances.bot_restart_scheduled_at', ['time' => $instanceHealthModal->bot_restart_scheduled_at->setTimezone(Request::header('X-Timezone'))]) }}<br>
                        <span class="small">{{ __('views/instances.bot_restart_reason') }}: {{ $instanceHealthModal->bot_restart_reason }}</span>
                    </div>
                @endif
                @if($instanceHealth['checks'] !== [])
                    <div class="list-group">
                        @foreach($instanceHealth['checks'] as $check)
                        <div class="list-group-item d-flex align-items-start gap-2">
                            <i class="fa-solid fa-{{ $check['healthy'] ? 'circle-check text-success' : 'circle-xmark text-danger' }} mt-1" aria-hidden="true"></i>
                            <div>
                                <strong>{{ __('views/instances.'.$check['label']) }}</strong><br>
                                <span class="{{ $check['healthy'] ? 'text-success' : 'text-danger' }}">{{ $check['message'] }}</span>
                                @if($check['permissions'] !== [])
                                    <br><span class="small text-muted">{{ __('views/instances.health_required_permissions') }}: <code>{{ implode(', ', $check['permissions']) }}</code></span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
                @if($instanceHealthModal->health_last_error_at)
                    @php($errorResolved = ! $hasUnresolvedRuntimeError && $instanceHealth['healthy'])
                    <div class="alert alert-{{ $errorResolved ? 'warning' : 'danger' }} mt-3 mb-0" role="alert">
                        <strong>{{ $errorResolved ? __('views/instances.health_last_error_resolved') : __('views/instances.health_last_error') }}</strong><br>
                        {{ $instanceHealthModal->health_last_error }}<br>
                        <span class="small">{{ $instanceHealthModal->health_last_error_at->setTimezone(Request::header('X-Timezone')) }}</span>
                        @if($instanceHealthModal->health_problem_started_at && ! $errorResolved)
                            <br><span class="small">{{ __('views/instances.health_problem_since', ['time' => $instanceHealthModal->health_problem_started_at->setTimezone(Request::header('X-Timezone'))]) }}</span>
                        @endif
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/instances.health_modal_close') }}</button>
            </div>
        </div>
    </div>
</div>

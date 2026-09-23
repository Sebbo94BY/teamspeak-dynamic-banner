<div class="container">
    @if ($system_status_warning_count + $system_status_danger_count == 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-success" role="alert">
                    {{ __('views/inc/system/systemstatus.installation_has_no_errors') }}
                </div>
            </div>
        </div>
    @endif
    @if ($system_status_warning_count > 0)
        <div class="row mt-2">
            <div class="col-lg-12">
                <div class="alert alert-warning" role="alert">
                    {{ trans_choice('views/inc/system/systemstatus.installation_has_warnings', $system_status_warning_count, ['warning_count' => $system_status_warning_count]) }}
                </div>
            </div>
        </div>
    @endif
    @if ($system_status_danger_count > 0)
        <div class="row mt-2">
            <div class="col-lg-12">
                <div class="alert alert-danger" role="alert">
                    {{ trans_choice('views/inc/system/systemstatus.installation_has_critical_errors', $system_status_danger_count, ['danger_count' => $system_status_danger_count]) }}
                </div>
            </div>
        </div>
    @endif
    <hr>
        @php
        //define a variable to control the description <td>
        $descStatus = false;
        @endphp
    <div class="accordion" id="accordionSystemStatus">
        <div class="accordion-item">
            <h2 class="accordion-header" id="accordionSystemStatusPHPHeading">
                <a class="accordion-button @if($php_warning_count == 0  && $php_error_count == 0 ) collapsed @endif fw-bold bg-light text-decoration-none" data-bs-toggle="collapse" data-bs-target="#accordionSystemStatusPHP" aria-expanded="false" aria-controls="accordionSystemStatusPHP">
                    <div class="col-lg-9">
                        <span class="fs-5 fw-bold text-dark">{{ __('views/inc/system/systemstatus.accordion_section_php') }}</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($php_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> {{ __('views/inc/system/systemstatus.accordion_error') }}</span>
                        @elseif($php_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> {{ __('views/inc/system/systemstatus.accordion_warning') }}</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> {{ __('views/inc/system/systemstatus.accordion_operational') }}</span>
                        @endif
                    </div>
                </a>
            </h2>
            <div id="accordionSystemStatusPHP" class="accordion-collapse collapse @if($php_warning_count > 0  || $php_error_count > 0 ) show @endif " aria-labelledby="accordionSystemStatusPHPHeading">
                <div class="accordion-body bg-light">
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="col-lg-6 border-0" scope="col"></th>
                                <th class="col-lg-6 border-0" scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($php_status['VERSION'] as $phpVersion)
                                <tr>
                                    <td class="border-0">{{ $phpVersion->name }} <code>{{$phpVersion->required_value}}</code></td>
                                    <td class="border-0">
                                        @switch($phpVersion->severity)
                                            @case('success')
                                                <i class="fa-solid fa-check-circle text-success me-3"></i>
                                                @break
                                            @default
                                                <i class="fa-solid fa-triangle-exclamation text-warning me-3"></i>
                                        @endswitch
                                        {{$phpVersion->current_value}}
                                    </td>
                                </tr>
                            @endforeach
                            @php $descStatus = true; @endphp
                            @foreach($php_status_extension as $key => $extension)
                                <tr>
                                    <td class="border-0">@if($descStatus == true) {{ __('views/inc/system/systemstatus.accordion_section_php_extensions') }} @php $descStatus = false; @endphp @endif</td>
                                    <td class="border-0">
                                        @switch($extension->severity)
                                            @case('success')
                                                <i class="fa-solid fa-check-circle text-success me-3"></i>
                                                @break
                                            @case('warning')
                                                <i class="fa-solid fa-triangle-exclamation text-warning me-3"></i>
                                                @break
                                            @case('danger')
                                                <i class="fa-solid fa-circle-xmark text-danger me-3"></i>
                                                @break
                                            @default
                                                <i class="fa-solid fa-info-circle text-info me-3"></i>
                                        @endswitch
                                        {{$extension->name}}
                                    </td>
                                </tr>
                            @endforeach
                            @foreach($php_status_ini_settings as $key => $iniSettings)
                                <tr>
                                    <td class="border-0">{{$iniSettings->name}} <code>{{$iniSettings->required_value}}</code></td>
                                    <td class="border-0">
                                        @switch($iniSettings->severity)
                                            @case('success')
                                                <i class="fa-solid fa-check-circle text-success me-3"></i>
                                                @break
                                            @case('warning')
                                                <i class="fa-solid fa-triangle-exclamation text-warning me-3"></i>
                                                @break
                                            @case('danger')
                                                <i class="fa-solid fa-circle-xmark text-danger me-3"></i>
                                                @break
                                            @default
                                                <i class="fa-solid fa-info-circle text-info me-3"></i>
                                        @endswitch
                                        {{$iniSettings->current_value}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="accordionSystemStatusDatabaseHeading">
                <a class="accordion-button @if($db_warning_count == 0  && $db_error_count == 0 ) collapsed @endif fw-bold bg-light text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#accordionSystemStatusDatabase" aria-expanded="true" aria-controls="accordionSystemStatusDatabase">
                    <div class="col-lg-9">
                        <span class="fs-5 fw-bold text-dark">{{ __('views/inc/system/systemstatus.accordion_section_database') }}</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($db_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> {{ __('views/inc/system/systemstatus.accordion_error') }}</span>
                        @elseif($db_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> {{ __('views/inc/system/systemstatus.accordion_warning') }}</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> {{ __('views/inc/system/systemstatus.accordion_operational') }}</span>
                        @endif
                    </div>
                </a>
            </h2>
            <div id="accordionSystemStatusDatabase" class="accordion-collapse collapse @if($db_warning_count > 0  || $db_error_count > 0 ) show @endif" aria-labelledby="accordionSystemStatusDatabaseHeading">
                <div class="accordion-body bg-light">
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="col-lg-6 border-0" scope="col"></th>
                                <th class="col-lg-6 border-0" scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($db_status_connection as $key => $dbStatus)
                                <tr>
                                    <td class="border-0">{{$dbStatus->name}} <code>{{$dbStatus->required_value}}</code></td>
                                    <td class="border-0">
                                        @switch($dbStatus->severity)
                                            @case('success')
                                                <i class="fa-solid fa-check-circle text-success me-3"></i>
                                                @break
                                            @case('warning')
                                                <i class="fa-solid fa-triangle-exclamation text-warning me-3"></i>
                                                @break
                                            @case('danger')
                                                <i class="fa-solid fa-circle-xmark text-danger me-3"></i>
                                                @break
                                            @default
                                                <i class="fa-solid fa-info-circle text-info me-3"></i>
                                        @endswitch
                                        {{$dbStatus->current_value}}
                                    </td>
                                </tr>
                            @endforeach
                            @foreach($db_status_Settings as $key => $dbSettings)
                                <tr>
                                    <td class="border-0">{{$dbSettings->name}} <code>{{$dbSettings->required_value}}</code></td>
                                    <td class="border-0">
                                        @switch($dbSettings->severity)
                                            @case('success')
                                                <i class="fa-solid fa-check-circle text-success me-3"></i>
                                                @break
                                            @case('warning')
                                                <i class="fa-solid fa-triangle-exclamation text-warning me-3"></i>
                                                @break
                                            @case('danger')
                                                <i class="fa-solid fa-circle-xmark text-danger me-3"></i>
                                                @break
                                            @default
                                                <i class="fa-solid fa-info-circle text-info me-3"></i>
                                        @endswitch
                                        {{$dbSettings->current_value}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="accordionSystemStatusPermissionHeading">
                <a class="accordion-button fw-bold bg-light text-decoration-none @if($permission_warning_count == 0  && $permission_error_count == 0 ) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#accordionSystemStatusPermission" aria-expanded="true" aria-controls="accordionSystemStatusPermission">
                    <div class="col-lg-9">
                        <span class="fs-5 fw-bold text-dark">{{ __('views/inc/system/systemstatus.accordion_section_permissions') }}</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($permission_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> {{ __('views/inc/system/systemstatus.accordion_error') }}</span>
                        @elseif($permission_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> {{ __('views/inc/system/systemstatus.accordion_warning') }}</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> {{ __('views/inc/system/systemstatus.accordion_operational') }}</span>
                        @endif
                    </div>
                </a>
            </h2>
            <div id="accordionSystemStatusPermission" class="accordion-collapse collapse @if($permission_warning_count > 0  || $permission_error_count > 0 ) show @endif" aria-labelledby="accordionSystemStatusPermissionHeading">
                <div class="accordion-body bg-light">
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="col-lg-6 border-0" scope="col"></th>
                                <th class="col-lg-6 border-0" scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $descStatus = true; @endphp
                            @foreach($permission_status_dir  as $key => $permissions)
                                <tr>
                                    <td class="border-0">
                                        @if($descStatus == true)
                                            {{ __('views/inc/system/systemstatus.accordion_section_permissions_directories') }} <code>{{ __('views/inc/system/systemstatus.accordion_section_permissions_directories_required_value') }}</code></td>
                                            @php $descStatus = false;  @endphp
                                        @endif
                                    <td class="border-0">
                                        @switch($permissions->severity)
                                            @case('success')
                                                <i class="fa-solid fa-check-circle text-success me-3"></i>
                                                @break
                                            @case('warning')
                                                <i class="fa-solid fa-triangle-exclamation text-warning me-3"></i>
                                                @break
                                            @case('danger')
                                                <i class="fa-solid fa-circle-xmark text-danger me-3"></i>
                                                @break
                                            @default
                                                <i class="fa-solid fa-info-circle text-info me-3"></i>
                                        @endswitch
                                        {{$permissions->name}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="accordionSystemStatusQueueHealthHeading">
                <a class="accordion-button collapsed fw-bold bg-light text-decoration-none @if($queue_health_warning_count == 0  && $queue_health_error_count == 0 ) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#accordionSystemStatusQueueHealth" aria-expanded="false" aria-controls="accordionSystemStatusQueueHealth">
                    <div class="col-lg-9">
                        <span class="fs-5 fw-bold text-dark">{{ __('views/inc/system/systemstatus.accordion_section_queue_health') }}</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($queue_health_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> {{ __('views/inc/system/systemstatus.accordion_error') }}</span>
                        @elseif($queue_health_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> {{ __('views/inc/system/systemstatus.accordion_warning') }}</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> {{ __('views/inc/system/systemstatus.accordion_operational') }}</span>
                        @endif
                    </div>
                </a>
            </h2>
            <div id="accordionSystemStatusQueueHealth" class="accordion-collapse collapse @if($queue_health_warning_count > 0  || $queue_health_error_count > 0 ) show @endif" aria-labelledby="accordionSystemStatusQueueHealthHeading">
                <div class="accordion-body bg-light">
                    <div class="col-lg-12">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <p class="mb-0 text-muted">{{ __('views/inc/system/systemstatus.accordion_section_queue_health_intro') }}</p>
                            <form method="get" action="{{ route('administration.systemstatus') }}" class="d-flex align-items-center gap-2">
                                <label for="queueHistoryRange" class="small text-nowrap">{{ __('views/inc/system/systemstatus.accordion_section_queue_health_history_range') }}</label>
                                <select id="queueHistoryRange" name="queue_history_range" class="form-select form-select-sm" onchange="this.form.submit()">
                                    @foreach($queue_metric_history_ranges as $range => $minutes)
                                        <option value="{{ $range }}" @selected($queue_history_range === $range)>{{ __('views/inc/system/systemstatus.accordion_section_queue_health_history_range_'.$range) }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('views/inc/system/systemstatus.accordion_section_queue_health_history_range_apply') }}</button>
                            </form>
                        </div>
                        @foreach($queue_health_sections as $queueHealthSection)
                            <h3 class="fs-6 fw-bold mt-3">{{ $queueHealthSection['name'] }}</h3>
                            <div class="row g-3">
                            @foreach($queueHealthSection['metrics'] as $queueSize)
                                <div class="col-md-6">
                                    <div class="border rounded bg-white h-100 p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            @switch($queueSize->severity)
                                                @case('success')
                                                    <i class="fa-solid fa-check-circle text-success me-2"></i>
                                                    @break
                                                @case('warning')
                                                    <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>
                                                    @break
                                                @case('danger')
                                                    <i class="fa-solid fa-circle-xmark text-danger me-2"></i>
                                                    @break
                                                @default
                                                    <i class="fa-solid fa-circle-info text-info me-2"></i>
                                            @endswitch
                                            <span class="fw-bold">{{ $queueSize->name }}</span>
                                        </div>
                                        <div class="fs-4 fw-bold">{{ $queueSize->current_value }}</div>
                                        @if(isset($queueSize->history_key) && $queueHealthSection['history']->count() > 1)
                                            @php
                                                $historyValues = $queueHealthSection['history']->pluck($queueSize->history_key)->map(fn ($value) => (int) $value)->values();
                                                $historyMaximum = max(1, $historyValues->max());
                                                $historyDivisor = max(1, $historyValues->count() - 1);
                                                $historyPoints = $historyValues->map(fn ($value, $index) => round($index * 100 / $historyDivisor, 2).','.round(28 - ($value * 24 / $historyMaximum), 2))->implode(' ');
                                            @endphp
                                            <div class="queue-metric-chart position-relative mt-2 text-{{ $queueSize->severity === 'warning' ? 'warning' : 'primary' }}" data-queue-metric-chart>
                                                <svg class="w-100" viewBox="0 0 100 32" height="42" role="img" aria-label="{{ __('views/inc/system/systemstatus.accordion_section_queue_health_history_label', ['range' => __('views/inc/system/systemstatus.accordion_section_queue_health_history_range_'.$queue_history_range)]) }}">
                                                    <line x1="0" y1="28" x2="100" y2="28" stroke="currentColor" stroke-opacity="0.2" stroke-width="1" />
                                                    <polyline points="{{ $historyPoints }}" fill="none" stroke="currentColor" stroke-width="2" vector-effect="non-scaling-stroke" />
                                                    @foreach($queueHealthSection['history'] as $index => $snapshot)
                                                        <circle class="queue-metric-point" cx="{{ round($index * 100 / $historyDivisor, 2) }}" cy="{{ round(28 - ($historyValues[$index] * 24 / $historyMaximum), 2) }}" r="2" fill="currentColor" tabindex="0" data-tooltip="{{ $snapshot->recorded_at->format('d.m.Y H:i') }}: {{ $historyValues[$index] }}" aria-label="{{ $snapshot->recorded_at->format('d.m.Y H:i') }}: {{ $historyValues[$index] }}" />
                                                    @endforeach
                                                </svg>
                                                <div class="queue-metric-tooltip position-absolute d-none rounded bg-dark px-2 py-1 small text-white" style="z-index: 1; pointer-events: none;"></div>
                                                <div class="small text-muted">{{ __('views/inc/system/systemstatus.accordion_section_queue_health_history_label', ['range' => __('views/inc/system/systemstatus.accordion_section_queue_health_history_range_'.$queue_history_range)]) }}</div>
                                            </div>
                                        @elseif(isset($queueSize->history_key))
                                            <div class="mt-2 small text-muted">{{ __('views/inc/system/systemstatus.accordion_section_queue_health_history_collecting') }}</div>
                                        @endif
                                        <p class="mb-0 mt-2 small text-muted">{{ $queueSize->required_value }}</p>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="accordionSystemStatusRedisHeading">
                <a class="accordion-button collapsed fw-bold bg-light text-decoration-none @if($redis_warning_count == 0  && $redis_error_count == 0 ) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#accordionSystemStatusRedis" aria-expanded="false" aria-controls="accordionSystemStatusRedis">
                    <div class="col-lg-9">
                        <span class="fs-5 fw-bold text-dark">{{ __('views/inc/system/systemstatus.accordion_section_redis') }}</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($redis_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> {{ __('views/inc/system/systemstatus.accordion_error') }}</span>
                        @elseif($redis_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> {{ __('views/inc/system/systemstatus.accordion_warning') }}</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> {{ __('views/inc/system/systemstatus.accordion_operational') }}</span>
                        @endif
                    </div>
                </a>
            </h2>
            <div id="accordionSystemStatusRedis" class="accordion-collapse collapse @if($redis_warning_count > 0  || $redis_error_count > 0 ) show @endif" aria-labelledby="accordionSystemStatusRedisHeading">
                <div class="accordion-body bg-light">
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="col-lg-6 border-0" scope="col"></th>
                                <th class="col-lg-6 border-0" scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($redis_status_connection as $key => $redisConnection)
                                <tr>
                                    <td class="border-0">{{$redisConnection->name}} <code>{{$redisConnection->required_value}}</code></td>
                                    <td class="border-0">
                                        @switch($redisConnection->severity)
                                            @case('success')
                                                <i class="fa-solid fa-check-circle text-success me-3"></i>
                                                @break
                                            @case('warning')
                                                <i class="fa-solid fa-triangle-exclamation text-warning me-3"></i>
                                                @break
                                            @case('danger')
                                                <i class="fa-solid fa-circle-xmark text-danger me-3"></i>
                                                @break
                                            @default
                                                <i class="fa-solid fa-info-circle text-info me-3"></i>
                                        @endswitch
                                        {{$redisConnection->current_value}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="accordionSystemStatusFfmpegHeading">
                <a class="accordion-button collapsed fw-bold bg-light text-decoration-none @if($ffmpeg_warning_count == 0  && $ffmpeg_error_count == 0 ) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#accordionSystemStatusFfmpeg" aria-expanded="false" aria-controls="accordionSystemStatusFfmpeg">
                    <div class="col-lg-9">
                        <span class="fs-5 fw-bold text-dark">{{ __('views/inc/system/systemstatus.accordion_section_ffmpeg') }}</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($ffmpeg_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> {{ __('views/inc/system/systemstatus.accordion_error') }}</span>
                        @elseif($ffmpeg_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> {{ __('views/inc/system/systemstatus.accordion_warning') }}</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> {{ __('views/inc/system/systemstatus.accordion_operational') }}</span>
                        @endif
                    </div>
                </a>
            </h2>
            <div id="accordionSystemStatusFfmpeg" class="accordion-collapse collapse @if($ffmpeg_warning_count > 0  || $ffmpeg_error_count > 0 ) show @endif" aria-labelledby="accordionSystemStatusFfmpegHeading">
                <div class="accordion-body bg-light">
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="col-lg-6 border-0" scope="col"></th>
                                <th class="col-lg-6 border-0" scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($ffmpeg_version as $key => $ffmpeg_version)
                                <tr>
                                    <td class="border-0">{{$ffmpeg_version->name}} <code>{{$ffmpeg_version->required_value}}</code></td>
                                    <td class="border-0">
                                        @switch($ffmpeg_version->severity)
                                            @case('success')
                                                <i class="fa-solid fa-check-circle text-success me-3"></i>
                                                @break
                                            @case('warning')
                                                <i class="fa-solid fa-triangle-exclamation text-warning me-3"></i>
                                                @break
                                            @case('danger')
                                                <i class="fa-solid fa-circle-xmark text-danger me-3"></i>
                                                @break
                                            @default
                                                <i class="fa-solid fa-info-circle text-info me-3"></i>
                                        @endswitch
                                        {{$ffmpeg_version->current_value}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="accordionSystemStatusMailHeading">
                <a class="accordion-button collapsed fw-bold bg-light text-decoration-none @if($mail_warning_count == 0  && $mail_error_count == 0 ) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#accordionSystemStatusMail" aria-expanded="false" aria-controls="accordionSystemStatusMail">
                    <div class="col-lg-9">
                        <span class="fs-5 fw-bold text-dark">{{ __('views/inc/system/systemstatus.accordion_section_mail') }}</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($mail_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> {{ __('views/inc/system/systemstatus.accordion_error') }}</span>
                        @elseif($mail_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> {{ __('views/inc/system/systemstatus.accordion_warning') }}</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> {{ __('views/inc/system/systemstatus.accordion_operational') }}</span>
                        @endif
                    </div>
                </a>
            </h2>
            <div id="accordionSystemStatusMail" class="accordion-collapse collapse @if($mail_warning_count > 0  || $mail_error_count > 0 ) show @endif" aria-labelledby="accordionSystemStatusMailHeading">
                <div class="accordion-body bg-light">
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="col-lg-6 border-0" scope="col"></th>
                                <th class="col-lg-6 border-0" scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($mail_status_connection as $key => $mailConnection)
                                <tr>
                                    <td class="border-0">{{$mailConnection->name}} <code>{{$mailConnection->required_value}}</code></td>
                                    <td class="border-0">
                                        @switch($mailConnection->severity)
                                            @case('success')
                                                <i class="fa-solid fa-check-circle text-success me-3"></i>
                                                @break
                                            @case('warning')
                                                <i class="fa-solid fa-triangle-exclamation text-warning me-3"></i>
                                                @break
                                            @case('danger')
                                                <i class="fa-solid fa-circle-xmark text-danger me-3"></i>
                                                @break
                                            @default
                                                <i class="fa-solid fa-info-circle text-info me-3"></i>
                                        @endswitch
                                        {{$mailConnection->current_value}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if(Route::currentRouteName() != 'setup.installer.requirements')
        <div class="accordion-item">
            <h2 class="accordion-header" id="accordionSystemStatusVersionHeading">
                <a class="accordion-button collapsed fw-bold bg-light text-decoration-none @if($version_warning_count == 0  && $version_error_count == 0 ) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#accordionSystemStatusVersion" aria-expanded="false" aria-controls="accordionSystemStatusVersion">
                    <div class="col-lg-9">
                        <span class="fs-5 fw-bold text-dark">{{ __('views/inc/system/systemstatus.accordion_section_version') }}</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($version_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> {{ __('views/inc/system/systemstatus.accordion_error') }}</span>
                        @elseif($version_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> {{ __('views/inc/system/systemstatus.accordion_warning') }}</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> {{ __('views/inc/system/systemstatus.accordion_operational') }}</span>
                        @endif
                    </div>
                </a>
            </h2>
            <div id="accordionSystemStatusVersion" class="accordion-collapse collapse @if($version_warning_count > 0  || $version_error_count > 0 ) show @endif" aria-labelledby="accordionSystemStatusVersionHeading">
                <div class="accordion-body bg-light">
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="col-lg-6 border-0" scope="col"></th>
                                <th class="col-lg-6 border-0" scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($version_status_software as $key => $versionSoftware)
                                <tr>
                                    <td class="border-0">{{$versionSoftware->name}}</td>
                                    <td class="border-0">
                                        @switch($versionSoftware->severity)
                                            @case('success')
                                                <i class="fa-solid fa-check-circle text-success me-3"></i>
                                                @break
                                            @case('warning')
                                                <i class="fa-solid fa-triangle-exclamation text-warning me-3"></i>
                                                @break
                                            @case('danger')
                                                <i class="fa-solid fa-circle-xmark text-danger me-3"></i>
                                                @break
                                            @default
                                                <i class="fa-solid fa-info-circle text-info me-3"></i>
                                        @endswitch
                                        {{$versionSoftware->current_value}}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="border-0">{{ __('views/inc/system/systemstatus.accordion_section_version_bootstrap') }}</td>
                                <td class="border-0"><i class="fa-solid fa-info-circle text-info me-3"></i><span id="bootstrap_version"></span></td>
                            </tr>
                            <tr>
                                <td class="border-0">{{ __('views/inc/system/systemstatus.accordion_section_version_datatable') }}</td>
                                <td class="border-0"><i class="fa-solid fa-info-circle text-info me-3"></i><span id="datatable_version"></span></td>
                            </tr>
                            <tr>
                                <td class="border-0">{{ __('views/inc/system/systemstatus.accordion_section_version_jquery') }}</td>
                                <td class="border-0"><i class="fa-solid fa-info-circle text-info me-3"></i><span id="jquery_version"></span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="accordion-item">
            <h2 class="accordion-header" id="accordionSystemStatusVariousHeading">
                <a class="accordion-button collapsed fw-bold bg-light text-decoration-none @if($various_warning_count == 0  && $various_error_count == 0 ) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#accordionSystemStatusVarious" aria-expanded="false" aria-controls="accordionSystemStatusVarious">
                    <div class="col-lg-9">
                        <span class="fs-5 fw-bold text-dark">{{ __('views/inc/system/systemstatus.accordion_section_various') }}</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($various_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> {{ __('views/inc/system/systemstatus.accordion_error') }}</span>
                        @elseif($various_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> {{ __('views/inc/system/systemstatus.accordion_warning') }}</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> {{ __('views/inc/system/systemstatus.accordion_operational') }}</span>
                        @endif
                    </div>
                </a>
            </h2>
            <div id="accordionSystemStatusVarious" class="accordion-collapse collapse @if($various_warning_count > 0  || $various_error_count > 0 ) show @endif" aria-labelledby="accordionSystemStatusVariousHeading">
                <div class="accordion-body bg-light">
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="col-lg-6 border-0" scope="col"></th>
                                <th class="col-lg-6 border-0" scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($various_status_information as $key => $variousInformation)
                                <tr>
                                    <td class="border-0">{{$variousInformation->name}} <code>{{$variousInformation->required_value}}</code></td>
                                    <td class="border-0">
                                        @switch($variousInformation->severity)
                                            @case('success')
                                                <i class="fa-solid fa-check-circle text-success me-3"></i>
                                                @break
                                            @case('warning')
                                                <i class="fa-solid fa-triangle-exclamation text-warning me-3"></i>
                                                @break
                                            @case('danger')
                                                <i class="fa-solid fa-circle-xmark text-danger me-3"></i>
                                                @break
                                            @default
                                                <i class="fa-solid fa-info-circle text-info me-3"></i>
                                        @endswitch
                                        {{$variousInformation->current_value}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="row mt-3">
        <div class="col-lg-12">
            <p><span class="fw-bold">{{ __('views/inc/system/systemstatus.legend_label') }}:</span>
                <i class="fa-solid fa-circle-check text-success"></i> {{ __('views/inc/system/systemstatus.icon_operational') }},
                <i class="fa-solid fa-triangle-exclamation text-warning"></i> {{ __('views/inc/system/systemstatus.icon_warning') }},
                <i class="fa-solid fa-circle-xmark text-danger"></i> {{ __('views/inc/system/systemstatus.icon_error') }},
                <i class="fa-solid fa-circle-info text-info"></i> {{ __('views/inc/system/systemstatus.icon_information') }}
            </p>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-queue-metric-chart]').forEach((chart) => {
            const tooltip = chart.querySelector('.queue-metric-tooltip');

            chart.querySelectorAll('.queue-metric-point').forEach((point) => {
                const showTooltip = () => {
                    const chartBounds = chart.getBoundingClientRect();
                    const pointBounds = point.getBoundingClientRect();

                    tooltip.textContent = point.dataset.tooltip;
                    tooltip.classList.remove('d-none');
                    tooltip.style.left = `${pointBounds.left - chartBounds.left}px`;
                    tooltip.style.top = `${Math.max(0, pointBounds.top - chartBounds.top - tooltip.offsetHeight - 4)}px`;
                };

                point.addEventListener('mouseenter', showTooltip);
                point.addEventListener('focus', showTooltip);
                point.addEventListener('mouseleave', () => tooltip.classList.add('d-none'));
                point.addEventListener('blur', () => tooltip.classList.add('d-none'));
            });
        });
    });
</script>

<script type="module">
    $(document).ready(function () {
        $("#bootstrap_version").html(bootstrap.Tooltip.VERSION);
        $("#datatable_version").html($.fn.dataTable.version);
        $("#jquery_version").html($.fn.jquery);
    });
</script>

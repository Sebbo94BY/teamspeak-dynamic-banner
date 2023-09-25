<div class="container">
    @if ($system_status_warning_count + $system_status_danger_count == 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-success" role="alert">
                    There are no problems with your installation.
                </div>
            </div>
        </div>
    @endif
    @if ($system_status_warning_count > 0)
        <div class="row mt-2">
            <div class="col-lg-12">
                <div class="alert alert-warning" role="alert">
                    Your installation has {{ $system_status_warning_count }} {{ \Illuminate\Support\Str::plural("warning", $system_status_warning_count) }}, which you might want to fix for the best software experience.
                </div>
            </div>
        </div>
    @endif
    @if ($system_status_danger_count > 0)
        <div class="row mt-2">
            <div class="col-lg-12">
                <div class="alert alert-danger" role="alert">
                    Your installation has {{ $system_status_danger_count }} critical {{ \Illuminate\Support\Str::plural("issue", $system_status_danger_count) }}, which you need to fix that everything works properly.
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
                        <span class="fs-5 fw-bold text-dark">PHP</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($php_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> Error</span>
                        @elseif($php_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> Warning</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> Operational</span>
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
                                    <td class="border-0">@if($descStatus == true) PHP Extensions @php $descStatus = false; @endphp @endif</td>
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
                        <span class="fs-5 fw-bold text-dark">Database</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($db_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> Error</span>
                        @elseif($db_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> Warning</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> Operational</span>
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
                        <span class="fs-5 fw-bold text-dark">Permissions</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($permission_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> Error</span>
                        @elseif($permission_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> Warning</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> Operational</span>
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
                                            Directories <code>{{$permissions->required_value}}</code></td>
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
            <h2 class="accordion-header" id="accordionSystemStatusRedisHeading">
                <a class="accordion-button collapsed fw-bold bg-light text-decoration-none @if($redis_warning_count == 0  && $redis_error_count == 0 ) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#accordionSystemStatusRedis" aria-expanded="false" aria-controls="accordionSystemStatusRedis">
                    <div class="col-lg-9">
                        <span class="fs-5 fw-bold text-dark">Redis</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($redis_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> Error</span>
                        @elseif($redis_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> Warning</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> Operational</span>
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
        @if(Route::currentRouteName() != 'setup.installer.requirements')
        <div class="accordion-item">
            <h2 class="accordion-header" id="accordionSystemStatusVersionHeading">
                <a class="accordion-button collapsed fw-bold bg-light text-decoration-none @if($version_warning_count == 0  && $version_error_count == 0 ) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#accordionSystemStatusVersion" aria-expanded="false" aria-controls="accordionSystemStatusVersion">
                    <div class="col-lg-9">
                        <span class="fs-5 fw-bold text-dark">Version</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($version_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> Error</span>
                        @elseif($version_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> Warning</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> Operational</span>
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
                        <span class="fs-5 fw-bold text-dark">Various</span>
                    </div>
                    <div class="col-lg-2 me-5">
                        @if($various_error_count > 0)
                            <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> Error</span>
                        @elseif($various_warning_count > 0)
                            <span class="fs-5 fw-bold text-warning"><i class="fa fa-triangle-exclamation"></i> Warning</span>
                        @else
                            <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> Operational</span>
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
                            <tr>
                                <td class="border-0">Bootstrap Version</td>
                                <td class="border-0"><i class="fa-solid fa-info-circle text-info me-3"></i><span id="bootstrap_version"></span></td>
                            </tr>
                            <tr>
                                <td class="border-0">Datatable Version</td>
                                <td class="border-0"><i class="fa-solid fa-info-circle text-info me-3"></i><span id="datatable_version"></span></td>
                            </tr>
                            <tr>
                                <td class="border-0">jQuery Version</td>
                                <td class="border-0"><i class="fa-solid fa-info-circle text-info me-3"></i><span id="jquery_version"></span></td>
                            </tr>
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
            <p><span class="fw-bold">Legende:</span>
                <i class="fa-solid fa-circle-check text-success"></i> Good (no issues),
                <i class="fa-solid fa-triangle-exclamation text-warning"></i> Warning (limited functionality),
                <i class="fa-solid fa-circle-xmark text-danger"></i> Misconfiguration (something will not work),
                <i class="fa-solid fa-circle-info text-info"></i> Information (just for your information)
            </p>
        </div>
    </div>
</div>

<script type="module">
    $(document).ready(function () {
        $("#bootstrap_version").html(bootstrap.Tooltip.VERSION);
        $("#datatable_version").html($.fn.dataTable.version);
        $("#jquery_version").html($.fn.jquery);
    });
</script>

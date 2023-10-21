@extends('layout')

@section('site_title')
    {{ __('views/banner/configuration.banner_configuration') }}
@endsection

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold fs-3">{{ $banner_template->banner->name }} / {{ $banner_template->name }} / {{ __('views/banner/configuration.banner_configuration') }}</h1>
        </div>
    </div>
    <hr>
</div>
<div class="container">
    <div class="row justify-content-between">
        <div class="col-lg-2">
            <a class="btn btn-primary btn-sm fw-bold" href="#modalAvailableVariables-{{$banner_template->banner->instance_id}}" data-bs-toggle="modal" data-bs-target="#modalAvailableVariables-{{$instance->first()->id}}"><i class="fa-solid fa-square-root-variable fa-lg me-2"></i></a>
        </div>
        <div class="col-lg-1 d-grid">
            <a class="btn btn-secondary btn-sm fw-bold" href="{{ route('banner.templates', ['banner_id'=>$banner_template->banner_id]) }}">{{ __('views/banner/configuration.back_button') }}</a>
        </div>
    </div>
    <hr>
</div>
<div class="container mt-3">
    @include('inc.standard-alerts')
    <div class="row">
        <div class="col-lg-6">
            <p class="fs-5 m-0 mb-3 fw-bold">{{ __('views/banner/configuration.preview_with_grid_system') }}</p>
            <img class="img-fluid shadow-lg p-1 mb-2 bg-white rounded" id="templateWithGrid" src="{{ asset($banner_template->file_path_drawed_grid_text.'/'.$banner_template->template->filename) }}" alt="{{ $banner_template->template->alias }}">
        </div>
        <div class="col-lg-6">
            <p class="fs-5 m-0 mb-3 fw-bold">{{ __('views/banner/configuration.preview_without_grid_system') }}</p>
            <img class="img-fluid shadow-lg p-1 mb-2 bg-white rounded" id="renderedTemplate" src="{{ asset($banner_template->file_path_drawed_text.'/'.$banner_template->template->filename) }}" alt="{{ $banner_template->template->alias }}">
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-lg-12">
            <p class="fs-5 m-0 mb-2 fw-bold">{{ __('views/banner/configuration.about_grid_system') }}</p>
            <p class="m-0 text-muted">{{ __('views/banner/configuration.grid_system_purpose') }}</p>
            <p class="m-0 text-muted">{{ __('views/banner/configuration.get_x_y_coordinates_on_click') }}</p>
            <p class="m-0 text-muted">{{ __('views/banner/configuration.grid_system_explanation') }}</p>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-lg-3">
            <label for="x_coordinate_preview" class="form-label">{{ __('views/banner/configuration.x_coordinate') }}</label>
            <input type="number" class="form-control" name="x_coordinate_preview" id="x_coordinate_preview" min="0" max="{{ $banner_template->template->width }}" value="0" readonly>
        </div>
        <div class="col-lg-3">
            <label for="y_coordinate_preview" class="form-label">{{ __('views/banner/configuration.y_coordinate') }}</label>
            <input type="number" class="form-control" name="y_coordinate_preview" id="y_coordinate_preview" min="0" max="{{ $banner_template->template->height }}" value="0" readonly>
        </div>
    </div>
    <hr>
    <div class="row">
        <form id="template-configuration" method="POST" action="{{ route('banner.template.configuration.upsert', ['banner_template_id' => $banner_template->id]) }}" class="row g-3 needs-validation" novalidate>
            @method('patch')
            @csrf
            <div class="col-lg-12">
                <div class="accordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="NameHeading">
                            <a class="accordion-button collapsed text-decoration-none text-dark fw-bold bg-light" data-bs-toggle="collapse" data-bs-target="#name" aria-expanded="true" aria-controls="name">
                                {{ __('views/banner/configuration.name_accordion_headline') }}
                            </a>
                        </h2>
                        <div id="name" class="accordion-collapse collapse" aria-labelledby="NameHeading">
                            <div class="accordion-body">
                                <input class="form-control" id="validationName" type="text" name="name" value="{{ old('name', (isset($banner_template)) ? $banner_template->name : '') }}"
                                    placeholder="{{ __('views/banner/configuration.name_placeholder') }}" aria-label="validationName" aria-describedby="nameHelp validationNameFeedback" required>
                                <div id="nameHelp" class="form-text">{{ __('views/banner/configuration.name_help') }}</div>
                                <div class="valid-feedback">{{ __('views/banner/configuration.form_validation_looks_good') }}</div>
                                <div id="validationNameFeedback" class="invalid-feedback">{{ __('views/banner/configuration.name_validation_error') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="redirectUrlHeading">
                            <a class="accordion-button collapsed text-decoration-none text-dark fw-bold bg-light" data-bs-toggle="collapse" data-bs-target="#redirectUrl" aria-expanded="true" aria-controls="redirectUrl">
                                <div class="col-lg-9">
                                    {{ __('views/banner/configuration.redirect_url_accordion_headline') }}
                                </div>
                                <div class="col-lg-2 text-end">
                                    @if(isset($banner_template) && $banner_template->redirect_url != null)
                                        <span class="badge text-bg-success ms-2">{{ __('views/banner/configuration.accordion_status_configured') }}</span>
                                    @else
                                        <span class="badge text-bg-warning ms-2">{{ __('views/banner/configuration.accordion_status_not_configured') }}</span>
                                    @endif
                                </div>
                            </a>
                        </h2>
                        <div id="redirectUrl" class="accordion-collapse collapse" aria-labelledby="redirectUrl">
                            <div class="accordion-body">
                                <p class="mt-2">
                                    {!! __(
                                        'views/banner/configuration.redirect_url_hostbanner_url',
                                        [
                                            'hostbanner_url' => route('api.banner.redirect_url', ['banner_id' => base_convert($banner_template->banner_id, 10, 35)])
                                        ]
                                    ) !!}
                                </p>
                                <div class="input-group">
                                    <input class="form-control" type="url" id="validationRedirectUrl" name="redirect_url" value="{{ old('redirect_url', (isset($banner_template)) ? $banner_template->redirect_url : '') }}"
                                        placeholder="{{ __('views/banner/configuration.redirect_url_placeholder') }}" aria-label="validationRedirectUrl">
                                    <button class="btn btn-outline-primary" type="button" @if (isset($banner_template) and empty($banner_template->redirect_url)) disabled @endif>{{ __('views/banner/configuration.test_redirect_button') }}</button>
                                </div>
                                <div class="valid-feedback">{{ __('views/banner/configuration.form_validation_looks_good') }}</div>
                                <div class="invalid-feedback">{{ __('views/banner/configuration.redirect_url_validation_error') }}</div>
                                <div class="form-text">{{ __('views/banner/configuration.redirect_url_help') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="autoEnableHeading">
                            <a class="accordion-button collapsed text-decoration-none text-dark fw-bold bg-light" data-bs-toggle="collapse" data-bs-target="#autoEnable" aria-expanded="false" aria-controls="autoEnable">
                                <div class="col-lg-9">
                                    {{ __('views/banner/configuration.enable_at_accordion_headline') }}
                                </div>
                                <div class="col-lg-2 text-end">
                                    @if(isset($banner_template) && $banner_template->enable_at != null)
                                        <span class="badge text-bg-success ms-2">{{ __('views/banner/configuration.accordion_status_configured') }}</span>
                                    @else
                                        <span class="badge text-bg-warning ms-2">{{ __('views/banner/configuration.accordion_status_not_configured') }}</span>
                                    @endif
                                </div>
                            </a>
                        </h2>
                        <div id="autoEnable" class="accordion-collapse collapse" aria-labelledby="autoEnable">
                            <div class="accordion-body">
                                <p class="mt-2">{{ __('views/banner/configuration.enable_at_use_case') }}</p>
                                <input class="form-control" type="datetime-local" id="validationEnableAt" name="enable_at"
                                    value="{{ old('enable_at', (isset($banner_template) and !is_null($banner_template->enable_at)) ? Carbon\Carbon::parse($banner_template->enable_at)->setTimezone(Request::header('X-Timezone')) : '') }}" aria-label="validationEnableAt">
                                <div class="valid-feedback">{{ __('views/banner/configuration.form_validation_looks_good') }}</div>
                                <div class="invalid-feedback">{{ __('views/banner/configuration.enable_at_validation_error') }}</div>
                                <div class="form-text">{{ __('views/banner/configuration.enable_at_help') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="autoDisableHeading">
                            <a class="accordion-button collapsed text-decoration-none text-dark fw-bold bg-light" data-bs-toggle="collapse" data-bs-target="#autoDisable" aria-expanded="false" aria-controls="autoDisable">
                                <div class="col-lg-9">
                                    {{ __('views/banner/configuration.disable_at_accordion_headline') }}
                                </div>
                                <div class="col-lg-2 text-end">
                                    @if(isset($banner_template) && $banner_template->disable_at != null)
                                        <span class="badge text-bg-success ms-2">{{ __('views/banner/configuration.accordion_status_configured') }}</span>
                                    @else
                                        <span class="badge text-bg-warning ms-2">{{ __('views/banner/configuration.accordion_status_not_configured') }}</span>
                                    @endif
                                </div>
                            </a>
                        </h2>
                        <div id="autoDisable" class="accordion-collapse collapse" aria-labelledby="autoDisable">
                            <div class="accordion-body">
                                <p class="mt-2">{{ __('views/banner/configuration.disable_at_use_case') }}</p>
                                <input class="form-control" type="datetime-local" id="validationDisableAt" name="disable_at"
                                    value="{{ old('disable_at', (isset($banner_template) and !is_null($banner_template->disable_at)) ? Carbon\Carbon::parse($banner_template->disable_at)->setTimezone(Request::header('X-Timezone')) : '') }}" aria-label="validationDisableAt">
                                <div class="valid-feedback">{{ __('views/banner/configuration.form_validation_looks_good') }}</div>
                                <div class="invalid-feedback">{{ __('views/banner/configuration.disable_at_validation_error') }}</div>
                                <div class="form-text">{{ __('views/banner/configuration.disable_at_help') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="timeBasedDeActivationHeading">
                            <a class="accordion-button collapsed text-decoration-none text-dark fw-bold bg-light" data-bs-toggle="collapse" data-bs-target="#timeBasedDeActivation" aria-expanded="false" aria-controls="timeBasedDeActivation">
                                <div class="col-lg-9">
                                    {{ __('views/banner/configuration.time_based_de_activation_accordion_headline') }}
                                </div>
                                <div class="col-lg-2 text-end">
                                    @if(isset($banner_template) and ($banner_template->time_based_enable_at != null or $banner_template->time_based_disable_at != null))
                                        <span class="badge text-bg-success ms-2">{{ __('views/banner/configuration.accordion_status_configured') }}</span>
                                    @else
                                        <span class="badge text-bg-warning ms-2">{{ __('views/banner/configuration.accordion_status_not_configured') }}</span>
                                    @endif
                                </div>
                            </a>
                        </h2>
                        <div id="timeBasedDeActivation" class="accordion-collapse collapse" aria-labelledby="timeBasedDeActivation">
                            <div class="accordion-body">
                                <p class="mt-2">{{ __('views/banner/configuration.time_based_de_activation_use_case') }}</p>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <label for="validationTimeBasedEnableAt" class="form-label">{{ __('views/banner/configuration.time_based_de_activation_enable_at') }}</label>
                                        <input class="form-control" type="time" id="validationTimeBasedEnableAt" name="time_based_enable_at"
                                            value="{{ old('time_based_enable_at', (isset($banner_template) and !is_null($banner_template->time_based_enable_at)) ? Carbon\Carbon::parse($banner_template->time_based_enable_at)->setTimezone(Request::header('X-Timezone'))->format('H:i') : '') }}" aria-label="validationTimeBasedEnableAt">
                                        <div class="valid-feedback">{{ __('views/banner/configuration.form_validation_looks_good') }}</div>
                                        <div class="invalid-feedback">{{ __('views/banner/configuration.time_based_de_activation_enable_at_validation_error') }}</div>
                                        <div class="form-text">{{ __('views/banner/configuration.time_based_de_activation_enable_at_help') }}</div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="validationTimeBasedDisableAt" class="form-label">{{ __('views/banner/configuration.time_based_de_activation_disable_at') }}</label>
                                        <input class="form-control" type="time" id="validationTimeBasedDisableAt" name="time_based_disable_at"
                                            value="{{ old('time_based_disable_at', (isset($banner_template) and !is_null($banner_template->time_based_disable_at)) ? Carbon\Carbon::parse($banner_template->time_based_disable_at)->setTimezone(Request::header('X-Timezone'))->format('H:i') : '') }}" aria-label="validationTimeBasedDisableAt">
                                        <div class="valid-feedback">{{ __('views/banner/configuration.form_validation_looks_good') }}</div>
                                        <div class="invalid-feedback">{{ __('views/banner/configuration.time_based_de_activation_disable_at_validation_error') }}</div>
                                        <div class="form-text">{{ __('views/banner/configuration.time_based_de_activation_disable_at_help') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="twitchDeActivationHeading">
                            <a class="accordion-button collapsed text-decoration-none text-dark fw-bold bg-light" data-bs-toggle="collapse" data-bs-target="#twitchDeActivation" aria-expanded="false" aria-controls="twitchDeActivation">
                                <div class="col-lg-9">
                                    {{ __('views/banner/configuration.twitch_based_de_activation_accordion_headline') }}
                                </div>
                                <div class="col-lg-2 text-end">
                                    @if (isset($banner_template) and !is_null($banner_template->twitch_streamer_id))
                                        @if (is_null($twitch_api) or is_null($twitch_api->client_id) or is_null($twitch_api->client_secret))
                                        <span class="badge text-bg-warning ms-2">{{ __('views/banner/configuration.accordion_status_configured_but_ignored') }}</span>
                                        @else
                                        <span class="badge text-bg-success ms-2">{{ __('views/banner/configuration.accordion_status_configured') }}</span>
                                        @endif
                                    @else
                                        <span class="badge text-bg-warning ms-2">{{ __('views/banner/configuration.accordion_status_not_configured') }}</span>
                                    @endif
                                </div>
                            </a>
                        </h2>
                        <div id="twitchDeActivation" class="accordion-collapse collapse" aria-labelledby="twitchDeActivation">
                            <div class="accordion-body">
                                @if (is_null($twitch_api) or is_null($twitch_api->client_id) or is_null($twitch_api->client_secret))
                                <div class="row mt-2">
                                    <div class="col-lg-12">
                                        <div class="alert alert-warning">
                                            <p>{{ __('views/banner/configuration.twitch_based_de_activation_no_twitch_api_credentials_are_configured') }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <p class="mt-2">{{ __('views/banner/configuration.twitch_based_de_activation_use_case') }}</p>
                                <label for="validationTwitchBasedDeActivation" class="form-label">{{ __('views/banner/configuration.twitch_based_de_activation_twitch_streamer_id') }}</label>
                                <select class="form-control" id="validationTwitchBasedDeActivation" name="twitch_streamer_id" aria-label="validationTwitchBasedDeActivation">
                                    <option value="">Disable</option>
                                    @foreach ($twitch_streamer as $streamer)
                                    <option value="{{ $streamer->id }}" @if ($banner_template->twitch_streamer_id == $streamer->id) selected @endif>{{ $streamer->stream_url }}</option>
                                    @endforeach
                                </select>
                                <div class="valid-feedback">{{ __('views/banner/configuration.form_validation_looks_good') }}</div>
                                <div class="invalid-feedback">{{ __('views/banner/configuration.twitch_streamer_id_validation_error') }}</div>
                                <div class="form-text">{{ __('views/banner/configuration.twitch_based_de_activation_twitch_streamer_id_help') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="bannerConfigHeading">
                            <a class="accordion-button text-decoration-none text-dark fw-bold bg-light" data-bs-toggle="collapse" data-bs-target="#bannerConfig" aria-expanded="false" aria-controls="bannerConfig">
                                <div class="col-lg-9">
                                    {{ __('views/banner/configuration.text_configurations_accordion_headline') }}
                                </div>
                                <div class="col-lg-2 text-end">
                                    @if($banner_template->configurations->count() > 0)
                                        <span class="badge text-bg-success ms-2">{{ trans_choice('views/banner/configuration.accordion_status_has_configurations', $banner_template->configurations->count(), ['count_configurations' => $banner_template->configurations->count()]) }}</span>
                                    @else
                                        <span class="badge text-bg-warning ms-2">{{ __('views/banner/configuration.accordion_status_no_configurations') }}</span>
                                    @endif
                                </div>
                            </a>
                        </h2>
                        <div id="bannerConfig" class="accordion-collapse collapse show" aria-labelledby="autoDisable">
                            <div class="accordion-body">
                                @if($banner_template->configurations->count() > 0)
                                    @foreach($banner_template->configurations as $configuration)
                                    <div class="mt-2" id="config-row-{{ $configuration->id }}">
                                        @include('inc.banners.banner-config-input', ['template' => $banner_template->template, 'configuration' => $configuration])
                                    </div>
                                    @endforeach
                                    @php unset($configuration) @endphp
                                    <div id="new-config-row-1" class="d-none">
                                        @include('inc.banners.banner-config-input', ['template' => $banner_template->template])
                                    </div>
                                @else
                                    <div id="new-config-row-1">
                                        @include('inc.banners.banner-config-input', ['template' => $banner_template->template])
                                    </div>
                                @endif
                                <div class="row mb-2 justify-content-between">
                                    <div class="col-lg-2">
                                        <button type="button" class="form-control btn btn-success" id="add-config-row">{{ __('views/banner/configuration.add_row_button') }}</button>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="submit" class="form-control btn btn-primary" id="add-config-row">{{ __('views/banner/configuration.save_button') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <hr>
</div>
@include('inc.banner-config-script')

@include('inc.form-validation')

@include('modals.modal-variables', [
        'twitch_streamer_variables' => $twitch_streamer_variables,
        'instanceVariableModal' => $instance,
    ])

@endsection

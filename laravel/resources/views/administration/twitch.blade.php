@extends('layout')

@section('site_title')
    {{ __('views/administration/twitch.twitch') }}
@endsection

@section('dataTables_script')
    <script>
        $(document).ready( function () {
            $('#fonts').DataTable({
                "oLanguage": {
                    "sLengthMenu": "_MENU_",
                }
            });
        } );
    </script>
@endsection

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold fs-3">{{ __('views/administration/twitch.twitch') }}</h1>
        </div>
    </div>
    <hr>
</div>

<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <div class="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="TwitchApiCredentialsHeading">
                        <a class="accordion-button collapsed fw-bold bg-light text-decoration-none
                            @if (is_null($twitch_api) or is_null($twitch_api->client_id) or is_null($twitch_api->client_secret) or Carbon\Carbon::now()->gt($twitch_api->expires_at)) collapsed @endif"
                            type="button" data-bs-toggle="collapse" data-bs-target="#twitch_api_credentials" aria-expanded="false" aria-controls="twitch_api_credentials">
                            <div class="col-lg-9">
                                <span class="fs-5 fw-bold text-dark">{{ __('views/administration/twitch.api_credentials_accordion_headline') }}</span>
                            </div>
                            <div class="col-lg-2 me-5">
                                @if (is_null($twitch_api) or is_null($twitch_api->client_id) or is_null($twitch_api->client_secret))
                                    <span class="fs-5 fw-bold text-warning"><i class="fa fa-circle-exclamation"></i> {{ __('views/administration/twitch.api_credentials_accordion_unconfigured') }}</span>    
                                @elseif (is_null($twitch_api->access_token) or Carbon\Carbon::now()->gt($twitch_api->expires_at))
                                    <span class="fs-5 fw-bold text-danger"><i class="fa fa-circle-xmark"></i> {{ __('views/administration/twitch.api_credentials_accordion_invalid_credentials') }}</span>
                                @else
                                    <span class="fs-5 fw-bold text-success"><i class="fa fa-check-circle"></i> {{ __('views/administration/twitch.api_credentials_accordion_valid_credentials') }}</span>
                                @endif
                            </div>
                        </a>
                    </h2>
                    <div id="twitch_api_credentials" class="accordion-collapse collapse" aria-labelledby="TwitchApiCredentialsHeading">
                        <div class="accordion-body">
                            @can('edit twitch api credentials')
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="alert alert-primary" role="alert">
                                            <p>{{ __('views/administration/twitch.api_usage_information') }}</p>
                                            <p>{!! __('views/administration/twitch.twitch_register_app_documentation') !!}</p>
                                            <p>{{ __('views/administration/twitch.installation_instructions') }}:</p>
                                            <ol>
                                                <li>{{ __('views/administration/twitch.step_login_or_create_twitch_account') }}</li>
                                                <li>{!! __('views/administration/twitch.step_open_twitch_dev_console') !!}</li>
                                                <li>{!! __('views/administration/twitch.step_open_applications') !!}</li>
                                                <li>{!! __('views/administration/twitch.step_open_register_app') !!}</li>
                                                <li>{{ __('views/administration/twitch.step_fill_out_the_form') }}</li>
                                                <ul>
                                                    <li>{!! __('views/administration/twitch.step_fill_out_the_form_name') !!}</li>
                                                    <li>{!! __('views/administration/twitch.step_fill_out_the_form_oauth_redirect_urls') !!}</li>
                                                    <li>{!! __('views/administration/twitch.step_fill_out_the_form_category') !!}</li>
                                                </ul>
                                                <li>{!! __('views/administration/twitch.step_open_new_app') !!}</li>
                                                <li>{!! __('views/administration/twitch.step_copy_and_insert_client_id') !!}</li>
                                                <li>{!! __('views/administration/twitch.step_copy_and_insert_client_secret') !!}</li>
                                                <li>{!! __('views/administration/twitch.step_submit_form') !!}</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>

                                <form id="upsert-twitch-api-credentials" method="POST" action="{{ route('administration.twitch.update_api_credentials') }}" class="needs-validation" novalidate>
                                    @method('patch')
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <label for="validationApiClientId" class="form-label fw-bold">{{ __('views/administration/twitch.api_client_id') }}</label>
                                            <input class="form-control" id="validationApiClientId" type="text" name="client_id"
                                                value="{{ old('client_id', (isset($twitch_api->client_id)) ? $twitch_api->client_id : '') }}"
                                                placeholder="{{ __('views/administration/twitch.api_client_id_placeholder') }}" aria-label="validationApiClientId"
                                                aria-describedby="apiClientIdHelp validationApiClientIdFeedback" required>
                                            <div id="apiClientIdHelp" class="form-text">{{ __('views/administration/twitch.api_client_id_help') }}</div>
                                            <div class="valid-feedback">{{ __('views/administration/twitch.form_validation_looks_good') }}</div>
                                            <div id="validationApiClientIdFeedback" class="invalid-feedback">{{ __('views/administration/twitch.api_client_id_validation_error') }}</div>
                                        </div>

                                        <div class="col-lg-6">
                                            <label for="validationApiClientSecret" class="form-label fw-bold">{{ __('views/administration/twitch.api_client_secret') }}</label>
                                            <input class="form-control" id="validationApiClientSecret" type="password" name="client_secret"
                                                value="{{ old('client_secret', (isset($twitch_api->client_secret)) ? $twitch_api->client_secret : '') }}"
                                                placeholder="{{ __('views/administration/twitch.api_client_secret_placeholder') }}" aria-label="validationApiClientSecret"
                                                aria-describedby="apiClientSecretHelp validationApiClientSecretFeedback" required>
                                            <div id="apiClientSecretHelp" class="form-text">{{ __('views/administration/twitch.api_client_secret_help') }}</div>
                                            <div class="valid-feedback">{{ __('views/administration/twitch.form_validation_looks_good') }}</div>
                                            <div id="validationApiClientSecretFeedback" class="invalid-feedback">{{ __('views/administration/twitch.api_client_secret_validation_error') }}</div>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-lg-3">
                                            <button type="submit" class="form-control btn btn-primary">{{ __('views/administration/twitch.save_button') }}</button>
                                        </div>
                                        @can('delete twitch api credentials')
                                            @if (! is_null($twitch_api))
                                                <div class="col-lg-3">
                                                    <button type="button" class="form-control btn btn-danger" data-bs-toggle="modal"
                                                        data-bs-target="#deleteTwitchApiCredentials">
                                                        {{ __('views/administration/twitch.delete_api_credentials_button') }}
                                                    </button>
                                                </div>
                                            @endif
                                        @endcan
                                    </div>
                                </form>
                            @else
                            <div class="row mt-2">
                                <div class="col-lg-12">
                                    <div class="alert alert-primary" role="alert">
                                        {{ __('views/administration/twitch.no_permissions_to_edit_the_api_credentials') }}
                                    </div>
                                </div>
                            </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
</div>

@can('add twitch streamers')
<div class="container">
    <div class="row">
        <div class="col-lg-3">
            <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addTwitchStreamer">
                {{ __('views/administration/twitch.add_streamer_button') }}
            </button>
        </div>
    </div>
    <hr>
</div>
@endcan
<div class="container">
    @include('inc.standard-alerts')

    @if($twitch_streamer->count() == 0)
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-primary" role="alert">
                        {{ __('views/administration/twitch.no_streamer_added_info') }}
                        @can('add twitch streamers')
                            <button class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#addTwitchStreamer">{{ __('views/administration/twitch.add_streamer_button') }}</button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    @if (is_null($twitch_api) or is_null($twitch_api->client_id) or is_null($twitch_api->client_secret))
    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="alert alert-warning">
                <p>{{ __('views/administration/twitch.no_twitch_api_credentials_are_configured') }}</p>
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-lg-12">
            <table class="table table-striped" id="fonts">
                <thead>
                <tr>
                    <th class="col-lg-1">{{ __('views/administration/twitch.table_stream_status') }}</th>
                    <th class="col-lg-8">{{ __('views/administration/twitch.table_stream_url') }}</th>
                    <th class="col-lg-2">{{ __('views/administration/twitch.table_last_modified') }}</th>
                    <th class="col-lg-1"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($twitch_streamer as $streamer)
                <tr>
                    <td>
                        @if ($streamer->is_live)
                        <span class="badge text-bg-success">{{ __('views/administration/twitch.table_stream_status_online') }}</span>
                        @else
                        <span class="badge text-bg-danger">{{ __('views/administration/twitch.table_stream_status_offline') }}</span>
                        @endif
                    </td>
                    <td><a href="{{ $streamer->stream_url }}" target="_blank">{{ $streamer->stream_url }}</a></td>
                    <td>{{ Carbon\Carbon::parse($streamer->updated_at)->setTimezone(Request::header('X-Timezone')) }}</td>
                    <td>
                        <div class="d-flex">
                            @can('edit twitch streamers')
                                <button class="btn btn-link px-0 me-2" type="button" data-bs-toggle="modal" data-bs-target="#editTwitchStreamer-{{$streamer->id}}"><i class="fa-solid fa-pencil text-primary fa-lg"></i></button>
                            @endcan
                            @can('delete twitch streamers')
                                <button class="btn btn-link px-0 me-2" type="button" data-bs-toggle="modal" data-bs-target="#deleteTwitchStreamer-{{$streamer->id}}"><i class="fa-solid fa-trash text-danger fa-lg"></i></button>
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

@can('delete twitch api credentials')
    @include('modals.delete-feedback.modal-delete-twitch-api-credentials')
@endcan

@can('add twitch streamers')
    @include('modals.twitch.modal-add')
@endcan

@foreach($twitch_streamer as $streamer)
    @can('edit twitch streamers')
        @include('modals.twitch.modal-edit', ['streamer' => $streamer])
    @endcan

    @can('delete twitch streamers')
        @include('modals.delete-feedback.modal-delete-twitch', ['streamer' => $streamer])
    @endcan
@endforeach

@endsection

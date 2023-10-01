@extends('layout')

@section('site_title')
    {{ __('views/profile/edit.profile') }}
@endsection

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold fs-3">{{ __('views/profile/edit.profile') }}</h1>
        </div>
    </div>
    <hr>
</div>
<div class="container mt-3">
    @include('inc.standard-alerts')
    <div class="row">
        <div class="col-lg-6 d-flex">
            <form method="POST" action="{{ route('profile.update') }}" class="card flex-fill needs-validation" novalidate>
                @method('patch')
                @csrf
                <div class="card-body">
                    <div class="mb-2">
                        <label for="validationName" class="form-label fw-bold">{{ __('views/profile/edit.name_label') }}</label>
                        <input type="text" class="form-control" id="validationName" name="name" value="{{ old('name', $user->name) }}"
                               aria-describedby="validationNameHelp validationNameFeedback" placeholder="{{ __('views/profile/edit.name_input_placeholder') }}" required>
                        <div id="validationNameHelp" class="form-text">{{ __('views/profile/edit.name_help') }}</div>
                        <div class="valid-feedback">{{ __('views/profile/edit.form_validation_looks_good') }}</div>
                        <div id="validationNameFeedback" class="invalid-feedback">{{ __('views/profile/edit.name_validation_error') }}</div>
                    </div>
                    <div class="mb-2">
                        <label for="validationEmail" class="form-label fw-bold">{{ __('views/profile/edit.email_label') }}</label>
                        <input type="email" class="form-control" id="validationEmail" name="email" value="{{ old('email', $user->email) }}"
                               aria-describedby="validationEmailHelp validationEmailFeedback" placeholder="{{ __('views/profile/edit.email_input_placeholder') }}" required>
                        <div id="validationEmailHelp" class="form-text">{{ __("views/profile/edit.email_help") }}</div>
                        <div class="valid-feedback">{{ __('views/profile/edit.form_validation_looks_good') }}</div>
                        <div id="validationEmailFeedback" class="invalid-feedback">{{ __('views/profile/edit.email_validation_error') }}</div>
                    </div>
                    <div class="mb-2">
                        <label for="validationLocalizationId" class="form-label fw-bold">{{ __('views/profile/edit.language_label') }}</label>
                        <select class="form-select" name="localization_id" id="validationLocalizationId" aria-describedby="localizationIdHelp" required>
                            @foreach ($localizations as $localization)
                            @if (old('localization_id', $user->localization_id) == $localization->id) "selected"
                            <option value="{{ $localization->id }}" selected>{{ $localization->language_name }}</option>
                            @else
                            <option value="{{ $localization->id }}">{{ $localization->language_name }}</option>
                            @endif
                            @endforeach
                        </select>
                        <div id="localizationIdHelp" class="form-text">{{ __('views/profile/edit.language_help') }}</div>
                        <div class="valid-feedback">{{ __('views/profile/edit.form_validation_looks_good') }}</div>
                        <div class="invalid-feedback">{{ __('views/profile/edit.language_validation_error') }}</div>
                    </div>
                    <div class="mb-2">
                        <label for="validationTimezone" class="form-label fw-bold">{{ __('views/profile/edit.timezone_label') }}</label>
                        <select class="form-select" name="timezone" id="validationTimezone" aria-describedby="timezoneHelp" required>
                            <option value="null" @if (is_null($user->timezone)) selected @endif>{{ __('views/profile/edit.timezone_auto_detect_option') }}</option>
                            @foreach (timezone_identifiers_list(DateTimeZone::ALL) as $timezone)
                            @if (old('timezone', $user->timezone) == $timezone)
                            <option value="{{ $timezone }}" selected>{{ $timezone }}</option>
                            @else
                            <option value="{{ $timezone }}">{{ $timezone }}</option>
                            @endif
                            @endforeach
                        </select>
                        <div id="timezoneHelp" class="form-text">{{ __('views/profile/edit.timezone_help') }}</div>
                        <div class="valid-feedback">{{ __('views/profile/edit.form_validation_looks_good') }}</div>
                        <div class="invalid-feedback">{{ __('views/profile/edit.timezone_validation_error') }}</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <button class="btn btn-primary" type="submit">{{ __('views/profile/edit.update_profile_button') }}</button>
                </div>
            </form>
        </div>
        <div class="col-lg-6 d-flex">
            <form method="POST" action="{{ route('profile.update.password') }}" class="card flex-fill needs-validation"
                  oninput ='password_confirmation.setCustomValidity(password_confirmation.value !== password.value ? "Passwords do not match." : "")'
                  novalidate>
                @method('patch')
                @csrf
                <div class="card-body">
                    <div class="mb-2">
                        <label for="validationCurrentPassword" class="form-label fw-bold">{{ __('views/profile/edit.current_password_label') }}</label>
                        <input type="password" class="form-control" id="validationCurrentPassword" minlength="8" name="current_password"
                               aria-describedby="validationCurrentPasswordFeedbackHelp validationCurrentPasswordFeedback" placeholder="{{ __('views/profile/edit.current_password_input_placeholder') }}" required>
                        <div id="validationNewPasswordConfirmationHelp" class="form-text">{{ __('views/profile/edit.current_password_help') }}</div>
                        <div class="valid-feedback">{{ __('views/profile/edit.form_validation_looks_good') }}</div>
                        <div id="validationCurrentPasswordFeedback" class="invalid-feedback">{{ __('views/profile/edit.current_password_validation_error') }}</div>
                    </div>
                    <div class="mb-2">
                        <label for="validationNewPassword" class="form-label fw-bold">{{ __('views/profile/edit.new_password_label') }}</label>
                        <input type="password" class="form-control" id="validationNewPassword" minlength="8" name="password"
                               aria-describedby="validationNewPasswordHelp validationNewPasswordFeedback" placeholder="{{ __('views/profile/edit.new_password_input_placeholder') }}" required>
                        <div id="validationNewPasswordHelp" class="form-text">{{ __('views/profile/edit.new_password_help') }}</div>
                        <div class="valid-feedback">{{ __('views/profile/edit.form_validation_looks_good') }}</div>
                        <div id="validationNewPasswordFeedback" class="invalid-feedback">{{ __('views/profile/edit.new_password_validation_error') }}</div>
                    </div>
                    <div class="mb-2">
                        <label for="validationNewPasswordConfirmation" class="form-label fw-bold">{{ __('views/profile/edit.confirm_password_label') }}</label>
                        <input type="password" class="form-control" id="validationNewPasswordConfirmation" minlength="8" name="password_confirmation"
                               aria-describedby="validationNewPasswordConfirmationHelp validationNewPasswordConfirmationFeedback" placeholder="{{ __('views/profile/edit.confirm_password_input_placeholder') }}" required>
                        <div id="validationNewPasswordConfirmationHelp" class="form-text">{{ __('views/profile/edit.confirm_password_help') }}</div>
                        <div class="valid-feedback">{{ __('views/profile/edit.form_validation_looks_good') }}</div>
                        <div id="validationNewPasswordConfirmationFeedback" class="invalid-feedback">{{ __('views/profile/edit.confirm_password_validation_error') }}</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <button class="btn btn-primary" type="submit">{{ __('views/profile/edit.change_password_button') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Check if passwords match
    $("#pwdId, #validationPasswordConfirmation").on("keyup", function () {
        if (
            $("#validationPassword").val() != "" &&
            $("#validationPasswordConfirmation").val() != "" &&
            $("#validationPassword").val() == $("#validationPasswordConfirmation").val()
        )
        {}
    });
</script>
@include('inc.form-validation')
@endsection

@extends('layout')

@section('site_title')
    {{ __('views/setup/installer/user.installer_create_super_user') }}
@endsection

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold fs-3">{{ __('views/setup/installer/user.installer_create_super_user') }}</h1>
        </div>
    </div>
    <hr>
</div>
<div class="container mt-3">
    @include('inc.standard-alerts')
    <form method="POST" action="{{ route('setup.installer.user.create') }}" class="row g-3 needs-validation"
          oninput ='password_confirmation.setCustomValidity(password_confirmation.value !== password.value ? "Passwords do not match." : "")'
          novalidate>
        @csrf
        <div class="row mb-3">
            <div class="col-lg-12">
                <button class="btn btn-primary" type="submit">{{ __('views/setup/installer/user.next_button') }}</button>
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-3">
                    <label for="validationName" class="form-label fw-bold">{{ __('views/setup/installer/user.name') }}</label>
                    <input type="text" class="form-control" id="validationName" name="name" value="{{ old('name') }}"
                        aria-describedby="validationNameHelp validationNameFeedback" placeholder="{{ __('views/setup/installer/user.name_placeholder') }}" required>
                    <div id="validationNameHelp" class="form-text">{{ __('views/setup/installer/user.name_help') }}</div>
                    <div class="valid-feedback">{{ __('views/setup/installer/user.form_validation_looks_good') }}</div>
                    <div id="validationNameFeedback" class="invalid-feedback">{{ __('views/setup/installer/user.name_validation_error') }}</div>
                </div>
                <div class="mb-3">
                    <label for="validationEmail" class="form-label fw-bold">{{ __('views/setup/installer/user.email') }}</label>
                    <input type="email" class="form-control" id="validationEmail" name="email" value="{{ old('email') }}"
                        aria-describedby="validationEmailHelp validationEmailFeedback" placeholder="{{ __('views/setup/installer/user.email_placeholder') }}" required>
                    <div id="validationEmailHelp" class="form-text">{{ __('views/setup/installer/user.email_help') }}</div>
                    <div class="valid-feedback">{{ __('views/setup/installer/user.form_validation_looks_good') }}</div>
                    <div id="validationEmailFeedback" class="invalid-feedback">{{ __('views/setup/installer/user.email_validation_error') }}</div>
                </div>
                <div class="mb-3">
                    <label for="validationPassword" class="form-label fw-bold">{{ __('views/setup/installer/user.password') }}</label>
                    <input type="password" class="form-control" id="validationPassword" minlength="8" name="password"
                        aria-describedby="validationPasswordHelp validationPasswordFeedback" placeholder="{{ __('views/setup/installer/user.password_placeholder') }}" required>
                    <div id="validationPasswordHelp" class="form-text">{{ __('views/setup/installer/user.password_help') }}</div>
                    <div class="valid-feedback">{{ __('views/setup/installer/user.form_validation_looks_good') }}</div>
                    <div id="validationPasswordFeedback" class="invalid-feedback">{{ __('views/setup/installer/user.password_validation_error') }}</div>
                </div>
                <div class="mb-3">
                    <label for="validationPasswordConfirmation" class="form-label fw-bold">{{ __('views/setup/installer/user.password_confirmation') }}</label>
                    <input type="password" class="form-control" id="validationPasswordConfirmation" minlength="8" name="password_confirmation"
                        aria-describedby="validationPasswordConfirmationHelp validationPasswordConfirmationFeedback" placeholder="{{ __('views/setup/installer/user.password_confirmation_placeholder') }}" required>
                    <div id="validationPasswordConfirmationHelp" class="form-text">{{ __('views/setup/installer/user.password_confirmation_help') }}</div>
                    <div class="valid-feedback">{{ __('views/setup/installer/user.form_validation_looks_good') }}</div>
                    <div id="validationPasswordConfirmationFeedback" class="invalid-feedback">{{ __('views/setup/installer/user.password_confirmation_validation_error') }}</div>
                </div>
            </div>
        </div>
    </form>
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

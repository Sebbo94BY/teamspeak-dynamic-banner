@extends('layout')

@section('site_title')
    Edit Profile
@endsection

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold fs-3">Profile</h1>
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
                        <label for="validationName" class="form-label fw-bold">Name</label>
                        <input type="text" class="form-control" id="validationName" name="name" value="{{ old('name', $user->name) }}"
                               aria-describedby="validationNameHelp validationNameFeedback" placeholder="e.g. MyNickname" required>
                        <div id="validationNameHelp" class="form-text">Your users display name.</div>
                        <div class="valid-feedback">{{ __("Looks good!") }}</div>
                        <div id="validationNameFeedback" class="invalid-feedback">{{ __("Please provide a valid name.") }}</div>
                    </div>
                    <div class="mb-2">
                        <label for="validationEmail" class="form-label fw-bold">E-Mail</label>
                        <input type="email" class="form-control" id="validationEmail" name="email" value="{{ old('email', $user->email) }}"
                               aria-describedby="validationEmailHelp validationEmailFeedback" placeholder="e.g. max@example.com" required>
                        <div id="validationEmailHelp" class="form-text">Your email address.</div>
                        <div class="valid-feedback">{{ __("Looks good!") }}</div>
                        <div id="validationEmailFeedback" class="invalid-feedback">{{ __("Please provide a valid email.") }}</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <button class="btn btn-primary" type="submit">Update Profile</button>
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
                        <label for="validationCurrentPassword" class="form-label fw-bold">Current Password</label>
                        <input type="password" class="form-control" id="validationCurrentPassword" minlength="8" name="current_password"
                               aria-describedby="validationCurrentPasswordFeedback" placeholder="e.g. myOldPassword" required>
                        <div class="valid-feedback">{{ __("Looks good!") }}</div>
                        <div id="validationCurrentPasswordFeedback" class="invalid-feedback">{{ __("Please provide a valid current password.") }}</div>
                    </div>
                    <div class="mb-2">
                        <label for="validationNewPassword" class="form-label fw-bold">New Password</label>
                        <input type="password" class="form-control" id="validationNewPassword" minlength="8" name="password"
                               aria-describedby="validationNewPasswordHelp validationNewPasswordFeedback" placeholder="e.g. myNewPassword" required>
                        <div id="validationNewPasswordHelp" class="form-text">Your new login password. (minimum 8 characters)</div>
                        <div class="valid-feedback">{{ __("Looks good!") }}</div>
                        <div id="validationNewPasswordFeedback" class="invalid-feedback">{{ __("Please provide a valid password.") }}</div>
                    </div>
                    <div class="mb-2">
                        <label for="validationNewPasswordConfirmation" class="form-label fw-bold">Confirm new Password</label>
                        <input type="password" class="form-control" id="validationNewPasswordConfirmation" minlength="8" name="password_confirmation"
                               aria-describedby="validationNewPasswordConfirmationHelp validationNewPasswordConfirmationFeedback" placeholder="e.g. myNewPassword" required>
                        <div id="validationNewPasswordConfirmationHelp" class="form-text">Repeat your new password to confirm it.</div>
                        <div class="valid-feedback">{{ __("Looks good!") }}</div>
                        <div id="validationNewPasswordConfirmationFeedback" class="invalid-feedback">{{ __("Please repeat your above password correct.") }}</div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <button class="btn btn-primary" type="submit">Update Password</button>
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

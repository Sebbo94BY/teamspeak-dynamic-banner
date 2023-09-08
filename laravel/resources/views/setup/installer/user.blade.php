@extends('layout')

@section('site_title')
    Setup - Super Admin
@endsection

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold fs-3">Installer - Create Super User</h1>
        </div>
    </div>
    <hr>
</div>
<div class="container mt-3">
    @include('inc.standard-alerts')
    <div class="row">
        <div class="col-lg-12">
            <form method="POST" action="{{ route('setup.installer.user.create') }}" class="row g-3 needs-validation" novalidate>
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="validationName" class="form-label fw-bold">Name</label>
                            <input type="text" class="form-control" id="validationName" name="name" value="{{ old('name') }}" placeholder="e.g. MyNickname" required>
                            <div class="form-text">Your users display name.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid name.") }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="validationEmail" class="form-label fw-bold">E-Mail</label>
                            <input type="text" class="form-control" id="validationEmail" name="email" value="{{ old('email') }}" placeholder="e.g. max@example.com" required>
                            <div class="form-text">Your email address.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid email.") }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="validationPassword" class="form-label fw-bold">Password</label>
                            <input type="password" class="form-control" id="validationPassword" minlength="8" name="password" placeholder="e.g. myVery!S3cretP4ssw0rd" required>
                            <div class="form-text">Your login password. (minimum 8 characters)</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid password.") }}</div>
                        </div>
                        <div class="mb-3">
                            <label for="validationPasswordConfirmation" class="form-label fw-bold">Confirm Password</label>
                            <input type="password" class="form-control" id="validationPasswordConfirmation" minlength="8" name="password_confirmation" placeholder="e.g. myVery!S3cretP4ssw0rd"  required>
                            <div class="form-text">Repeat your password to confirm it.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please repeat your above password correct.") }}</div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <button class="btn btn-primary" type="submit">Next</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@include('inc.form-validation')
@endsection

@extends('layout')

@section('site_title')
    Set New Password
@endsection

@section('content')
    <div class="container my-auto">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-12 my-auto">
                        <h1 class="fw-bold fs-3 ms-3">Reset Password</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0">
                    <div class="card-body">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="email">{{ __('Email Address') }}:</label>
                                <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ $email ?? old('email') }}"
                                       aria-describedby="emailFeedback" placeholder="john.doe@example.de" required>
                                @error('email')
                                <span id="emailFeedback" class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="password">Password</label>
                                <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password"
                                       aria-describedby="passwordFeedback" placeholder="New Password" required>
                                @error('password')
                                <span id="passwordFeedback" class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="password-confirm">Confirm Password</label>
                                <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password_confirmation"
                                       aria-describedby="passwordConfirmFeedback" placeholder="Confirm Password" required>
                                @error('password_confirmation')
                                <span id="passwordConfirmFeedback" class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-12 d-grid">
                                    <button type="submit" class="btn btn-primary">Reset Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

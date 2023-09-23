@extends('layout')

@section('site_title')
    Forgot Password
@endsection

@section('content')
    <div class="container my-auto">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-12 my-auto">
                        <h1 class="fw-bold fs-3 ms-3">Password Reset Link</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0">
                    <div class="card-body">
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="email">{{ __('Email Address') }}:</label>
                                <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email') }}"
                                       aria-describedby="emailFeedback" placeholder="john.doe@example.de">
                                @error('email')
                                <span id="emailFeedback" class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                                @enderror
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-12 d-grid">
                                    <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

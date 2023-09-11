@extends('layout')

@section('site_title')
    Login
@endsection

@section('content')
    <div class="container my-auto">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-lg-8 my-auto">
                        <h1 class="fw-bold fs-3 ms-3">Login</h1>
                    </div>
                    <div class="col-lg-4">
                        <img class="card-img pe-3" src="{{asset('img/design/lock.png')}}" alt="Logo" oncontextmenu="return false">
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-6">
                @include('inc.standard-alerts')
                <div class="card border-0">
                    <div class="card-body">
                        <form method="post" action="{{ Route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="email">{{ __('Email Address') }}:</label>
                                <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="john.doe@example.de">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" for="password">{{ __('Password') }}:</label>
                                <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" placeholder="Gib dein Passwort ein">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-12 d-grid">
                                    <button class="btn btn-primary">Login</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

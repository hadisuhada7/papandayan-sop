@extends('adminlte::page')

@section('title', 'Confirm Password')

@section('content')
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="{{ url('/') }}" class="h1"><b>Papandayan</b> SOP</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div class="input-group mb-3">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" placeholder="{{ __('Password') }}" required autocomplete="current-password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">{{ __('Confirm') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop


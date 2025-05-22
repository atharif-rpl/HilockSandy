@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">{{ __('Login') }}</div>

            <div class="card-body">
                {{-- Session Status (misal, untuk pesan setelah reset password) --}}
                @if (session('status'))
                    <div class="alert alert-success mb-3" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>

                    <div class="d-grid mb-0">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Login') }}
                        </button>

                        {{-- Jika Anda ingin menambahkan link "Forgot Password" --}}
                        {{-- @if (Route::has('password.request'))
                            <a class="btn btn-link mt-2" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif --}}
                    </div>
                     <div class="text-center mt-3">
                        <a href="{{ route('register') }}">
                            Don't have an account? Register
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
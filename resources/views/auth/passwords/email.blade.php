@extends('master')

@section('title'){{ __('Reset Password') }} - {{ config('app.name') }} @stop
@section('content')
    @include('partials.figma-header')

    <section class="figma-register-section">
        <div class="figma-register-container">
            <div class="figma-login-wrapper">
                <div class="figma-login-main" style="max-width: 520px; margin: 0 auto;">
                    <div class="figma-register-card">
                        <h2>{{ __('Reset Password') }}</h2>
                        <p class="mb-4">Enter your email address and we will send you a password reset link.</p>

                        @if (session('status'))
                            <div class="figma-alert figma-alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}" class="figma-form">
                            @csrf

                            <div class="figma-form-group">
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="figma-form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                    placeholder="{{ __('E-Mail Address') }}"
                                    required
                                    autofocus
                                >
                                @if ($errors->has('email'))
                                    <span class="figma-form-error">{{ $errors->first('email') }}</span>
                                @endif
                            </div>

                            <button type="submit" class="figma-btn figma-btn-primary figma-btn-block">
                                {{ __('Send Password Reset Link') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.figma-footer')
@endsection

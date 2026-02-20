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

                        <form method="POST" action="{{ route('password.update') }}" class="figma-form">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="figma-form-group">
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ $email ?? old('email') }}"
                                    class="figma-form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                    placeholder="{{ __('E-Mail Address') }}"
                                    required
                                    autofocus
                                >
                                @if ($errors->has('email'))
                                    <span class="figma-form-error">{{ $errors->first('email') }}</span>
                                @endif
                            </div>

                            <div class="figma-form-group">
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="figma-form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                    placeholder="{{ __('Password') }}"
                                    required
                                >
                                @if ($errors->has('password'))
                                    <span class="figma-form-error">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                            <div class="figma-form-group">
                                <input
                                    id="password-confirm"
                                    type="password"
                                    name="password_confirmation"
                                    class="figma-form-input"
                                    placeholder="{{ __('Confirm Password') }}"
                                    required
                                >
                            </div>

                            <button type="submit" class="figma-btn figma-btn-primary figma-btn-block">
                                {{ __('Reset Password') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.figma-footer')
@endsection

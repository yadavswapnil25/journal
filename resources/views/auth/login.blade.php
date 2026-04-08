@extends('master')
@section('title'){{ trans('prs.login_now') }} - {{ config('app.name') }} @stop
@section('description', 'Login to International Journal of Advanced Research in English Studies')
@section('content')
    
    @php
        $is_admin_login = $is_admin_login ?? false;
        if (!empty($_GET['user_id']) && !empty($_GET['email_type'])) {
            $user_id = $_GET['user_id'];
            $email_type = $_GET['email_type'];
            if (!empty($_GET['status']) && !empty($_GET['id'])) {
                $action = route('login',['user_id='.$user_id,'email_type='.$email_type,'status='.$_GET['status'],'id='.$_GET['id']]);
            } elseif (!empty($_GET['status'])) {
                $action = route('login',['user_id='.$user_id,'email_type='.$email_type,'status='.$_GET['status']]);
            } elseif (!empty($_GET['invoice_id'])) {
                $action = route('login',['user_id='.$user_id,'email_type='.$email_type,'invoice_id='.$_GET['invoice_id']]);
            } else {
                $action = route('login',['user_id='.$user_id,'email_type='.$email_type]);
            }
        }else{
            $action = $is_admin_login ? route('admin.login.submit') : route('login');
        }
        $reg_data = App\Models\SiteManagement::getMetaValue('reg_data');
        $login_focus = '';
        $register_focus = '';
        if (!empty($_GET['type'])) {
            if ($_GET['type'] == 'login') {
                $login_focus = 'autofocus';
            } elseif ($_GET['type'] == 'register') {
                $register_focus = 'autofocus';
            }
        }
    @endphp

    @include('partials.figma-header')

    {{-- Login Section --}}
    <section class="figma-register-section">
        <div class="figma-register-container">
            <div class="figma-login-wrapper">
                {{-- Login Form --}}
                <div class="figma-login-main">
                    <div class="figma-register-card">
                        <h2>{{ $is_admin_login ? 'Admin Login' : trans('prs.login_now') }}</h2>
                        @if (Session::has('message'))
                            <div class="figma-alert figma-alert-success">
                                {{ Session::get('message') }}
                            </div>
                        @elseif (Session::has('error'))
                            <div class="figma-alert figma-alert-error">
                                {{ Session::get('error') }}
                            </div>
                        @endif
                        <form method="POST" action="{{$action}}" class="figma-form">
                            @csrf
                            @if($is_admin_login)
                                <input type="hidden" name="login_context" value="admin">
                            @endif
                            @if(!$is_admin_login)
                                <div class="figma-form-group">
                                    <label class="figma-form-label" style="display:block; margin-bottom: 8px;">Login as</label>
                                    <div style="display:flex; gap:16px; flex-wrap:wrap;">
                                        <label class="figma-checkbox" style="width:auto;">
                                            <input type="radio" name="login_as" value="author" {{ old('login_as', 'author') === 'author' ? 'checked' : '' }}>
                                            <span>Author</span>
                                        </label>
                                        <label class="figma-checkbox" style="width:auto;">
                                            <input type="radio" name="login_as" value="editor" {{ old('login_as') === 'editor' ? 'checked' : '' }}>
                                            <span>Editor</span>
                                        </label>
                                        <label class="figma-checkbox" style="width:auto;">
                                            <input type="radio" name="login_as" value="reviewer" {{ old('login_as') === 'reviewer' ? 'checked' : '' }}>
                                            <span>Reviewer</span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                            <div class="figma-form-group">
                                <input type="email" name="email" value="{{$errors->has('email') ? old('email') : ''}}" 
                                    class="figma-form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                    placeholder="{{trans('prs.ph_email')}}" {{$login_focus}}>
                                @if ($errors->has('email'))
                                    <span class="figma-form-error">
                                        {{$errors->first('email')}}
                                    </span>
                                @endif
                            </div>
                            <div class="figma-form-group">
                                <input type="password" name="password" 
                                    class="figma-form-input {{ $errors->has('password') ? 'is-invalid' : '' }}" 
                                    placeholder="{{trans('prs.ph_pass')}}">
                                @if ($errors->has('password'))
                                    <span class="figma-form-error">
                                        {{$errors->first('password')}}
                                    </span>
                                @endif
                            </div>
                            <div class="figma-form-group figma-form-row">
                                <label class="figma-checkbox">
                                    <input type="checkbox" id="remember" name="remember">
                                    <span>{{trans('prs.keep_logged_in')}}</span>
                                </label>
                                <a href="{{ route('password.request') }}" class="figma-link">{{trans('prs.forgot_pass')}}</a>
                            </div>
                            <button type="submit" class="figma-btn figma-btn-primary figma-btn-block">{{trans('prs.btn_login')}}</button>
                        </form>
                        <div class="figma-login-divider">
                            <span>Don't have an account?</span>
                        </div>
                        <a href="{{ route('register') }}" class="figma-btn figma-btn-secondary figma-btn-block">
                            {{trans('prs.register')}}
                        </a>
                    </div>
                </div>

                {{-- Register Sidebar (if reg_data exists) --}}
                @if (!empty($reg_data))
                <div class="figma-register-sidebar">
                    <div class="figma-register-card">
                        <h3>{{trans('prs.reg_now')}}</h3>
                        <p>Create a new account to submit articles, review papers, and access exclusive content.</p>
                        <a href="{{ route('register') }}" class="figma-btn figma-btn-primary figma-btn-block">
                            {{trans('prs.register')}}
                        </a>
                        <div class="figma-register-info">
                            @foreach ($reg_data as $key => $value)
                                <h4>{{$value['title']}}</h4>
                                <div class="figma-register-description">
                                    @php echo htmlspecialchars_decode(stripslashes($value['desc'])); @endphp
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    @include('partials.figma-footer')

@endsection

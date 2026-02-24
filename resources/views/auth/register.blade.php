@extends('master')
@section('title'){{ trans('prs.reg_now') }} - {{ config('app.name') }} @stop
@section('description', 'Register for International Journal of Advanced Research in English Studies')
@section('content')
    
    @php
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
            $action = route('login');
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

    {{-- Register Section --}}
    <section class="figma-register-section">
        <div class="figma-register-container">
            <div class="figma-register-wrapper">
                {{-- Login Sidebar --}}
                <div class="figma-register-sidebar">
                    <div class="figma-register-card">
                        <h3>{{trans('prs.login_now')}}</h3>
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
                    </div>
                </div>

                {{-- Register Form --}}
                <div class="figma-register-main">
                    <div class="figma-register-card">
                        <h2>{{trans('prs.reg_now')}}</h2>
                        <div class="provider-site-wrap" v-show="loading" v-cloak>
                            <div class="provider-loader">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('register') }}" class="figma-form" id="register_form" @submit="showloading()">
                            @csrf
                            <div class="figma-form-row">
                                <div class="figma-form-group">
                                    <input id="name" type="text" 
                                        class="figma-form-input {{ $errors->has('name') ? 'is-invalid' : '' }}" 
                                        name="name" value="{{ old('name') }}"
                                        placeholder="{{trans('prs.ph_firstname')}}" required {{$register_focus}}>
                                    @if ($errors->has('name'))
                                        <span class="figma-form-error">
                                            {{ $errors->first('name') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="figma-form-group">
                                    <input type="text" name="sur_name" value="{{ old('sur_name') }}" 
                                        class="figma-form-input {{ $errors->has('sur_name') ? 'is-invalid' : '' }}"
                                        placeholder="{{trans('prs.ph_surname')}}*" required>
                                    @if ($errors->has('sur_name'))
                                        <span class="figma-form-error">
                                            {{ $errors->first('sur_name') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="figma-form-group">
                                <input id="email" type="email" 
                                    class="figma-form-input {{ $errors->register->first('email') ? 'is-invalid' : '' }}"  
                                    name="email"
                                    value="{{$errors->register->first('email') ? old('email') : ''}}" 
                                    placeholder="{{trans('prs.ph_email')}}" required>
                                @if ($errors->register->first('email'))
                                    <span class="figma-form-error">
                                        {{$errors->register->first('email')}}
                                    </span>
                                @endif
                            </div>
                            <div class="figma-form-group">
                                <input id="mobile_number" type="tel" 
                                    class="figma-form-input {{ $errors->register->first('mobile_number') ? 'is-invalid' : '' }}"  
                                    name="mobile_number"
                                    value="{{ old('mobile_number') }}" 
                                    placeholder="Mobile Number *" required>
                                @if ($errors->register->first('mobile_number'))
                                    <span class="figma-form-error">
                                        {{$errors->register->first('mobile_number')}}
                                    </span>
                                @endif
                            </div>
                            <div class="figma-form-group">
                                <input id="institutional_affiliation" type="text" 
                                    class="figma-form-input {{ $errors->register->first('institutional_affiliation') ? 'is-invalid' : '' }}"  
                                    name="institutional_affiliation"
                                    value="{{ old('institutional_affiliation') }}" 
                                    placeholder="Institutional Affiliation">
                                @if ($errors->register->first('institutional_affiliation'))
                                    <span class="figma-form-error">
                                        {{$errors->register->first('institutional_affiliation')}}
                                    </span>
                                @endif
                            </div>
                            <div class="figma-form-row">
                                <div class="figma-form-group">
                                    <input id="password" type="password" 
                                        class="figma-form-input {{ $errors->register->first('password') ? 'is-invalid' : '' }}" 
                                        name="password"
                                        placeholder="{{trans('prs.ph_pass')}}" required>
                                    @if ($errors->register->first('password'))
                                        <span class="figma-form-error">
                                            {{$errors->register->first('password')}}
                                        </span>
                                    @endif
                                </div>
                                <div class="figma-form-group">
                                    <input id="password-confirm" type="password" 
                                        class="figma-form-input" 
                                        name="password_confirmation" 
                                        placeholder="{{trans('prs.ph_cnfrm_pass')}}" required>
                                </div>
                            </div>
                            {{-- Hidden field to always set role as author --}}
                            <input type="hidden" name="role" value="author">
                            <div class="figma-form-group">
                                <label class="figma-checkbox">
                                    <input type="checkbox" id="terms_condition" 
                                        name="terms_condition" 
                                        class="{{ $errors->register->first('terms_condition') ? 'is-invalid' : '' }}" 
                                        value="registered">
                                    <span>{{trans('prs.terms_note')}} <a href="javascript:void(0);">{{trans('prs.terms_conditions')}}</a></span>
                                </label>
                                @if ($errors->register->first('terms_condition'))
                                    <span class="figma-form-error">
                                        {{$errors->register->first('terms_condition')}}
                                    </span>
                                @endif
                            </div>
                            <button type="submit" class="figma-btn figma-btn-primary figma-btn-block">{{trans('prs.btn_reg') }}</button>
                        </form>
                        @if (!empty($reg_data))
                            <div class="figma-register-info">
                                @foreach ($reg_data as $key => $value)
                                    <h3>{{$value['title']}}</h3>
                                    <div class="figma-register-description">
                                        @php echo htmlspecialchars_decode(stripslashes($value['desc'])); @endphp
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.figma-footer')

@endsection

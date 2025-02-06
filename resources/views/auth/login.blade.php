@extends('layouts.auth')
@section('page-title')
    {{__('Login')}}
@endsection
@php
    $languages = App\Models\Utility::languages();
    $settings = App\Models\Utility::settings();
    config(
        [
            'captcha.secret' => $settings['google_recaptcha_secret'],
            'captcha.sitekey' => $settings['google_recaptcha_key'],
            'options' => [
                'timeout' => 30,
            ],
        ]
    );
@endphp
@section('language-bar')
    <div class="lang-dropdown-only-desk">
        <li class="dropdown dash-h-item drp-language">
            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="drp-text"> {{ ucFirst($languages[$lang]) }}
                </span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                @foreach(Utility::languages() as $code => $language)
                    <a href="{{ route('login',$code) }}" tabindex="0" class="dropdown-item {{ $code == $lang ? 'active':'' }}">
                        <span>{{ ucFirst($language)}}</span>
                    </a>
                @endforeach
            </div>
        </li>
    </div>
@endsection
@section('content')
    <div class="card-body">
        <div>
            <h2 class="mb-3 f-w-600">{{ __('Login') }}</h2>
        </div>
        <div class="custom-login-form">
            {{Form::open(array('route'=>'login','method'=>'post','id'=>'loginForm','class'=>'needs-validation','novalidate'=>''))}}
                <div class="form-group mb-3">
                    {{Form::label('email',__('Email'),array('class' => 'form-label','id'=>'email'))}}
                    {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter your email')))}}
                    @error('email')
                        <span class="invalid-email text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-3 pss-field">
                    {{Form::label('password',__('Password'),array('class' => 'form-label','id'=>'password'))}}
                    {{Form::password('password',array('class'=>'form-control','placeholder'=>__('Password')))}}
                    @error('password')
                        <span class="invalid-password text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        @if (Route::has('password.request'))
                            <span>
                                <a href="{{ route('password.request', $lang) }}" tabindex="0">{{ __('Forgot Your Password?') }}</a>
                            </span>
                        @endif
                    </div>
                </div>
                @if($settings['recaptcha_module'] == 'yes')
                    <div class="form-group col-lg-12 col-md-12 mt-3">
                        {!! NoCaptcha::display($settings['cust_darklayout']=='on' ? ['data-theme' => 'dark'] : []) !!}
                        @error('g-recaptcha-response')
                        <span class="small text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                @endif
                <div class="d-grid">
                    {{Form::submit(__('Login'),array('class'=>'btn btn-primary mt-2','id'=>'saveBtn'))}}
                </div>
                @if(Utility::getValByName('signup')=='on')
                    <p class="my-4 text-center">{{ __("Don't have an account?") }}
                        <a href="{{route('register',$lang)}}" tabindex="0">{{__('Register')}}</a>
                    </p>
                @endif
            {{Form::close()}}
        </div>
    </div>
@endsection

@push('custom-scripts')
    @if($settings['recaptcha_module'] == 'yes')
        {!! NoCaptcha::renderJs() !!}
    @endif

    <script src="{{asset('libs/jquery/dist/jquery.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $("#loginForm").submit(function (e) {
                $("#saveBtn").attr("disabled", true);
                return true;
            });
        });
    </script>
@endpush

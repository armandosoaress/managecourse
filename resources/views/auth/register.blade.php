@extends('layouts.auth')
@section('page-title')
    {{ __('Register') }}
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
                    <a href="{{ route('register', $code) }}" tabindex="0" class="dropdown-item {{ $code == $lang ? 'active':'' }}">
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
            <h2 class="mb-3 f-w-600">{{ __('Register') }}</h2>
        </div>
            @if (session('status'))
                <div class="mb-4 font-medium text-lg text-green-600 text-danger">
                    {{ __('Email SMTP settings does not configured so please contact to your site admin.') }}
                </div>
            @endif
        <div class="custom-login-form">
            {{ Form::open(['route' => 'register', 'method' => 'post', 'id' => 'loginForm']) }}
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Name') }}</label>
                    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Your Name')]) }}
                    @error('name')
                        <span class="error invalid-name text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Store Name') }}</label>
                    {{ Form::text('store_name', null, ['class' => 'form-control', 'placeholder' => __('Enter Store Name')]) }}
                    @error('store_name')
                        <span class="error invalid-store_name text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Email') }}</label>
                    {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Your Email')]) }}
                    @error('email')
                        <span class="error invalid-email text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Password') }}</label>
                    {{ Form::password('password', ['class' => 'form-control', 'id' => 'input-password', 'placeholder' => __('Enter Your Password')]) }}
                    @error('password')
                        <span class="error invalid-password text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('Confirm password') }}</label>
                    {{ Form::password('password_confirmation', ['class' => 'form-control', 'id' => 'confirm-input-password', 'placeholder' => __('Enter Your Confirm Password')]) }}
                    @error('password_confirmation')
                        <span class="error invalid-password_confirmation text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
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
                    {{ Form::submit(__('Create my account'), ['class' => 'btn btn-primary btn-block mt-2', 'id' => 'saveBtn']) }}
                </div>

                <p class="my-4 text-center">{{ __('Already have an acocunt?') }}
                    <a href="{{ route('login', $lang) }}"  tabindex="0">{{ __('Login') }}</a>
                </p>
            {{ Form::close() }}
        </div>
    </div>
@endsection
@push('custom-scripts')
    @if($settings['recaptcha_module'] == 'yes')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush

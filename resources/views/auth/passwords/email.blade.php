@extends('layouts.auth')
@section('page-title')
    {{ __('Reset Password') }}
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
                    <a href="{{ route('password.request', $code) }}" tabindex="0" class="dropdown-item {{ $code == $lang ? 'active':'' }}">
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
            <h2 class="mb-3 f-w-600">{{ __('Forgot Password') }}</h2>
        </div>
        @if (session('status'))
            <small class="text-muted">{{ session('status') }}</small>
        @endif
        <div>
        {{ Form::open(['route' => 'password.email', 'method' => 'post', 'id' => 'loginForm']) }}
            <div class="form-group mb-3">
                {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
                {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Your Email')]) }}
                @error('email')
                    <span class="invalid-email text-danger" role="alert">
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
                {{ Form::submit(__('Forgot Password'), ['class' => 'btn btn-primary  btn-block mt-2', 'id' => 'saveBtn']) }}
            </div>
            <p class="my-4 text-center">{{ __('Back to') }}?
                <a href="{{ route('login', $lang) }}">{{ __('Login') }}</a>
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

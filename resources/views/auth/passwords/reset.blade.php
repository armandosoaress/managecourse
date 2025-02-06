@extends('layouts.auth')
@section('page-title')
    {{__('Reset Password')}}
@endsection
@php
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
@section('content')
    <div class="card-body">
        <div class="">
            <h2 class="mb-3 f-w-600">{{ __('Reset Password') }}</h2>
        </div>
        <div class="custom-login-form">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->token }}">
                <div class="form-group mb-3">
                    {{Form::label('E-Mail',__('E-Mail'),array('class' => 'form-label'))}}
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    {{Form::label('Password',__('Password'),array('class' => 'form-label'))}}
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>

                        </span>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    {{Form::label('password-confirm',__('Confirm Password'),array('class' => 'form-label'))}}
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
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
                    <button type="submit" class="btn btn-primary text-white">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('storefront.theme4.user.user')
@section('page-title')
    {{ __('Login') }} - {{ $store->tagline ? $store->tagline : config('APP_NAME', ucfirst($store->name)) }}
@endsection
@push('css-page')
@endpush
@section('head-title')
    {{ __('Student Login') }}
@endsection
@section('content')
    <div class="wrapper">
        <section class="login-page padding-bottom padding-top">
            <div class="banner-image">
                <img src="{{ asset('assets/themes/theme4/images/login-page-img.png') }}" alt="">
            </div>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-12">
                        <div class="login-left-side">
                            <div class="section-title">
                                <h2>{{ __('Forgot Password') }}</h2>
                            </div>
                            <div class="form-wrapper">
                                {!! Form::open(['route' => ['student.password.email', $slug]], ['method' => 'POST']) !!}
                                <p class="small">{{ __('Enter your email below to proceed') }}.</p>
                                <div class="form-group">
                                    {{ Form::text('email', null, ['class' => 'form-control form-control-lg', 'placeholder' => __('Enter Your Email')]) }}
                                    @error('email')
                                        <span class="error invalid-password text-danger" role="alert">
                                            <strong>{{ __('We can not find a student with that email address') }}.</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="text-center">
                                    {{ Form::submit(__('Send password reset link'), ['class' => 'btn btn-block btn-lg btn-primary mt-4 mb-3', 'id' => 'saveBtn']) }}
                                    {{ __('Back to') }}
                                    <a href="{{ route('student.login', $slug) }}">{{ __('Login') }}</a>
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="login-left">
                            <img src="{{ asset('./assets/themes/theme4/images/login-img.svg') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('script-page')
    <script>
        if ('{!! !empty($is_cart) && $is_cart == true !!}') {
            show_toastr('Error', 'You need to login!', 'error');
        }
    </script>
@endpush

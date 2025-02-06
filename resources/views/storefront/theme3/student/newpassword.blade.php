@extends('storefront.theme3.user.user')
@section('page-title')
    {{ __('Login') }} - {{ $store->tagline ? $store->tagline : config('APP_NAME', ucfirst($store->name)) }}
@endsection
@push('css-page')
@endpush
@section('head-title')
    {{ __('Student Login') }}
@endsection
@section('content')
    <section class="login-page padding-bottom padding-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-12">
                    <div class="login-left-side">
                        <div class="section-title">
                            <h3>{{ __('Password Reset') }}</h3>
                        </div>
                        <div class="form-wrapper">
                            {!! Form::open(['route' => ['student.password.update', $slug]], ['method' => 'post']) !!}
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="form-group">
                                {{ Form::text('email', null, ['class' => 'form-control form-control-lg', 'placeholder' => __('Enter Your Email')]) }}
                                @error('email')
                                    <span class="error invalid-password text-danger" role="alert">
                                        <strong>{{ __('We can not find a student with that email address') }}.</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                {{ Form::password('password', ['class' => 'form-control form-control-lg', 'placeholder' => __('Enter Your Password')]) }}
                                @error('password')
                                    <span class="error invalid-password text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                {{ Form::password('password_confirmation', ['class' => 'form-control form-control-lg', 'placeholder' => __('Confirm Password')]) }}
                                @error('password_confirmation')
                                    <span class="error invalid-password_confirmation text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="text-center">
                                {{ Form::submit(__('Reset'), ['class' => 'btn btn-block btn-lg btn-primary mt-4 mb-3', 'id' => 'saveBtn']) }}
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="login-left">
                        <div class="advance-search">
                            <div class="advance-search-back">
                                <div class="advance-search-image">
                                    <img src="{{ asset('assets/themes/theme3/images/banner-img.jpg') }}" alt="Image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script-page')
    <script>
        if ('{!! !empty($is_cart) && $is_cart == true !!}') {
            show_toastr('Error', 'You need to login!', 'error');
        }
    </script>
@endpush

@extends('storefront.theme1.user.user')
@section('page-title')
    {{ __('Login') }} - {{ $store->tagline ? $store->tagline : config('APP_NAME', ucfirst($store->name)) }}
@endsection
@push('css-page')
@endpush
@section('head-title')
    {{ __('Rest Password') }}
@endsection
@section('content')
    <section class="register-page padding-bottom padding-top">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-12">
                    <div class="section-title text-center">
                        <h3>{{ __('Forgot') }} <b>{{ __('Password') }}</b></h3>
                    </div>
                    <div class="form-wrapper">
                        {!! Form::open(
                            [
                                'route' => ['student.password.email', $slug, !empty($is_cart) && $is_cart == true ? $is_cart : false],
                                'class' => 'login-form',
                            ],
                            ['method' => 'POST'],
                        ) !!}
                        <div class="form-container">
                            <div class="form-heading">
                                <h5>{{ __('Password Reset') }}</h5>
                            </div>
                        </div>
                        <div class="form-container">
                            <div class="row">
                                <div class="col-md-12 col-12">
                                    <div class="form-group">
                                        {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Your Email')]) }}
                                        @error('email')
                                            <span class="error invalid-password text-danger" role="alert">
                                                <strong>{{ __('We can not find a student with that email address') }}.</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 col-12">
                                    <div for="exampleInputPassword1" class="form-group">
                                        {{ Form::submit(__('Send password reset link'), ['class' => 'btn btn-block btn-lg btn-primary mt-4 mb-3', 'id' => 'saveBtn']) }}
                                        <div class="d-block mt-2 px-md-5"><small> {{ __('Back to') }}</small>
                                            <a href="{{ route('student.login', $slug) }}">{{ __('Login') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {!! Form::close() !!}
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

@extends('layouts.admin')

@php
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $logo_img = \App\Models\Utility::getValByName('company_logo');
    $logo_light = \App\Models\Utility::getValByName('company_logo_light');
    $s_logo = \App\Models\Utility::get_file('uploads/store_logo/');
    // $logo = asset(Storage::url('uploads/logo/'));
    $logo_dark = Utility::getValByName('company_logo_dark');
    // $light_logo = Utility::getValByName('company_logo_light');
    // $company_logo = \App\Models\Utility::GetLogo();
    $company_favicon = Utility::getValByName('company_favicon');
    $store_logo = asset(Storage::url('uploads/store_logo/'));
    $lang = Utility::getValByName('default_language');
    $user = \Auth::user();
    if (Auth::user()->type != 'super admin') {
        $store_lang = $store_settings->lang;
    }
    $SITE_RTL = $settings['SITE_RTL'];
    if ($SITE_RTL == '') {
        $SITE_RTL == 'off';
    }
    $google_calender = $settings['google_calender_enabled'];
    if ($google_calender == '') {
        $google_calender == 'off';
    }
    // storage setting
    $file_type = config('files_types');
    $setting = App\Models\Utility::settings();

    $local_storage_validation = $setting['local_storage_validation'];
    $local_storage_validations = explode(',', $local_storage_validation);

    $s3_storage_validation = $setting['s3_storage_validation'];
    $s3_storage_validations = explode(',', $s3_storage_validation);

    $wasabi_storage_validation = $setting['wasabi_storage_validation'];
    $wasabi_storage_validations = explode(',', $wasabi_storage_validation);
@endphp

@section('page-title')
    @if (Auth::user()->type == 'super admin')
        {{ __('Settings') }}
    @else
        {{ __('Store Settings') }}
    @endif
@endsection
@section('title')
    @if (Auth::user()->type == 'super admin')
        {{ __('Settings') }}
    @else
        {{ __('Store Settings') }}
    @endif
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Settings') }}</li>
@endsection
@section('action-btn')
@endsection
@section('filter')
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('libs/summernote/summernote-bs4.css') }}">
    <style>
        hr {
            margin: 8px;
        }
    </style>
@endpush
@push('script-page')

@endpush

    @section('content')

        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-xl-3">
                        <div class="card sticky-top" style="top:30px">
                            <div class="list-group list-group-flush" id="useradd-sidenav">
                                @if (Auth::user()->type == 'super admin')
                                    <a href="#site_setting" id="site_setting_tab"
                                        class="list-group-item list-group-item-action border-0 active">{{ __('Brand Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#payment-setting" id="payment-setting_tab"
                                        class="list-group-item list-group-item-action border-0">{{ __('Payment Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#email_setting" id="system_setting_tab"
                                        class="list-group-item list-group-item-action border-0">{{ __('Email Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#recaptcha-settings" id="recaptcha_setting_tab"
                                        class="list-group-item list-group-item-action border-0">{{ __('ReCaptcha Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#storage_settings" id="storage_setting_tab"
                                        class="list-group-item list-group-item-action border-0">{{ __('Storage Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#cache_settings" id="cache_setting_tab"
                                        class="list-group-item list-group-item-action border-0">{{ __('Cache Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#cookie_settings" id="cache_setting_tab"
                                        class="list-group-item list-group-item-action border-0">{{ __('Cookie Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#chatgpt_settings" id="chatgpt_setting_tab"
                                        class="list-group-item list-group-item-action border-0">{{ __('Chat GPT Key Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                @else
                                    <a href="#store_site_setting" id="site_setting_tab"
                                        class="list-group-item list-group-item-action border-0">{{ __('Brand Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#store_setting" id="store_setting_tab"
                                        class="list-group-item list-group-item-action border-0 active">{{ __('Store Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#store_payment-setting" id="payment-setting_tab"
                                        class="list-group-item list-group-item-action border-0">{{ __('Store Payment Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#store_email_setting" id="email_store_setting"
                                        class="list-group-item list-group-item-action border-0">{{ __('Store Email Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#certificate_setting" id="certificate_store_setting"
                                        class="list-group-item list-group-item-action border-0">{{ __('Certificate Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#slack-setting" id="slack_store_setting"
                                        class="list-group-item list-group-item-action border-0">{{ __('Slack Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#telegram-setting" id="telegram_store_setting"
                                        class="list-group-item list-group-item-action border-0">{{ __('Telegram Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#google-calender-setting" id="google-calender_setting"
                                        class="list-group-item list-group-item-action border-0">{{ __('Google Calender Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#pixel_setting" id="pixel_setting_tab"
                                        class="list-group-item list-group-item-action border-0">{{ __('Pixel Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                    <a href="#webhook_setting" id="webhook_setting_tab"
                                        class="list-group-item list-group-item-action border-0">{{ __('Webhook Settings') }}
                                        <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                    </a>
                                @endif

                            </div>
                        </div>
                    </div>
                    <div class="col-xl-9">
                        @if (Auth::user()->type == 'super admin')
                            <div id="site_setting" class="card">
                                <div class="card-header">
                                    <h5 class="mb-2">{{ __('Brand Settings') }}</h5>
                                </div>
                                {{ Form::model($settings, ['route' => 'business.setting', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="small-title">{{ __('Dark Logo') }}</h5>
                                                </div>
                                                <div class="card-body setting-card setting-logo-box p-3">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="logo-content logo-set-bg text-center py-2">
                                                                <a href="{{ $logo . (isset($logo_img) && !empty($logo_img) ? $logo_img : 'logo-dark.png') .'?'. time() }}"
                                                                    target="_blank">
                                                                    <img src="{{ $logo . (isset($logo_img) && !empty($logo_img) ? $logo_img : 'logo-dark.png') . '?'. time() }}"
                                                                        id="logoDark" class="big-logo">
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="choose-files mt-4">
                                                                <label for="logo_dark" class="form-label d-block">
                                                                    <div class="bg-primary m-auto"><i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                        <input type="file" name="logo_dark" id="logo_dark"
                                                                            class="form-control file mb-3"
                                                                            data-filename="logo_dark"
                                                                            onchange="document.getElementById('logoDark').src = window.URL.createObjectURL(this.files[0])">
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="small-title">{{ __('Light Logo') }}</h5>
                                                </div>
                                                <div class="card-body setting-card setting-logo-box p-3">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="logo-content logo-set-bg  text-center py-2">
                                                                <a href="{{ $logo . (isset($logo_img) && !empty($logo_img) ? $logo_img : 'logo-light.png').'?'. time() }}"
                                                                    target="_blank">
                                                                    <img src="{{ $logo . (isset($logo_img) && !empty($logo_img) ? $logo_img : 'logo-light.png') . '?'. time() }}"
                                                                        id="logoLight" class="big-logo img_setting">
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="choose-files mt-4">
                                                                <label for="logo_light" class="form-label d-block">
                                                                    <div class="bg-primary m-auto"> <i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                        <input type="file" name="logo_light"
                                                                            id="logo_light" class="form-control file mb-3"
                                                                            data-filename="company_logo_update"
                                                                            onchange="document.getElementById('logoLight').src = window.URL.createObjectURL(this.files[0])">
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="small-title">{{ __('Favicon') }}</h5>
                                                </div>
                                                <div class="card-body setting-card setting-logo-box p-3">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="logo-content logo-set-bg  text-center py-2 mt-2">
                                                                <a href="{{ $logo . 'favicon.png'.'?'. time() }}" target="_blank">
                                                                    <img src="{{ $logo . 'favicon.png' . '?'. time() }}" id="blah" width="50px" class="img_setting">
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">

                                                            <div class="choose-files mt-4">
                                                                <label for="favicon" class="form-label d-block">
                                                                    <div class="bg-primary m-auto"> <i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                        <input type="file" name="favicon" id="favicon"
                                                                            class="form-control file mb-3"
                                                                            data-filename="company_logo_update"
                                                                            onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            {{ Form::label('title_text', __('Title Text'), ['class' => 'form-label']) }}
                                            {{ Form::text('title_text', null, ['class' => 'form-control', 'placeholder' => __('Title Text')]) }}
                                            @error('title_text')
                                                <span class="invalid-title_text" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            {{ Form::label('footer_text', __('Footer Text'), ['class' => 'form-label']) }}
                                            {{ Form::text('footer_text', null, ['class' => 'form-control', 'placeholder' => __('Footer Text')]) }}
                                            @error('footer_text')
                                                <span class="invalid-footer_text" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-4">
                                            {{ Form::label('default_language', __('Default Language'), ['class' => 'form-label']) }}
                                            <div class="changeLanguage">
                                                <select name="default_language" id="default_language"
                                                    class="form-control form-select">
                                                    @foreach (Utility::languages() as $code => $language)
                                                        <option @if ($lang == $code) selected @endif
                                                            value="{{ $code }}">
                                                            {{ ucfirst($language) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-xl-12 col-md-12">
                                            <div class="row">
                                                <div class="col switch-width">
                                                    <div class="form-group ml-2 mr-3">
                                                        {{ Form::label('display_landing_page_', __('Enable Landing Page'), ['class' => 'form-label']) }}
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" data-toggle="switchbutton"
                                                                data-onstyle="primary" class=""
                                                                name="display_landing_page" id="display_landing_page"
                                                                {{ $settings['display_landing_page'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="display_landing_page"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col switch-width">
                                                    <div class="form-group ml-2 mr-3 ">
                                                        <label class="form-label">{{ __('Enable RTL') }}</label>
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" data-toggle="switchbutton"
                                                                data-onstyle="primary" class="" name="SITE_RTL"
                                                                id="SITE_RTL"
                                                                {{ $settings['SITE_RTL'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label" for="SITE_RTL"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col switch-width">
                                                    <div class="form-group ml-2 mr-3 ">
                                                        {{ Form::label('signup', __('Enable Sign-Up Page'), ['class' => 'form-label']) }}
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" data-toggle="switchbutton"
                                                                data-onstyle="primary" class="" name="signup"
                                                                id="signup"
                                                                {{ $settings['signup'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label" for="signup"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col switch-width">
                                                    <div class="form-group ml-2 mr-3 ">
                                                        {{ Form::label('email_verification', __('Email Verification'), ['class' => 'form-label']) }}
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" data-toggle="switchbutton"
                                                                data-onstyle="primary" class="" name="email_verification"
                                                                id="email_verification"
                                                                {{ $settings['email_verification'] == 'on' ? 'checked="checked"' : '' }}>
                                                            <label class="custom-control-label" for="email_verification"></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <h4 class="small-title">{{ __('Theme Customizer') }}</h4>
                                            <div class="setting-card setting-logo-box p-3">
                                                <div class="row">
                                                    <div class="pct-body">
                                                        <div class="row">
                                                            <div class="col-lg-4 col-xl-4 col-md-4">
                                                                <h6 class="mt-2">
                                                                    <i data-feather="credit-card"
                                                                        class="me-2"></i>{{ __('Primary color settings') }}
                                                                </h6>
                                                                <hr class="my-2" />
                                                                <div class="theme-color themes-color">
                                                                    <a href="#!" class="{{($settings['color'] == 'theme-1') ? 'active_color' : ''}}" data-value="theme-1" onclick="check_theme('theme-1')"></a>
                                                                    <input type="radio" class="theme_color" name="color" value="theme-1" style="display: none;">
                                                                    <a href="#!" class="{{($settings['color'] == 'theme-2') ? 'active_color' : ''}} " data-value="theme-2" onclick="check_theme('theme-2')"></a>
                                                                    <input type="radio" class="theme_color" name="color" value="theme-2" style="display: none;">
                                                                    <a href="#!" class="{{($settings['color'] == 'theme-3') ? 'active_color' : ''}}" data-value="theme-3" onclick="check_theme('theme-3')"></a>
                                                                    <input type="radio" class="theme_color" name="color" value="theme-3" style="display: none;">
                                                                    <a href="#!" class="{{($settings['color'] == 'theme-4') ? 'active_color' : ''}}" data-value="theme-4" onclick="check_theme('theme-4')"></a>
                                                                    <input type="radio" class="theme_color" name="color" value="theme-4" style="display: none;">
                                                                    <a href="#!" class="{{($settings['color'] == 'theme-5') ? 'active_color' : ''}}" data-value="theme-5" onclick="check_theme('theme-5')"></a>
                                                                    <input type="radio" class="theme_color" name="color" value="theme-5" style="display: none;">
                                                                    <br>
                                                                    <a href="#!" class="{{($settings['color'] == 'theme-6') ? 'active_color' : ''}}" data-value="theme-6" onclick="check_theme('theme-6')"></a>
                                                                    <input type="radio" class="theme_color" name="color" value="theme-6" style="display: none;">
                                                                    <a href="#!" class="{{($settings['color'] == 'theme-7') ? 'active_color' : ''}}" data-value="theme-7" onclick="check_theme('theme-7')"></a>
                                                                    <input type="radio" class="theme_color" name="color" value="theme-7" style="display: none;">
                                                                    <a href="#!" class="{{($settings['color'] == 'theme-8') ? 'active_color' : ''}}" data-value="theme-8" onclick="check_theme('theme-8')"></a>
                                                                    <input type="radio" class="theme_color" name="color" value="theme-8" style="display: none;">
                                                                    <a href="#!" class="{{($settings['color'] == 'theme-9') ? 'active_color' : ''}}" data-value="theme-9" onclick="check_theme('theme-9')"></a>
                                                                    <input type="radio" class="theme_color" name="color" value="theme-9" style="display: none;">
                                                                    <a href="#!" class="{{($settings['color'] == 'theme-10') ? 'active_color' : ''}}" data-value="theme-10" onclick="check_theme('theme-10')"></a>
                                                                    <input type="radio" class="theme_color" name="color" value="theme-10" style="display: none;">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-xl-4 col-md-4">
                                                                <h6 class="mt-2">
                                                                    <i data-feather="layout"
                                                                        class="me-2"></i>{{ __('Sidebar settings') }}
                                                                </h6>
                                                                <hr class="my-2" />
                                                                <div class="form-check form-switch">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        id="cust-theme-bg" name="cust_theme_bg"
                                                                        {{ Utility::getValByName('cust_theme_bg') == 'on' ? 'checked' : '' }} />
                                                                    <label class="form-check-label f-w-600 pl-1"
                                                                        for="cust-theme-bg">{{ __('Transparent layout') }}</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-xl-4 col-md-4">
                                                                <h6 class="mt-2">
                                                                    <i data-feather="sun"
                                                                        class="me-2"></i>{{ __('Layout settings') }}
                                                                </h6>
                                                                <hr class="my-2" />
                                                                <div class="form-check form-switch mt-2">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        id="cust-darklayout" name="cust_darklayout"
                                                                        {{ Utility::getValByName('cust_darklayout') == 'on' ? 'checked' : '' }} />
                                                                    <label class="form-check-label f-w-600 pl-1"
                                                                        for="cust-darklayout">{{ __('Dark Layout') }}</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    {{ Form::submit(__('Save Changes'), ['class' => 'btn btn-primary']) }}
                                </div>
                                {{ Form::close() }}
                            </div>

                            <div id="payment-setting" class="card">
                                <div class="card-header">
                                    <h5 class="mb-2">{{ __('Payment Settings') }}</h5>
                                    <small
                                        class="text-secondary">{{ __('These details will be used to collect subscription plan payments.Each subscription plan will have a payment button based on the below configuration') }}</small>
                                </div>
                                {{ Form::open(['route' => 'payment.setting', 'method' => 'post']) }}
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                    <label class="col-form-label">{{ __('Currency') }} *</label>
                                                    <input type="text" name="currency" class="form-control"
                                                        value="{{ isset($admin_payment_setting['currency']) ? $admin_payment_setting['currency'] : '' }}" required>
                                                    <small class="text-xs">
                                                        {{ __('Note: Add currency code as per three-letter ISO code') }}.
                                                        <a href="https://stripe.com/docs/currencies"
                                                            target="_blank">{{ __('You can find out how to do that here.') }}</a>
                                                    </small>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                    <label for="currency_symbol"
                                                        class="col-form-label">{{ __('Currency Symbol*') }}</label>
                                                    <input type="text" name="currency_symbol" class="form-control"
                                                        value="{{ isset($admin_payment_setting['currency_symbol']) ? $admin_payment_setting['currency_symbol'] : '' }}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 faq">
                                            <div class="accordion accordion-flush setting-accordion" id="accordionExample">

                                                <!-- Manually -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-15">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse15"
                                                            aria-expanded="true" aria-controls="collapse2">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Manually') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="manually_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="manually_enabled" id="manually_enabled"
                                                                        {{ isset($admin_payment_setting['manually_enabled']) && $admin_payment_setting['manually_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="manually_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse15"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-15"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <small class="m-0">{{ __('Requesting manual payment for the planned amount for the subscriptions plan.') }}</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Bank transfer -->
                                                <div class="accordion-item">
                                                    <h2 class=" accordion-header" id="heading-2-16">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse16"
                                                            aria-expanded="false" aria-controls="collapse16">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Bank Transfer') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1 d-inline-block">
                                                                    <input type="hidden" name="enable_bank"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input input-primary"
                                                                        name="enable_bank" id="enable_bank" {{ isset($admin_payment_setting['enable_bank']) && $admin_payment_setting['enable_bank'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label"
                                                                        for="enable_bank"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse16" class="accordion-collapse collapse"
                                                        aria-labelledby="heading-2-16" data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        <label class="form-label">{{ __('Bank Details') }}</label>

                                                                        <textarea type="text" name="bank_number" id="bank_number" class="form-control" rows="6" placeholder="{{ __('Bank Transfer Number') }}">{{ isset($admin_payment_setting['bank_number']) ? $admin_payment_setting['bank_number'] : '' }}</textarea>
                                                                        <small>{{ __('Example : Bank : bank name </br> Account Number : 0000 0000 </br>') }}</small>
                                                                        @if ($errors->has('bank_number'))
                                                                            <span class="invalid-feedback d-block">
                                                                                {{ $errors->first('bank_number') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Strip -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-2">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse1"
                                                            aria-expanded="true" aria-controls="collapse1">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Stripe') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_stripe_enabled" value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_stripe_enabled" id="is_stripe_enabled"
                                                                        {{ isset($admin_payment_setting['is_stripe_enabled']) && $admin_payment_setting['is_stripe_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_stripe_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse1"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-2"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="stripe_key"
                                                                            class="col-form-label">{{ __('Stripe Key') }}</label>
                                                                        <input class="form-control"
                                                                            placeholder="{{ __('Stripe Key') }}"
                                                                            name="stripe_key" type="text"
                                                                            value="{{ isset($admin_payment_setting['stripe_key']) ? $admin_payment_setting['stripe_key'] : '' }}"
                                                                            id="stripe_key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="stripe_secret"
                                                                            class="col-form-label">{{ __('Stripe Secret') }}</label>
                                                                        <input class="form-control "
                                                                            placeholder="{{ __('Stripe Secret') }}"
                                                                            name="stripe_secret" type="text"
                                                                            value="{{ isset($admin_payment_setting['stripe_secret']) ? $admin_payment_setting['stripe_secret'] : '' }}"
                                                                            id="stripe_secret">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Paypal -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-3">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse2"
                                                            aria-expanded="true" aria-controls="collapse2">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Paypal') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_paypal_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_paypal_enabled" id="is_paypal_enabled"
                                                                        {{ isset($admin_payment_setting['is_paypal_enabled']) && $admin_payment_setting['is_paypal_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_paypal_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse2"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-3"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="paypal-label col-form-label"
                                                                        for="paypal_mode">{{ __('Paypal Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($admin_payment_setting['paypal_mode']) && $admin_payment_setting['paypal_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="paypal_mode" value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($admin_payment_setting['paypal_mode']) && $admin_payment_setting['paypal_mode'] == '') || (isset($admin_payment_setting['paypal_mode']) && $admin_payment_setting['paypal_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>{{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($admin_payment_setting['paypal_mode']) && $admin_payment_setting['paypal_mode'] == 'live' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="paypal_mode" value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($admin_payment_setting['paypal_mode']) && $admin_payment_setting['paypal_mode'] == 'live' ? 'checked="checked"' : '' }}>{{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_client_id"
                                                                            class="col-form-label">{{ __('Client ID') }}</label>
                                                                        <input type="text" name="paypal_client_id"
                                                                            id="paypal_client_id" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paypal_client_id']) ? $admin_payment_setting['paypal_client_id'] : '' }}"
                                                                            placeholder="{{ __('Client ID') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="paypal_secret_key"
                                                                            id="paypal_secret_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paypal_secret_key']) ? $admin_payment_setting['paypal_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Paystack -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-4">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse3"
                                                            aria-expanded="true" aria-controls="collapse3">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Paystack') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_paystack_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_paystack_enabled"
                                                                        id="is_paystack_enabled"
                                                                        {{ isset($admin_payment_setting['is_paystack_enabled']) && $admin_payment_setting['is_paystack_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_paystack_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse3"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-4"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_client_id"
                                                                            class="col-form-label">{{ __('Public Key') }}</label>
                                                                        <input type="text" name="paystack_public_key"
                                                                            id="paystack_public_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paystack_public_key']) ? $admin_payment_setting['paystack_public_key'] : '' }}"
                                                                            placeholder="{{ __('Public Key') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paystack_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="paystack_secret_key"
                                                                            id="paystack_secret_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paystack_secret_key']) ? $admin_payment_setting['paystack_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- FLUTTERWAVE -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-5">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse4"
                                                            aria-expanded="true" aria-controls="collapse4">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Flutterwave') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_flutterwave_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_flutterwave_enabled"
                                                                        id="is_flutterwave_enabled"
                                                                        {{ isset($admin_payment_setting['is_flutterwave_enabled']) && $admin_payment_setting['is_flutterwave_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_flutterwave_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse4"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-5"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_client_id"
                                                                            class="col-form-label">{{ __('Public Key') }}</label>
                                                                        <input type="text" name="flutterwave_public_key"
                                                                            id="flutterwave_public_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['flutterwave_public_key']) ? $admin_payment_setting['flutterwave_public_key'] : '' }}"
                                                                            placeholder="{{ __('Public Key') }}"
                                                                            placeholder="Public Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paystack_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="flutterwave_secret_key"
                                                                            id="flutterwave_secret_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['flutterwave_secret_key']) ? $admin_payment_setting['flutterwave_secret_key'] : '' }}"
                                                                            placeholder="Secret Key">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Razorpay -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-6">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse5"
                                                            aria-expanded="true" aria-controls="collapse5">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Razorpay') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_razorpay_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_razorpay_enabled"
                                                                        id="is_razorpay_enabled"
                                                                        {{ isset($admin_payment_setting['is_razorpay_enabled']) && $admin_payment_setting['is_razorpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_razorpay_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse5"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-6"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="razorpay_public_key"
                                                                            class="col-form-label">{{ __('Public Key') }}</label>

                                                                        <input type="text" name="razorpay_public_key"
                                                                            id="razorpay_public_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['razorpay_public_key']) ? $admin_payment_setting['razorpay_public_key'] : '' }}"
                                                                            placeholder="Public Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="razorpay_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="razorpay_secret_key"
                                                                            id="razorpay_secret_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['razorpay_secret_key']) ? $admin_payment_setting['razorpay_secret_key'] : '' }}"
                                                                            placeholder="Secret Key">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Paytm -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-7">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse6"
                                                            aria-expanded="true" aria-controls="collapse6">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Paytm') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_paytm_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_paytm_enabled" id="is_paytm_enabled"
                                                                        {{ isset($admin_payment_setting['is_paytm_enabled']) && $admin_payment_setting['is_paytm_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_paytm_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse6"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-7"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="paypal-label col-form-label"
                                                                        for="paypal_mode">{{ __('Paytm Environment') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($admin_payment_setting['paytm_mode']) && $admin_payment_setting['paytm_mode'] == 'local' ? 'active' : '' }}">

                                                                                        <input type="radio"
                                                                                            name="paytm_mode" value="local"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($admin_payment_setting['paytm_mode']) && $admin_payment_setting['paytm_mode'] == '') || (isset($admin_payment_setting['paytm_mode']) && $admin_payment_setting['paytm_mode'] == 'local') ? 'checked="checked"' : '' }}>

                                                                                        {{ __('Local') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($admin_payment_setting['paytm_mode']) && $admin_payment_setting['paytm_mode'] == 'live' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="paytm_mode"
                                                                                            value="production"
                                                                                            class="form-check-input"
                                                                                            {{ isset($admin_payment_setting['paytm_mode']) && $admin_payment_setting['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>

                                                                                        {{ __('Production') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="paytm_public_key"
                                                                            class="col-form-label">{{ __('Merchant ID') }}</label>
                                                                        <input type="text" name="paytm_merchant_id"
                                                                            id="paytm_merchant_id" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paytm_merchant_id']) ? $admin_payment_setting['paytm_merchant_id'] : '' }}"
                                                                            placeholder="{{ __('Merchant ID') }}"
                                                                            placeholder="Merchant ID">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="paytm_secret_key"
                                                                            class="col-form-label">{{ __('Merchant Key') }}</label>
                                                                        <input type="text" name="paytm_merchant_key"
                                                                            id="paytm_merchant_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paytm_merchant_key']) ? $admin_payment_setting['paytm_merchant_key'] : '' }}"
                                                                            placeholder="Merchant Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="paytm_industry_type"
                                                                            class="col-form-label">{{ __('Industry Type') }}</label>
                                                                        <input type="text" name="paytm_industry_type"
                                                                            id="paytm_industry_type" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paytm_industry_type']) ? $admin_payment_setting['paytm_industry_type'] : '' }}"
                                                                            placeholder="Industry Type">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Mercado Pago-->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-8">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse7"
                                                            aria-expanded="true" aria-controls="collapse7">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Mercado Pago') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_mercado_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_mercado_enabled" id="is_mercado_enabled"
                                                                        {{ isset($admin_payment_setting['is_mercado_enabled']) && $admin_payment_setting['is_mercado_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_mercado_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse7"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-8"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="coingate-label col-form-label"
                                                                        for="mercado_mode">{{ __('Mercado Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark{{ isset($admin_payment_setting['mercado_mode']) && $admin_payment_setting['mercado_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="mercado_mode"
                                                                                            value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($admin_payment_setting['mercado_mode']) && $admin_payment_setting['mercado_mode'] == '') || (isset($admin_payment_setting['mercado_mode']) && $admin_payment_setting['mercado_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($admin_payment_setting['mercado_mode']) && $admin_payment_setting['mercado_mode'] == 'live' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="mercado_mode" value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($admin_payment_setting['mercado_mode']) && $admin_payment_setting['mercado_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mercado_access_token"
                                                                            class="col-form-label">{{ __('Access Token') }}</label>
                                                                        <input type="text" name="mercado_access_token"
                                                                            id="mercado_access_token" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['mercado_access_token']) ? $admin_payment_setting['mercado_access_token'] : '' }}"
                                                                            placeholder="{{ __('Access Token') }}" />
                                                                        @if ($errors->has('mercado_secret_key'))
                                                                            <span class="invalid-feedback d-block">
                                                                                {{ $errors->first('mercado_access_token') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Mollie -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-9">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse8"
                                                            aria-expanded="true" aria-controls="collapse8">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Mollie') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_mollie_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_mollie_enabled" id="is_mollie_enabled"
                                                                        {{ isset($admin_payment_setting['is_mollie_enabled']) && $admin_payment_setting['is_mollie_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_mollie_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse8"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-9"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_api_key"
                                                                            class="col-form-label">{{ __('Mollie Api Key') }}</label>
                                                                        <input type="text" name="mollie_api_key"
                                                                            id="mollie_api_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['mollie_api_key']) ? $admin_payment_setting['mollie_api_key'] : '' }}"
                                                                            placeholder="Mollie Api Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_profile_id"
                                                                            class="col-form-label">{{ __('Mollie Profile Id') }}</label>
                                                                        <input type="text" name="mollie_profile_id"
                                                                            id="mollie_profile_id" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['mollie_profile_id']) ? $admin_payment_setting['mollie_profile_id'] : '' }}"
                                                                            placeholder="Mollie Profile Id">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_partner_id"
                                                                            class="col-form-label">{{ __('Mollie Partner Id') }}</label>
                                                                        <input type="text" name="mollie_partner_id"
                                                                            id="mollie_partner_id" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['mollie_partner_id']) ? $admin_payment_setting['mollie_partner_id'] : '' }}"
                                                                            placeholder="Mollie Partner Id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Skrill -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-10">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse9"
                                                            aria-expanded="true" aria-controls="collapse9">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Skrill') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_skrill_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_skrill_enabled" id="is_skrill_enabled"
                                                                        {{ isset($admin_payment_setting['is_skrill_enabled']) && $admin_payment_setting['is_skrill_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_skrill_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse9"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-10"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_api_key"
                                                                            class="col-form-label">{{ __('Skrill Email') }}</label>
                                                                        <input type="text" name="skrill_email"
                                                                            id="skrill_email" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['skrill_email']) ? $admin_payment_setting['skrill_email'] : '' }}"
                                                                            placeholder="Enter Skrill Email">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- CoinGate -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-11">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse10"
                                                            aria-expanded="true" aria-controls="collapse10">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('CoinGate') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_coingate_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_coingate_enabled"
                                                                        id="is_coingate_enabled"
                                                                        {{ isset($admin_payment_setting['is_coingate_enabled']) && $admin_payment_setting['is_coingate_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_coingate_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse10"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-11"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="col-form-label"
                                                                        for="coingate_mode">{{ __('CoinGate Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($admin_payment_setting['coingate_mode']) && $admin_payment_setting['coingate_mode'] == 'sandbox' ? 'active' : '' }}">

                                                                                        <input type="radio"
                                                                                            name="coingate_mode"
                                                                                            value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($admin_payment_setting['coingate_mode']) && $admin_payment_setting['coingate_mode'] == '') || (isset($admin_payment_setting['coingate_mode']) && $admin_payment_setting['coingate_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($admin_payment_setting['coingate_mode']) && $admin_payment_setting['coingate_mode'] == 'live' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="coingate_mode"
                                                                                            value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($admin_payment_setting['coingate_mode']) && $admin_payment_setting['coingate_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="coingate_auth_token"
                                                                            class="col-form-label">{{ __('CoinGate Auth Token') }}</label>
                                                                        <input type="text" name="coingate_auth_token"
                                                                            id="coingate_auth_token" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['coingate_auth_token']) ? $admin_payment_setting['coingate_auth_token'] : '' }}"
                                                                            placeholder="{{ __('CoinGate Auth Token') }}">
                                                                    </div>

                                                                    @if ($errors->has('coingate_auth_token'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('coingate_auth_token') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- PaymentWall -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-12">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse11"
                                                            aria-expanded="true" aria-controls="collapse11">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('PaymentWall') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_paymentwall_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_paymentwall_enabled"
                                                                        id="is_paymentwall_enabled"
                                                                        {{ isset($admin_payment_setting['is_paymentwall_enabled']) && $admin_payment_setting['is_paymentwall_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_paymentwall_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse11"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-12"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paymentwall_public_key"
                                                                            class="col-form-label">{{ __('Public Key') }}</label>
                                                                        <input type="text" name="paymentwall_public_key"
                                                                            id="paymentwall_public_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paymentwall_public_key']) ? $admin_payment_setting['paymentwall_public_key'] : '' }}"
                                                                            placeholder="{{ __('Public Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('paymentwall_public_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paymentwall_public_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paymentwall_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="paymentwall_secret_key"
                                                                            id="paymentwall_secret_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paymentwall_secret_key']) ? $admin_payment_setting['paymentwall_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('paymentwall_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paymentwall_secret_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Toyyibpay -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-13">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse13"
                                                            aria-expanded="true" aria-controls="collapse13">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Toyyibpay') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_toyyibpay_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_toyyibpay_enabled"
                                                                        id="is_toyyibpay_enabled"
                                                                        {{ isset($admin_payment_setting['is_toyyibpay_enabled']) && $admin_payment_setting['is_toyyibpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_toyyibpay_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse13"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-13"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="category_code"
                                                                            class="col-form-label">{{ __('Category Code') }}</label>
                                                                        <input type="text" name="category_code"
                                                                            id="category_code" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['category_code']) ? $admin_payment_setting['category_code'] : '' }}"
                                                                            placeholder="{{ __('Category Code') }}">
                                                                    </div>
                                                                    @if ($errors->has('category_code'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('category_code') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="toyyibpay_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="toyyibpay_secret_key"
                                                                            id="toyyibpay_secret_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['toyyibpay_secret_key']) ? $admin_payment_setting['toyyibpay_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('toyyibpay_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('toyyibpay_secret_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Payfast -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-14">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse14"
                                                            aria-expanded="true" aria-controls="collapse14">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Payfast') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_payfast_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_payfast_enabled"
                                                                        id="is_payfast_enabled"
                                                                        {{ isset($admin_payment_setting['is_payfast_enabled']) && $admin_payment_setting['is_payfast_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_payfast_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse14"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-14"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="payfast-label col-form-label"
                                                                        for="payfast_mode">{{ __('Payfast Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($admin_payment_setting['payfast_mode']) && $admin_payment_setting['payfast_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="payfast_mode" value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($admin_payment_setting['payfast_mode']) && $admin_payment_setting['payfast_mode'] == '') || (isset($admin_payment_setting['payfast_mode']) &&
                                                                                            $admin_payment_setting['payfast_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>{{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($admin_payment_setting['payfast_mode']) && $admin_payment_setting['payfast_mode'] == 'live' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="payfast_mode" value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($admin_payment_setting['payfast_mode']) && $admin_payment_setting['payfast_mode'] == 'live' ? 'checked="checked"' : '' }}>{{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="payfast_merchant_id"
                                                                            class="col-form-label">{{ __('Merchant ID') }}</label>
                                                                        <input type="text" name="payfast_merchant_id"
                                                                            id="payfast_merchant_id" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['payfast_merchant_id']) ? $admin_payment_setting['payfast_merchant_id'] : '' }}"
                                                                            placeholder="{{ __('Merchant ID') }}">
                                                                    </div>
                                                                    @if ($errors->has('payfast_merchant_id'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('payfast_merchant_id') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="payfast_merchant_key"
                                                                            class="col-form-label">{{ __('Merchant Key') }}</label>
                                                                        <input type="text" name="payfast_merchant_key"
                                                                            id="payfast_merchant_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['payfast_merchant_key']) ? $admin_payment_setting['payfast_merchant_key'] : '' }}"
                                                                            placeholder="{{ __('Merchant Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('payfast_merchant_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('payfast_merchant_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="payfast_signature"
                                                                            class="col-form-label">{{ __('Salt Passphrase') }}</label>
                                                                        <input type="text" name="payfast_signature"
                                                                            id="payfast_signature" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['payfast_signature']) ? $admin_payment_setting['payfast_signature'] : '' }}"
                                                                            placeholder="{{ __('Salt Passphrase') }}">
                                                                    </div>
                                                                    @if ($errors->has('payfast_signature'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('payfast_signature') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Iyzipay -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-17">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse17"
                                                            aria-expanded="true" aria-controls="collapse17">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Iyzipay') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_iyzipay_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_iyzipay_enabled"
                                                                        id="is_iyzipay_enabled"
                                                                        {{ isset($admin_payment_setting['is_iyzipay_enabled']) && $admin_payment_setting['is_iyzipay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_iyzipay_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse17"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-17"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="iyzipay-label col-form-label"
                                                                        for="iyzipay_mode">{{ __('Iyzipay Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($admin_payment_setting['iyzipay_mode']) && $admin_payment_setting['iyzipay_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="iyzipay_mode" value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($admin_payment_setting['iyzipay_mode']) && $admin_payment_setting['iyzipay_mode'] == '') || (isset($admin_payment_setting['iyzipay_mode']) &&
                                                                                            $admin_payment_setting['iyzipay_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>{{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($admin_payment_setting['iyzipay_mode']) && $admin_payment_setting['iyzipay_mode'] == 'live' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="iyzipay_mode" value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($admin_payment_setting['iyzipay_mode']) && $admin_payment_setting['iyzipay_mode'] == 'live' ? 'checked="checked"' : '' }}>{{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="iyzipay_public_key"
                                                                            class="col-form-label">{{ __('Public Key') }}</label>
                                                                        <input type="text" name="iyzipay_public_key"
                                                                            id="iyzipay_public_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['iyzipay_public_key']) ? $admin_payment_setting['iyzipay_public_key'] : '' }}"
                                                                            placeholder="{{ __('Public Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('iyzipay_public_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('iyzipay_public_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="iyzipay_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="iyzipay_secret_key"
                                                                            id="iyzipay_secret_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['iyzipay_secret_key']) ? $admin_payment_setting['iyzipay_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('iyzipay_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('iyzipay_secret_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Sspay -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-18">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse18"
                                                            aria-expanded="true" aria-controls="collapse18">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Sspay') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_sspay_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_sspay_enabled"
                                                                        id="is_sspay_enabled"
                                                                        {{ isset($admin_payment_setting['is_sspay_enabled']) && $admin_payment_setting['is_sspay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_sspay_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse18"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-18"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="sspay_category_code"
                                                                            class="col-form-label">{{ __('Category Code') }}</label>
                                                                        <input type="text" name="sspay_category_code"
                                                                            id="sspay_category_code" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['sspay_category_code']) ? $admin_payment_setting['sspay_category_code'] : '' }}"
                                                                            placeholder="{{ __('Category Code') }}">
                                                                    </div>
                                                                    @if ($errors->has('sspay_category_code'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('sspay_category_code') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="sspay_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="sspay_secret_key"
                                                                            id="sspay_secret_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['sspay_secret_key']) ? $admin_payment_setting['sspay_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('sspay_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('sspay_secret_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- PayTab -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-19">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse19"
                                                            aria-expanded="true" aria-controls="collapse19">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('PayTab') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_paytab_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_paytab_enabled"
                                                                        id="is_paytab_enabled"
                                                                        {{ isset($admin_payment_setting['is_paytab_enabled']) && $admin_payment_setting['is_paytab_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_paytab_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse19"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-19"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paytab_profile_id"
                                                                            class="col-form-label">{{ __('Profile Id') }}</label>
                                                                        <input type="text" name="paytab_profile_id"
                                                                            id="paytab_profile_id" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paytab_profile_id']) ? $admin_payment_setting['paytab_profile_id'] : '' }}"
                                                                            placeholder="{{ __('Profile Id') }}">
                                                                    </div>
                                                                    @if ($errors->has('paytab_profile_id'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paytab_profile_id') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paytab_server_key"
                                                                            class="col-form-label">{{ __('Server Key') }}</label>
                                                                        <input type="text" name="paytab_server_key"
                                                                            id="paytab_server_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paytab_server_key']) ? $admin_payment_setting['paytab_server_key'] : '' }}"
                                                                            placeholder="{{ __('Server Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('paytab_server_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paytab_server_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paytab_region"
                                                                            class="col-form-label">{{ __('Region') }}</label>
                                                                        <input type="text" name="paytab_region"
                                                                            id="paytab_region" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paytab_region']) ? $admin_payment_setting['paytab_region'] : '' }}"
                                                                            placeholder="{{ __('Region') }}">
                                                                    </div>
                                                                    @if ($errors->has('paytab_region'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paytab_region') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Benefit -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-20">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse20"
                                                            aria-expanded="true" aria-controls="collapse20">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Benefit') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_benefit_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_benefit_enabled"
                                                                        id="is_benefit_enabled"
                                                                        {{ isset($admin_payment_setting['is_benefit_enabled']) && $admin_payment_setting['is_benefit_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_benefit_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse20"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-20"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="benefit_api_key"
                                                                            class="col-form-label">{{ __('Benefit Key') }}</label>
                                                                        <input type="text" name="benefit_api_key"
                                                                            id="benefit_api_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['benefit_api_key']) ? $admin_payment_setting['benefit_api_key'] : '' }}"
                                                                            placeholder="{{ __('Benefit Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('benefit_api_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('benefit_api_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="benefit_secret_key"
                                                                            class="col-form-label">{{ __('Benefit Secret Key') }}</label>
                                                                        <input type="text" name="benefit_secret_key"
                                                                            id="benefit_secret_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['benefit_secret_key']) ? $admin_payment_setting['benefit_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Benefit Secret Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('benefit_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('benefit_secret_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- CashFree -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-21">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse21"
                                                            aria-expanded="true" aria-controls="collapse21">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Cashfree') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_cashfree_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_cashfree_enabled"
                                                                        id="is_cashfree_enabled"
                                                                        {{ isset($admin_payment_setting['is_cashfree_enabled']) && $admin_payment_setting['is_cashfree_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_cashfree_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse21"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-21"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="cashfree_api_key"
                                                                            class="col-form-label">{{ __('Cashfree Key') }}</label>
                                                                        <input type="text" name="cashfree_api_key"
                                                                            id="cashfree_api_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['cashfree_api_key']) ? $admin_payment_setting['cashfree_api_key'] : '' }}"
                                                                            placeholder="{{ __('Cashfree Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('cashfree_api_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('cashfree_api_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="cashfree_secret_key"
                                                                            class="col-form-label">{{ __('Cashfree Secret Key') }}</label>
                                                                        <input type="text" name="cashfree_secret_key"
                                                                            id="cashfree_secret_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['cashfree_secret_key']) ? $admin_payment_setting['cashfree_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Cashfree Secret Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('cashfree_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('cashfree_secret_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Aamarpay -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-22">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse22"
                                                            aria-expanded="true" aria-controls="collapse22">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Aamarpay') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_aamarpay_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_aamarpay_enabled"
                                                                        id="is_aamarpay_enabled"
                                                                        {{ isset($admin_payment_setting['is_aamarpay_enabled']) && $admin_payment_setting['is_aamarpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_aamarpay_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse22"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-22"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aamarpay_store_id"
                                                                            class="col-form-label">{{ __('Store Id') }}</label>
                                                                        <input type="text" name="aamarpay_store_id"
                                                                            id="aamarpay_store_id" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['aamarpay_store_id']) ? $admin_payment_setting['aamarpay_store_id'] : '' }}"
                                                                            placeholder="{{ __('Store Id') }}">
                                                                    </div>
                                                                    @if ($errors->has('aamarpay_store_id'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('aamarpay_store_id') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aamarpay_signature_key"
                                                                            class="col-form-label">{{ __('Signature Key') }}</label>
                                                                        <input type="text" name="aamarpay_signature_key"
                                                                            id="aamarpay_signature_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['aamarpay_signature_key']) ? $admin_payment_setting['aamarpay_signature_key'] : '' }}"
                                                                            placeholder="{{ __('Signature Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('aamarpay_signature_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('aamarpay_signature_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aamarpay_description"
                                                                            class="col-form-label">{{ __('Description') }}</label>
                                                                        <input type="text" name="aamarpay_description"
                                                                            id="aamarpay_description" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['aamarpay_description']) ? $admin_payment_setting['aamarpay_description'] : '' }}"
                                                                            placeholder="{{ __('Description') }}">
                                                                    </div>
                                                                    @if ($errors->has('aamarpay_description'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('aamarpay_description') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Paytr -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-23">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse23"
                                                            aria-expanded="true" aria-controls="collapse23">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('PayTR') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_paytr_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_paytr_enabled"
                                                                        id="is_paytr_enabled"
                                                                        {{ isset($admin_payment_setting['is_paytr_enabled']) && $admin_payment_setting['is_paytr_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_paytr_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse23"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-23"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paytr_merchant_id"
                                                                            class="col-form-label">{{ __('Merchant Id') }}</label>
                                                                        <input type="text" name="paytr_merchant_id"
                                                                            id="paytr_merchant_id" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paytr_merchant_id']) ? $admin_payment_setting['paytr_merchant_id'] : '' }}"
                                                                            placeholder="{{ __('Merchant Id') }}">
                                                                    </div>
                                                                    @if ($errors->has('paytr_merchant_id'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paytr_merchant_id') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paytr_merchant_key"
                                                                            class="col-form-label">{{ __('Merchant Key') }}</label>
                                                                        <input type="text" name="paytr_merchant_key"
                                                                            id="paytr_merchant_key" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paytr_merchant_key']) ? $admin_payment_setting['paytr_merchant_key'] : '' }}"
                                                                            placeholder="{{ __('Merchant Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('paytr_merchant_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paytr_merchant_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paytr_merchant_salt"
                                                                            class="col-form-label">{{ __('Merchant Salt') }}</label>
                                                                        <input type="text" name="paytr_merchant_salt"
                                                                            id="paytr_merchant_salt" class="form-control"
                                                                            value="{{ isset($admin_payment_setting['paytr_merchant_salt']) ? $admin_payment_setting['paytr_merchant_salt'] : '' }}"
                                                                            placeholder="{{ __('Description') }}">
                                                                    </div>
                                                                    @if ($errors->has('paytr_merchant_salt'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paytr_merchant_salt') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Yookassa --}}
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-24">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse24"
                                                            aria-expanded="true" aria-controls="collapse24">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Yookassa') }}
                                                            </span>

                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1">
                                                                    <input type="hidden" name="is_yookassa_enabled"
                                                                        value="off">
                                                                    <input type="checkbox"
                                                                        class="form-check-input input-primary"
                                                                        name="is_yookassa_enabled" id="is_yookassa_enabled"
                                                                        {{ isset($admin_payment_setting['is_yookassa_enabled']) && $admin_payment_setting['is_yookassa_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="customswitchv1-2"></label>
                                                                </div>
                                                            </div>

                                                        </button>
                                                    </h2>

                                                    <div id="collapse24"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-24"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="yookassa_shop_id"
                                                                            class="form-label">{{ __('Shop ID Key') }}</label>
                                                                        <input type="text" name="yookassa_shop_id"
                                                                            id="yookassa_shop_id" class="form-control"
                                                                            value="{{ !isset($admin_payment_setting['yookassa_shop_id']) || is_null($admin_payment_setting['yookassa_shop_id']) ? '' : $admin_payment_setting['yookassa_shop_id'] }}"
                                                                            placeholder="{{ __('Shop ID Key') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="yookassa_secret"
                                                                            class="form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="yookassa_secret"
                                                                            id="yookassa_secret" class="form-control"
                                                                            value="{{ !isset($admin_payment_setting['yookassa_secret']) || is_null($admin_payment_setting['yookassa_secret']) ? '' : $admin_payment_setting['yookassa_secret'] }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Midtrans --}}
                                                <div class="accordion-item card shadow-none">
                                                    <h2 class="accordion-header" id="heading-2-25">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse25"
                                                            aria-expanded="true" aria-controls="collapse25">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Midtrans') }}
                                                            </span>

                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1">
                                                                    <input type="hidden" name="is_midtrans_enabled"
                                                                        value="off">
                                                                    <input type="checkbox"
                                                                        class="form-check-input input-primary"
                                                                        name="is_midtrans_enabled" id="is_midtrans_enabled"
                                                                        {{ isset($admin_payment_setting['is_midtrans_enabled']) && $admin_payment_setting['is_midtrans_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="customswitchv1-2"></label>
                                                                </div>
                                                            </div>

                                                        </button>
                                                    </h2>

                                                    <div id="collapse25" class="accordion-collapse collapse"aria-labelledby="heading-2-25"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="midtrans-label col-form-label"
                                                                        for="midtrans_mode">{{ __('Midtrans Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($admin_payment_setting['midtrans_mode']) && $admin_payment_setting['midtrans_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="midtrans_mode" value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($admin_payment_setting['midtrans_mode']) && $admin_payment_setting['midtrans_mode'] == '') || (isset($admin_payment_setting['midtrans_mode']) && $admin_payment_setting['midtrans_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>{{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($admin_payment_setting['midtrans_mode']) && $admin_payment_setting['midtrans_mode'] == 'live' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="midtrans_mode" value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($admin_payment_setting['midtrans_mode']) && $admin_payment_setting['midtrans_mode'] == 'live' ? 'checked="checked"' : '' }}>{{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="midtrans_secret"
                                                                            class="form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="midtrans_secret"
                                                                            id="midtrans_secret" class="form-control"
                                                                            value="{{ !isset($admin_payment_setting['midtrans_secret']) || is_null($admin_payment_setting['midtrans_secret']) ? '' : $admin_payment_setting['midtrans_secret'] }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Xendit --}}
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-26">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse26"
                                                            aria-expanded="true" aria-controls="collapse26">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Xendit') }}
                                                            </span>

                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1">
                                                                    <input type="hidden" name="is_xendit_enabled"
                                                                        value="off">
                                                                    <input type="checkbox"
                                                                        class="form-check-input input-primary"
                                                                        name="is_xendit_enabled" id="is_xendit_enabled"
                                                                        {{ isset($admin_payment_setting['is_xendit_enabled']) && $admin_payment_setting['is_xendit_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="customswitchv1-2"></label>
                                                                </div>
                                                            </div>

                                                        </button>
                                                    </h2>

                                                    <div id="collapse26" class="accordion-collapse collapse"aria-labelledby="heading-2-26"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="xendit_api"
                                                                            class="form-label">{{ __('API Key') }}</label>
                                                                        <input type="text" name="xendit_api"
                                                                            id="xendit_api" class="form-control"
                                                                            value="{{ !isset($admin_payment_setting['xendit_api']) || is_null($admin_payment_setting['xendit_api']) ? '' : $admin_payment_setting['xendit_api'] }}"
                                                                            placeholder="{{ __('API Key') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="xendit_token"
                                                                            class="form-label">{{ __('Token') }}</label>
                                                                        <input type="text" name="xendit_token"
                                                                            id="xendit_token" class="form-control"
                                                                            value="{{ !isset($admin_payment_setting['xendit_token']) || is_null($admin_payment_setting['xendit_token']) ? '' : $admin_payment_setting['xendit_token'] }}"
                                                                            placeholder="{{ __('Token') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    {{ Form::submit(__('Save Changes'), ['class' => 'btn btn-primary']) }}
                                </div>
                                {{ Form::close() }}
                            </div>

                            <div id="email_setting" class="card">
                                <div class="card-header">
                                    <h5 class="mb-2">{{ __('Email Settings') }}</h5>
                                </div>
                                {{ Form::open(['route' => 'email.setting', 'method' => 'post']) }}
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_driver', __('Mail Driver'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_driver', isset($settings['mail_driver']) ? $settings['mail_driver'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Driver')]) }}
                                            @error('mail_driver')
                                                <span class="invalid-mail_driver" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_host', __('Mail Host'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_host', isset($settings['mail_host']) ? $settings['mail_host'] : '', ['class' => 'form-control ', 'placeholder' => __('Enter Mail Host')]) }}
                                            @error('mail_host')
                                                <span class="invalid-mail_driver" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_port', __('Mail Port'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_port', isset($settings['mail_port']) ? $settings['mail_port'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Port')]) }}
                                            @error('mail_port')
                                                <span class="invalid-mail_port" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_username', __('Mail Username'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_username', isset($settings['mail_username']) ? $settings['mail_username'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Username')]) }}
                                            @error('mail_username')
                                                <span class="invalid-mail_username" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_password', __('Mail Password', ['class' => 'form-label'])) }}
                                            {{ Form::text('mail_password', isset($settings['mail_password']) ? $settings['mail_password'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Password')]) }}
                                            @error('mail_password')
                                                <span class="invalid-mail_password" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_encryption', __('Mail Encryption'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_encryption', isset($settings['mail_encryption']) ? $settings['mail_encryption'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail Encryption')]) }}
                                            @error('mail_encryption')
                                                <span class="invalid-mail_encryption" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_from_address', __('Mail From Address'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_from_address', isset($settings['mail_from_address']) ? $settings['mail_from_address'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail From Address')]) }}
                                            @error('mail_from_address')
                                                <span class="invalid-mail_from_address" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_from_name', __('Mail From Name'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_from_name', isset($settings['mail_driver']) ? $settings['mail_driver'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Mail From Name')]) }}
                                            @error('mail_from_name')
                                                <span class="invalid-mail_from_name" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row card-footer reverse-rtl-row">
                                    <div class="form-group col-md-6">
                                        <a href="#" data-url="{{ route('test.mail') }}"
                                            data-title="{{ __('Send Test Mail') }}" class="btn btn-primary send_email">
                                            {{ __('Send Test Mail') }}
                                        </a>
                                    </div>
                                    <div class="form-group col-md-6 text-end">
                                        {{ Form::submit(__('Save Changes'), ['class' => 'btn btn-primary']) }}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>

                            <div id="recaptcha-settings" class="card">
                                <form method="POST" action="{{ route('recaptcha.settings.store') }}"
                                    accept-charset="UTF-8">
                                    @csrf
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5 class="mb-2">{{ __('ReCaptcha Settings') }}</h5>
                                                <a href="https://phppot.com/php/how-to-get-google-recaptcha-site-and-secret-key/"
                                                    target="_blank" class="text-blue">
                                                    <small>({{ __('How to Get Google reCaptcha Site and Secret key') }})</small>
                                                </a>
                                            </div>
                                            <div class="col switch-width text-end">
                                                <div class="form-group mb-0">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" data-toggle="switchbutton"
                                                            data-onstyle="primary" class="" name="recaptcha_module"
                                                            id="recaptcha_module" value="yes"
                                                            {{ $settings['recaptcha_module'] == 'yes' ? 'checked="checked"' : '' }}>
                                                        <label class="custom-control-label" for="recaptcha_module"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                <label for="google_recaptcha_key"
                                                    class="form-label">{{ __('Google Recaptcha Key') }}</label>
                                                <input class="form-control"
                                                    placeholder="{{ __('Enter Google Recaptcha Key') }}"
                                                    name="google_recaptcha_key" type="text"
                                                    value="{{ $settings['google_recaptcha_key'] }}" id="google_recaptcha_key">
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                <label for="google_recaptcha_secret"
                                                    class="form-label">{{ __('Google Recaptcha Secret') }}</label>
                                                <input class="form-control"
                                                    placeholder="{{ __('Enter Google Recaptcha Secret') }}"
                                                    name="google_recaptcha_secret" type="text"
                                                    value="{{ $settings['google_recaptcha_secret'] }}" id="google_recaptcha_secret">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="col-lg-12  text-end">
                                            <input type="submit" value="{{ __('Save Changes') }}"
                                                class="btn btn-primary">
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div id="storage_settings" class="card mb-3">
                                {{ Form::open(['route' => 'storage.setting.store', 'enctype' => 'multipart/form-data']) }}
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-lg-10 col-md-10 col-sm-10">
                                            <h5 class="">{{ __('Storage Settings') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="pe-2">
                                            <input type="radio" class="btn-check" name="storage_setting"
                                                id="local-outlined" autocomplete="off"
                                                {{ $settings['storage_setting'] == 'local' ? 'checked' : '' }}
                                                value="local" checked>
                                            <label class="btn btn-outline-primary"
                                                for="local-outlined">{{ __('Local') }}</label>
                                        </div>
                                        <div class="pe-2">
                                            <input type="radio" class="btn-check" name="storage_setting"
                                                id="s3-outlined" autocomplete="off"
                                                {{ $settings['storage_setting'] == 's3' ? 'checked' : '' }} value="s3">
                                            <label class="btn btn-outline-primary" for="s3-outlined">
                                                {{ __('AWS S3') }}</label>
                                        </div>

                                        <div class="pe-2">
                                            <input type="radio" class="btn-check" name="storage_setting"
                                                id="wasabi-outlined" autocomplete="off"
                                                {{ $settings['storage_setting'] == 'wasabi' ? 'checked' : '' }}
                                                value="wasabi">
                                            <label class="btn btn-outline-primary"
                                                for="wasabi-outlined">{{ __('Wasabi') }}</label>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <div
                                            class="local-setting row {{ $settings['storage_setting'] == 'local' ? ' ' : 'd-none' }}">
                                            <div class="col-lg-6 col-md-11 col-sm-12">
                                                {{ Form::label('local_storage_validation', __('Only Upload Files'), ['class' => ' form-label']) }}
                                                <select name="local_storage_validation[]" class="form-control"
                                                    name="choices-multiple-remove-button"
                                                    id="choices-multiple-remove-button" placeholder="This is a placeholder"
                                                    multiple>
                                                    @foreach ($file_type as $f)
                                                        <option @if (in_array($f, $local_storage_validations)) selected @endif>
                                                            {{ $f }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                        for="local_storage_max_upload_size">{{ __('Max upload size ( In KB)') }}</label>
                                                    <input type="number" name="local_storage_max_upload_size"
                                                        class="form-control"
                                                        value="{{ !isset($settings['local_storage_max_upload_size']) || is_null($settings['local_storage_max_upload_size']) ? '' : $settings['local_storage_max_upload_size'] }}"
                                                        placeholder="{{ __('Max upload size') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="s3-setting row {{ $settings['storage_setting'] == 's3' ? ' ' : 'd-none' }}">
                                            <div class=" row ">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="s3_key">{{ __('S3 Key') }}</label>
                                                        <input type="text" name="s3_key" class="form-control"
                                                            value="{{ !isset($settings['s3_key']) || is_null($settings['s3_key']) ? '' : $settings['s3_key'] }}"
                                                            placeholder="{{ __('S3 Key') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="s3_secret">{{ __('S3 Secret') }}</label>
                                                        <input type="text" name="s3_secret" class="form-control"
                                                            value="{{ !isset($settings['s3_secret']) || is_null($settings['s3_secret']) ? '' : $settings['s3_secret'] }}"
                                                            placeholder="{{ __('S3 Secret') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="s3_region">{{ __('S3 Region') }}</label>
                                                        <input type="text" name="s3_region" class="form-control"
                                                            value="{{ !isset($settings['s3_region']) || is_null($settings['s3_region']) ? '' : $settings['s3_region'] }}"
                                                            placeholder="{{ __('S3 Region') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="s3_bucket">{{ __('S3 Bucket') }}</label>
                                                        <input type="text" name="s3_bucket" class="form-control"
                                                            value="{{ !isset($settings['s3_bucket']) || is_null($settings['s3_bucket']) ? '' : $settings['s3_bucket'] }}"
                                                            placeholder="{{ __('S3 Bucket') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="s3_url">{{ __('S3 URL') }}</label>
                                                        <input type="text" name="s3_url" class="form-control"
                                                            value="{{ !isset($settings['s3_url']) || is_null($settings['s3_url']) ? '' : $settings['s3_url'] }}"
                                                            placeholder="{{ __('S3 URL') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="s3_endpoint">{{ __('S3 Endpoint') }}</label>
                                                        <input type="text" name="s3_endpoint" class="form-control"
                                                            value="{{ !isset($settings['s3_endpoint']) || is_null($settings['s3_endpoint']) ? '' : $settings['s3_endpoint'] }}"
                                                            placeholder="{{ __('S3 Bucket') }}">
                                                    </div>
                                                </div>
                                                <div class="form-group col-8 switch-width">
                                                    {{ Form::label('s3_storage_validation', __('Only Upload Files'), ['class' => ' form-label']) }}
                                                    <select name="s3_storage_validation[]" class="form-control"
                                                        name="choices-multiple-remove-button"
                                                        id="choices-multiple-remove-button1"
                                                        placeholder="This is a placeholder" multiple>
                                                        @foreach ($file_type as $f)
                                                            <option @if (in_array($f, $s3_storage_validations)) selected @endif>
                                                                {{ $f }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="s3_max_upload_size">{{ __('Max upload size (In KB)') }}</label>
                                                        <input type="number" name="s3_max_upload_size"
                                                            class="form-control"
                                                            value="{{ !isset($settings['s3_max_upload_size']) || is_null($settings['s3_max_upload_size']) ? '' : $settings['s3_max_upload_size'] }}"
                                                            placeholder="{{ __('Max upload size') }}">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div
                                            class="wasabi-setting row {{ $settings['storage_setting'] == 'wasabi' ? ' ' : 'd-none' }}">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="s3_key">{{ __('Wasabi Key') }}</label>
                                                        <input type="text" name="wasabi_key" class="form-control"
                                                            value="{{ !isset($settings['wasabi_key']) || is_null($settings['wasabi_key']) ? '' : $settings['wasabi_key'] }}"
                                                            placeholder="{{ __('Wasabi Key') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="s3_secret">{{ __('Wasabi Secret') }}</label>
                                                        <input type="text" name="wasabi_secret" class="form-control"
                                                            value="{{ !isset($settings['wasabi_secret']) || is_null($settings['wasabi_secret']) ? '' : $settings['wasabi_secret'] }}"
                                                            placeholder="{{ __('Wasabi Secret') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="s3_region">{{ __('Wasabi Region') }}</label>
                                                        <input type="text" name="wasabi_region" class="form-control"
                                                            value="{{ !isset($settings['wasabi_region']) || is_null($settings['wasabi_region']) ? '' : $settings['wasabi_region'] }}"
                                                            placeholder="{{ __('Wasabi Region') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="wasabi_bucket">{{ __('Wasabi Bucket') }}</label>
                                                        <input type="text" name="wasabi_bucket" class="form-control"
                                                            value="{{ !isset($settings['wasabi_bucket']) || is_null($settings['wasabi_bucket']) ? '' : $settings['wasabi_bucket'] }}"
                                                            placeholder="{{ __('Wasabi Bucket') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="wasabi_url">{{ __('Wasabi URL') }}</label>
                                                        <input type="text" name="wasabi_url" class="form-control"
                                                            value="{{ !isset($settings['wasabi_url']) || is_null($settings['wasabi_url']) ? '' : $settings['wasabi_url'] }}"
                                                            placeholder="{{ __('Wasabi URL') }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="wasabi_root">{{ __('Wasabi Root') }}</label>
                                                        <input type="text" name="wasabi_root" class="form-control"
                                                            value="{{ !isset($settings['wasabi_root']) || is_null($settings['wasabi_root']) ? '' : $settings['wasabi_root'] }}"
                                                            placeholder="{{ __('Wasabi Bucket') }}">
                                                    </div>
                                                </div>
                                                <div class="form-group col-8 switch-width">
                                                    {{ Form::label('wasabi_storage_validation', __('Only Upload Files'), ['class' => 'form-label']) }}

                                                    <select name="wasabi_storage_validation[]" class="form-control"
                                                        name="choices-multiple-remove-button"
                                                        id="choices-multiple-remove-button2"
                                                        placeholder="This is a placeholder" multiple>
                                                        @foreach ($file_type as $f)
                                                            <option @if (in_array($f, $wasabi_storage_validations)) selected @endif>
                                                                {{ $f }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                            for="wasabi_root">{{ __('Max upload size ( In KB)') }}</label>
                                                        <input type="number" name="wasabi_max_upload_size"
                                                            class="form-control"
                                                            value="{{ !isset($settings['wasabi_max_upload_size']) || is_null($settings['wasabi_max_upload_size']) ? '' : $settings['wasabi_max_upload_size'] }}"
                                                            placeholder="{{ __('Max upload size') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <input class="btn btn-print-invoice btn-primary m-r-10" type="submit"
                                        value="{{ __('Save Changes') }}">
                                </div>
                                {{ Form::close() }}
                            </div>

                            {{-- Cache setting --}}
                            <div id="cache_settings" class="card mb-3">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5 class="h6 md-0">{{ __('Cache Settings') }}</h5>
                                            <small class="text-secondary">This is a page meant for more advanced users, simply ignore it if you don't understand what cache is.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 form-group">
                                            <label for="Current cache size" class="form-label">{{__('Current cache size')}}</label>
                                            <div class="input-group">
                                                <input type="text" readonly value="{{ Utility::GetCacheSize() }}" class="form-control">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-transparent">{{__('MB')}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <a href = "{{ url('config-cache') }}" class="btn btn-m btn-primary m-r-10 ">{{ __('Clear Cache') }}</a>
                                </div>
                            </div>

                            <!--cookie consent Setting-->
                            <div class="card" id="cookie_settings">
                                {{Form::model($settings,array('route'=>'cookie.setting','method'=>'post'))}}
                                <div class="card-header flex-column flex-lg-row  d-flex align-items-lg-center gap-2 justify-content-between">
                                    <h5>{{ __('Cookie Settings') }}</h5>
                                    <div class="d-flex align-items-center">
                                        {{ Form::label('enable_cookie', __('Enable cookie'), ['class' => 'col-form-label p-0 fw-bold me-3']) }}
                                        <div class="custom-control custom-switch"  onclick="enablecookie()">
                                            <input type="checkbox" data-toggle="switchbutton" data-onstyle="primary" name="enable_cookie" class="form-check-input input-primary "
                                                id="enable_cookie" {{ $settings['enable_cookie'] == 'on' ? ' checked ' : '' }} >
                                            <label class="custom-control-label mb-1" for="enable_cookie"></label>
                                        </div>


                                    </div>
                                </div>
                                <div class="card-body cookieDiv {{ $settings['enable_cookie'] == 'off' ? 'disabledCookie ' : '' }}">
                                    <div class="row ">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch custom-switch-v1" id="cookie_log">
                                                <input type="checkbox" name="cookie_logging" class="form-check-input input-primary cookie_setting"
                                                    id="cookie_logging"  onclick="enableButton()" {{ $settings['cookie_logging'] == 'on' ? ' checked ' : '' }}>
                                                <label class="form-check-label" for="cookie_logging">{{__('Enable logging')}}</label>
                                            </div>
                                            <div class="form-group" >
                                                {{ Form::label('cookie_title', __('Cookie Title'), ['class' => 'col-form-label' ]) }}
                                                {{ Form::text('cookie_title', null, ['class' => 'form-control cookie_setting'] ) }}
                                            </div>
                                            <div class="form-group ">
                                                {{ Form::label('cookie_description', __('Cookie Description'), ['class' => ' form-label']) }}
                                                {!! Form::textarea('cookie_description', null, ['class' => 'form-control cookie_setting', 'rows' => '3']) !!}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-6 form-check form-switch custom-switch-v1 ">
                                                    <input type="checkbox" name="necessary_cookies" class="form-check-input input-primary"
                                                        id="necessary_cookies" checked onclick="return false">
                                                    <label class="form-check-label" for="necessary_cookies">{{__('Strictly necessary cookies')}}</label>
                                                </div>
                                                @if(Utility::getValByName('chatgpt_key'))
                                                    <div class="col-6 text-end align-items-center justify-content-between">
                                                        <a class="btn btn-primary btn-sm mx-1 text-white" data-size="lg" data-ajax-popup-over="true" data-url="{{ route('generate',['cookie']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                                                            <i class="fas fa-robot"></i> {{ __('Generate With AI') }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="form-group ">
                                                {{ Form::label('strictly_cookie_title', __(' Strictly Cookie Title'), ['class' => 'col-form-label']) }}
                                                {{ Form::text('strictly_cookie_title', null, ['class' => 'form-control cookie_setting']) }}
                                            </div>
                                            <div class="form-group ">
                                                {{ Form::label('strictly_cookie_description', __('Strictly Cookie Description'), ['class' => ' form-label']) }}
                                                {!! Form::textarea('strictly_cookie_description', null, ['class' => 'form-control cookie_setting ', 'rows' => '3']) !!}
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <h5>{{__('More Information')}}</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                {{ Form::label('more_information_description', __('Contact Us Description'), ['class' => 'col-form-label']) }}
                                                {{ Form::text('more_information_description', null, ['class' => 'form-control cookie_setting']) }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                {{ Form::label('contactus_url', __('Contact Us URL'), ['class' => 'col-form-label']) }}
                                                {{ Form::text('contactus_url', null, ['class' => 'form-control cookie_setting']) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <div class="modal-footer d-flex align-items-center gap-2 flex-sm-column flex-lg-row justify-content-between" >
                                        <div id="csv_file">
                                            @if(isset($settings['cookie_logging']) && $settings['cookie_logging'] == 'on')
                                            <label for="file" class="form-label">{{__('Download cookie accepted data')}}</label>
                                                <a href="{{ asset(Storage::url('uploads/sample')) . '/data.csv' }}" class="btn btn-primary mr-2 ">
                                                    <i class="ti ti-download"></i>
                                                </a>
                                                @endif
                                        </div>
                                        <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-primary">
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>

                            {{-- ChatGPT key settings --}}
                            <div class="card" id="chatgpt_settings">
                                {{Form::model($settings,array('route'=>'settings.chatgptkey','method'=>'post'))}}
                                    <div class="card-header">
                                        <h5>{{ __('Chat GPT Key Settings') }}</h5>
                                        <small>{{ __('Edit your key details') }}</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="form-group">
                                                {{ Form::text('chatgpt_key',isset($settings['chatgpt_key']) ? $settings['chatgpt_key'] : '',['class'=>'form-control','placeholder'=>__('Enter Chatgpt Key Here')]) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-end">
                                        <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                                    </div>
                                {{ Form::close() }}
                            </div>
                        @else

                            <div id="store_site_setting" class="card">
                                <div class="card-header">
                                    <h5 class="mb-2">{{ __('Brand Settings') }}</h5>
                                </div>
                                {{ Form::model($settings, ['route' => 'business.setting', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="small-title">{{ __('Dark Logo') }}</h5>
                                                </div>
                                                <div class="card-body setting-card setting-logo-box p-3">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="logo-content logo-set-bg  text-center py-2">
                                                                    <a href="{{$logo.(isset($logo_dark) && !empty($logo_dark)?$logo_dark:'logo-dark.png').'?'. time()}}" target="_blank">
                                                                        <img src="{{$logo.(isset($logo_dark) && !empty($logo_dark)?$logo_dark:'logo-dark.png').'?'. time()}}" id="blah2" class="big-logo">
                                                                    </a>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="choose-files mt-4">
                                                                <label for="logo_dark" class="form-label d-block">
                                                                    <div class=" bg-primary company_logo_update m-auto"> <i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                        <input type="file" name="logo_dark"
                                                                            id="logo_dark" class="form-control file"
                                                                            data-filename="company_logo_update"
                                                                            onchange="document.getElementById('blah2').src = window.URL.createObjectURL(this.files[0])">
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="small-title">{{ __('Light Logo') }}</h5>
                                                </div>
                                                <div class="card-body setting-card setting-logo-box p-3">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="logo-content logo-set-bg  text-center py-2">
                                                                <a href="{{$logo.(isset($logo_light) && !empty($logo_light)?$logo_light:'logo-light.png').'?'. time()}}" target="_blank">
                                                                    <img src="{{$logo.(isset($logo_light) && !empty($logo_light)?$logo_light:'logo-light.png').'?'. time()}}" id="blah3" class="big-logo img_setting">
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="choose-files mt-4">
                                                                <label for="logo_light" class="form-label d-block">
                                                                    <div class=" bg-primary m-auto"> <i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                        <input type="file" name="logo_light"
                                                                            id="logo_light" class="form-control file"
                                                                            data-filename="company_logo_update"
                                                                            onchange="document.getElementById('blah3').src = window.URL.createObjectURL(this.files[0])">
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 class="small-title">{{ __('Favicon') }}</h5>
                                                </div>
                                                <div class="card-body setting-card setting-logo-box p-3">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="logo-content logo-set-bg text-center py-2 mt-2">
                                                                <a href="{{$logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png').'?'. time()}}" target="_blank">
                                                                    <img src="{{$logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png').'?'. time()}}" id="blah4" width="50px" class="img_setting">
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="choose-files mt-4">
                                                                <label for="favicon" class="form-label d-block">
                                                                    <div class="bg-primary m-auto"> <i
                                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                        <input type="file" name="favicon"
                                                                            id="favicon" class="form-control file"
                                                                            data-filename="company_logo_update"
                                                                            onchange="document.getElementById('blah4').src = window.URL.createObjectURL(this.files[0])">
                                                                    </div>
                                                                    {{-- <input type="file" name="favicon" id="favicon" class="form-control file" data-filename="company_logo_update"> --}}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            {{ Form::label('title_text', __('Title Text')) }}
                                            {{ Form::text('title_text', null, ['class' => 'form-control', 'placeholder' => __('Title Text')]) }}
                                            @error('title_text')
                                                <span class="invalid-title_text" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('footer_text', __('Footer Text')) }}
                                            {{ Form::text('footer_text', null, ['class' => 'form-control', 'placeholder' => __('Footer Text')]) }}
                                            @error('footer_text')
                                                <span class="invalid-footer_text" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="site_date_format"
                                                class="form-control-label">{{ __('Date Format') }}</label>
                                            <select type="text" name="site_date_format" class="form-control selectric"
                                                id="site_date_format">
                                                <option value="M j, Y"
                                                    @if (@$settings['site_date_format'] == 'M j, Y') selected="selected" @endif>Jan 1,2015
                                                </option>
                                                <option value="d-m-Y"
                                                    @if (@$settings['site_date_format'] == 'd-m-Y') selected="selected" @endif>dd-mm-yyyy</option>
                                                <option value="m-d-Y"
                                                    @if (@$settings['site_date_format'] == 'm-d-Y') selected="selected" @endif>mm-dd-yyyy</option>
                                                <option value="Y-m-d"
                                                    @if (@$settings['site_date_format'] == 'Y-m-d') selected="selected" @endif>yyyy-mm-dd</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="site_time_format"
                                                class="form-control-label">{{ __('Time Format') }}</label>
                                            <select type="text" name="site_time_format" class="form-control selectric"
                                                id="site_time_format">
                                                <option value="g:i A"
                                                    @if (@$settings['site_time_format'] == 'g:i A') selected="selected" @endif>10:30 PM
                                                </option>
                                                <option value="g:i a"
                                                    @if (@$settings['site_time_format'] == 'g:i a') selected="selected" @endif>10:30 pm
                                                </option>
                                                <option value="H:i"
                                                    @if (@$settings['site_time_format'] == 'H:i') selected="selected" @endif>22:30
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('default_language', __('Default Language'), ['class' => 'form-control-label']) }}
                                            <div class="changeLanguage">
                                                <select name="default_language" id="default_language"
                                                    class="form-control form-select">
                                                    @foreach (Utility::languages() as $code => $language)
                                                        <option @if ($lang == $code) selected @endif
                                                            value="{{ $code }}">
                                                            {{ ucfirst($language) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{Form::label('timezone',__('Timezone'),array('class' => 'form-control-label'))}}
                                            <select type="text" name="timezone" class="form-control custom-select" id="timezone">
                                                <option value="">{{__('Select Timezone')}}</option>
                                                @foreach($timezones as $k=>$timezone)
                                                    <option value="{{$k}}" {{($settings['timezone']==$k)?'selected':''}}>{{$timezone}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col switch-width">
                                            <div class="form-group ml-2 mr-3 ">
                                                <label class="form-label">{{ __('Enable RTL') }}</label>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" data-toggle="switchbutton"
                                                        data-onstyle="primary" class="" name="SITE_RTL"
                                                        id="SITE_RTL"
                                                        {{ $settings['SITE_RTL'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label" for="SITE_RTL"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <h4 class="small-title">{{ __('Theme Customizer') }}</h4>
                                        <div class="setting-card setting-logo-box p-3">
                                            <div class="row">
                                                <div class="pct-body">
                                                    <div class="row">
                                                        <div class="col-lg-4 col-xl-4 col-md-4">
                                                            <h6 class="mt-2">
                                                                <i data-feather="credit-card"
                                                                    class="me-2"></i>{{ __('Primary color settings') }}
                                                            </h6>
                                                            <hr class="my-2" />
                                                            <div class="theme-color themes-color">
                                                                <a href="#!" class="{{($settings['color'] == 'theme-1') ? 'active_color' : ''}}" data-value="theme-1" onclick="check_theme('theme-1')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-1" style="display: none;">
                                                                <a href="#!" class="{{($settings['color'] == 'theme-2') ? 'active_color' : ''}} " data-value="theme-2" onclick="check_theme('theme-2')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-2" style="display: none;">
                                                                <a href="#!" class="{{($settings['color'] == 'theme-3') ? 'active_color' : ''}}" data-value="theme-3" onclick="check_theme('theme-3')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-3" style="display: none;">
                                                                <a href="#!" class="{{($settings['color'] == 'theme-4') ? 'active_color' : ''}}" data-value="theme-4" onclick="check_theme('theme-4')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-4" style="display: none;">
                                                                <a href="#!" class="{{($settings['color'] == 'theme-5') ? 'active_color' : ''}}" data-value="theme-5" onclick="check_theme('theme-5')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-5" style="display: none;">
                                                                <br>
                                                                <a href="#!" class="{{($settings['color'] == 'theme-6') ? 'active_color' : ''}}" data-value="theme-6" onclick="check_theme('theme-6')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-6" style="display: none;">
                                                                <a href="#!" class="{{($settings['color'] == 'theme-7') ? 'active_color' : ''}}" data-value="theme-7" onclick="check_theme('theme-7')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-7" style="display: none;">
                                                                <a href="#!" class="{{($settings['color'] == 'theme-8') ? 'active_color' : ''}}" data-value="theme-8" onclick="check_theme('theme-8')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-8" style="display: none;">
                                                                <a href="#!" class="{{($settings['color'] == 'theme-9') ? 'active_color' : ''}}" data-value="theme-9" onclick="check_theme('theme-9')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-9" style="display: none;">
                                                                <a href="#!" class="{{($settings['color'] == 'theme-10') ? 'active_color' : ''}}" data-value="theme-10" onclick="check_theme('theme-10')"></a>
                                                                <input type="radio" class="theme_color" name="color" value="theme-10" style="display: none;">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4 col-xl-4 col-md-4">
                                                            <h6 class="mt-2">
                                                                <i data-feather="layout"
                                                                    class="me-2"></i>{{ __('Sidebar settings') }}
                                                            </h6>
                                                            <hr class="my-2" />
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="cust-theme-bg" name="cust_theme_bg"
                                                                    {{ Utility::getValByName('cust_theme_bg') == 'on' ? 'checked' : '' }} />
                                                                <label class="form-check-label f-w-600 pl-1"
                                                                    for="cust-theme-bg">{{ __('Transparent layout') }}</label>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4 col-xl-4 col-md-4">
                                                            <h6 class="mt-2">
                                                                <i data-feather="sun"
                                                                    class="me-2"></i>{{ __('Layout settings') }}
                                                            </h6>
                                                            <hr class="my-2" />
                                                            <div class="form-check form-switch mt-2">
                                                                <input type="checkbox" class="form-check-input"
                                                                    id="cust-darklayout" name="cust_darklayout"
                                                                    {{ Utility::getValByName('cust_darklayout') == 'on' ? 'checked' : '' }} />
                                                                <label class="form-check-label f-w-600 pl-1"
                                                                    for="cust-darklayout">{{ __('Dark Layout') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-end">
                                        {{ Form::submit(__('Save Changes'), ['class' => 'btn btn-primary']) }}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>

                            <div id="store_setting" class="card">
                                <div class="card-header">
                                    <h5 class="mb-2">{{ __('Store Settings') }}</h5>
                                </div>
                                {{ Form::model($store_settings, ['route' => ['settings.store', $store_settings['id']], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                <div class="card-body pt-0">
                                    <div class=" setting-card">
                                        <div class="row mt-2">
                                            <div class="col-sm-4">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="small-title">{{ __('Logo') }}</h5>
                                                    </div>
                                                    <div class="card-body setting-card setting-logo-box p-3">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="logo-content logo-set-bg  text-center py-2">
                                                                    @if (!empty($store_settings['logo']))
                                                                        <a href="{{ $s_logo . (isset($store_settings['logo']) && !empty($store_settings['logo']) ? $store_settings['logo'] : 'logo.png').'?'. time() }}"
                                                                            target="_blank">
                                                                            <img src="{{ $s_logo . (isset($store_settings['logo']) && !empty($store_settings['logo']) ? $store_settings['logo'] : 'logo.png').'?'. time() }}"
                                                                                id="blah5" class="big-logo img_setting">
                                                                        </a>
                                                                    @else
                                                                        <a href="{{ $s_logo . 'logo.png' }}"
                                                                            target="_blank">
                                                                            <img src="{{ $s_logo . 'logo.png' }}"
                                                                                id="blah5" class="big-logo img_setting">
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="choose-files mt-4">
                                                                    <label for="logo" class="form-label d-block">
                                                                        <div class=" bg-primary m-auto company_logo_update">
                                                                            <i
                                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                            <input type="file" name="logo"
                                                                                id="logo" class="form-control file"
                                                                                data-filename="company_logo_update"
                                                                                onchange="document.getElementById('blah5').src = window.URL.createObjectURL(this.files[0])">
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="small-title">{{ __('Invoice Logo') }}</h5>
                                                    </div>
                                                    <div class="card-body setting-card setting-logo-box p-3">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="logo-content logo-set-bg  text-center py-2">
                                                                    <a href="{{ $s_logo . (isset($store_settings['invoice_logo']) && !empty($store_settings['invoice_logo']) ? $store_settings['invoice_logo'] : 'invoice_logo.png').'?'. time() }}"
                                                                        target="_blank">
                                                                        <img src="{{ $s_logo . (isset($store_settings['invoice_logo']) && !empty($store_settings['invoice_logo']) ? $store_settings['invoice_logo'] : 'invoice_logo.png').'?'. time() }}"
                                                                            id="blah6" class="big-logo img_setting">
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="choose-files mt-4">
                                                                    <label for="invoice_logo" class="form-label d-block">
                                                                        <div class=" bg-primary m-auto company_logo_update">
                                                                            <i
                                                                                class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                                            <input type="file" name="invoice_logo"
                                                                                id="invoice_logo" class="form-control file"
                                                                                data-filename="company_logo_update"
                                                                                onchange="document.getElementById('blah6').src = window.URL.createObjectURL(this.files[0])">
                                                                        </div>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                {{ Form::label('store_name', __('Store Name'), ['class' => 'form-label']) }}
                                                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Store Name')]) !!}
                                                @error('store_name')
                                                    <span class="invalid-store_name" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-6">
                                                {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
                                                {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Email')]) }}
                                                @error('email')
                                                    <span class="invalid-email" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            @if ($plan->enable_custdomain == 'on')
                                                <div class="col-md-6 py-4">
                                                    <div class="radio-button-group mts">
                                                        <div class="item">
                                                            <label
                                                                class="btn btn-outline-primary {{ $store_settings['enable_storelink'] == 'on' ? 'active' : '' }}">
                                                                <input type="radio" class="domain_click  radio-button"
                                                                    name="enable_domain" value="enable_storelink"
                                                                    id="enable_storelink"
                                                                    {{ $store_settings['enable_storelink'] == 'on' ? 'checked' : '' }}>
                                                                {{ __('Store Link') }}
                                                            </label>
                                                        </div>
                                                        <div class="item">
                                                            <label
                                                                class="btn btn-outline-primary {{ $store_settings['enable_domain'] == 'on' ? 'active' : '' }}">
                                                                <input type="radio" class="domain_click radio-button"
                                                                    name="enable_domain" value="enable_domain"
                                                                    id="enable_domain"
                                                                    {{ $store_settings['enable_domain'] == 'on' ? 'checked' : '' }}>
                                                                {{ __('Domain') }}
                                                            </label>
                                                        </div>
                                                        @if ($plan->enable_custsubdomain == 'on')
                                                            <div class="item">
                                                                <label
                                                                    class="btn btn-outline-primary {{ $store_settings['enable_subdomain'] == 'on' ? 'active' : '' }}">
                                                                    <input type="radio" class="domain_click radio-button"
                                                                        name="enable_domain" value="enable_subdomain"
                                                                        id="enable_subdomain"
                                                                        {{ $store_settings['enable_subdomain'] == 'on' ? 'checked' : '' }}>
                                                                    {{ __('Sub Domain') }}
                                                                </label>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="text-sm mt-2" id="domainnote" style="display: none">
                                                        {{ __('Note : Before add custom domain, your domain A record is pointing to our server IP :') }}{{ $serverIp }}
                                                        <br>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6" id="StoreLink"
                                                    style="{{ $store_settings['enable_storelink'] == 'on' ? 'display: block' : 'display: none' }}">
                                                    {{ Form::label('store_link', __('Store Link'), ['class' => 'form-label']) }}
                                                    <div class="input-group">
                                                        <input type="text" value="{{ $store_settings['store_url'] }}"
                                                            id="myInput" class="form-control d-inline-block"
                                                            aria-label="Recipient's username"
                                                            aria-describedby="button-addon2" readonly>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-primary" type="button"
                                                                onclick="myFunction()" id="button-addon2"><i
                                                                    class="far fa-copy"></i>
                                                                {{ __('Copy Link') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-6 domain"
                                                    style="{{ $store_settings['enable_domain'] == 'on' ? 'display:block' : 'display:none' }}">
                                                    {{ Form::label('store_domain', __('Custom Domain'), ['class' => 'form-label']) }}
                                                    {{ Form::text('domains', $store_settings['domains'], ['class' => 'form-control', 'placeholder' => __('xyz.com')]) }}
                                                </div>
                                                @if ($plan->enable_custsubdomain == 'on')
                                                    <div class="form-group col-md-6 sundomain"
                                                        style="{{ $store_settings['enable_subdomain'] == 'on' ? 'display:block' : 'display:none' }}">
                                                        {{ Form::label('store_subdomain', __('Sub Domain'), ['class' => 'form-label']) }}
                                                        <div class="input-group">
                                                            {{ Form::text('subdomain', $store_settings['slug'], ['class' => 'form-control', 'placeholder' => __('Enter Domain'), 'readonly']) }}
                                                            <div class="input-group-append">
                                                                <span class="input-group-text"
                                                                    id="basic-addon2">.{{ $subdomain_name }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="form-group col-md-6" id="StoreLink">
                                                    {{ Form::label('store_link', __('Store Link'), ['class' => 'form-label']) }}
                                                    <div class="input-group">
                                                        <input type="text" value="{{ $store_settings['store_url'] }}"
                                                            id="myInput" class="form-control d-inline-block"
                                                            aria-label="Recipient's username"
                                                            aria-describedby="button-addon2" readonly>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-primary" type="button"
                                                                onclick="myFunction()" id="button-addon2"><i
                                                                    class="far fa-copy"></i>
                                                                {{ __('Copy Link') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="form-group col-md-4">
                                                {{ Form::label('tagline', __('Tagline'), ['class' => 'form-label']) }}
                                                {{ Form::text('tagline', null, ['class' => 'form-control', 'placeholder' => __('Tagline')]) }}
                                                @error('tagline')
                                                    <span class="invalid-tagline" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-4">
                                                {{ Form::label('address', __('Address'), ['class' => 'form-label']) }}
                                                {{ Form::text('address', null, ['class' => 'form-control', 'placeholder' => __('Address')]) }}
                                                @error('address')
                                                    <span class="invalid-address" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-4">
                                                {{ Form::label('city', __('City'), ['class' => 'form-label']) }}
                                                {{ Form::text('city', null, ['class' => 'form-control', 'placeholder' => __('City')]) }}
                                                @error('city')
                                                    <span class="invalid-city" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-4">
                                                {{ Form::label('state', __('State'), ['class' => 'form-label']) }}
                                                {{ Form::text('state', null, ['class' => 'form-control', 'placeholder' => __('State')]) }}
                                                @error('state')
                                                    <span class="invalid-state" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-4">
                                                {{ Form::label('zipcode', __('Zipcode'), ['class' => 'form-label']) }}
                                                {{ Form::text('zipcode', null, ['class' => 'form-control', 'placeholder' => __('Zipcode')]) }}
                                                @error('zipcode')
                                                    <span class="invalid-zipcode" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-4">
                                                {{ Form::label('country', __('Country'), ['class' => 'form-label']) }}
                                                {{ Form::text('country', null, ['class' => 'form-control', 'placeholder' => __('Country')]) }}
                                                @error('country')
                                                    <span class="invalid-country" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-3">
                                                {{ Form::label('store_default_language', __('Store Default Language'), ['class' => 'form-label']) }}
                                                <div class="changeLanguage">
                                                    <select name="store_default_language" id="store_default_language"
                                                        class="form-control form-select">
                                                        @foreach (\App\Models\Utility::languages() as $code => $language)
                                                            <option @if ($store_lang == $code) selected @endif
                                                                value="{{ $code }}">
                                                                {{ ucfirst($language) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-3">
                                                <label class="form-check-label" for="enable_rating"></label>
                                                <div class="form-check form-check form-switch custom-control-inline mt-3">
                                                    <input type="checkbox" class="form-check-input" role="switch"
                                                        name="enable_rating" id="enable_rating"
                                                        {{ $store_settings['enable_rating'] == 'on' ? 'checked=checked' : '' }}>
                                                    {{ Form::label('enable_rating', __('Rating Display'), ['class' => 'form-check-label mb-3']) }}
                                                </div>
                                            </div>
                                            @if ($plan->blog == 'on')
                                                <div class="form-group col-md-3">
                                                    <label class="form-check-label" for="blog_enable"></label>
                                                    <div class="form-check form-check form-switch custom-control-inline mt-3">
                                                        <input type="checkbox" class="form-check-input" role="switch"
                                                            name="blog_enable" id="blog_enable"
                                                            {{ $store_settings['blog_enable'] == 'on' ? 'checked=checked' : '' }}>
                                                        {{ Form::label('blog_enable', __('Blog Menu Dispay'), ['class' => 'form-check-label mb-3']) }}
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <i class="fab fa-google" aria-hidden="true"></i>
                                                    {{ Form::label('google_analytic', __('Google Analytic'), ['class' => 'form-label']) }}
                                                    {{ Form::text('google_analytic', null, ['class' => 'form-control', 'placeholder' => 'UA-XXXXXXXXX-X']) }}
                                                    @error('google_analytic')
                                                        <span class="invalid-google_analytic" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{ Form::label('storejs', __('Store Custom JS'), ['class' => 'form-label']) }}
                                                    {{ Form::textarea('storejs', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Store Custom JS')]) }}
                                                    @error('storejs')
                                                        <span class="invalid-storejs" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                                    {{ Form::label('facebook_pixel_code', __('Facebook Pixel'), ['class' => 'form-label']) }}
                                                    {{ Form::text('fbpixel_code', null, ['class' => 'form-control', 'placeholder' => 'UA-0000000-0']) }}
                                                    @error('facebook_pixel_code')
                                                        <span class="invalid-google_analytic" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{ Form::label('zoom_account_id', __('Zoom Account ID'), ['class' => 'form-label']) }}
                                                    {{ Form::text('zoom_account_id', isset($store_settings['zoom_account_id']) ? $store_settings['zoom_account_id'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Zoom API Key')]) }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{ Form::label('zoom_client_id', __('Zoom Client ID'), ['class' => 'form-label']) }}
                                                    {{ Form::text('zoom_client_id', isset($store_settings['zoom_client_id']) ? $store_settings['zoom_client_id'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Zoom API Key')]) }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{ Form::label('zoom_client_secret', __('Zoom Client Secret Key'), ['class' => 'form-label']) }}
                                                    {{ Form::text('zoom_client_secret', isset($store_settings['zoom_client_secret']) ? $store_settings['zoom_client_secret'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Zoom API Secret')]) }}
                                                </div>
                                            </div>

                                            <div class="col-12 pt-4">
                                                <h5 class="h6 mb-0">{{ __('Footer') }}</h5>
                                                <hr class="my-4">
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <i class="fas    fa-copyright" aria-hidden="true"></i>
                                                    {{ Form::label('footer_note', __('Footer'), ['class' => 'form-label']) }}
                                                    {{ Form::text('footer_note', null, ['class' => 'form-control', 'placeholder' => __('Footer Note')]) }}
                                                    @error('footer_note')
                                                        <span class="invalid-footer_note" role="alert">
                                                            <strong class="text-danger">{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="col-sm-12 px-2">
                                        <div class="text-end">
                                            @can('delete store')
                                                <button type="button" class="btn bs-pass-para btn-secondary btn-light"
                                                    data-title="{{ __('Delete') }}"
                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                    data-confirm-yes="delete-form-{{ $store_settings->id }}">
                                                    <span class="text-black">{{ __('Delete Store') }}</span>
                                                </button>
                                            @endcan
                                            {{ Form::submit(__('Save Changes'), ['class' => 'btn btn-primary mx-2']) }}
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                                {!! Form::open([
                                    'method' => 'DELETE',
                                    'route' => ['ownerstore.destroy', $store_settings->id],
                                    'id' => 'delete-form-' . $store_settings->id,
                                ]) !!}
                                {!! Form::close() !!}
                            </div>

                            <div class="card" id="store_payment-setting">
                                <div class="card-header">
                                    <h5>{{ __('Store Payment Settings') }}</h5>
                                    <small
                                        class="text-secondary">{{ __('These details will be used to purchase course payments. Each course will have a payment button based on the below configuration.') }}</small>
                                </div>
                                {{ Form::open(['route' => ['owner.payment.setting', $store_settings->slug], 'method' => 'post']) }}
                                <div class="card-body">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                    <label class="col-form-label">{{ __('Currency') }} *</label>
                                                    <input type="text" name="currency" class="form-control"
                                                        id="currency" value="{{ $store_settings['currency_code'] }}"
                                                        required>
                                                    <small class="text-xs">
                                                        {{ __('Note: Add currency code as per three-letter ISO code') }}.
                                                        <a href="https://stripe.com/docs/currencies"
                                                            target="_blank">{{ __(' You can find out how to do that here.') }}</a>
                                                    </small>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                    <label for="currency_symbol"
                                                        class="col-form-label">{{ __('Currency Symbol') }}</label>
                                                    <input type="text" name="currency_symbol" class="form-control"
                                                        id="currency_symbol" value="{{ $store_settings['currency'] }}"
                                                        required>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                    <label class="form-label mb-3"
                                                        for="example3cols3Input">{{ __('Currency Symbol Position') }}</label>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-check form-check-inline mb-3">
                                                                <input type="radio" id="customRadio5"
                                                                    name="currency_symbol_position" value="pre"
                                                                    class="form-check-input"
                                                                    @if ($store_settings['currency_symbol_position'] == 'pre') checked @endif>
                                                                <label class="form-check-label"
                                                                    for="customRadio5">{{ __('Pre') }}</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-check form-check-inline mb-3">
                                                                <input type="radio" id="customRadio6"
                                                                    name="currency_symbol_position" value="post"
                                                                    class="form-check-input"
                                                                    @if ($store_settings['currency_symbol_position'] == 'post') checked @endif>
                                                                <label class="form-check-label"
                                                                    for="customRadio6">{{ __('Post') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                    <label class="form-label mb-3"
                                                        for="example3cols3Input">{{ __('Currency Symbol Space') }}</label>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-check form-check-inline mb-3">
                                                                <input type="radio" id="customRadio7"
                                                                    name="currency_symbol_space" value="with"
                                                                    class="form-check-input"
                                                                    @if ($store_settings['currency_symbol_space'] == 'with') checked @endif>
                                                                <label class="form-check-label"
                                                                    for="customRadio7">{{ __('With Space') }}</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-check form-check-inline mb-3">
                                                                <input type="radio" id="customRadio8"
                                                                    name="currency_symbol_space" value="without"
                                                                    class="form-check-input"
                                                                    @if ($store_settings['currency_symbol_space'] == 'without' || $store_settings['currency_symbol_space'] == null) checked @endif>
                                                                <label class="form-check-label"
                                                                    for="customRadio8">{{ __('Without Space') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 faq">
                                            <div class="accordion accordion-flush setting-accordion" id="accordionExample">

                                                <!-- Bank transfer -->
                                                <div class="accordion-item">
                                                    <h2 class=" accordion-header" id="heading-2-1">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse1"
                                                            aria-expanded="false" aria-controls="collapse1">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Bank Transfer') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1 d-inline-block">
                                                                    <input type="hidden" name="enable_bank"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input input-primary"
                                                                        name="enable_bank" id="enable_bank"
                                                                        {{ $store_settings['enable_bank'] == 'on' ? 'checked="checked"' : '' }}>
                                                                        <label class="form-check-label"
                                                                        for="enable_bank"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse1" class="accordion-collapse collapse"
                                                        aria-labelledby="heading-2-1" data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        <label class="col-form-label mb-0">{{ __('Bank Details') }}</label>
                                                                        <textarea type="text" rows="3" name="bank_number" id="bank_number" class="form-control" value="" placeholder="{{ __('Bank Transfer Number') }}">{{ $store_settings['bank_number'] }}</textarea>
                                                                        <small>{{ __('Example : Bank : bank name </br> Account Number : 0000 0000 </br>') }}</small>
                                                                        {{-- <small>{{ __('Note: Input your bank details including bank name.') }}</small> --}}

                                                                        @if ($errors->has('bank_number'))
                                                                            <span class="invalid-feedback d-block">
                                                                                {{ $errors->first('bank_number') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Strip -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-2">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse2"
                                                            aria-expanded="true" aria-controls="collapse2">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Stripe') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1 d-inline-block">
                                                                    <input type="hidden" name="is_stripe_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_stripe_enabled" id="is_stripe_enabled"
                                                                        {{ isset($store_payment_setting['is_stripe_enabled']) && $store_payment_setting['is_stripe_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_stripe_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse2"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-2"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="stripe_key"
                                                                            class="col-form-label">{{ __('Stripe Key') }}</label>
                                                                        <input class="form-control"
                                                                            placeholder="{{ __('Stripe Key') }}"
                                                                            name="stripe_key" type="text"
                                                                            value="{{ isset($store_payment_setting['stripe_key']) ? $store_payment_setting['stripe_key'] : '' }}"
                                                                            id="stripe_key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="stripe_secret"
                                                                            class="col-form-label">{{ __('Stripe Secret') }}</label>
                                                                        <input class="form-control "
                                                                            placeholder="{{ __('Stripe Secret') }}"
                                                                            name="stripe_secret" type="text"
                                                                            value="{{ isset($store_payment_setting['stripe_secret']) ? $store_payment_setting['stripe_secret'] : '' }}"
                                                                            id="stripe_secret">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Paypal -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-3">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse3"
                                                            aria-expanded="true" aria-controls="collapse3">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Paypal') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1 d-inline-block">
                                                                    <input type="hidden" name="is_paypal_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_paypal_enabled" id="is_paypal_enabled"
                                                                        {{ isset($store_payment_setting['is_paypal_enabled']) && $store_payment_setting['is_paypal_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_paypal_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse3"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-3"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="paypal-label text-dark col-form-label"
                                                                        for="paypal_mode">{{ __('Paypal Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio"
                                                                                            name="paypal_mode"
                                                                                            value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($store_payment_setting['paypal_mode']) && $store_settings['paypal_mode'] == '') || (isset($store_payment_setting['paypal_mode']) && $store_settings['paypal_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>{{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label class="form-check-labe text-dark">
                                                                                        <input type="radio"
                                                                                            name="paypal_mode"
                                                                                            value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($store_payment_setting['paypal_mode']) && $store_settings['paypal_mode'] == 'live' ? 'checked="checked"' : '' }}>{{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_client_id"
                                                                            class="col-form-label">{{ __('Client ID') }}</label>
                                                                        <input type="text" name="paypal_client_id"
                                                                            id="paypal_client_id" class="form-control"
                                                                            value="{{ isset($store_settings['paypal_client_id']) ? $store_settings['paypal_client_id'] : '' }}"
                                                                            placeholder="{{ __('Client ID') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="paypal_secret_key"
                                                                            id="paypal_secret_key" class="form-control"
                                                                            value="{{ isset($store_settings['paypal_secret_key']) ? $store_settings['paypal_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Paystack -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-4">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse4"
                                                            aria-expanded="true" aria-controls="collapse4">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Paystack') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1 d-inline-block">
                                                                    <input type="hidden" name="is_paystack_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_paystack_enabled"
                                                                        id="is_paystack_enabled"{{ isset($store_payment_setting['is_paystack_enabled']) && $store_payment_setting['is_paystack_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_paystack_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse4"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-4"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_client_id"
                                                                            class="col-form-label">{{ __('Public Key') }}</label>
                                                                        <input type="text" name="paystack_public_key"
                                                                            id="paystack_public_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['paystack_public_key']) ? $store_payment_setting['paystack_public_key'] : '' }}"
                                                                            placeholder="{{ __('Public Key') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paystack_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="paystack_secret_key"
                                                                            id="paystack_secret_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['paystack_secret_key']) ? $store_payment_setting['paystack_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- FLUTTERWAVE -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-5">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse5"
                                                            aria-expanded="true" aria-controls="collapse5">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Flutterwave') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1 d-inline-block">
                                                                    <input type="hidden" name="is_flutterwave_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_flutterwave_enabled"
                                                                        id="is_flutterwave_enabled"
                                                                        {{ isset($store_payment_setting['is_flutterwave_enabled']) && $store_payment_setting['is_flutterwave_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_flutterwave_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse5"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-5"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paypal_client_id"
                                                                            class="col-form-label">{{ __('Public Key') }}</label>
                                                                        <input type="text" name="flutterwave_public_key"
                                                                            id="flutterwave_public_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['flutterwave_public_key']) ? $store_payment_setting['flutterwave_public_key'] : '' }}"
                                                                            placeholder="Public Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paystack_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="flutterwave_secret_key"
                                                                            id="flutterwave_secret_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['flutterwave_secret_key']) ? $store_payment_setting['flutterwave_secret_key'] : '' }}"
                                                                            placeholder="Secret Key">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Razorpay -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-6">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse6"
                                                            aria-expanded="true" aria-controls="collapse6">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Razorpay') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1 d-inline-block">
                                                                    <input type="hidden" name="is_razorpay_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_razorpay_enabled"
                                                                        id="is_razorpay_enabled"
                                                                        {{ isset($store_payment_setting['is_razorpay_enabled']) && $store_payment_setting['is_razorpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_razorpay_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse6"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-6"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="razorpay_public_key"
                                                                            class="col-form-label">{{ __('Public Key') }}</label>

                                                                        <input type="text" name="razorpay_public_key"
                                                                            id="razorpay_public_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['razorpay_public_key']) ? $store_payment_setting['razorpay_public_key'] : '' }}"
                                                                            placeholder="Public Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="razorpay_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="razorpay_secret_key"
                                                                            id="razorpay_secret_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['razorpay_secret_key']) ? $store_payment_setting['razorpay_secret_key'] : '' }}"
                                                                            placeholder="Secret Key">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Paytm -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-7">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse7"
                                                            aria-expanded="true" aria-controls="collapse7">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Paytm') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1 d-inline-block">
                                                                    <input type="hidden" name="is_paytm_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_paytm_enabled" id="is_paytm_enabled"
                                                                        {{ isset($store_payment_setting['is_paytm_enabled']) && $store_payment_setting['is_paytm_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_paytm_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse7"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-7"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="paypal-label col-form-label"
                                                                        for="paypal_mode">{{ __('Paytm Environment') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($store_payment_setting['paytm_mode']) && $store_payment_setting['paytm_mode'] == 'local' ? 'active' : '' }}">

                                                                                        <input type="radio"
                                                                                            name="paytm_mode" value="local"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($store_payment_setting['paytm_mode']) && $store_payment_setting['paytm_mode'] == '') || (isset($store_payment_setting['paytm_mode']) && $store_payment_setting['paytm_mode'] == 'local') ? 'checked="checked"' : '' }}>

                                                                                        {{ __('Local') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($store_payment_setting['paytm_mode']) && $store_payment_setting['paytm_mode'] == 'live' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="paytm_mode"
                                                                                            value="production"
                                                                                            class="form-check-input"
                                                                                            {{ isset($store_payment_setting['paytm_mode']) && $store_payment_setting['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Production') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="paytm_public_key"
                                                                            class="col-form-label">{{ __('Merchant ID') }}</label>
                                                                        <input type="text" name="paytm_merchant_id"
                                                                            id="paytm_merchant_id" class="form-control"
                                                                            value="{{ isset($store_payment_setting['paytm_merchant_id']) ? $store_payment_setting['paytm_merchant_id'] : '' }}"
                                                                            placeholder="Merchant ID">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="paytm_secret_key"
                                                                            class="col-form-label">{{ __('Merchant Key') }}</label>
                                                                        <input type="text" name="paytm_merchant_key"
                                                                            id="paytm_merchant_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['paytm_merchant_key']) ? $store_payment_setting['paytm_merchant_key'] : '' }}"
                                                                            placeholder="Merchant Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="paytm_industry_type"
                                                                            class="col-form-label">{{ __('Industry Type') }}</label>
                                                                        <input type="text" name="paytm_industry_type"
                                                                            id="paytm_industry_type" class="form-control"
                                                                            value="{{ isset($store_payment_setting['paytm_industry_type']) ? $store_payment_setting['paytm_industry_type'] : '' }}"
                                                                            placeholder="Industry Type">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Mercado Pago-->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-8">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse8"
                                                            aria-expanded="true" aria-controls="collapse8">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Mercado Pago') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1 d-inline-block">
                                                                    <input type="hidden" name="is_mercado_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_mercado_enabled"
                                                                        id="is_mercado_enabled"
                                                                        {{ isset($store_payment_setting['is_mercado_enabled']) && $store_payment_setting['is_mercado_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_mercado_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse8"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-8"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="coingate-label col-form-label"
                                                                        for="mercado_mode">{{ __('Mercado Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="mr-2" style="margin-right: 15px;">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($store_payment_setting['mercado_mode']) && $store_payment_setting['mercado_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="mercado_mode"
                                                                                            value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($store_payment_setting['mercado_mode']) && $store_payment_setting['mercado_mode'] == '') || (isset($store_payment_setting['mercado_mode']) && $store_payment_setting['mercado_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="mr-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($store_payment_setting['mercado_mode']) && $store_payment_setting['mercado_mode'] == 'live' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="mercado_mode"
                                                                                            value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($store_payment_setting['mercado_mode']) && $store_payment_setting['mercado_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mercado_access_token"
                                                                            class="col-form-label">{{ __('Access Token') }}</label>
                                                                        <input type="text" name="mercado_access_token"
                                                                            id="mercado_access_token" class="form-control"
                                                                            value="{{ isset($store_payment_setting['mercado_access_token']) ? $store_payment_setting['mercado_access_token'] : '' }}"
                                                                            placeholder="{{ __('Access Token') }}" />
                                                                        @if ($errors->has('mercado_secret_key'))
                                                                            <span class="invalid-feedback d-block">
                                                                                {{ $errors->first('mercado_access_token') }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Mollie -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-9">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse9"
                                                            aria-expanded="true" aria-controls="collapse9">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Mollie') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_mollie_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_mollie_enabled" id="is_mollie_enabled"
                                                                        {{ isset($store_payment_setting['is_mollie_enabled']) && $store_payment_setting['is_mollie_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_mollie_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse9"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-9"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_api_key"
                                                                            class="col-form-label">{{ __('Mollie Api Key') }}</label>
                                                                        <input type="text" name="mollie_api_key"
                                                                            id="mollie_api_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['mollie_api_key']) ? $store_payment_setting['mollie_api_key'] : '' }}"
                                                                            placeholder="Mollie Api Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_profile_id"
                                                                            class="col-form-label">{{ __('Mollie Profile Id') }}</label>
                                                                        <input type="text" name="mollie_profile_id"
                                                                            id="mollie_profile_id" class="form-control"
                                                                            value="{{ isset($store_payment_setting['mollie_profile_id']) ? $store_payment_setting['mollie_profile_id'] : '' }}"
                                                                            placeholder="Mollie Profile Id">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_partner_id"
                                                                            class="col-form-label">{{ __('Mollie Partner Id') }}</label>
                                                                        <input type="text" name="mollie_partner_id"
                                                                            id="mollie_partner_id" class="form-control"
                                                                            value="{{ isset($store_payment_setting['mollie_profile_id']) ? $store_payment_setting['mollie_profile_id'] : '' }}"
                                                                            placeholder="Mollie Partner Id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Skrill -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-10">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse10"
                                                            aria-expanded="true" aria-controls="collapse10">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Skrill') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_skrill_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_skrill_enabled" id="is_skrill_enabled"
                                                                        {{ isset($store_payment_setting['is_skrill_enabled']) && $store_payment_setting['is_skrill_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_skrill_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse10"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-10"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="mollie_api_key"
                                                                            class="col-form-label">{{ __('Skrill Email') }}</label>
                                                                        <input type="text" name="skrill_email"
                                                                            id="skrill_email" class="form-control"
                                                                            value="{{ isset($store_payment_setting['skrill_email']) ? $store_payment_setting['skrill_email'] : '' }}"
                                                                            placeholder="Enter Skrill Email">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- CoinGate -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-11">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse11"
                                                            aria-expanded="true" aria-controls="collapse11">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('CoinGate') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_coingate_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_coingate_enabled"
                                                                        id="is_coingate_enabled"
                                                                        {{ isset($store_payment_setting['is_coingate_enabled']) && $store_payment_setting['is_coingate_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_coingate_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse11"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-11"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="col-form-label"
                                                                        for="coingate_mode">{{ __('CoinGate Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($store_payment_setting['coingate_mode']) == 'sandbox' ? 'active' : '' }}">

                                                                                        <input type="radio"
                                                                                            name="coingate_mode"
                                                                                            value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($store_payment_setting['coingate_mode']) && $store_settings['coingate_mode'] == '') || (isset($store_payment_setting['coingate_mode']) && $store_settings['coingate_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>

                                                                                        {{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($store_payment_setting['coingate_mode']) && $store_payment_setting['coingate_mode'] == 'live' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="coingate_mode"
                                                                                            value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($store_payment_setting['coingate_mode']) && $store_settings['coingate_mode'] == 'live' ? 'checked="checked"' : '' }}>
                                                                                        {{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="coingate_auth_token"
                                                                            class="col-form-label">{{ __('CoinGate Auth Token') }}</label>
                                                                        <input type="text" name="coingate_auth_token"
                                                                            id="coingate_auth_token" class="form-control"
                                                                            value="{{ isset($store_payment_setting['coingate_auth_token']) ? $store_payment_setting['coingate_auth_token'] : '' }}"
                                                                            placeholder="{{ __('CoinGate Auth Token') }}">
                                                                    </div>

                                                                    @if ($errors->has('coingate_auth_token'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('coingate_auth_token') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- PaymentWall -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-12">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse12"
                                                            aria-expanded="true" aria-controls="collapse12">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('PaymentWall') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_paymentwall_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_paymentwall_enabled"
                                                                        id="is_paymentwall_enabled"
                                                                        {{ isset($store_payment_setting['is_paymentwall_enabled']) && $store_payment_setting['is_paymentwall_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_paymentwall_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse12"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-12"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paymentwall_public_key"
                                                                            class="col-form-label">{{ __('Public Key') }}</label>
                                                                        <input type="text" name="paymentwall_public_key"
                                                                            id="paymentwall_public_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['paymentwall_public_key']) ? $store_payment_setting['paymentwall_public_key'] : '' }}"
                                                                            placeholder="{{ __('Public Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('paymentwall_public_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paymentwall_public_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paymentwall_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="paymentwall_secret_key"
                                                                            id="paymentwall_secret_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['paymentwall_secret_key']) ? $store_payment_setting['paymentwall_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('paymentwall_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paymentwall_secret_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Toyyibpay -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-13">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse13"
                                                            aria-expanded="true" aria-controls="collapse13">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Toyyibpay') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_toyyibpay_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_toyyibpay_enabled"
                                                                        id="is_toyyibpay_enabled"
                                                                        {{ isset($store_payment_setting['is_toyyibpay_enabled']) && $store_payment_setting['is_toyyibpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_toyyibpay_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse13"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-13"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="category_code"
                                                                            class="col-form-label">{{ __('Category Code') }}</label>
                                                                        <input type="text" name="category_code"
                                                                            id="category_code" class="form-control"
                                                                            value="{{ isset($store_payment_setting['category_code']) ? $store_payment_setting['category_code'] : '' }}"
                                                                            placeholder="{{ __('Category Code') }}">
                                                                    </div>
                                                                    @if ($errors->has('category_code'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('category_code') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="toyyibpay_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="toyyibpay_secret_key"
                                                                            id="toyyibpay_secret_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['toyyibpay_secret_key']) ? $store_payment_setting['toyyibpay_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('toyyibpay_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('toyyibpay_secret_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Payfast -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-14">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse14"
                                                            aria-expanded="true" aria-controls="collapse14">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Payfast') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_payfast_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_payfast_enabled"
                                                                        id="is_payfast_enabled"
                                                                        {{ isset($store_payment_setting['is_payfast_enabled']) && $store_payment_setting['is_payfast_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_payfast_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse14"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-14"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="payfast-label col-form-label"
                                                                        for="payfast_mode">{{ __('Payfast Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($store_payment_setting['payfast_mode']) && $store_payment_setting['payfast_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="payfast_mode" value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($store_payment_setting['payfast_mode']) && $store_payment_setting['payfast_mode'] == '') || (isset($store_payment_setting['payfast_mode']) &&
                                                                                            $store_payment_setting['payfast_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>{{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($store_payment_setting['payfast_mode']) && $store_payment_setting['payfast_mode'] == 'live' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="payfast_mode" value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($store_payment_setting['payfast_mode']) && $store_payment_setting['payfast_mode'] == 'live' ? 'checked="checked"' : '' }}>{{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="payfast_merchant_id"
                                                                            class="col-form-label">{{ __('Merchant ID') }}</label>
                                                                        <input type="text" name="payfast_merchant_id"
                                                                            id="payfast_merchant_id" class="form-control"
                                                                            value="{{ isset($store_payment_setting['payfast_merchant_id']) ? $store_payment_setting['payfast_merchant_id'] : '' }}"
                                                                            placeholder="{{ __('Merchant ID') }}">
                                                                    </div>
                                                                    @if ($errors->has('payfast_merchant_id'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('payfast_merchant_id') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="payfast_merchant_key"
                                                                            class="col-form-label">{{ __('Merchant Key') }}</label>
                                                                        <input type="text" name="payfast_merchant_key"
                                                                            id="payfast_merchant_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['payfast_merchant_key']) ? $store_payment_setting['payfast_merchant_key'] : '' }}"
                                                                            placeholder="{{ __('Merchant Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('payfast_merchant_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('payfast_merchant_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="payfast_signature"
                                                                            class="col-form-label">{{ __('Salt Passphrase') }}</label>
                                                                        <input type="text" name="payfast_signature"
                                                                            id="payfast_signature" class="form-control"
                                                                            value="{{ isset($store_payment_setting['payfast_signature']) ? $store_payment_setting['payfast_signature'] : '' }}"
                                                                            placeholder="{{ __('Salt Passphrase') }}">
                                                                    </div>
                                                                    @if ($errors->has('payfast_signature'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('payfast_signature') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Iyzipay -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-15">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse15"
                                                            aria-expanded="true" aria-controls="collapse15">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Iyzipay') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_iyzipay_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_iyzipay_enabled"
                                                                        id="is_iyzipay_enabled"
                                                                        {{ isset($store_payment_setting['is_iyzipay_enabled']) && $store_payment_setting['is_iyzipay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_iyzipay_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse15"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-15"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="iyzipay-label col-form-label"
                                                                        for="iyzipay_mode">{{ __('Iyzipay Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($store_payment_setting['iyzipay_mode']) && $store_payment_setting['iyzipay_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="iyzipay_mode" value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($store_payment_setting['iyzipay_mode']) && $store_payment_setting['iyzipay_mode'] == '') || (isset($store_payment_setting['iyzipay_mode']) &&
                                                                                            $store_payment_setting['iyzipay_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>{{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($store_payment_setting['iyzipay_mode']) && $store_payment_setting['iyzipay_mode'] == 'live' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="iyzipay_mode" value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($store_payment_setting['iyzipay_mode']) && $store_payment_setting['iyzipay_mode'] == 'live' ? 'checked="checked"' : '' }}>{{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="iyzipay_public_key"
                                                                            class="col-form-label">{{ __('Public Key') }}</label>
                                                                        <input type="text" name="iyzipay_public_key"
                                                                            id="iyzipay_public_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['iyzipay_public_key']) ? $store_payment_setting['iyzipay_public_key'] : '' }}"
                                                                            placeholder="{{ __('Public Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('iyzipay_public_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('iyzipay_public_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="iyzipay_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="iyzipay_secret_key"
                                                                            id="iyzipay_secret_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['iyzipay_secret_key']) ? $store_payment_setting['iyzipay_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('iyzipay_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('iyzipay_secret_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Sspay -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-16">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse16"
                                                            aria-expanded="true" aria-controls="collapse16">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Sspay') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_sspay_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_sspay_enabled"
                                                                        id="is_sspay_enabled"
                                                                        {{ isset($store_payment_setting['is_sspay_enabled']) && $store_payment_setting['is_sspay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_sspay_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse16"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-16"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="sspay_category_code"
                                                                            class="col-form-label">{{ __('Category Code') }}</label>
                                                                        <input type="text" name="sspay_category_code"
                                                                            id="sspay_category_code" class="form-control"
                                                                            value="{{ isset($store_payment_setting['sspay_category_code']) ? $store_payment_setting['sspay_category_code'] : '' }}"
                                                                            placeholder="{{ __('Category Code') }}">
                                                                    </div>
                                                                    @if ($errors->has('sspay_category_code'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('sspay_category_code') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="sspay_secret_key"
                                                                            class="col-form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="sspay_secret_key"
                                                                            id="sspay_secret_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['sspay_secret_key']) ? $store_payment_setting['sspay_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('sspay_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('sspay_secret_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- PayTab -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-17">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse17"
                                                            aria-expanded="true" aria-controls="collapse17">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('PayTab') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_paytab_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_paytab_enabled"
                                                                        id="is_paytab_enabled"
                                                                        {{ isset($store_payment_setting['is_paytab_enabled']) && $store_payment_setting['is_paytab_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_paytab_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse17"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-17"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paytab_profile_id"
                                                                            class="col-form-label">{{ __('Profile Id') }}</label>
                                                                        <input type="text" name="paytab_profile_id"
                                                                            id="paytab_profile_id" class="form-control"
                                                                            value="{{ isset($store_payment_setting['paytab_profile_id']) ? $store_payment_setting['paytab_profile_id'] : '' }}"
                                                                            placeholder="{{ __('Profile Id') }}">
                                                                    </div>
                                                                    @if ($errors->has('paytab_profile_id'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paytab_profile_id') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paytab_server_key"
                                                                            class="col-form-label">{{ __('Server Key') }}</label>
                                                                        <input type="text" name="paytab_server_key"
                                                                            id="paytab_server_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['paytab_server_key']) ? $store_payment_setting['paytab_server_key'] : '' }}"
                                                                            placeholder="{{ __('Server Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('paytab_server_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paytab_server_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paytab_region"
                                                                            class="col-form-label">{{ __('Region') }}</label>
                                                                        <input type="text" name="paytab_region"
                                                                            id="paytab_region" class="form-control"
                                                                            value="{{ isset($store_payment_setting['paytab_region']) ? $store_payment_setting['paytab_region'] : '' }}"
                                                                            placeholder="{{ __('Region') }}">
                                                                    </div>
                                                                    @if ($errors->has('paytab_region'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paytab_region') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Benefit -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-18">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse18"
                                                            aria-expanded="true" aria-controls="collapse18">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Benefit') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_benefit_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_benefit_enabled"
                                                                        id="is_benefit_enabled"
                                                                        {{ isset($store_payment_setting['is_benefit_enabled']) && $store_payment_setting['is_benefit_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_benefit_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse18"
                                                        class="accordion-collapse collapse" aria-labelledby="heading-2-18" data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="benefit_api_key"
                                                                            class="col-form-label">{{ __('Benefit Key') }}</label>
                                                                        <input type="text" name="benefit_api_key"
                                                                            id="benefit_api_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['benefit_api_key']) ? $store_payment_setting['benefit_api_key'] : '' }}"
                                                                            placeholder="{{ __('Benefit Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('benefit_api_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('benefit_api_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="benefit_secret_key"
                                                                            class="col-form-label">{{ __('Benefit Secret Key') }}</label>
                                                                        <input type="text" name="benefit_secret_key"
                                                                            id="benefit_secret_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['benefit_secret_key']) ? $store_payment_setting['benefit_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Benefit Secret Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('benefit_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('benefit_secret_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- CashFree -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-19">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse19"
                                                            aria-expanded="true" aria-controls="collapse19">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Cashfree') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_cashfree_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_cashfree_enabled"
                                                                        id="is_cashfree_enabled"
                                                                        {{ isset($store_payment_setting['is_cashfree_enabled']) && $store_payment_setting['is_cashfree_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_cashfree_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse19"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-19"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="cashfree_api_key"
                                                                            class="col-form-label">{{ __('Cashfree Key') }}</label>
                                                                        <input type="text" name="cashfree_api_key"
                                                                            id="cashfree_api_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['cashfree_api_key']) ? $store_payment_setting['cashfree_api_key'] : '' }}"
                                                                            placeholder="{{ __('Cashfree Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('cashfree_api_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('cashfree_api_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="cashfree_secret_key"
                                                                            class="col-form-label">{{ __('Cashfree Secret Key') }}</label>
                                                                        <input type="text" name="cashfree_secret_key"
                                                                            id="cashfree_secret_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['cashfree_secret_key']) ? $store_payment_setting['cashfree_secret_key'] : '' }}"
                                                                            placeholder="{{ __('Cashfree Secret Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('cashfree_secret_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('cashfree_secret_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Aamarpay -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-20">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse20"
                                                            aria-expanded="true" aria-controls="collapse20">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Aamarpay') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_aamarpay_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_aamarpay_enabled"
                                                                        id="is_aamarpay_enabled"
                                                                        {{ isset($store_payment_setting['is_aamarpay_enabled']) && $store_payment_setting['is_aamarpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_aamarpay_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse20"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-20"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aamarpay_store_id"
                                                                            class="col-form-label">{{ __('Store Id') }}</label>
                                                                        <input type="text" name="aamarpay_store_id"
                                                                            id="aamarpay_store_id" class="form-control"
                                                                            value="{{ isset($store_payment_setting['aamarpay_store_id']) ? $store_payment_setting['aamarpay_store_id'] : '' }}"
                                                                            placeholder="{{ __('Store Id') }}">
                                                                    </div>
                                                                    @if ($errors->has('aamarpay_store_id'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('aamarpay_store_id') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aamarpay_signature_key"
                                                                            class="col-form-label">{{ __('Signature Key') }}</label>
                                                                        <input type="text" name="aamarpay_signature_key"
                                                                            id="aamarpay_signature_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['aamarpay_signature_key']) ? $store_payment_setting['aamarpay_signature_key'] : '' }}"
                                                                            placeholder="{{ __('Signature Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('aamarpay_signature_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('aamarpay_signature_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="aamarpay_description"
                                                                            class="col-form-label">{{ __('Description') }}</label>
                                                                        <input type="text" name="aamarpay_description"
                                                                            id="aamarpay_description" class="form-control"
                                                                            value="{{ isset($store_payment_setting['aamarpay_description']) ? $store_payment_setting['aamarpay_description'] : '' }}"
                                                                            placeholder="{{ __('Description') }}">
                                                                    </div>
                                                                    @if ($errors->has('aamarpay_description'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('aamarpay_description') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Paytr -->
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-21">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse21"
                                                            aria-expanded="true" aria-controls="collapse21">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('PayTR') }}
                                                            </span>
                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable:')}}</span>
                                                                <div class="form-check form-switch d-inline-block custom-switch-v1">
                                                                    <input type="hidden" name="is_paytr_enabled"
                                                                        value="off">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="is_paytr_enabled"
                                                                        id="is_paytr_enabled"
                                                                        {{ isset($store_payment_setting['is_paytr_enabled']) && $store_payment_setting['is_paytr_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="custom-control-label form-label"
                                                                        for="is_paytr_enabled"></label>
                                                                </div>
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse21"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-21"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paytr_merchant_id"
                                                                            class="col-form-label">{{ __('Merchant Id') }}</label>
                                                                        <input type="text" name="paytr_merchant_id"
                                                                            id="paytr_merchant_id" class="form-control"
                                                                            value="{{ isset($store_payment_setting['paytr_merchant_id']) ? $store_payment_setting['paytr_merchant_id'] : '' }}"
                                                                            placeholder="{{ __('Merchant Id') }}">
                                                                    </div>
                                                                    @if ($errors->has('paytr_merchant_id'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paytr_merchant_id') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paytr_merchant_key"
                                                                            class="col-form-label">{{ __('Merchant Key') }}</label>
                                                                        <input type="text" name="paytr_merchant_key"
                                                                            id="paytr_merchant_key" class="form-control"
                                                                            value="{{ isset($store_payment_setting['paytr_merchant_key']) ? $store_payment_setting['paytr_merchant_key'] : '' }}"
                                                                            placeholder="{{ __('Merchant Key') }}">
                                                                    </div>
                                                                    @if ($errors->has('paytr_merchant_key'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paytr_merchant_key') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="paytr_merchant_salt"
                                                                            class="col-form-label">{{ __('Merchant Salt') }}</label>
                                                                        <input type="text" name="paytr_merchant_salt"
                                                                            id="paytr_merchant_salt" class="form-control"
                                                                            value="{{ isset($store_payment_setting['paytr_merchant_salt']) ? $store_payment_setting['paytr_merchant_salt'] : '' }}"
                                                                            placeholder="{{ __('Description') }}">
                                                                    </div>
                                                                    @if ($errors->has('paytr_merchant_salt'))
                                                                        <span class="invalid-feedback d-block">
                                                                            {{ $errors->first('paytr_merchant_salt') }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Yookassa --}}
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-22">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse22"
                                                            aria-expanded="true" aria-controls="collapse22">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Yookassa') }}
                                                            </span>

                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1">
                                                                    <input type="hidden" name="is_yookassa_enabled"
                                                                        value="off">
                                                                    <input type="checkbox"
                                                                        class="form-check-input input-primary"
                                                                        name="is_yookassa_enabled" id="is_yookassa_enabled"
                                                                        {{ isset($store_payment_setting['is_yookassa_enabled']) && $store_payment_setting['is_yookassa_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="customswitchv1-2"></label>
                                                                </div>
                                                            </div>

                                                        </button>
                                                    </h2>

                                                    <div id="collapse22"
                                                        class="accordion-collapse collapse"aria-labelledby="heading-2-22"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="yookassa_shop_id"
                                                                            class="form-label">{{ __('Shop ID Key') }}</label>
                                                                        <input type="text" name="yookassa_shop_id"
                                                                            id="yookassa_shop_id" class="form-control"
                                                                            value="{{ !isset($store_payment_setting['yookassa_shop_id']) || is_null($store_payment_setting['yookassa_shop_id']) ? '' : $store_payment_setting['yookassa_shop_id'] }}"
                                                                            placeholder="{{ __('Shop ID Key') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="yookassa_secret"
                                                                            class="form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="yookassa_secret"
                                                                            id="yookassa_secret" class="form-control"
                                                                            value="{{ !isset($store_payment_setting['yookassa_secret']) || is_null($store_payment_setting['yookassa_secret']) ? '' : $store_payment_setting['yookassa_secret'] }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Midtrans --}}
                                                <div class="accordion-item card shadow-none">
                                                    <h2 class="accordion-header" id="heading-2-23">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse23"
                                                            aria-expanded="true" aria-controls="collapse23">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Midtrans') }}
                                                            </span>

                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1">
                                                                    <input type="hidden" name="is_midtrans_enabled"
                                                                        value="off">
                                                                    <input type="checkbox"
                                                                        class="form-check-input input-primary"
                                                                        name="is_midtrans_enabled" id="is_midtrans_enabled"
                                                                        {{ isset($store_payment_setting['is_midtrans_enabled']) && $store_payment_setting['is_midtrans_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="customswitchv1-2"></label>
                                                                </div>
                                                            </div>

                                                        </button>
                                                    </h2>

                                                    <div id="collapse23" class="accordion-collapse collapse"aria-labelledby="heading-2-23"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <label class="midtrans-label col-form-label"
                                                                        for="midtrans_mode">{{ __('Midtrans Mode') }}</label>
                                                                    <br>
                                                                    <div class="d-flex">
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($store_payment_setting['midtrans_mode']) && $store_payment_setting['midtrans_mode'] == 'sandbox' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="midtrans_mode" value="sandbox"
                                                                                            class="form-check-input"
                                                                                            {{ (isset($store_payment_setting['midtrans_mode']) && $store_payment_setting['midtrans_mode'] == '') || (isset($store_payment_setting['midtrans_mode']) && $store_payment_setting['midtrans_mode'] == 'sandbox') ? 'checked="checked"' : '' }}>{{ __('Sandbox') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="me-2">
                                                                            <div class="border card p-3">
                                                                                <div class="form-check">
                                                                                    <label
                                                                                        class="form-check-labe text-dark {{ isset($store_payment_setting['midtrans_mode']) && $store_payment_setting['midtrans_mode'] == 'live' ? 'active' : '' }}">
                                                                                        <input type="radio"
                                                                                            name="midtrans_mode" value="live"
                                                                                            class="form-check-input"
                                                                                            {{ isset($store_payment_setting['midtrans_mode']) && $store_payment_setting['midtrans_mode'] == 'live' ? 'checked="checked"' : '' }}>{{ __('Live') }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="midtrans_secret"
                                                                            class="form-label">{{ __('Secret Key') }}</label>
                                                                        <input type="text" name="midtrans_secret"
                                                                            id="midtrans_secret" class="form-control"
                                                                            value="{{ !isset($store_payment_setting['midtrans_secret']) || is_null($store_payment_setting['midtrans_secret']) ? '' : $store_payment_setting['midtrans_secret'] }}"
                                                                            placeholder="{{ __('Secret Key') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Xendit --}}
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading-2-24">
                                                        <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapse24"
                                                            aria-expanded="true" aria-controls="collapse24">
                                                            <span class="d-flex align-items-center">
                                                                {{ __('Xendit') }}
                                                            </span>

                                                            <div class="d-flex align-items-center">
                                                                <span class="me-2">{{__('Enable')}}</span>
                                                                <div class="form-check form-switch custom-switch-v1">
                                                                    <input type="hidden" name="is_xendit_enabled"
                                                                        value="off">
                                                                    <input type="checkbox"
                                                                        class="form-check-input input-primary"
                                                                        name="is_xendit_enabled" id="is_xendit_enabled"
                                                                        {{ isset($store_payment_setting['is_xendit_enabled']) && $store_payment_setting['is_xendit_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="customswitchv1-2"></label>
                                                                </div>
                                                            </div>

                                                        </button>
                                                    </h2>

                                                    <div id="collapse24" class="accordion-collapse collapse"aria-labelledby="heading-2-24"data-bs-parent="#accordionExample">
                                                        <div class="accordion-body">
                                                            <div class="row">

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="xendit_api"
                                                                            class="form-label">{{ __('API Key') }}</label>
                                                                        <input type="text" name="xendit_api"
                                                                            id="xendit_api" class="form-control"
                                                                            value="{{ !isset($store_payment_setting['xendit_api']) || is_null($store_payment_setting['xendit_api']) ? '' : $store_payment_setting['xendit_api'] }}"
                                                                            placeholder="{{ __('API Key') }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="xendit_token"
                                                                            class="form-label">{{ __('Token') }}</label>
                                                                        <input type="text" name="xendit_token"
                                                                            id="xendit_token" class="form-control"
                                                                            value="{{ !isset($store_payment_setting['xendit_token']) || is_null($store_payment_setting['xendit_token']) ? '' : $store_payment_setting['xendit_token'] }}"
                                                                            placeholder="{{ __('Token') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-end">
                                        <input class="btn  btn-primary" type="submit"
                                            value="{{ __('Save Changes') }}">
                                    </div>
                                </div>
                                {{ Form::close() }}

                            </div>

                            <div class="card" id="store_email_setting">
                                <div class="card-header">
                                    <h5>{{ __('Store Email Settings') }}</h5>
                                </div>
                                {{ Form::open(['route' => ['owner.email.setting', $store_settings->slug], 'method' => 'post']) }}
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_driver', __('Mail Driver'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_driver', $store_settings->mail_driver, ['class' => 'form-control', 'placeholder' => __('Enter Mail Driver')]) }}
                                            @error('mail_driver')
                                                <span class="invalid-mail_driver" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_host', __('Mail Host'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_host', $store_settings->mail_host, ['class' => 'form-control ', 'placeholder' => __('Enter Mail Host')]) }}
                                            @error('mail_host')
                                                <span class="invalid-mail_driver" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_port', __('Mail Port'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_port', $store_settings->mail_port, ['class' => 'form-control', 'placeholder' => __('Enter Mail Port')]) }}
                                            @error('mail_port')
                                                <span class="invalid-mail_port" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_username', __('Mail Username'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_username', $store_settings->mail_username, ['class' => 'form-control', 'placeholder' => __('Enter Mail Username')]) }}
                                            @error('mail_username')
                                                <span class="invalid-mail_username" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_password', __('Mail Password'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_password', $store_settings->mail_password, ['class' => 'form-control', 'placeholder' => __('Enter Mail Password')]) }}
                                            @error('mail_password')
                                                <span class="invalid-mail_password" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_encryption', __('Mail Encryption'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_encryption', $store_settings->mail_encryption, ['class' => 'form-control', 'placeholder' => __('Enter Mail Encryption')]) }}
                                            @error('mail_encryption')
                                                <span class="invalid-mail_encryption" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_from_address', __('Mail From Address'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_from_address', $store_settings->mail_from_address, ['class' => 'form-control', 'placeholder' => __('Enter Mail From Address')]) }}
                                            @error('mail_from_address')
                                                <span class="invalid-mail_from_address" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            {{ Form::label('mail_from_name', __('Mail From Name'), ['class' => 'form-label']) }}
                                            {{ Form::text('mail_from_name', $store_settings->mail_from_name, ['class' => 'form-control', 'placeholder' => __('Enter Mail From Name')]) }}
                                            @error('mail_from_name')
                                                <span class="invalid-mail_from_name" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row card-footer reverse-rtl-row">
                                    <div class="form-group col-md-6">
                                        <a href="#" data-url="{{ route('test.mail') }}"
                                            data-title="{{ __('Send Test Mail') }}" class="btn btn-primary send_email">
                                            {{ __('Send Test Mail') }}
                                        </a>
                                    </div>
                                    <div class="form-group col-md-6 text-end">
                                        {{ Form::submit(__('Save Changes'), ['class' => 'btn btn-primary']) }}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>

                            <div class="card" id="certificate_setting">
                                <div class="card-header">
                                    <h5>{{ __('Certificate Settings') }}</h5>
                                </div>
                                <div class="card-body">
                                    <form id="setting-form" method="post"
                                        action="{{ route('certificate.template.setting') }}">
                                        @csrf
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <h6 class="font-weight-bold">{{ __('Certificate Variable') }}
                                                            </h6>
                                                            <div class="col-6 float-left">
                                                                <p class="mb-1">{{ __('Store Name') }} : <span
                                                                        class="pull-right text-primary">{header_name}</span>
                                                                </p>
                                                                <p class="mb-1">{{ __('Student Name') }} : <span
                                                                        class="pull-right text-primary">{student_name}</span>
                                                                </p>
                                                                <p class="mb-1">{{ __('Course Time') }} : <span
                                                                        class="pull-right text-primary">{course_time}</span>
                                                                </p>
                                                                <p class="mb-1">{{ __('Course Name') }} : <span
                                                                        class="pull-right text-primary">{course_name}</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label for="storejs"
                                                                    class="form-label">{store_name}</label>
                                                                {{ Form::text('header_name', $store->header_name, ['class' => 'form-control', 'placeholder' => '{header_name}']) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-body pb-0">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="row justify-content-between">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label for="address"
                                                                        class="form-label">{{ __('Certificate Template') }}</label>
                                                                    <select class="form-control select2"
                                                                        name="certificate_template">
                                                                        @foreach (Utility::templateData()['templates'] as $key => $template)
                                                                            <option value="{{ $key }}"
                                                                                {{ isset($store->certificate_template) && $store->certificate_template == $key ? 'selected' : '' }}>
                                                                                {{ $template }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-10">
                                                                <div class="form-group">
                                                                    <label
                                                                        class="form-label form-label">{{ __('Color Input') }}</label>
                                                                    <div class="row gutters-xs">
                                                                        @foreach (Utility::templateData()['colors'] as $key => $color)
                                                                            <div class="col-auto">
                                                                                <label class="colorinput">
                                                                                    <input name="certificate_color"
                                                                                        type="radio"
                                                                                        value="{{ $color['hex'] }}"
                                                                                        class="colorinput-input"
                                                                                        {{ isset($store->certificate_color) && $store->certificate_color == $color['hex'] ? 'checked' : '' }}
                                                                                        data-gradiant='{{ $color['gradiant'] }}'>
                                                                                    <span class="colorinput-color"
                                                                                        style="background: #{{ $color['hex'] }}"></span>
                                                                                </label>
                                                                            </div>
                                                                        @endforeach
                                                                        <input type="hidden" name="gradiant"
                                                                            id="gradiant"
                                                                            value="{{ $color['gradiant'] }} ">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="col-md-2 mb-3 text-end align-items-end d-flex justify-content-end">
                                                                <button class="btn btn-primary">
                                                                    {{ __('Save Changes') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <iframe id="certificate_frame" class="certificate_iframe w-100"
                                                            frameborder="0"
                                                            src="{{ route('certificate.preview', [isset($store->certificate_template) && !empty($store->certificate_template) ? $store->certificate_template : 'template1', isset($store->certificate_color) && !empty($store->certificate_color) ? $store->certificate_color : 'b10d0d', isset($store->certificate_gradiant) && !empty($store->certificate_gradiant) ? $store->certificate_gradiant : 'color-one']) }}"></iframe>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card" id="slack-setting">
                                <div class="card-header">
                                    <h5>{{ __('Slack Settings') }}</h5>
                                </div>
                                {{ Form::open(['route' => 'slack.setting', 'id' => 'setting-form', 'method' => 'post', 'class' => 'd-contents']) }}
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 class="small-title">{{ __('Slack Webhook URL') }}</h4>
                                            <div class="col-md-8">
                                                {{ Form::text('slack_webhook', isset($notifications['slack_webhook']) ? $notifications['slack_webhook'] : '', ['class' => 'form-control w-100', 'placeholder' => __('Enter Slack Webhook URL'), 'required' => 'required']) }}
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-4 mb-2">
                                            <h4 class="small-title">{{ __('Module Setting') }}</h4>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-group">
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span>{{ __('New Course') }}</span>
                                                    <div class="form-check form-check form-switch custom-control-inline">
                                                        {{ Form::checkbox('course_notification', '1', isset($notifications['course_notification']) && $notifications['course_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'course_notification']) }}
                                                        <label class="form-check-label" for="course_notification"></label>
                                                    </div>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span>{{ __('New Order') }}</span>
                                                    <div class="form-check form-check form-switch custom-control-inline">
                                                        {{ Form::checkbox('order_notification', '1', isset($notifications['order_notification']) && $notifications['order_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'order_notification']) }}
                                                        <label class="form-check-label" for="order_notification"></label>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-group">
                                                <li class="list-group-item d-flex justify-content-between">
                                                    <span> {{ __('New Zoom Meeting') }}</span>
                                                    <div class="form-check form-check form-switch custom-control-inline">
                                                        {{ Form::checkbox('zoom_meeting_notification', '1', isset($notifications['zoom_meeting_notification']) && $notifications['zoom_meeting_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'zoom_meeting_notification']) }}
                                                        <label class="form-check-label"
                                                            for="zoom_meeting_notification"></label>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-end">
                                        <input class="btn btn-primary" type="submit" value="{{ __('Save Changes') }}">
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>

                            <div class="card" id="telegram-setting">
                                <div class="card-header">
                                    <h5>{{__('Telegram Settings') }}</h5>
                                </div>
                                {{ Form::open(['route' => 'telegram.setting', 'id' => 'telegram-setting', 'method' => 'post', 'class' => 'd-contents']) }}
                                <div class="card-body">
                                    <div class="row">
                                        <div class="card-body pd-0">
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label class="form-label mb-0">{{ __('Telegram AccessToken') }}</label>
                                                    <br>
                                                    {{ Form::text('telegram_accestoken', isset($notifications['telegram_accestoken']) ? $notifications['telegram_accestoken'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Telegram AccessToken')]) }}
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label class="form-label mb-0">{{ __('Telegram ChatID') }}</label>
                                                    <br>
                                                    {{ Form::text('telegram_chatid', isset($notifications['telegram_chatid']) ? $notifications['telegram_chatid'] : '', ['class' => 'form-control', 'placeholder' => __('Enter Telegram ChatID')]) }}
                                                </div>
                                            </div>
                                            <div class="col-md-12 mt-4 mb-2">
                                                <h4 class="small-title">{{ __('Module Setting') }}</h4>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <ul class="list-group">
                                                        <li class="list-group-item d-flex justify-content-between">
                                                            <span>{{ __('New Course') }}</span>
                                                            <div
                                                                class="form-check form-check form-switch custom-control-inline">
                                                                {{ Form::checkbox('telegram_course_notification', '1', isset($notifications['telegram_course_notification']) && $notifications['telegram_course_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_course_notification']) }}
                                                                <label class="form-check-label"
                                                                    for="telegram_course_notification"></label>
                                                            </div>
                                                        </li>
                                                        <li class="list-group-item d-flex justify-content-between">
                                                            <span>{{ __('New Order') }}</span>
                                                            <div
                                                                class="form-check form-check form-switch custom-control-inline">
                                                                {{ Form::checkbox('telegram_order_notification', '1', isset($notifications['telegram_order_notification']) && $notifications['telegram_order_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_order_notification']) }}
                                                                <label class="form-check-label"
                                                                    for="telegram_order_notification"></label>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="list-group">
                                                        <li class="list-group-item d-flex justify-content-between">
                                                            <span> {{ __('New Zoom Meeting') }}</span>
                                                            <div
                                                                class="form-check form-check form-switch custom-control-inline">
                                                                {{ Form::checkbox('telegram_zoom_meeting_notification', '1', isset($notifications['telegram_zoom_meeting_notification']) && $notifications['telegram_zoom_meeting_notification'] == '1' ? 'checked' : '', ['class' => 'form-check-input', 'id' => 'telegram_zoom_meeting_notification']) }}
                                                                <label class="form-check-label"
                                                                    for="telegram_zoom_meeting_notification"></label>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="text-end">
                                        <input class="btn btn-primary" type="submit" value="{{ __('Save Changes') }}">
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                            <div class="card" id="google-calender-setting">
                                {{ Form::open(['url' => route('google.calender.settings'), 'enctype' => 'multipart/form-data']) }}
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5>{{ __('Google Calendar Settings') }}</h5>
                                        </div>
                                        <div class="col switch-width text-end">
                                            <div class="form-group mb-0">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" data-toggle="switchbutton"
                                                        data-onstyle="primary" class="" name="google_calender"
                                                        id="google_calender"
                                                        {{ $settings['google_calender_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="google_calender_enabled"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                            {{ Form::label('Google calendar id', __('Google Calendar Id'), ['class' => 'col-form-label']) }}
                                            {{ Form::text('google_clender_id', !empty($settings['google_clender_id']) ? $settings['google_clender_id'] : '', ['class' => 'form-control ', 'placeholder' => 'Google Calendar Id'])}}
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                            {{ Form::label('Google calendar json file', __('Google Calendar json File'), ['class' => 'col-form-label']) }}
                                            <input type="file" class="form-control"
                                                name="google_calender_json_file" id="file">
                                            {{-- {{Form::text('zoom_secret_key', !empty($settings['zoom_secret_key']) ? $settings['zoom_secret_key'] : '' ,array('class'=>'form-control', 'placeholder'=>'Google Calendar json File'))}} --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button class="btn-submit btn btn-primary" type="submit">
                                        {{ __('Save Changes') }}
                                    </button>
                                </div>
                            </div>
                            {{ Form::close() }}

                            {{-- Pixel Settings --}}
                            <div class="card" id="pixel_setting">
                                {{-- <div class="custom-fields"> --}}
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5 class="">{{ __('Pixel Fields Settings') }}</h5>
                                                <small>{{ __('Enter Your Pixel Fields Settings') }}</small>
                                            </div>
                                            <div class="col-6 text-end">
                                                <div class="text-end align-items-end d-flex justify-content-end">
                                                    <a class="btn btn-sm btn-primary btn-icon" data-ajax-popup="true" data-url="{{ route('owner.pixel.create') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Create') }}" data-title="{{ __('Create New Pixel') }}">
                                                        <i  class="ti ti-plus text-white"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="">
                                            <div class="table-responsive custom-field-table">
                                                <table class="table" data-repeater-list="fields">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>{{ __('Platform') }}</th>
                                                            <th>{{ __('Pixel Id') }}</th>
                                                            <th>{{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($PixelFields as  $PixelField)
                                                            <tr>
                                                                <td>
                                                                    {{ $PixelField->platform }}
                                                                </td>
                                                                <td>
                                                                    {{ $PixelField->pixel_id }}
                                                                </td>
                                                                <td class="text-right">
                                                                    <div class="action-btn bg-danger ms-2">
                                                                        <a class="bs-pass-para mx-3 btn btn-sm align-items-center" href="#" data-title="{{ __('Delete pixel') }}" data-confirm="{{ __('Are You Sure?') }}" data-text="{{ __('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="pixel-delete-form-{{ $PixelField->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Delete') }}">
                                                                            <i class="ti ti-trash text-white"></i>
                                                                        </a>
                                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['pixel.delete', $PixelField->id], 'id' => 'pixel-delete-form-' . $PixelField->id]) !!}
                                                                        {!! Form::close() !!}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                {{-- </div> --}}
                            </div>

                            {{-- Weebhook Setting --}}
                            <div class="card" id="webhook_setting">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-sm-8">
                                            <h5>{{ __('Webhook Settings') }}</h5>
                                        </div>
                                        <div class="col-lg-4 col-md-4 text-end">
                                            <div class="text-end align-items-end d-flex justify-content-end">
                                                <div class="form-check custom-control custom-switch">
                                                    <a href="#" data-url="{{ route('webhook.create') }}"
                                                        data-size="md" data-ajax-popup="true" data-bs-toggle="tooltip"
                                                        title="{{ __('Create') }}"data-title="{{ __('Create New Webhook') }}"
                                                        class="btn btn-sm btn-primary btn-icon">
                                                        <i class="ti ti-plus"></i>
                                                    </a>
                                                    <label class="custom-control-label form-label" for="is_enabled"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body table-border-style">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead class="thead-light">
                                                <tr class="col-md-6">
                                                    <th scope="col" class="sort" data-sort="module">
                                                        {{ __('Module') }}</th>
                                                    <th scope="col" class="sort" data-sort="url">
                                                        {{ __('URL') }}</th>
                                                    <th scope="col" class="sort" data-sort="method">
                                                        {{ __('Method') }}</th>
                                                    <th scope="col" class="sort" data-sort="">
                                                        {{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($webhooks as $webhook)
                                                    <tr class="Action">
                                                        <td>
                                                            <label for="module"
                                                                class="control-label text-decoration-none tag-lable-{{ $webhook->id }}">{{ $webhook->module }}</label>
                                                        </td>
                                                        <td>
                                                            <label for="url"
                                                                class="control-label text-decoration-none tag-lable-{{ $webhook->id }}">{{ $webhook->url }}</label>
                                                        </td>
                                                        <td>
                                                            <label for="method"
                                                                class="control-label text-decoration-none tag-lable-{{ $webhook->id }}">{{ $webhook->method }}</label>
                                                        </td>
                                                        <td class="">
                                                            <div class="action-btn bg-info ms-2">
                                                                <a class="mx-3 btn btn-sm  align-items-center"
                                                                    data-url="{{ route('webhook.edit', $webhook->id) }}"
                                                                    data-size="md" data-bs-toggle="tooltip"
                                                                    data-bs-original-title="{{ __('Edit') }}"
                                                                    data-bs-placement="top" data-ajax-popup="true"
                                                                    data-title="{{ __('Edit WebHook') }}"
                                                                    class="edit-icon"
                                                                    data-original-title="{{ __('Edit') }}"><i
                                                                        class="ti ti-edit text-white"></i></a>
                                                            </div>
                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['webhook.destroy', $webhook->id]]) !!}
                                                                <a href="#!"
                                                                    class="mx-3 btn btn-sm align-items-center text-white show_confirm"
                                                                    data-bs-toggle="tooltip" title='Delete'>
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @endsection
    @push('script-page')
        <script src="{{ asset('libs/jquery-mask-plugin/dist/jquery.mask.min.js') }}"></script>
        <script>
            function myFunction() {
                var copyText = document.getElementById("myInput");
                copyText.select();
                copyText.setSelectionRange(0, 99999)
                document.execCommand("copy");
                show_toastr('Success', 'Link copied', 'success');
            }
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.js"></script>
        <script>
            $(document).ready(function() {
                $('.repeater').repeater({
                    initEmpty: false,
                    show: function() {
                        $(this).slideDown();
                    },
                    hide: function(deleteElement) {
                        if (confirm('Are you sure you want to delete this element?')) {
                            $(this).slideUp(deleteElement);
                        }
                    },
                    isFirstItemUndeletable: true
                })
            });
            $("#eventBtn").click(function() {
                $("#BigButton").clone(true).appendTo("#fileUploadsContainer").find("input").val("").end();
            });
            $("#testimonial_eventBtn").click(function() {
                $("#BigButton2").clone(true).appendTo("#fileUploadsContainer2").find("input").val("").end();
            });

            $(document).on('click', '#remove', function() {
                var qq = $('.BigButton').length;

                if (qq > 1) {
                    var dd = $(this).attr('data-id');
                    $(this).parents('#BigButton').remove();
                }
            });

            $(".deleteRecord").click(function() {
                var name = $(this).data("name");
                var token = $("meta[name='csrf-token']").attr("content");
                var a = ('{{ route('brand.file.delete', [Auth::user()->current_store, '_name']) }}');
                $.ajax({
                    url: a.replace('_name', name),
                    type: 'DELETE',
                    data: {
                        "name": name,
                        "_token": token,
                    },
                    success: function(response) {
                        show_toastr('Success', response.success, 'success');
                        $('.product_Image[data-value="' + response.name + '"]').remove();
                    },
                    error: function(response) {
                        show_toastr('Error', response.error, 'error');
                    }
                });
            });

            $(document).on('click', 'input[name="theme_color"]', function() {
                var eleParent = $(this).attr('data-theme');
                $('#themefile').val(eleParent);
                // $('#themefile').val($(this).attr('data-key'));
                var imgpath = $(this).attr('data-imgpath');
                $('.' + eleParent + '_img').attr('src', imgpath);
            });

            $(document).ready(function() {
                setTimeout(function(e) {
                    var checked = $("input[type=radio][name='theme_color']:checked");
                    $('#themefile').val(checked.attr('data-theme'));
                    // $('#themefile').val(checked.attr('data-key'));
                    $('.' + checked.attr('data-theme') + '_img').attr('src', checked.attr('data-imgpath'));
                }, 300);
            });

            $(".color1").click(function() {
                var dataId = $(this).attr("data-id");
                $('#' + dataId).trigger('click');
                var first_check = $('#' + dataId).find('.color-0').trigger("click");
            });


            $(document).on("change", "select[name='certificate_template'], input[name='certificate_color']", function() {
                var template = $("select[name='certificate_template']").val();
                var color = $("input[name='certificate_color']:checked").val();
                var gradiant = $(this).data('gradiant');
                $('#gradiant').val(gradiant);
                $('#certificate_frame').attr('src', '{{ url('/certificate/preview') }}/' + template + '/' + color +
                    '/' + gradiant);
            });
        </script>

        <script>
            var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                target: '#useradd-sidenav',
                offset: 300,
            })
            $(".list-group-item").click(function() {
                $('.list-group-item').filter(function() {
                    // return this.href == id;
                }).parent().removeClass('text-primary');
            });
        </script>
        <script>
            function check_theme(color_val) {
                $('input[value="' + color_val + '"]').prop('checked', true);
                $('input[value="' + color_val + '"]').attr('checked', true);
                $('a[data-value]').removeClass('active_color');
                $('a[data-value="' + color_val + '"]').addClass('active_color');
            }
            var scrollSpy = new bootstrap.ScrollSpy(document.body, {
                target: '#useradd-sidenav',
                offset: 300
            })
        </script>
        {{-- <script>
        function check_theme(color_val) {
            $('.theme-color').prop('checked', false);
            $('input[value="' + color_val + '"]').prop('checked', true);
        }
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
        </script> --}}


        <script>

            $(document).on('change', '[name=storage_setting]', function() {
                if ($(this).val() == 's3') {
                    $('.s3-setting').removeClass('d-none');
                    $('.wasabi-setting').addClass('d-none');
                    $('.local-setting').addClass('d-none');
                } else if ($(this).val() == 'wasabi') {
                    $('.s3-setting').addClass('d-none');
                    $('.wasabi-setting').removeClass('d-none');
                    $('.local-setting').addClass('d-none');
                } else {
                    $('.s3-setting').addClass('d-none');
                    $('.wasabi-setting').addClass('d-none');
                    $('.local-setting').removeClass('d-none');
                }
            });
        </script>
        <script>
        @if (Auth::user()->type == 'super admin')
            var multipleCancelButton = new Choices(
                '#choices-multiple-remove-button', {
                    removeItemButton: true,
                }
            );
            var multipleCancelButton = new Choices(
                '#choices-multiple-remove-button1', {
                    removeItemButton: true,
                }
            );
            var multipleCancelButton = new Choices(
                '#choices-multiple-remove-button2', {
                    removeItemButton: true,
                }
            );
        @endif
        </script>

        <script src="{{ asset('libs/summernote/summernote-bs4.js') }}"></script>
        <script type="text/javascript">
            function enablecookie() {
                const element = $('#enable_cookie').is(':checked');
                $('.cookieDiv').addClass('disabledCookie');
                if (element==true) {
                    $('.cookieDiv').removeClass('disabledCookie');
                    $("#cookie_logging").attr('checked', true);
                } else {
                    $('.cookieDiv').addClass('disabledCookie');
                    $("#cookie_logging").attr('checked', false);
                }
            }
        </script>
        <script>
            $(document).on("click", '.send_email', function(e) {
                e.preventDefault();
                var title = $(this).attr('data-title');

                var size = 'md';
                var url = $(this).attr('data-url');
                if (typeof url != 'undefined') {
                    $("#commonModal .modal-title").html(title);
                    $("#commonModal .modal-dialog").addClass('modal-' + size);
                    $("#commonModal").modal('show');

                    $.post(url, {
                        _token:'{{csrf_token()}}',
                        mail_driver: $("#mail_driver").val(),
                        mail_host: $("#mail_host").val(),
                        mail_port: $("#mail_port").val(),
                        mail_username: $("#mail_username").val(),
                        mail_password: $("#mail_password").val(),
                        mail_encryption: $("#mail_encryption").val(),
                        mail_from_address: $("#mail_from_address").val(),
                        mail_from_name: $("#mail_from_name").val(),
                    }, function(data) {
                        $('#commonModal .modal-body').html(data);
                    });
                }
            });

            $(document).on('submit', '#test_email', function(e) {
                e.preventDefault();
                $("#email_sending").show();
                var post = $(this).serialize();
                var url = $(this).attr('action');
                $.ajax({
                    type: "post",
                    url: url,
                    data: post,
                    cache: false,
                    beforeSend: function() {
                        $('#test_email .btn-create').attr('disabled', 'disabled');
                    },
                    success: function(data) {
                        if (data.is_success) {
                            show_toastr('success', data.message, 'success');
                        } else {
                            show_toastr('error', data.message, 'error');
                        }
                        $("#email_sending").hide();
                        $('#commonModal').modal('hide');
                    },
                    complete: function() {
                        $('#test_email .btn-create').removeAttr('disabled');
                    },
                });
            });
        </script>

    @endpush

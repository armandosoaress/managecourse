@php
    $setting = App\Models\Utility::colorset();
    $color = (!empty($setting['color'])) ? $setting['color'] : 'theme-3';
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_logo = \App\Models\Utility::GetLogo();
    $settings = App\Models\Utility::settings();
    $footer_text = isset(Utility::settings()['footer_text']) ? Utility::settings()['footer_text'] : '';
    if($setting['cust_darklayout'] == 'on'){
        $company_logo = 'logo-light.png';
    }else{
        $company_logo = 'logo-dark.png';
    }
    $lang = \App::getLocale('lang');
    if($lang == 'ar' || $lang == 'he'){
        $setting['SITE_RTL'] = 'on';
    }
@endphp


<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $setting['SITE_RTL'] == 'on'?'rtl':''}}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="LMSGo - Learning Management System">
    <meta name="keywords" content="Dashboard Template"/>
    <meta name="author" content="Rajodiya Infotech">

    <title> {{( Utility::getValByName('title_text')) ? Utility::getValByName('title_text') : config('app.name', 'LMSGo-SaaS')}} - @yield('page-title') </title>
    <!-- Favicon -->
    <link rel="icon" href="{{\App\Models\Utility::get_file('uploads/logo/').'/favicon.png'.'?'. time()}}" type="image/png">

    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">

    <!-- vendor css -->

    <!-- vendor css -->
    @if($setting['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
    @endif
    @if( isset($setting['cust_darklayout']) && $setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @endif

    @if(isset($setting['SITE_RTL']) && $setting['SITE_RTL'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth-rtl.css')}}" id="main-style-link">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth.css')}}" id="main-style-link">
    @endif
    @if($setting['cust_darklayout']=='on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth-dark.css')}}" id="main-style-link">
    @endif
    @if ($setting['cust_darklayout'] == 'on')
        <style>
            .g-recaptcha {
                filter: invert(1) hue-rotate(180deg) !important;
            }
        </style>
    @endif

</head>
<body class="{{ $color }}">
    <div class="custom-login">
        <div class="login-bg-img">
            <img src="{{ asset('assets/images/auth/'.$color.'.svg') }}" class="login-bg-1">
            <img src="{{ asset('assets/images/auth/common.svg') }}" class="login-bg-2">
        </div>
        <div class="bg-login bg-primary"></div>
        <div class="custom-login-inner">
            <header class="dash-header">
                <nav class="navbar navbar-expand-md default">
                    <div class="container">
                        <div class="navbar-brand">
                            <a href="#">
                                <img src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png').'?'. time() }}" class="logo" alt="logo" loading="lazy" style="width: 150px;" />
                            </a>
                        </div>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarlogin">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarlogin" style="flex-grow: 0;">
                            <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0">
                                @include('landingpage::layouts.buttons')
                                @yield('language-bar')
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>
            <main class="custom-wrapper">
                <div class="custom-row">
                    <div class="card">
                        @yield('content')
                    </div>
                </div>
            </main>
            <footer>
                <div class="auth-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <span>&copy; {{date('Y')}} {{ (App\Models\Utility::getValByName('footer_text')) ? App\Models\Utility::getValByName('footer_text') :config('app.name', 'LmsGo Saas') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>

<!-- Required Js -->
<script src="{{ asset('assets/js/vendor-all.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script>
    feather.replace();
</script>
<script>
    feather.replace();
    var pctoggle = document.querySelector("#pct-toggler");
    if (pctoggle) {
        pctoggle.addEventListener("click", function() {
            if (
                !document.querySelector(".pct-customizer").classList.contains("active")
            ) {
                document.querySelector(".pct-customizer").classList.add("active");
            } else {
                document.querySelector(".pct-customizer").classList.remove("active");
            }
        });
    }
    function removeClassByPrefix(node, prefix) {
        for (let i = 0; i < node.classList.length; i++) {
            let value = node.classList[i];
            if (value.startsWith(prefix)) {
                node.classList.remove(value);
            }
        }
    }
</script>

<script src="{{ asset('js/site.core.js')}}"></script>
<script src="{{ asset('js/site.js')}}"></script>
{{-- <script src="{{ asset('js/site.core.js')}}"></script>
<script src="{{ asset('assets/js/site.js')}}"></script>
<script src="{{ asset('assets/js/demo.js')}}"></script> --}}

@stack('custom-scripts')
@if ($settings['enable_cookie'] == 'on')
        @include('layouts.cookie_consent')
@endif
</html>

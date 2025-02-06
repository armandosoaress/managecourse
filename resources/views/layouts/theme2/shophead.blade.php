<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">

    @yield('meta-data')

    <title>@yield('page-title')</title>

    <!-- Preloader -->
    <style>

    </style>
    @if(env('SITE_RTL')=='on')
        <link rel="stylesheet" href="{{ asset('css/bootstrap-rtl.css') }}">
    @endif
    @stack('css-page')
<!-- Favicon -->
    <link rel="icon" href="{{\App\Models\Utility::get_file('uploads/logo/').(!empty($settings->value)?$settings->value:'favicon.png').'?'. time()}}" type="image/png">
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{asset('libs/@fancyapps/fancybox/dist/jquery.fancybox.min.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('libs/@fortawesome/fontawesome-free/css/all.min.css')}}">
    <!-- Quick CSS -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="{{asset('libs/@fortawesome/fontawesome-free/css/all.min.css')}}"><!-- Page CSS -->
    <link rel="stylesheet" href="{{asset('libs/animate.css/animate.min.css')}}">
    <link rel="stylesheet" href="{{asset('libs/swiper/dist/css/swiper.min.css')}}">
    <!-- site CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.3.3.5.css')}}">


    {{-- <link rel="shortcut icon" href="assets/images/favicon.png"> --}}
    @if(!empty($store->store_theme))
        <link rel="stylesheet" href="{{asset('assets/themes/theme2/css/'.$store->store_theme)}}" id="stylesheet">
    @else
        <link rel="stylesheet" href="{{asset('assets/themes/theme2/css/dark-blue-color.css')}}" id="stylesheet">
    @endif


    {{-- <link rel="stylesheet" href="{{ asset('assets/themes/theme2/css/main-style.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('assets/themes/theme2/css/responsive.css') }}">

</head>

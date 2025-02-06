@extends('layouts.auth')
@php
    use App\Models\Utility;
    $logo=Utility::get_file('uploads/logo/');
    $company_logo=Utility::getValByName('company_logo');
    $settings = Utility::settings();
    if(empty($lang))
    {
        $lang = Utility::getValByName('default_language');

    }
    $languages = App\Models\Utility::languages();
@endphp
@push('custom-scripts')
    @if($settings['recaptcha_module'] == 'yes')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush
@section('page-title')
    {{__('Login')}}
@endsection

@section('language-bar')
    <div class="lang-dropdown-only-desk">
        <li class="dropdown dash-h-item drp-language">
            <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="drp-text"> {{ ucFirst($languages[$lang]) }}
                </span>
            </a>
            <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                @foreach(Utility::languages() as $code => $language)
                    <a href="{{ url('/verify-email',$code) }}" tabindex="0" class="dropdown-item {{ $code == $lang ? 'active':'' }}">
                        <span>{{ ucFirst($language)}}</span>
                    </a>
                @endforeach
            </div>
        </li>
    </div>
@endsection

@section('content')
    <div class="card-body">
        <div class="">
            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 font-medium text-sm text-green-600 text-primary">
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </div>
            @endif
            <div class="text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </div>
        </div>

        <div class="d-grid">
            <div class="row">
                <div class="col-auto">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary mt-2 btn-sm">{{ __('Resend Verification Email') }}
                        </button>
                    </form>
                </div>
                <div class="col-auto">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                    <button type="submit" class="btn btn-danger btn-sm mt-2">{{ __('Logout') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

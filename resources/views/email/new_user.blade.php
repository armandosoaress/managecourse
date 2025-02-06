@component('mail::message')

{{ __('Hello') }},<br>

{{__('Welcome to ')}}{{ config('app.name') }}

{{ __('Email') }} : {{$user->email}}

{{ __('Password') }} : {{$password}}


<a href="{{env('APP_URL')}}">{{env('APP_URL')}}</a>

{{ __('Thanks') }},<br>
{{ config('app.name') }}
@endcomponent

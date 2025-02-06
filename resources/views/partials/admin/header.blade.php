@php
$logo = \App\Models\Utility::get_file('uploads/logo/');
// $company_logo = Utility::getValByName('company_logo');
$company_logo = \App\Models\Utility::GetLogo();
$user = \Auth::user();
$plan = $user->currentPlan;

$profile=\App\Models\Utility::get_file('uploads/profile/');
$current_store = \Auth::user()->currentstore();

if ($currantLang == null) {
    $currantLang = 'en';
}
$LangName = $user->currentlang;
@endphp

@if (isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on')
    <header class="dash-header transprent-bg">
@else
    <header class="dash-header">
@endif
    <div class="header-wrapper">
        <div class="me-auto dash-mob-drp">
            <ul class="list-unstyled">
                <li class="dash-h-item mob-hamburger">
                    <a href="#!" class="dash-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger--arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>
                <li class="dropdown dash-h-item drp-company">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        {{-- <span class="theme-avtar">c</span> --}}
                        <span class="theme-avtar"><img alt="Image placeholder"  style="width:30px;"
                                src="{{ !empty($users->avatar) ? $profile . $users->avatar : $profile . '/avatar.png' }}"></span>
                        <span class="hide-mob ms-2">{{__('Hi')}}, {{\Auth::user()->name}}!</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>

                    <div class="dropdown-menu dash-h-dropdown">
                        <a href="{{ route('profile') }}" class="dropdown-item">
                            <i class="ti ti-user"></i>
                            <span>{{ __('My profile') }}</span>
                        </a>

                        <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
                            <i class="ti ti-power"></i>
                            <span>{{ __('Logout') }}</span>
                        </a>
                        <form id="frm-logout" action="{{ route('logout') }}" method="POST" class="d-none">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </li>
            </ul>
        </div>
        <div class="ms-auto">
            <ul class="list-unstyled">
                @impersonating($guard = null)
                    <li class="dropdown dash-h-item drp-company">
                        <a class="btn btn-danger btn-sm me-3" href="{{ route('exit.company') }}"><i class="ti ti-ban"></i>
                            {{ __('Exit Company Login') }}
                        </a>
                    </li>
                @endImpersonating
                @auth('web')
                    @if(Auth::user()->type != 'super admin')
                        <li class="dropdown dash-h-item drp-language">
                            @can('create store')
                                <a href="#" data-size="xl" data-url="{{ route('store-resource.create') }}" data-ajax-popup="true" data-title="{{__('Create New Store')}}" class="dash-head-link dropdown-toggle arrow-none me-0 cust-btn">
                                    <i class="ti ti-circle-plus"></i><span class="hide-mob">{{ __('Create New Store')}}</span>
                                </a>
                            @endcan
                        </li>
                    @endif
                @endauth
                @if(Auth::user()->type != 'super admin')
                    <li class="dropdown dash-h-item drp-language">
                        <a class="dash-head-link dropdown-toggle arrow-none me-0 cust-btn"
                        data-bs-toggle="dropdown"
                        href="#"
                        role="button"
                        aria-haspopup="false"
                        aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="bottom"   data-bs-original-title="Select your bussiness">
                        <i class="ti ti-building-store"></i>
                        <span class="hide-mob">{{__(ucfirst($current_store->name))}}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                        </a>
                        <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                            @php
                                $user = \Auth::user()->currentuser();
                            @endphp
                            @foreach($user->stores as $store)
                                @if($store->is_active)
                                    <a href="@if(Auth::user()->current_store == $store->id)#@else {{ route('change_store',$store->id) }} @endif" title="{{ $store->name }}" class="dropdown-item">
                                        @if(Auth::user()->current_store == $store->id)
                                            <i class="ti ti-checks text-primary"></i>
                                        @endif
                                        <span>{{ $store->name }}</span>
                                    </a>
                                @else
                                    <a href="#" class="dropdown-item" title="{{__('Locked')}}">
                                        <i class="ti ti-lock"></i>
                                        <span>{{ $store->name }}</span>
                                        @if(isset($store->pivot->permission))
                                            @if($store->pivot->permission =='Owner')
                                                <span class="badge bg-primary">{{__($store->pivot->permission)}}</span>
                                            @else
                                                <span class="badge bg-secondary">{{__('Shared')}}</span>
                                            @endif
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                            <div class="dropdown-divider m-0"></div>
                        </div>
                    </li>
                @endif
                <li class="dropdown dash-h-item drp-language">

                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-world nocolor"></i>
                        <span class="drp-text hide-mob">{{ucFirst($LangName->fullName)}}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        @foreach (App\Models\Utility::languages() as $code => $lang)
                            <a href="{{ route('change.language', $code) }}"
                                class="dropdown-item {{ $currantLang == $code ? 'text-primary' : '' }}">
                                <span>{{ucFirst($lang)}}</span>
                            </a>
                        @endforeach
                        @can('create language')
                            <a href="#" class="dropdown-item py-1 text-primary" data-ajax-popup="true" data-size="md" data-title="{{ __('Create Language') }}" data-url="{{ route('create.language') }}">
                                {{__('Create Language')}}
                            </a>
                        @endcan
                        @if(Auth::user()->type == 'super admin')
                            <a href="{{route('manage.language',[$currantLang])}}" class="dropdown-item py-1 text-primary">
                                {{ __('Manage Language') }}
                            </a>
                        @endif
                    </div>
                </li>

            </ul>
        </div>
    </div>
</header>



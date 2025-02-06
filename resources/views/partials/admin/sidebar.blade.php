@php
    $logo=\App\Models\Utility::get_file('uploads/logo/');
    $company_logo = \App\Models\Utility::GetLogo();
    if(\Auth::user()->type!='Owner' && \Auth::user()->type!='super admin'){
        $user = \App\Models\User::where('id',\Auth::user()->created_by)->first();
    }
    else {
        $user = \Auth::user();
    }
    $plan =$user->currentPlan;
@endphp

@if (isset($setting['cust_theme_bg']) && $setting['cust_theme_bg'] == 'on')
    <nav class="dash-sidebar light-sidebar transprent-bg">
@else
    <nav class="dash-sidebar light-sidebar">
@endif

    <div class="navbar-wrapper">
        <div class="m-header  main-logo">
            <a href="{{ route('dashboard') }}" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                <img src="{{ $logo . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png') .'?'. time()}}"
                    alt="{{ config('app.name', 'LMSGo SaaS') }}" class="logo logo-lg nav-sidebar-logo" />

                    {{-- <img src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png') }}"  alt="{{ config('app.name', 'Storego') }}" alt="logo" class="logo logo-lg nav-sidebar-logo"> --}}
            </a>
        </div>
        <div class="navbar-content">
            <ul class="dash-navbar">
                @if(Auth::user()->type == 'super admin')

                    @can('manage dashboard')
                        <li class="dash-item {{ (\Request::route()->getName()=='dashboard') ? ' active' : '' }}">
                            <a href="{{route('dashboard')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-home"></i></span><span class="dash-mtext">{{__('Dashboard')}}</span></a>
                        </li>
                    @endcan

                    @can('manage store')
                        <li class="dash-item {{ (\Request::route()->getName()=='store-resource.index' || \Request::route()->getName()=='store.grid' || \Request::route()->getName()=='store.subDomain' || \Request::route()->getName()=='store.customDomain') ? ' active' : '' }}">
                            <a href="{{route('store-resource.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-shopping-cart"></i></span><span class="dash-mtext">{{__('Stores')}}</span></a>
                        </li>
                    @endcan

                    @can('manage coupon')
                        <li class="dash-item {{ (\Request::route()->getName()=='coupons.index' || \Request::route()->getName()=='coupons.show')  ? ' active' : '' }}">
                            <a href="{{ route('coupons.index') }}" class="dash-link"><span class="dash-micon"><i class="ti ti-discount-2"></i></span><span class="dash-mtext">{{ __('Coupons') }}</span></a>
                        </li>
                    @endcan

                    @can('manage plan')
                        <li class="dash-item {{ (\Request::route()->getName()=='plans.index' || \Request::route()->getName()=='stripe') ? ' active' : '' }}">
                            <a href="{{ route('plans.index') }}" class="dash-link"><span class="dash-micon"><i class="ti ti-award"></i></span><span class="dash-mtext">{{ __('Plans') }}</span></a>
                        </li>
                    @endcan

                    @can('manage plan request')
                        <li class="dash-item {{ (\Request::route()->getName()=='plan_request.index') ? ' active' : '' }}">
                            <a href="{{ route('plan_request.index') }}" class="dash-link {{ request()->is('plan_request*') ? 'active' : '' }}"><span class="dash-micon"><i class="ti ti-brand-telegram"></i></span><span class="dash-mtext">{{__('Plan Request')}}</span></a>
                        </li>
                    @endcan

                    @include('landingpage::menu.landingpage')

                    @can('manage settings')
                        <li class="dash-item {{ (\Request::route()->getName()=='setting.index' || \Request::route()->getName()=='store.editproducts') ? ' active' : '' }}">
                            <a href="{{ route('settings') }}" class="dash-link"><span class="dash-micon"><i class="ti ti-settings"></i></span><span class="dash-mtext">
                                    {{__('Settings')}}
                            </span></a>
                        </li>
                    @endcan

                @else
                    @if( Gate::check('manage dashboard') || Gate::check('manage store analytics') || Gate::check('manage order') || Gate::check('show order'))
                        <li class="dash-item dash-hasmenu {{ (\Request::route()->getName()=='orders.index' || \Request::route()->getName()=='orders.show') ? ' active dash-trigger' : '' }}">
                            <a href="#!" class="dash-link"><span class="dash-micon"><i class="ti ti-home"></i></span><span class="dash-mtext">{{__('Dashboard')}}</span><span class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage dashboard')
                                    <li class="dash-item">
                                        <a class="dash-link" href="{{route('dashboard')}}">{{ __('Dashboard') }}</a>
                                    </li>
                                @endcan
                                @can('manage store analytics')
                                    <li class="dash-item">
                                        <a class="dash-link" href="{{route('storeanalytic')}}">{{ __('Store Analytics') }}</a>
                                    </li>
                                @endcan
                                @can('manage order')
                                    <li class="dash-item {{ (\Request::route()->getName()=='orders.index' || \Request::route()->getName()=='orders.show') ? ' active' : '' }}">
                                        <a class="dash-link" href="{{route('orders.index')}}">{{ __('Orders') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endif

                    @can('manage store settings')
                        <li class="dash-item dash-hasmenu {{ (Request::segment(1) == 'theme.index' || \Request::route()->getName()=='store.editproducts')  ? ' active dash-trigger ' : 'collapsed' }}">
                            <a href="{{route('theme.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-layout-2"></i></span><span class="dash-mtext">{{__('Themes')}}</span></a>
                        </li>
                    @endcan

                    @if( Gate::check('manage user') || Gate::check('manage role'))
                        <li class="dash-item dash-hasmenu  {{ (Request::segment(1) == 'users' || Request::segment(1) == 'users-logs'|| Request::segment(1) == 'roles' || Request::segment(1) == 'permissions' )?' active dash-trigger':''}}">
                            <a href="#!" class="dash-link "><span class="dash-micon"><i class="ti ti-users"></i></span><span class="dash-mtext">{{__('Staff')}}</span>
                                <span class="dash-arrow"><i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="dash-submenu {{ ( Request::segment(1) == 'roles' || Request::segment(1) == 'permissions')?'show':''}}">
                                @can('manage user')
                                    <li class="dash-item {{ (Request::route()->getName() == 'users.index' || Request::route()->getName() == 'users.create' || Request::route()->getName() == 'users.edit' || Request::route()->getName() == 'users.logs' || Request::route()->getName() == 'userslog.view' || Request::route()->getName() == 'userslog.destroy') ? ' active' : '' }}">
                                        <a class="dash-link" href="{{ route('users.index') }}">{{__('User')}}</a>
                                    </li>
                                @endcan
                                @can('manage role')
                                    <li class="dash-item {{ (Request::route()->getName() == 'roles.index' || Request::route()->getName() == 'roles.create' || Request::route()->getName() == 'roles.edit') ? ' active' : '' }}">
                                        <a class="dash-link" href="{{route('roles.index')}}">{{ __('Role') }}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endif
                    @if( Gate::check('manage course') ||  Gate::check('manage category') ||   Gate::check('manage subcategory') ||  Gate::check('manage custom page') ||  Gate::check('manage blog') ||  Gate::check('manage subscriber') ||  Gate::check('manage course coupon'))
                        <li class="dash-item dash-hasmenu {{ (\Request::route()->getName()=='course.index' || \Request::route()->getName()=='course.create' || \Request::route()->getName()=='course.edit' || \Request::route()->getName()=='chapters.create' || \Request::route()->getName()=='chapters.edit' || \Request::route()->getName()=='category.index' || \Request::route()->getName()=='subcategory.index' || \Request::route()->getName()=='custom-page.index' || \Request::route()->getName()=='blog.index' || \Request::route()->getName()=='subscriptions.index' || \Request::route()->getName()=='product-coupon.index' || \Request::route()->getName()=='product-coupon.show') ? 'active dash-trigger' : '' }}">
                            <a href="#!" class="dash-link"><span class="dash-micon"><i class="ti ti-license"></i></span><span class="dash-mtext">{{__('Shop')}}</span><span class="dash-arrow"><i data-feather="chevron-right"></i></span></a>
                            <ul class="dash-submenu">
                                @can('manage course')
                                <li class="dash-item  {{ (\Request::route()->getName()=='course.index' || \Request::route()->getName()=='course.create' || \Request::route()->getName()=='course.edit' || \Request::route()->getName()=='chapters.create' || \Request::route()->getName()=='chapters.edit') ? ' active' : '' }}">
                                    <a class="dash-link" href="{{route('course.index')}}"> {{__('Course')}}</a>
                                </li>
                                @endcan
                                @can('manage category')
                                    <li class="dash-item">
                                        <a class="dash-link" href="{{route('category.index')}}">{{__('Category')}}</a>
                                    </li>
                                @endcan

                                @can('manage subcategory')
                                    <li class="dash-item">
                                        <a class="dash-link" href="{{route('subcategory.index')}}">{{__('Subcategory')}}</a>
                                    </li>
                                @endcan

                                @if ($plan->additional_page == 'on')
                                    @can('manage custom page')
                                        <li class="dash-item">
                                            <a class="dash-link" href="{{route('custom-page.index')}}">{{__('Custom Page')}}</a>
                                        </li>
                                    @endcan
                                @endif

                                @if ($plan->blog == 'on')
                                    @can('manage blog')
                                        <li class="dash-item">
                                            <a class="dash-link" href="{{route('blog.index')}}">{{__('Blog')}}</a>
                                        </li>
                                    @endcan
                                @endif

                                @can('manage subscriber')
                                    <li class="dash-item">
                                        <a class="dash-link" href="{{route('subscriptions.index')}}"> {{__('Subscriber')}}</a>
                                    </li>
                                @endcan

                                @can('manage course coupon')
                                    <li class="dash-item  {{ (\Request::route()->getName()=='product-coupon.index' || \Request::route()->getName()=='product-coupon.show') ? ' active' : '' }}">
                                        <a class="dash-link" href="{{route('product-coupon.index')}}">{{__('Coupons')}}</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endif



                    @can('manage student')
                        <li class="dash-item dash-hasmenu {{ Request::segment(1) == 'student.index' || \Request::route()->getName()=='student.logs' || Request::route()->getName() == 'student.show'  ? ' active dash-trigger ' : 'collapsed' }}">
                            <a href="{{route('student.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-user"></i></span><span class="dash-mtext">{{__('Student')}}</span></a>
                        </li>
                    @endcan


                    @can('manage plan')
                        <li class="dash-item {{ (\Request::route()->getName()=='plans.index' || \Request::route()->getName()=='stripe') ? ' active' : '' }}">
                            <a href="{{ route('plans.index') }}" class="dash-link"><span class="dash-micon"><i class="ti ti-award"></i></span><span class="dash-mtext">{{ __('Plans') }}</span></a>
                        </li>
                    @endcan

                    @if( \Auth::user()->type == 'Owner')
                        <li class="dash-item {{ (\Request::route()->getName()=='notification-templates.index') ? ' active' : '' }}">
                            <a href="{{route('notification-templates.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-notification"></i></span><span class="dash-mtext">{{__('Notification')}}</span></a>
                        </li>
                    @endif
                    @can('manage zoom meeting')
                        <li class="dash-item {{ (\Request::route()->getName()=='zoom-meeting.index' || \Request::route()->getName()=='zoom-meeting.calender') ? ' active' : '' }}">
                            <a href="{{route('zoom-meeting.index')}}" class="dash-link"><span class="dash-micon"><i class="ti ti-video"></i></span><span class="dash-mtext">{{__('Zoom Meeting')}}</span></a>
                        </li>
                    @endcan

                    @can('manage store settings')
                        <li class="dash-item {{ (\Request::route()->getName()=='setting.index' ) ? ' active' : '' }}">
                            <a href="{{ route('settings') }}" class="dash-link"><span class="dash-micon"><i class="ti ti-settings"></i></span><span class="dash-mtext">
                                    {{__('Store Settings')}}
                            </span></a>
                        </li>
                    @endcan

                @endif
            </ul>

        </div>
    </div>
</nav>






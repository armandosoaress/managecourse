@extends('layouts.admin')
@section('page-title')
    {{__('Store')}}
@endsection
@section('title')
    {{__('Store')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{__('Store')}}</li>
@endsection
@section('action-btn')
<div class="text-end align-items-end d-flex justify-content-end">
    <div class="btn btn-sm btn-primary btn-icon ms-1">
        <a href="{{ route('store.subDomain') }}" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Sub Domain')}}"><i class="ti ti-subtask text-white"></i></a>
        {{-- {{__('Sub Domain')}} --}}
    </div>
    <div class="btn btn-sm btn-primary btn-icon ms-1">
        <a href="{{ route('store.customDomain') }}" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Custom Domain')}}"><i class="ti ti-home-2 text-white"></i></a>
        {{-- {{__('Custom Domain')}} --}}
    </div>

    <div class="btn btn-sm btn-primary btn-icon ms-1">
        <a href="{{ route('store-resource.index') }}" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('List View')}}"><i class="ti ti-list text-white"></i></a>
        {{-- {{__('List view')}} --}}
    </div>
    @can('create store')
        <div class="btn btn-sm btn-primary btn-icon ms-1">
            <a href="#" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create New Store')}}" data-ajax-popup="true" data-size="md" data-title="{{__('Create New Store')}}" data-url="{{ route('store-resource.create') }}"><i class="ti ti-plus text-white"></i></a>
        </div>
    @endcan
</div>
@endsection
@section('filter')
@endsection
@section('content')
    @if(\Auth::user()->type = 'super admin')
        <div class="row">
            @foreach($users as $user)
                <div class="col-md-4 col-xxl-3">
                    <div class="card hover-shadow-lg">
                        <div class="card-header border-0 pb-0">
                            <div class="card-header-right">
                                <div class="btn-group card-option">
                                    @if($user->is_disable == 1)
                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                    @else
                                        <div class="btn">
                                            <i class="ti ti-lock"></i>
                                        </div>
                                    @endif
                                    <div class="dropdown-menu dropdown-menu-end" style="">
                                        @can('edit store')
                                            <a href="#" data-size="md" data-url="{{ route('user.edit',$user->id) }}" data-ajax-popup="true" data-size="md" data-title="{{__('Edit Store')}}" class="dropdown-item"><i class="ti ti-edit"></i>
                                                <span>{{ __('Edit') }}</span>
                                            </a>
                                        @endcan

                                        @can('upgrade plan')
                                            <a href="#" data-size="md" data-url="{{ route('plan.upgrade',$user->id) }}" data-title="{{__('Upgrade Plan')}}" data-ajax-popup="true" class="dropdown-item"><i class="ti ti-trophy"></i>
                                                <span>{{ __('Upgrade Plan') }}</span>
                                            </a>
                                        @endcan
                                        @if(Auth::user()->type == "super admin")
                                            <a href="{{ route('login.with.company',$user->id) }}" class="dropdown-item"
                                                data-bs-original-title="{{ __('Login As Company') }}">
                                                <i class="ti ti-replace"></i>
                                                <span> {{ __('Login As Company') }}</span>
                                            </a>
                                        @endif

                                        @can('reset password')
                                            <a href="#" class="dropdown-item" data-url="{{route('store.reset',\Crypt::encrypt($user->id))}}" data-ajax-popup="true" data-size="md" data-title="{{ __('Change Password') }}">
                                                <i class="ti ti-key"></i>
                                                <span>{{ __('Reset Password') }}</span>
                                            </a>
                                        @endcan

                                        @can('delete store')
                                            <a class="bs-pass-para dropdown-item trigger--fire-modal-1" href="#"
                                                data-title="{{ __('Delete') }}" data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="delete-form-{{ $user->id }}">
                                                <i class="ti ti-trash"></i><span>{{ __('Delete') }} </span>
                                            </a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['user.destroy', $user->id], 'id' => 'delete-form-' . $user->id]) !!}
                                        {!! Form::close() !!}
                                        @endcan


                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <div class="avatar-parent-child">
                                <img alt="" src="{{ \App\Models\Utility::get_file("uploads/profile/").'/'}}{{ !empty($user->avatar)?$user->avatar:'avatar.png' }}" class="img-fluid rounded-circle card-avatar">
                            </div>

                            <h5 class="h6 mt-4 mb-0"> {{$user->name}}</h5>
                            <a href="#" class="d-block text-sm text-muted my-4"> {{$user->email}}</a>

                            <div class="mt-4">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-6 text-center">
                                            <h6 class="px-2">{{$user->currentPlan->name}}</h6>
                                    </div>
                                    <div class="col-6 text-center Id ">
                                        <a href="#" data-url="{{route('company.info', $user->id)}}" data-size="lg" data-ajax-popup="true" class="btn btn-outline-primary" data-title="{{__('Company Info')}}">{{__('AdminHub')}}</a>
                                    </div>
                                    <div class="col-12">
                                        <hr class="my-3">
                                    </div>

                                </div>
                            </div>
                            <div class="card mb-0">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <h6 class="mb-0">{{$user->countCourses($user->id)}}</h6>
                                            <p class="text-muted text-sm mb-0">{{ __('Courses')}}</p>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="mb-0">{{$user->countStores($user->id)}}</h6>
                                            <p class="text-muted text-sm mb-0">{{ __('Stores')}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="row justify-content-between align-items-center">
                                    @php
                                        $plan_expire_date = !empty($user->plan_expire_date) ? $user->plan_expire_date :'';
                                        if($plan_expire_date == '0000-00-00'){
                                            $plan_expire_date = date('d-m-Y');
                                        }
                                    @endphp
                                    <div class="col-12 text-center">
                                        <span class="text-dark text-xs">{{__('Plan Expired :' )}}
                                            @if(!empty($user->plan))
                                                {{Utility::dateFormat($plan_expire_date)}}
                                            @else
                                                --
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="col-md-3">
                <a href="#" class="btn-addnew-project"  data-ajax-popup="true" data-size="md" data-title="{{ __('Create New Store') }}" data-url="{{route('store-resource.create')}}">
                    <div class="bg-primary proj-add-icon">
                        <i class="ti ti-plus"></i>
                    </div>
                    <h6 class="mt-4 mb-2">{{ __('New Store') }}</h6>
                    <p class="text-muted text-center">{{ __('Click here to add New Store') }}</p>
                </a>
            </div>

        </div>
    @endif

@endsection

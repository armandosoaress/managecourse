@extends('layouts.admin')
@section('page-title')
    {{ __('Users') }}
@endsection
@section('title')
    {{ __('Users') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Users') }}</li>
@endsection
@section('action-btn')
<div class="text-end align-items-end d-flex justify-content-end">
    @can('create user')
        <a href="#" class="btn btn-sm btn-primary btn-icon ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add User')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Add User')}}" data-url="{{route('users.create')}}"><i class="ti ti-plus text-white"></i>
        </a>

    @endcan
    @if(\Auth::user()->type == 'Owner')
        <a href="{{route('users.logs')}}" class="btn btn-primary btn-sm ms-1" data-bs-toggle="tooltip" data-bs-placement="top"  title="{{__('User Logs')}}"><i class="ti ti-user-check"></i>
        </a>
    @endif
</div>
@endsection
@section('filter')
@endsection
@php
    $logo=\App\Models\Utility::get_file('uploads/profile/');
@endphp
@section('content')
    <div class="row">
        @foreach ($users as $user)
            <div class="col-lg-3 col-sm-6 col-md-6">
                <div class="card text-center">
                    <div class="card-header border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <div class="badge p-2 px-3 rounded bg-primary">{{ ucfirst($user->type) }}</div>
                            </h6>
                        </div>
                        @if (Gate::check('edit user') || Gate::check('delete user'))
                            <div class="card-header-right">
                                <div class="btn-group card-option">
                                    @if($user->is_disable == 1)
                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                    @else
                                        <div class="btn">
                                            <i class="ti ti-lock"></i>
                                        </div>
                                    @endif
                                    <div class="dropdown-menu dropdown-menu-end">
                                        @can('edit user')
                                            <a href="#" class="dropdown-item" data-url="{{ route('users.edit', $user->id) }}" data-size="md" data-ajax-popup="true" data-title="{{ __('Edit User') }}">
                                                <i class="ti ti-edit"></i>
                                                <span>{{ __('Edit') }}</span>
                                            </a>
                                        @endcan
                                        @can('delete user')
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user->id], 'id' => 'delete-form-' . $user->id]) !!}
                                            <a href="#" class="bs-pass-para dropdown-item"
                                                data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="delete-form-{{ $user->id }}"
                                                data-title="{{ __('Delete') }}"><i class="ti ti-trash"></i>
                                                <span>{{ __('Delete') }}</span></a>
                                            {!! Form::close() !!}
                                        @endcan
                                        @can('reset password user')
                                            <a href="#" class="dropdown-item" data-url="{{ route('users.reset', \Crypt::encrypt($user->id)) }}" data-ajax-popup="true" data-size="md" data-title="{{ __('Change Password') }}">
                                                <i class="ti ti-key"></i>
                                                <span>{{ __('Reset Password') }}</span>
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="avatar" style=" height: 80px;">
                            <a href="{{ !empty($user->avatar) ?($logo . $user->avatar) :  $logo."avatar.png" }}" class="avatar avatar-lg rounded-circle" target="_blank">
                                <img src="{{ !empty($user->avatar) ? ($logo . $user->avatar) :  $logo."avatar.png" }}" class="" width="30px" alt="">
                            </a>
                        </div>
                        <h4 class="mt-2 text-primary">{{ $user->name }}</h4>
                        <small class="">{{ $user->email }}</small>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="col-md-3">
            @can('create user')
                <a class="btn-addnew-project" data-url="{{ route('users.create') }}" data-title="{{ __('Add User') }}" data-ajax-popup="true" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Create') }}">
                    <div class="bg-primary proj-add-icon">
                        <i class="ti ti-plus"></i>
                    </div>
                    <h6 class="mt-4 mb-2">{{ __('New User') }}</h6>
                    <p class="text-muted text-center">{{ __('Click here to add New User') }}</p>
                </a>
            @endcan
        </div>
    </div>

@endsection

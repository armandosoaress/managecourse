@extends('layouts.admin')
@section('page-title')
    {{__('Manage Roles')}}
@endsection
@section('title')
{{__('Manage Roles')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Role')}}</li>
@endsection


@section('action-btn')
    <div class="text-end align-items-end d-flex justify-content-end">
        @can('create role')
        <div class="btn btn-sm btn-primary btn-icon m-1">
            <a href="#" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Add Category')}}" data-ajax-popup="true" data-size="xl" data-title="{{__('Add Category')}}"  data-url="{{ route('roles.create') }}"><i class="ti ti-plus text-white"></i></a>
        </div>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        <table id="pc-dt-simple" class="table">
                            <thead>
                            <tr>
                                <th>{{__('Role')}} </th>
                                <th>{{__('Permissions')}} </th>
                                <th width="150">{{__('Action')}} </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($roles as $role)
                                <tr class="font-style">
                                    <td class="Role">{{ $role->name }}</td>
                                    <td class="Permission">
                                        @php
                                            $role_name =  $role->permissions()->pluck('name');
                                        @endphp
                                        @for($j=0;$j<count($role_name);$j++)
                                            <span class="badge rounded-pill bg-primary">{{$role_name[$j]}}</span>
                                        @endfor
                                    </td>
                                    <td class="Action">
                                        <span>
                                            @can('edit role')
                                                <div class="action-btn bg-info ms-2">
                                                    <a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{__('Edit')}}" data-url="{{ route('roles.edit',$role->id) }}" data-size="xl" data-ajax-popup="true"  data-original-title="{{__('Edit')}}" >
                                                        <i class="ti ti-edit text-white"></i>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('delete role')
                                                <div class="action-btn bg-danger ms-2">
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id],'id'=>'delete-form-'.$role->id]) !!}
                                                    <a href="#" class="mx-3 btn btn-sm  align-items-center show_confirm" data-bs-toggle="tooltip" title="{{__('Delete')}}" ><i class="ti ti-trash text-white text-white"></i></a>

                                                    {!! Form::close() !!}
                                                </div>
                                            @endcan
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')
@section('page-title')
    {{__('Student')}}
@endsection
@section('title')
    {{__('Student')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Student') }}</li>
@endsection
@section('action-btn')
<div class="text-end align-items-end d-flex justify-content-end">
    @can('export student')
        <a href="{{route('student.export')}}" class="btn btn-sm btn-primary btn-icon ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Export')}}"><i class="ti ti-file-export text-white"></i></a>
    @endcan
    @can('manage student logs')
        <a href="{{route('student.logs')}}" class="btn btn-primary btn-sm ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Student Logs')}}"><i class="ti ti-user-check"></i>
        </a>
    @endcan
</div>
@endsection
@section('content')

    <!-- Listing -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <!-- Table -->
                <div class="card-body table-border-style">
                    <div class="table-responsive overflow_hidden">
                        <table id="pc-dt-simple" class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('Student Avatar')}}</th>
                                    <th scope="col">{{ __('Name')}}</th>
                                    <th scope="col">{{ __('Email')}}</th>
                                    <th scope="col">{{ __('Phone No')}}</th>
                                    <th scope="col" class="text-center">{{ __('Action')}}</th>
                                </tr>
                            </thead>
                            @if(count($students) > 0 && !empty($students))
                                <tbody class="list">
                                    @foreach ($students as $student)
                                        <tr>
                                            <td>
                                                <a href="{{\App\Models\Utility::get_file('uploads/profile/'.$student->avatar)}}" target="_blank">
                                                    <img alt="Image placeholder" src="{{\App\Models\Utility::get_file('uploads/profile/'.$student->avatar)}}" class="" style="width: 40px;">
                                                </a>
                                            </td>
                                            <td>{{$student->name}}</td>
                                            <td>{{$student->email}}</td>
                                            <td>{{$student->phone_number}}</td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <!-- Actions -->
                                                    @can('show student')
                                                        <div class="actions ml-3">
                                                            <div class="action-btn bg-warning ms-2">
                                                                <a href="{{route('student.show',$student->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-bs-toggle="tooltip" title="{{ __('Details') }}"> <span class="text-white"> <i class="ti ti-eye"></i></span></a>
                                                            </div>
                                                        </div>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            @else
                                <tbody>
                                    <tr>
                                        <td colspan="7">
                                            <div class="text-center">
                                                <i class="fas fa-folder-open text-primary" style="font-size: 48px;"></i>
                                                <h2>{{__('Opps')}}...</h2>
                                                <h6>{{__('No data Found')}}. </h6>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            @endif

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

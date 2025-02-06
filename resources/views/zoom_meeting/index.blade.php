@extends('layouts.admin')
@section('page-title')
    {{__('Zoom Meeting')}}
@endsection
@push('css-page')
<link rel="stylesheet" type="text/css" href="{{asset('css/daterangepicker.css')}}">
@endpush
@section('title')
    {{__('Zoom Meeting')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Zoom Meeting') }}</li>
@endsection


@section('action-btn')
<div class="text-end align-items-end d-flex justify-content-end">
    @can('manage zoom meeting')
        <div class="btn btn-sm btn-primary btn-icon ms-1">
            <a href="{{route('zoom-meeting.calender')}}" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Calendar')}}"><i class="ti ti-calendar text-white"></i></a>
        </div>
    @endcan

    @can('create zoom meeting')
        <div class="btn btn-sm btn-primary btn-icon ms-1">
            <a href="#" class="" id="add-user" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Meeting')}}" data-ajax-popup="true" data-size="lg" data-title="{{__('Create Meeting')}}" data-url="{{route('zoom-meeting.create')}}"><i class="ti ti-plus text-white"></i></a>
        </div>
    @endcan
</div>
@endsection
@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive overflow_hidden">
                    <table id="pc-dt-simple" class="table">
                        <thead class="thead-light">
                            <tr>
                                <th> {{ __('TITLE') }} </th>
                                <th> {{ __('COURSE') }}  </th>
                                <th> {{ __('STUDENT') }}  </th>
                                <th> {{ __('MEETING TIME') }} </th>
                                <th> {{ __('DURATION') }} </th>
                                <th> {{ __('JOIN URL') }} </th>
                                <th> {{ __('STATUS') }} </th>
                                <th class="text-right"> {{__('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($meetings as $item)
                                <tr>
                                    <td>{{$item->title}}</td>
                                    <td>{{$item->getCourseInfo->title}}</td>
                                    <td>
                                        <div class="avatar-group">
                                            @foreach($item->students($item->student_id) as $projectUser)
                                                <a href="#" class="user-group1">
                                                    <img alt="" @if(!empty($users->avatar)) src="{{$profile.'/'.$projectUser->avatar}}" @else avatar="{{(!empty($projectUser) ? $projectUser->name:'')}}" @endif data-original-title="{{(!empty($projectUser)?$projectUser->name:'hello')}}" data-toggle="tooltip" data-original-title="{{(!empty($projectUser)?$projectUser->name:'')}}" class="">
                                                </a>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>{{$item->start_date}}</td>
                                    <td>{{$item->duration}} {{__("Minutes")}}</td>
                                    <td>
                                        @if($item->created_by == \Auth::user()->current_store && $item->checkDateTime())
                                        <a href="{{$item->start_url}}" target="_blank"> {{__('Start meeting')}} <i class="fas fa-external-link-square-alt "></i></a>
                                        @elseif($item->checkDateTime())
                                            <a href="{{$item->join_url}}" target="_blank"> {{__('Join meeting')}} <i class="fas fa-external-link-square-alt "></i></a>
                                        @else
                                            -
                                        @endif

                                    </td>
                                    <td>
                                        @if($item->checkDateTime())
                                            @if($item->status == 'waiting')
                                                <span class="badge bg-info p-2 px-3 rounded">{{ucfirst($item->status)}}</span>
                                            @else
                                                <span class="badge bg-success p-2 px-3 rounded">{{ucfirst($item->status)}}</span>
                                            @endif
                                        @else
                                            <span class="badge bg-danger p-2 px-3 rounded">{{__("End")}}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @can('delete zoom meeting')
                                            <div class="action-btn bg-danger ms-2">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['zoom-meeting.destroy', $item->id]]) !!}
                                                    <a href="#!" class="mx-3 btn btn-sm  align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                        <i class="ti ti-trash text-white"></i>
                                                    </a>
                                                {!! Form::close() !!}
                                            </div>
                                        @endcan
                                    </td>
                                </tr>
                                @empty

                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@push('script-page')
<script src="{{url('js/daterangepicker.js')}}"></script>
<script type="text/javascript">

$(document).on('change', '#course_id', function() {
    getStudents($(this).val());
});
// alert('hgjh');
function getStudents(id){

    $("#students-div").html('');
        $('#students-div').append('<select class="form-control" id="student_id" name="students[]"  multiple></select>');

    $.get("{{url('get-students')}}/"+id, function(data, status){

        var list = '';
        $('#student_id').empty();
        if(data.length > 0){
            list += "<option value=''>  </option>";
        }else{
            list += "<option value=''> {{__('No Students')}} </option>";
        }
        $.each(data, function(i, item) {

            list += "<option value='"+item.id+"'>"+item.name+"</option>"
        });
        $('#student_id').html(list);
        var multipleCancelButton = new Choices('#student_id', {
                        removeItemButton: true,
        });
    });
}
</script>
@endpush

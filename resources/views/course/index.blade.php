@extends('layouts.admin')
@section('page-title')
    {{__('Course')}}
@endsection
@section('title')
    {{__('Courses')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Course') }}</li>
@endsection
@section('action-btn')
<div class="text-end align-items-end d-flex justify-content-end">
    @can('create course')
        <div class="btn btn-sm btn-primary btn-icon mx-1">
            <a href="{{route('course.create')}}" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Course')}}"><i class="ti ti-plus text-white"></i></a>
        </div>
    @endcan

    @can('export course')
        <div class="btn btn-sm btn-primary btn-icon mx-1">
            <a href="{{route('course.export')}}" class="" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Export Course')}}"><i class="ti ti-file-export text-white"></i></a>
        </div>
    @endcan
</div>

@endsection
@section('filter')
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{asset('libs/summernote/summernote-bs4.css')}}">
@endpush
@push('script-page')
    <script src="{{asset('libs/summernote/summernote-bs4.js')}}"></script>

    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
@endpush
@section('content')
<div class="row">
    <div class="col-sm-12">
        <!-- Listing -->
        <div class="card">
            <!-- Card header -->
            <div class="card-header">
                <h5>{{__('All Courses')}}</h5>
            </div>
            <!-- Table -->
            <div class="col-lg-12 col-md-12">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table id="pc-dt-simple" class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('Thumbnail')}}</th>
                                    <th scope="col">{{ __('Type')}}</th>
                                    <th scope="col">{{ __('Title')}}</th>
                                    <th scope="col">{{ __('Category')}}</th>
                                    <th scope="col">{{ __('Status')}}</th>
                                    <th scope="col">{{ __('Chapters')}}</th>
                                    <th scope="col">{{ __('Enrolled')}}</th>
                                    <th scope="col">{{ __('Price')}}</th>
                                    <th scope="col">{{ __('Created at')}}</th>
                                    @if(Gate::check('edit course') ||  Gate::check('delete course'))
                                        <th scope="col" class="text-right">{{ __('Action')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            @if(!empty($courses) && count($courses) > 0)
                                <tbody class="list">
                                    @foreach($courses as $course)
                                        <tr>
                                            <td scope="row">
                                                <div class="media align-items-center">
                                                    <div>
                                                        @if(!empty($course->thumbnail))
                                                            <a href="{{\App\Models\Utility::get_file('uploads/thumbnail/'.$course->thumbnail)}}" target="_blank">
                                                                <img alt="Image placeholder" src="{{\App\Models\Utility::get_file('uploads/thumbnail/'.$course->thumbnail)}}" class="" style="width: 80px;">
                                                            </a>
                                                        @else
                                                            <a href="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" target="_blank">
                                                                <img alt="Image placeholder" src="{{asset(Storage::url('uploads/is_cover_image/default.jpg'))}}" class="" style="width: 80px;">
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{$course->type}}</td>
                                            <td>{{$course->title}}</td>
                                            <td>{{!empty($course->category_id)?$course->category_id->name:'-'}}</td>
                                            <td>{{$course->status}}</td>
                                            <td>{{!empty($course->chapter_count)?$course->chapter_count->count():'0'}}</td>
                                            <td>{{$course->student_count->count()}}</td>
                                            <td>{{ ($course->is_free == 'on')? 'Free' : $course->price}}</td>
                                            <td>{{ Utility::dateFormat( $course->created_at)}}</td>
                                            @if(Gate::check('edit course') ||  Gate::check('delete course'))
                                                <td class="text-right">
                                                    <!-- Actions -->
                                                    <div class="actions">
                                                        @can('edit course')
                                                            <div class="action-btn bg-info ms-2">
                                                                <a href="{{route('course.edit',\Illuminate\Support\Facades\Crypt::encrypt($course->id))}}" class="mx-3 btn btn-sm d-inline-flex align-items-center"
                                                                > <span class="text-white"> <i class="ti ti-edit"></i></span></a>
                                                            </div>
                                                        @endcan
                                                        @can('delete course')
                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['course.destroy', $course->id]]) !!}
                                                                    <a href="#!" class="mx-3 btn btn-sm  align-items-center show_confirm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete')}}">
                                                                        <i class="ti ti-trash text-white"></i>
                                                                    </a>
                                                                {!! Form::close() !!}
                                                            </div>
                                                        @endcan
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            @else
                                <tbody>
                                    <tr>
                                        <td colspan="10">
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
</div>
@endsection


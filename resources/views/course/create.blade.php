@extends('layouts.admin')
@section('page-title')
    {{__('Course')}}
@endsection
@section('title')
    {{__('Create Course')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('course.index') }}">{{ __('Course') }}</a></li>
    <li class="breadcrumb-item">{{ __('create') }}</li>
@endsection
@php
    $plansetting = App\Models\Utility::plansetting();
@endphp
@section('action-btn')
    @if($plansetting['enable_chatgpt'] && $plansetting['enable_chatgpt']=='on')
        <a href="#" class="btn btn-primary" data-size="lg" data-ajax-popup-over="true" data-url="{{ route('generate',['course_seo']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
            <i class="fas fa-robot"></i> {{ __('Generate With AI') }}
        </a>
    @endif
    <a href="{{ route('course.index') }}" class="btn btn-light-secondary">{{__('Cancel')}}</a>
    <input type="button" value="{{ __('Save') }}" class="btn btn-primary btn-submit" id="submit-all">
@endsection
@section('filter')
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{asset('libs/summernote/summernote-bs4.css')}}">
@endpush
@push('script-page')
    <script src="{{asset('libs/summernote/summernote-bs4.js')}}"></script>
    <!-- Switch -->
    <script>
        $('#submit-all').on('click',function(){
            $('#frmTarget').submit();
        });
    </script>
    <script>

        $(document).ready(function(){
            type();
            $(document).on('click', '.code', function(e){
                var type = $(this).val();
                if(type == 'Quiz') {
                    $('#quiz-content-1').removeClass('d-none');
                    $('#quiz-content-1').addClass('d-block');
                    $('#course-content-1').removeClass('d-block');
                    $('#course-content-1').addClass('d-none');
                    $('#course-content-2').removeClass('d-block');
                    $('#course-content-2').addClass('d-none');
                    $('#course-content-3').removeClass('d-block');
                    $('#course-content-3').addClass('d-none');
                    $('#course-content-4').removeClass('d-block');
                    $('#course-content-4').addClass('d-none');


                    $('#duration').removeClass('d-block');
                    $('#duration').addClass('d-none');

                } else {

                    $('#course-content-1').removeClass('d-none');
                    $('#course-content-1').addClass('d-block');
                    $('#course-content-2').removeClass('d-none');
                    $('#course-content-2').addClass('d-block');
                    $('#course-content-3').removeClass('d-none');
                    $('#course-content-3').addClass('d-block');
                    $('#course-content-4').removeClass('d-none');
                    $('#course-content-4').addClass('d-block');

                    $('#duration').removeClass('d-none');
                    $('#duration').addClass('d-block');

                    $('#quiz-content-1').removeClass('d-block');
                    $('#quiz-content-1').addClass('d-none');
                }
            });
            $(document).on('click', '#customSwitches', function() {
                if($(this).is(":checked"))
                {
                    $('#price').addClass('d-none');
                    $('#price').removeClass('d-block');
                    $('#discount-div').addClass('d-none');
                    $('#discount-div').removeClass('d-block');
                }else{
                    $('#price').addClass('d-block');
                    $('#price').removeClass('d-none');
                    $('#discount-div').addClass('d-block');
                    $('#discount-div').removeClass('d-none');
                }
            });
            $(document).on('click', '#customSwitches2', function() {
                if($(this).is(":checked"))
                {
                    $('#discount').addClass('d-block');
                    $('#discount').removeClass('d-none');
                }else{
                    $('#discount').addClass('d-none');
                    $('#discount').removeClass('d-block');
                }
            });

            function type() {
                if ($('#customSwitches3').is(":checked")) {
                    $('#preview_type').addClass('d-block');
                    $('#preview_type').removeClass('d-none');

                    preview();
                } else {
                    $('#preview_type').addClass('d-none');
                    $('#preview_type').removeClass('d-block');

                    $('#preview-iframe-div').addClass('d-none');
                    $('#preview-iframe-div').removeClass('d-block');

                    $('#preview-video-div').addClass('d-none');
                    $('#preview-video-div').removeClass('d-block');

                    $('#preview-image-div').addClass('d-none');
                    $('#preview-image-div').removeClass('d-block');

                }
            }
            $(document).on('click', '#customSwitches3', function () {
                if ($('#customSwitches3').is(":checked")) {
                    $('#preview_type').addClass('d-block');
                    $('#preview_type').removeClass('d-none');

                    preview();
                } else {
                    $('#preview_type').addClass('d-none');
                    $('#preview_type').removeClass('d-block');

                    $('#preview-iframe-div').addClass('d-none');
                    $('#preview-iframe-div').removeClass('d-block');

                    $('#preview-video-div').addClass('d-none');
                    $('#preview-video-div').removeClass('d-block');

                    $('#preview-image-div').addClass('d-none');
                    $('#preview-image-div').removeClass('d-block');

                }
            });
            function preview() {
                $("#preview_type").change(function () {
                    $(this).find("option:selected").each(function () {
                        var optionValue = $(this).attr("value");
                        if (optionValue == 'Image') {

                            $('#preview-image-div').removeClass('d-none');
                            $('#preview-image-div').addClass('d-block');

                            $('#preview-iframe-div').addClass('d-none');
                            $('#preview-iframe-div').removeClass('d-block');

                            $('#preview-video-div').addClass('d-none');
                            $('#preview-video-div').removeClass('d-block');

                        } else if (optionValue == 'iFrame') {

                            $('#preview-image-div').addClass('d-none');
                            $('#preview-image-div').removeClass('d-block');

                            $('#preview-iframe-div').removeClass('d-none');
                            $('#preview-iframe-div').addClass('d-block');

                            $('#preview-video-div').addClass('d-none');
                            $('#preview-video-div').removeClass('d-block');

                        } else if (optionValue == 'Video File') {

                            $('#preview-image-div').addClass('d-none');
                            $('#preview-image-div').removeClass('d-block');

                            $('#preview-iframe-div').addClass('d-none');
                            $('#preview-iframe-div').removeClass('d-block');

                            $('#preview-video-div').removeClass('d-none');
                            $('#preview-video-div').addClass('d-block');
                        }
                    });
                }).change();
            }
        });
    </script>
    <!-- Subcategory -->
    <script>
        $(document).on('change', '#category_id', function () {
            var category_id = $(this).val();
            $.ajax({
                url: '{{route('course.getsubcategory')}}',
                type: 'POST',
                data: {
                    "category_id": category_id, "_token": "{{ csrf_token() }}",
                },
                success: function (data) {
                    $('#subcategory-div').empty();

                    var subcategory_option = '<select class="form-control" name="subcategory[]" id="subcategory" placeholder="{{__('Select Subcategory')}}"  multiple>';
                     subcategory_option += '<option value="" disabled>{{__('Select Subcategory')}}</option>';
                    $.each(data, function (key, value) {
                        subcategory_option += '<option value="' + key + '">' + value + '</option>';
                    });
                    subcategory_option += '</select>';

                    $("#subcategory-div").append(subcategory_option);
                    var multipleCancelButton = new Choices('#subcategory', {
                        removeItemButton: true,
                    });
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            var multipleCancelButton = new Choices(
               '#subcategory', {
                   removeItemButton: true,
               }
           );
        });
    </script>

@endpush
@section('content')
    <div class="row">
        <div class="col-sm-12">
            {{ Form::open(array('route' => 'course.store','id'=>'frmTarget','method' =>'post','enctype'=>'multipart/form-data')) }}
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3  d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">{{ __('SEO') }}</h4>
                    </div>
                    <div class="card shadow-none border border-primary">
                        <div id="account_edit" class="tabs-card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {{Form::label('meta_keywords',__('Meta Keywords'),['class'=>'form-label'])}}
                                            {{Form::text('meta_keywords',null,array('class'=>'form-control font-style','required'=>'required'))}}
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {{Form::label('meta_description',__('Meta Description'),['class'=>'form-label'])}}
                                            <textarea class="form-control" name="meta_description" id="exampleFormControlTextarea5" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="form-file">
                                                <label for="meta_image" class="form-label">{{ __('Meta Image') }}</label>
                                                <input type="file" class="form-control mb-2" name="meta_image" id="meta_image" aria-label="file example" onchange="document.getElementById('blah3').src = window.URL.createObjectURL(this.files[0])">
                                                <img src="" id="blah3" width="25%"/>
                                                <div class="invalid-feedback">{{ __('invalid form file') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3  d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">{{ __('Create Course') }}</h4>
                    </div>
                    <div class="card shadow-none border border-primary">
                        <div id="account_edit" class="tabs-card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        {{Form::label('title',__('Topic Title'),['class'=>'form-label'])}}
                                        {{Form::text('title',null,array('class'=>'form-control font-style','required' => 'required'))}}
                                    </div>
                                    <div class="form-group col-md-12">
                                        {{Form::label('course_requirements',__('Course Requirements'),['class'=>'form-label'])}}
                                        <textarea class="form-control summernote" name="course_requirements" id="exampleFormControlTextarea1" rows="3"></textarea>
                                    </div>
                                    <div class="form-group col-md-12">
                                        {{Form::label('course_description',__('Course Description'),['class'=>'form-label'])}}
                                        <textarea class="form-control summernote" name="course_description" id="exampleFormControlTextarea2" rows="3"></textarea>
                                    </div>

                                    <div class="form-group col-md-6" id="course-content-2">
                                        {{Form::label('category',__('Select Category'),['class'=>'form-label'])}}
                                        <select class="form-control" name="category" id="category_id" placeholder="{{__('Select Category')}}" required>
                                            <option value="">{{__('Select Category')}}</option>
                                                @foreach($category as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6" id="course-content-3">

                                        {{Form::label('subcategory',__('Select Subcategory'),['class'=>'form-label'])}}
                                        <div id="subcategory-div">
                                            <select class="form-control" name="subcategory[]" id="subcategory" placeholder="{{__('Select Subcategory')}}"  multiple>
                                                <option value="">{{__('Select category first')}}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        {{Form::label('level',__('Select Level'),['class'=>'form-label'])}}
                                        {!! Form::select('level',$level,null,array('class'=>'form-control')) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{Form::label('lang',__('Language'),['class'=>'form-label'])}}
                                        {{Form::text('lang',null,array('class'=>'form-control font-style','required'=>'required'))}}
                                    </div>
                                    <div class="form-group col-md-6" id="duration">
                                        {{Form::label('duration',__('Duration'),['class'=>'form-label'])}}
                                        {{Form::text('duration',null,array('class'=>'form-control font-style'))}}
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <div class="form-file mb-2">
                                                <label for="thumbnail" class="form-label">{{ __('Upload Thumbnail') }}</label>
                                                <input type="file" class="form-control mb-2" name="thumbnail" id="thumbnail" aria-label="file example" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                                <img src="" id="blah" width="25%"/>
                                                <div class="invalid-feedback">{{ __('invalid form file') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="custom-control form-group    custom-switch">
                                                    <div class="form-check form-switch custom-control-inline">
                                                        <input type="checkbox" class="form-check-input" role="switch" id="customSwitches" name="is_free">
                                                        {{Form::label('customSwitches',__('This is free'),['class'=>'form-check-label'])}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="custom-control form-group custom-switch">
                                                    <div class="form-check form-switch custom-control-inline">
                                                        <input type="checkbox" class="form-check-input" role="switch" id="customSwitches2" name="has_discount">
                                                        {{Form::label('customSwitches2',__('Discount'),['class'=>'form-check-label'])}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="custom-control form-group custom-switch">
                                                    <div class="form-check form-switch custom-control-inline">
                                                        <input type="checkbox" class="form-check-input" role="switch" id="customSwitches4"  name="featured_course">
                                                        {{Form::label('customSwitches4',__('Featured Course'),['class'=>'form-check-label'])}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="custom-control form-group custom-switch">
                                                    <div class="form-check form-switch custom-control-inline">
                                                        <input type="checkbox" class="form-check-input" role="switch" id="customSwitches3"  name="is_preview">
                                                        {{Form::label('customSwitches3',__('Preview'),['class'=>'custom-control-label form-check-label'])}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6 ml-auto" id="price">
                                        {{Form::label('price',__('Price'),['class'=>'form-label'])}}
                                        {{Form::number('price',null,array('class'=>'form-control font-style'))}}
                                    </div>
                                    <div class="col-md-6" id="discount-div">
                                        <div class="form-group ml-auto d-none" id="discount">
                                            {{Form::label('discount',__('Discount'),['class'=>'form-label'])}}
                                            {{Form::number('discount',null,array('class'=>'form-control font-style'))}}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6  ml-auto d-none" id="preview_type">
                                        {{Form::label('preview_type',__('Preview Type'),['class'=>'form-label'])}}
                                        {{Form::select('preview_type',$preview_type,null,array('class'=>'form-control font-style','id'=>'preview_type'))}}
                                    </div>
                                    <div class="form-group col-lg-6 d-none" id="preview-video-div">
                                        <div class="form-group">
                                            <div class="choose-file">
                                                <label for="preview_video" class="form-label">{{ __('Preview Video') }}</label>
                                                <input type="file" class="form-control" name="preview_video" id="preview_video" >
                                                <div class="invalid-feedback">{{ __('invalid form file') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6  d-none" id="preview-iframe-div">
                                        {{Form::label('preview_iframe',__('Preview iFrame'),['class'=>'form-label'])}}
                                        {{Form::text('preview_iframe',null,array('class'=>'form-control font-style'))}}
                                    </div>
                                    <div class="form-group col-lg-6 d-none" id="preview-image-div">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="form-file">
                                                    <label for="preview_image" class="form-label">{{ __('Preview Image') }}</label>
                                                    <input type="file" class="form-control mb-2" name="preview_image" id="preview_image" aria-label="file example" onchange="document.getElementById('blah2').src = window.URL.createObjectURL(this.files[0])">
                                                    <img src="" id="blah2" width="25%"/>
                                                    <div class="invalid-feedback">{{ __('invalid form file') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection





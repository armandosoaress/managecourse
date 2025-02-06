@php
    $plansetting = App\Models\Utility::plansetting();
@endphp
{{Form::model($category,array('route' => array('category.update', $category->id), 'method' => 'PUT' , 'enctype'=>'multipart/form-data')) }}
<div class="row">
    @if($plansetting['enable_chatgpt'] && $plansetting['enable_chatgpt']=='on')
        <div class="text-end align-items-center justify-content-between">
            <a href="#" class="btn btn-primary btn-sm mx-1" data-size="md" data-ajax-popup-over="true" data-url="{{ route('generate',['category']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                <i class="fas fa-robot"></i> {{ __('Generate With AI') }}
            </a>
        </div>
    @endif
    <div class="form-group col-lg-6 col-md-6">
        {!! Form::label('name', __('Name'),['class'=>'form-label']) !!}
        {!! Form::text('name', null, ['class' => 'form-control','required' => 'required']) !!}
    </div>
    <div class="form-group col-lg-6">
        <div class="col-12">
            <div class="form-file mb-3">
                <label for="category_image" class="form-label">{{ __('Upload category_image') }}</label>
                <input type="file" class="form-control" name="category_image" id="category_image" aria-label="file example">
                <a @if($category->category_image) href="{{\App\Models\Utility::get_file('uploads/category_image/'.$category->category_image)}}" @else href="{{asset(Storage::url('uploads/category_image/default.png'))}}" @endif target="_blank">
                    <img @if($category->category_image) src="{{\App\Models\Utility::get_file('uploads/category_image/'.$category->category_image)}}" @else src="{{asset(Storage::url('uploads/category_image/default.png'))}}" @endif name="category_image" id="category_image"  alt="user-image" class="avatar avatar-lg mt-3">
                </a>
                <div class="invalid-feedback">{{ __('invalid form file') }}</div>
            </div>
        </div>
    </div>
    <div class="form-group col-md-12">
        {{Form::label('description',__('Description'),array('class'=>'form-label')) }}
        {{Form::textarea('description',null,array('class'=>'form-control summernote','rows'=>3,'placeholder'=>__('Description')))}}
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Save') }}" class="btn btn-primary" id="submit-all">
</div>
{!! Form::close() !!}


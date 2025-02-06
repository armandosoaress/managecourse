@php
    $plansetting = App\Models\Utility::plansetting();
@endphp
{!! Form::open(['route' => 'category.store','method' => 'post', 'enctype'=>'multipart/form-data']) !!}
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
                <input type="file" class="form-control mb-2" name="category_image" id="category_image" aria-label="file example" onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                {{-- <img id="blah" alt="your image" width="100" height="100" /> --}}
                <img src="" id="blah" width="25%"/>
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

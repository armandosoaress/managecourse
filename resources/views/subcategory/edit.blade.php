@php
    $plansetting = App\Models\Utility::plansetting();
@endphp
{{Form::model($subcategory,array('route' => array('subcategory.update', $subcategory->id), 'method' => 'PUT')) }}
<div class="row">
    @if($plansetting['enable_chatgpt'] && $plansetting['enable_chatgpt']=='on')
        <div class="text-end align-items-center justify-content-between">
            <a href="#" class="btn btn-primary btn-sm mx-1" data-size="md" data-ajax-popup-over="true" data-url="{{ route('generate',['subcategory']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                <i class="fas fa-robot"></i> {{ __('Generate With AI') }}
            </a>
        </div>
    @endif
    <div class="form-group col-lg-6 col-md-6">
        {!! Form::label('name', __('Name'),['class'=>'form-label']) !!}
        {!! Form::text('name', null, ['class' => 'form-control','required' => 'required']) !!}
    </div>
    <div class="form-group col-lg-6 col-md-6">
        {!! Form::label('category', __('Category'),['class'=>'form-label']) !!}
        {!! Form::select('category',$category,null,array('class'=>'form-control' )) !!}
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Save') }}" class="btn btn-primary" id="submit-all">
</div>
{!! Form::close() !!}


@php
    $plansetting = App\Models\Utility::plansetting();
@endphp
{{Form::model($header,array('route' => array('headers.update',[$header->id,$course_id]), 'method' => 'PUT')) }}
<div class="row">
    @if($plansetting['enable_chatgpt'] && $plansetting['enable_chatgpt']=='on')
        <div class="text-end align-items-center justify-content-between">
            <a href="#" class="btn btn-primary btn-sm mx-1" data-size="md" data-ajax-popup-over="true" data-url="{{ route('generate',['header']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                <i class="fas fa-robot"></i> {{ __('Generate With AI') }}
            </a>
        </div>
    @endif
    <div class="form-group col-lg-12 col-md-12">
        {!! Form::label('title', __('Header'),['class'=>'form-label']) !!}
        {!! Form::text('title', null, ['class' => 'form-control','required' => 'required']) !!}
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Save') }}" class="btn btn-primary" id="submit-all">
    </div>

</div>
{!! Form::close() !!}


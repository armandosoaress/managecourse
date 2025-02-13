{{ Form::open(array('route' => array('store.language'))) }}
<div class="form-group">
    {{ Form::label('code', __('Language Code'),array('class'=>'form-label')) }}
    {{ Form::text('code', '', array('class' => 'form-control','required'=>'required')) }}
    @error('code')
    <span class="invalid-code" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
        </span>
    @enderror
</div>
<div class="form-group">
    {{ Form::label('fullname', __('Language Fullname'),array('class'=>'form-label'))}}
    {{ Form::text('fullname', '', array('class' => 'form-control','required'=>'required')) }}
    @error('fullname')
    <span class="invalid-code" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
        </span>
    @enderror
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    {{Form::submit(__('Create'),array('class'=>'btn btn-primary'))}}
</div>
{{ Form::close() }}


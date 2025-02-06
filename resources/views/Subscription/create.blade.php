{{Form::open(array('url'=>'subscriptions','method'=>'post'))}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email'),'required'=>'required'))}}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Save') }}" class="btn btn-primary">
</div>
{{Form::close()}}

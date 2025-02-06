<form method="post" action="{{ route('coupons.store') }}">
    @csrf
    <div class="row">
        @if(Utility::getValByName('chatgpt_key'))
            <div class="text-end align-items-center justify-content-between">
                <a href="#" class="btn btn-primary btn-sm mx-1" data-size="md" data-ajax-popup-over="true" data-url="{{ route('generate',['coupon']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                    <i class="fas fa-robot"></i> {{ __('Generate With AI') }}
                </a>
            </div>
        @endif
        <div class="form-group col-md-12">
            {{Form::label('name',__('Name'),array('class'=>'form-label'))}}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>

        <div class="form-group col-md-6">
            {{Form::label('discount',__('Discount') ,array('class'=>'form-label')) }}
            {{Form::number('discount',null,array('class'=>'form-control','step'=>'0.01','placeholder'=>__('Enter Discount'),'required'=>'required'))}}
            <span class="small">{{__('Note: Discount in Percentage')}}</span>
        </div>
        <div class="form-group col-md-6">
            {{Form::label('limit',__('Limit') ,array('class'=>'form-label'))}}
            {{Form::number('limit',null,array('class'=>'form-control','placeholder'=>__('Enter Limit'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-12" id="auto">
            {{Form::label('limit',__('Code') ,array('class'=>'form-label'))}}
            <div class="input-group">
                {{Form::text('code',null,array('class'=>'form-control','id'=>'auto-code','required'=>'required'))}}
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text" id="code-generate"><i class="fa fa-history pr-1"></i> {{__('Generate')}}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
    </div>
</form>


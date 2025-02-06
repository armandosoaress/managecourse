{{Form::open(array('route'=>'plans.store','method'=>'post','enctype'=>'multipart/form-data'))}}
@csrf
<div class="row">
    @if(Utility::getValByName('chatgpt_key'))
        <div class="text-end align-items-center justify-content-between">
            <a href="#" class="btn btn-primary btn-sm mx-1" data-size="md" data-ajax-popup-over="true" data-url="{{ route('generate',['plan']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                <i class="fas fa-robot"></i> {{ __('Generate With AI') }}
            </a>
        </div>
    @endif
    <div class="col-md-6">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'form-label')) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>

    <div class="col-md-6">

        <div class="form-group">
            <div class="col-12">
                {{Form::label('price',__('Price'),array('class'=>'form-label')) }}
                <div class="input-group">
                    <div class="input-group-text">{{$admin_payments_setting['currency_symbol']}}</div>
                    {{Form::number('price',null,array('class'=>'form-control','id'=>'monthly_price','min'=>'0','placeholder'=>__('Enter Price'),'required'=>'required'))}}
                </div>
            </div>
        </div>

    </div>

    <div class="form-group col-md-6">
        {{ Form::label('duration', __('Duration')) }}
        {!! Form::select('duration', $arrDuration, null,array('class' => 'form-select','required'=>'required')) !!}
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {{Form::label('max_stores',__('Maximum Store'),array('class'=>'form-label')) }}
            {{Form::number('max_stores',null,array('class'=>'form-control','id'=>'max_stores','placeholder'=>__('Enter Max Store'),'required'=>'required'))}}
            <span><small>{{__("Note: '-1' for lifetime")}}</small></span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{Form::label('max_courses',__('Maximum Courses Per Store'),array('class'=>'form-label')) }}
            {{Form::number('max_courses',null,array('class'=>'form-control','id'=>'max_courses','placeholder'=>__('Enter Max Courses'),'required'=>'required'))}}
            <span><small>{{__("Note: '-1' for lifetime")}}</small></span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{Form::label('max_users',__('Maximum User Per Store'),array('class'=>'form-label')) }}
            {{Form::number('max_users',null,array('class'=>'form-control','id'=>'max_users','placeholder'=>__('Enter Max User'),'required'=>'required'))}}
            <span><small>{{__("Note: '-1' for lifetime")}}</small></span>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {{Form::label('storage_limit',__('Maximum Storage Limit'),array('class'=>'form-label')) }}
            <div class="input-group">
                <input class="form-control" required="required" name="storage_limit" type="number" id="storage_limit">
                <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon2">{{__('MB')}}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="form-check  form-switch custom-control-inline pt-2">
            <input type="checkbox" class="form-check-input" name="enable_custdomain" id="enable_custdomain">
            <label class="form-check-label" for="enable_custdomain">{{__('Enable Domain')}}</label>
        </div>
    </div>
    <div class="col-6">
        <div class="form-check form-switch custom-control-inline pt-2">
            <input type="checkbox" class="form-check-input" name="enable_custsubdomain" id="enable_custsubdomain">
            <label class="form-check-label" for="enable_custsubdomain">{{__('Enable Sub Domain')}}</label>
        </div>
    </div>
    <div class="col-6">
        <div class="form-check form-switch custom-control-inline pt-2">
            <input type="checkbox" class="form-check-input" name="additional_page" id="additional_page">
            <label class="form-check-label" for="additional_page">{{__('Enable Additional Page')}}</label>
        </div>
    </div>
    <div class="col-6">
        <div class="form-check form-switch custom-control-inline pt-2">
            <input type="checkbox" class="form-check-input" name="blog" id="blog">
            <label class="form-check-label" for="blog">{{__('Enable Blog')}}</label>
        </div>
    </div>
    <div class="col-6">
        <div class="form-check  custom-control-inline form-switch pt-2">
            <input type="checkbox" class="form-check-input" name="enable_chatgpt" id="enable_chatgpt">
            <label class="form-check-label" for="enable_chatgpt">{{ __('Enable Chatgpt') }}</label>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('description',__('Description'),array('class'=>'form-label')) }}
            {{Form::textarea('description',null,array('class'=>'form-control','id'=>'description', 'rows' => 3,'placeholder'=>__('Enter Description')))}}
        </div>
    </div>
</div>
<div class="form-group text-end">
    {{Form::submit(__('Create Plan'),array('class'=>'btn btn-primary mr-auto'))}}
</div>
{{Form::close()}}

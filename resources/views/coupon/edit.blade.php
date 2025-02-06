<form method="post" action="{{ route('coupons.update', $coupon->id) }}">
    @csrf
    @method('PUT')
    <div class="row">
        @if(Utility::getValByName('chatgpt_key'))
            <div class="text-end align-items-center justify-content-between">
                <a href="#" class="btn btn-primary btn-sm mx-1" data-size="md" data-ajax-popup-over="true" data-url="{{ route('generate',['coupon']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                    <i class="fas fa-robot"></i> {{ __('Generate With AI') }}
                </a>
            </div>
        @endif
        <div class="form-group col-md-12">
            <label for="name">{{__('Name')}}</label>
            <input type="text" name="name" class="form-control" required value="{{$coupon->name}}">
        </div>

        <div class="form-group col-md-6">
            <label for="discount">{{__('Discount')}}</label>
            <input type="number" name="discount" class="form-control" required step="0.01" value="{{$coupon->discount}}">
            <span class="small">{{__('Note: Discount in Percentage')}}</span>
        </div>
        <div class="form-group col-md-6">
            <label for="limit">{{__('Limit')}}</label>
            <input type="number" name="limit" class="form-control" required value="{{$coupon->limit}}">
        </div>

        <div class="form-group col-md-12" id="auto">
            <label for="code">{{__('Code')}}</label>
            <div class="input-group">
                <input class="form-control" name="code" type="text" id="auto-code" value="{{$coupon->code}}">
                <div class="input-group-prepend">
                    <button type="button" class="input-group-text" id="code-generate"><i class="fa fa-history pr-1"></i> {{__('Generate')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
    </div>


</form>


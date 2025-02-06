@php
    $plansetting = App\Models\Utility::plansetting();
@endphp
{{Form::model($pageOption, array('route' => array('custom-page.update', $pageOption->id), 'method' => 'PUT')) }}
    <div class="row">
        @if($plansetting['enable_chatgpt'] && $plansetting['enable_chatgpt']=='on')
            <div class="text-end align-items-center justify-content-between">
                <a href="#" class="btn btn-primary btn-sm mx-1" data-size="md" data-ajax-popup-over="true" data-url="{{ route('generate',['custompage']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                    <i class="fas fa-robot"></i> {{ __('Generate With AI') }}
                </a>
            </div>
        @endif
        <div class="col-12">
            <div class="form-group">
                {{Form::label('name',__('Name'))}}
                {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name')))}}
                @error('name')
                    <span class="invalid-name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="form-group col-md-6">
            {{Form::label('enable_page_header',__('Page Header Display'),array('class'=>'form-check-label mb-3')) }}
            <div class="form-check form-check form-switch custom-control-inline">
                <input type="checkbox" class="form-check-input" name="enable_page_header" id="enable_page_header" {{ ($pageOption['enable_page_header'] == 'on') ? 'checked=checked' : '' }}>
                <label class="form-check-label" for="enable_page_header"></label>
            </div>
        </div>
        <div class="form-group col-md-12">
            {{Form::label('contents',__('Contents'),array('class'=>'form-label')) }}
            {{Form::textarea('contents',null,array('class'=>'form-control summernote','rows'=>3,'placeholder'=>__('Contents')))}}
            @error('contents')
                <span class="invalid-contents" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
    </div>
{{Form::close()}}


{{Form::open(array('url'=>'store-resource','method'=>'post'))}}
<div class="row">
    @if(\Auth::user()->type == 'super admin')
        @if(Utility::getValByName('chatgpt_key'))
            <div class="text-end align-items-center justify-content-between">
                <a href="#" class="btn btn-sm btn-primary" data-size="md" data-ajax-popup-over="true" data-url="{{ route('generate',['store']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                    <i class="fas fa-robot"></i> {{ __('Generate With AI') }}
                </a>
            </div>
        @endif
    @else
        @php
            $plansetting = \App\Models\Utility::plansetting();
        @endphp
        @if($plansetting['enable_chatgpt'] && $plansetting['enable_chatgpt']=='on')
            <div class="text-end align-items-center justify-content-between">
                <a href="#" class="btn btn-sm btn-primary" data-size="md" data-ajax-popup-over="true" data-url="{{ route('generate',['store']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                    <i class="fas fa-robot"></i> {{ __('Generate With AI') }}
                </a>
            </div>
        @endif
    @endif
    <div class="col-12">
        <div class="form-group">
            {{Form::label('store_name',__('Store Name')) }}
            {{Form::text('store_name',null,array('class'=>'form-control','placeholder'=>__('Enter Store Name'),'required'=>'required'))}}
        </div>
        @if(\Auth::user()->type != 'super admin')
            <div class="form-group">
                {{Form::label('store_theme',__('Store Theme')) }}
            </div>
            <div class="border border-primary rounded p-3">
                <div class="row gy-4 ">
                    <input id="themefile1" name="themefile" type="hidden" value="theme1">
                    @foreach (Utility::themeOne() as $key => $v)
                        <div class="col-xl-4 col-lg-4 col-md-6 overflow-hidden cc-selector mb-2">
                            <div class="border border-primary rounded">
                            <div class="theme-card-inner">
                                <div class="screen border rounded ">
                                    <img src="{{ asset(Storage::url('uploads/store_theme/' . $key . '/Home.png')) }}"
                                        class="color1 img-center pro_max_width pro_max_height {{ $key }}_img"
                                        data-id="{{ $key }}">
                                </div>
                                <div class="theme-content mt-3">
                                    <div class="row gutters-xs justify-content-center"
                                        id="{{'radio_'.$key }}">
                                        @foreach ($v as $css => $val)
                                            <div class="col-auto">
                                                <label class="colorinput">
                                                    <input name="theme_color" type="radio"
                                                        value="{{ $css }}"
                                                        data-key="theme{{ $loop->iteration }}"
                                                        data-theme="{{ $key }}"
                                                        data-imgpath="{{ $val['img_path'] }}"
                                                        class="colorinput-input color-{{ $loop->index++ }}"
                                                        {{ isset($store_settings['store_theme']) && $store_settings['store_theme'] == $css && $store_settings['theme_dir'] == $key ? 'checked' : '' }}>
                                                    <span class="colorinput-color"
                                                        style="background:#{{ $val['color'] }}"></span>
                                                </label>
                                            </div>
                                        @endforeach
                                        <div class="col-auto">
                                            @if (isset($store_settings['theme_dir']) && $store_settings['theme_dir'] == $key)
                                                <a href="{{ route('store.editproducts', [$store_settings->slug, $key]) }}"
                                                    class="btn btn-outline-primary theme_btn" type="button"
                                                    id="button-addon2">{{ __('Edit') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    @if(\Auth::user()->type == 'super admin')
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Name')) }}
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('email',__('Email')) }}
            {{Form::email('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('password',__('Password')) }}
            {{Form::password('password',array('class'=>'form-control','placeholder'=>__('Enter Password'),'required'=>'required'))}}
        </div>
    </div>
    @endif
</div>
    <div class="modal-footer">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        {{Form::submit(__('Save'),array('class'=>'btn btn-primary'))}}
    </div>


{{Form::close()}}

<script>
    $('body').on('click', 'input[name="theme_color"]', function() {
        var eleParent = $(this).attr('data-theme');
        $('#themefile1').val(eleParent);
        var imgpath = $(this).attr('data-imgpath');
        $('.' + eleParent + '_img').attr('src', imgpath);
    });

    $('body').ready(function() {

        setTimeout(function(e) {
            var checked = $("input[type=radio][name='theme_color']:checked");
            $('#themefile1').val(checked.attr('data-theme'));
            $('.' + checked.attr('data-theme') + '_img').attr('src', checked.attr('data-imgpath'));
        }, 300);
    });
    $(".color1").click(function() {
        var dataId = $(this).attr("data-id");
        $('#radio_' + dataId).trigger('click');
        var first_check = $('#radio_' + dataId).find('.color-0').trigger("click");
        $( ".theme-card" ).each(function() {
            $(".theme-card").removeClass('selected');
        });
        $('.s_' +dataId ).addClass('selected');
    });
</script>

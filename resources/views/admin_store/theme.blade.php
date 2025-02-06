@extends('layouts.admin')
@section('page-title')
    {{__('Themes')}}
@endsection
@section('title')
    {{__('Themes')}}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Manage Themes') }}</li>
@endsection
@section('content')

    <!-- Listing -->
    <div class="">
        {{ Form::open(['route' => ['store.changetheme', $store_settings->id], 'method' => 'POST']) }}
        <div class="d-flex mb-3 align-items-center justify-content-between">
            <h3 class="mb-2">{{ __('Themes') }}</h3>
            <input id="themefile" name="themefile" type="hidden" value="theme1">
            {{ Form::submit(__('Save Changes'), ['class' => 'btn btn-primary']) }}
        </div>

        <div class="border border-primary rounded p-3">
            <div class="row gy-4 ">
                @foreach (Utility::themeOne() as $key => $v)
                <div class="col-xl-3 col-lg-4 col-md-6 overflow-hidden cc-selector">
                        <div class="border border-primary rounded">
                            <div class="theme-card-inner">
                                <div class="screen theme-image border rounded">
                                    <img src="{{ asset(Storage::url('uploads/store_theme/' . $key . '/Home.png')) }}"
                                        class="color1 img-center pro_max_width pro_max_height {{ $key }}_img"
                                        data-id="{{ $key }}">
                                </div>
                                <div class="theme-content mt-3">
                                    <div class="row gutters-xs justify-content-center"
                                        id="{{ $key }}">
                                        @foreach ($v as $css => $val)
                                            <div class="col-auto">
                                                <label class="colorinput">
                                                    <input name="theme_color" type="radio"
                                                        value="{{ $css }}"
                                                        data-key="theme{{ $loop->iteration }}"
                                                        data-theme="{{ $key }}"
                                                        data-imgpath="{{ $val['img_path'] }}"
                                                        class="colorinput-input color-{{ $loop->index++ }}"
                                                        {{ isset($store_settings['store_theme']) && $store_settings['store_theme'] == $css ? 'checked' : '' }}>
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
        {{ Form::close() }}
    </div>
@endsection

@push('script-page')
<script>
    $(document).on('click', 'input[name="theme_color"]', function() {
        var eleParent = $(this).attr('data-theme');
        $('#themefile').val(eleParent);
        // $('#themefile').val($(this).attr('data-key'));
        var imgpath = $(this).attr('data-imgpath');
        $('.' + eleParent + '_img').attr('src', imgpath);
    });

    $(document).ready(function() {
        setTimeout(function(e) {
            var checked = $("input[type=radio][name='theme_color']:checked");
            $('#themefile').val(checked.attr('data-theme'));
            // $('#themefile').val(checked.attr('data-key'));
            $('.' + checked.attr('data-theme') + '_img').attr('src', checked.attr('data-imgpath'));
        }, 300);
    });

    $(".color1").click(function() {
        var dataId = $(this).attr("data-id");
        $('#' + dataId).trigger('click');
        var first_check = $('#' + dataId).find('.color-0').trigger("click");
    });
</script>
@endpush

@extends('storefront.theme2.user.user')
@section('page-title')
    {{ __('Login') }} - {{ $store->tagline ? $store->tagline : config('APP_NAME', ucfirst($store->name)) }}
@endsection
@push('css-page')
@endpush
@section('head-title')
    {{ __('Studgfhghent Login') }}
@endsection
@section('content')
    <div class="login-page-wrapper">
        <section class="login-page padding-bottom padding-top">
            <div class="banner-image">
                @php
                    $data = explode('.', $store->store_theme);
                @endphp

                @if ($data[0] == 'dark-blue-color')
                    <img src="{{ asset('assets/themes/theme2/images/banner-image1.png') }}" alt="">
                @elseif($data[0] == 'dark-green-color')
                    <img src="{{ asset('assets/themes/theme2/images/banner-image2.png') }}" alt="">
                @else
                    <img src="{{ asset('assets/themes/theme2/images/banner-image3.png') }}" alt="">
                @endif
            </div>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-12">
                        <div class="login-left-side">
                            <div class="section-title">
                                <h3>{{ __('Password Reset') }}</h3>
                            </div>
                            <div class="form-wrapper">
                                {!! Form::open(['route' => ['student.password.update', $slug]], ['method' => 'post']) !!}
                                <input type="hidden" name="token" value="{{ $token }}">
                                <div class="form-group">
                                    {{ Form::text('email', null, ['class' => 'form-control form-control-lg', 'placeholder' => __('Enter Your Email')]) }}
                                    @error('email')
                                        <span class="error invalid-password text-danger" role="alert">
                                            <strong>{{ __('We can not find a student with that email address') }}.</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    {{ Form::password('password', ['class' => 'form-control form-control-lg', 'placeholder' => __('Enter Your Password')]) }}
                                    @error('password')
                                        <span class="error invalid-password text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    {{ Form::password('password_confirmation', ['class' => 'form-control form-control-lg', 'placeholder' => __('Confirm Password')]) }}
                                    @error('password_confirmation')
                                        <span class="error invalid-password_confirmation text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="text-center">
                                    {{ Form::submit(__('Reset'), ['class' => 'btn btn-block btn-lg btn-primary mt-4 mb-3', 'id' => 'saveBtn']) }}
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                    @php
                        $main_homepage_header_text_key = array_search('Home-Header', array_column($getStoreThemeSetting, 'section_name'));
                        $header_enable = 'off';
                        $homepage_header_title = 'Improve Your<br> Skills with <br> ModernCourse.';
                        $homepage_header_Sub_text = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.';
                        $homepage_header_button = 'Show More';

                        if (!empty($getStoreThemeSetting[$main_homepage_header_text_key])) {
                            $homepage_header = $getStoreThemeSetting[$main_homepage_header_text_key];
                            $header_enable = $homepage_header['section_enable'];

                            $homepage_header_title_key = array_search('Title', array_column($homepage_header['inner-list'], 'field_name'));
                            $homepage_header_title = $homepage_header['inner-list'][$homepage_header_title_key]['field_default_text'];

                            $homepage_header_Sub_text_key = array_search('Sub Title', array_column($homepage_header['inner-list'], 'field_name'));
                            $homepage_header_Sub_text = $homepage_header['inner-list'][$homepage_header_Sub_text_key]['field_default_text'];

                            $homepage_header_button_key = array_search('Button', array_column($homepage_header['inner-list'], 'field_name'));
                            $homepage_header_button = $homepage_header['inner-list'][$homepage_header_button_key]['field_default_text'];
                        }

                        $sub_title[0] = 'Data Analyst';
                        $sub_title[1] = 'UX/UI Designer';
                        $sub_title[2] = 'Data Analyst';
                        $sub_title[3] = 'Data Engineer';
                        $sub_subtitle[0] = __('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the.');
                        $sub_subtitle[1] = __('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the.');
                        $sub_subtitle[2] = __('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the.');
                        $sub_subtitle[3] = __('Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the.');
                        $a[0] = '#';
                        $a[1] = '#';
                        $a[2] = '#';
                        $a[3] = '#';
                        $img[0] = 'theme2/images/data-analyst.jpg';
                        $img[1] = 'theme2/images/data-analyst.jpg';
                        $img[2] = 'theme2/images/data-analyst.jpg';
                        $img[3] = 'theme2/images/data-analyst.jpg';

                        if ($header_enable == 'on') {
                            if (!empty($getStoreThemeSetting[1])) {
                                $homepage_header1 = $getStoreThemeSetting[1];
                                foreach ($homepage_header1['inner-list'] as $key1 => $value1) {
                                    $sl = $value1['field_slug'];
                                    for ($i = 0; $i < $homepage_header1['loop_number']; $i++) {
                                        if (!empty($homepage_header1[$sl][$i])) {
                                            if ($sl == 'homepage-thumbnail-innerbox-1-title') {
                                                $sub_title[$i] = $homepage_header1[$sl][$i];
                                            }
                                            if ($sl == 'homepage-thumbnail-innerbox-1-sub-text') {
                                                $sub_subtitle[$i] = $homepage_header1[$sl][$i];
                                            }
                                            if ($sl == 'homepage-quick-link') {
                                                $a[$i] = $homepage_header1[$sl][$i];
                                            }
                                            if ($sl == 'homepage-thumbnail-innerbox-thumbnail') {
                                                $img[$i] = $homepage_header1[$sl][$i]['field_prev_text'];
                                            }
                                        } else {
                                            if ($sl == 'homepage-thumbnail-innerbox-1-title') {
                                                $sub_title[$i] = $value1['field_default_text'];
                                            }
                                            if ($sl == 'homepage-thumbnail-innerbox-1-sub-text') {
                                                $sub_subtitle[$i] = $value1['field_default_text'];
                                            }
                                            if ($sl == 'homepage-quick-link') {
                                                $a[$i] = $value1['field_default_text'];
                                            }
                                            if ($sl == 'homepage-thumbnail-innerbox-thumbnail') {
                                                $img[$i] = $value1['field_default_text'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    @endphp
                    <div class="col-lg-6 col-12">
                        <div class="banner-category-list">
                            @if ($header_enable == 'on')
                                <ul class="category-list">
                                    <li>
                                        <div class="category-card">
                                            <div class="cateory-card-inner">
                                                <div class="category-image">
                                                    <img src="{{ asset(Storage::url('uploads/' . $img[0])) }}"
                                                        alt="">
                                                    {{-- <img src="{{ asset('assets/themes/theme2/images/data-analyst.jpg') }}" alt="">  --}}
                                                </div>
                                                <div class="cateory-caption">
                                                    <h5>
                                                        <a href="{{ $a[0] }}">{{ $sub_title[0] }}</a>
                                                    </h5>
                                                    <p>{{ $sub_subtitle[0] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="category-card">
                                            <div class="cateory-card-inner">
                                                <div class="category-image">
                                                    <img src="{{ asset(Storage::url('uploads/' . $img[1])) }}"
                                                        alt="">
                                                    {{-- <img src="{{ asset('assets/themes/theme2/images/Designer.jpg') }}" alt=""> --}}
                                                </div>
                                                <div class="cateory-caption">
                                                    <h5>
                                                        <a href="{{ $a[1] }}">{{ $sub_title[1] }}</a>
                                                    </h5>
                                                    <p>{{ $sub_subtitle[1] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="category-card">
                                            <div class="cateory-card-inner">
                                                <div class="category-image">
                                                    <img src="{{ asset(Storage::url('uploads/' . $img[2])) }}"
                                                        alt="">
                                                    {{-- <img src="{{ asset('assets/themes/theme2/images/data-analyst.jpg') }}" alt=""> --}}
                                                </div>
                                                <div class="cateory-caption">
                                                    <h5>
                                                        <a href="{{ $a[2] }}">{{ $sub_title[2] }}</a>
                                                    </h5>
                                                    <p>{{ $sub_subtitle[2] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="category-card">
                                            <div class="cateory-card-inner">
                                                <div class="category-image">
                                                    <img src="{{ asset(Storage::url('uploads/' . $img[3])) }}"
                                                        alt="">
                                                    {{-- <img src="{{ asset('assets/themes/theme2/images/data-Engineer.jpg') }}" alt=""> --}}
                                                </div>
                                                <div class="cateory-caption">
                                                    <h5>
                                                        <a href="{{ $a[3] }}">{{ $sub_title[3] }}</a>
                                                    </h5>
                                                    <p>{{ $sub_subtitle[3] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>

                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('script-page')
    <script>
        if ('{!! !empty($is_cart) && $is_cart == true !!}') {
            show_toastr('Error', 'You need to login!', 'error');
        }
    </script>
@endpush

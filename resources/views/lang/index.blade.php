@extends('layouts.admin')
@push('css-page')
@endpush
@push('script-page')
@endpush
@section('page-title')
    {{__('Language')}}
@endsection
@section('title')
      {{__('Language')}}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Language') }}</li>
@endsection

@section('action-btn')
<div class="text-end align-items-end d-flex justify-content-end">
    @if($currantLang != (!empty( $settings['default_language']) ?  $settings['default_language'] : 'en'))
        <div class="form-check form-switch custom-switch-v1">
            <input type="hidden" name="disable_lang" value="off">
            <input type="checkbox" class="form-check-input input-primary" name="disable_lang" data-bs-placement="top" title="{{ __('Enable/Disable') }}" id="disable_lang" data-bs-toggle="tooltip" {{ !in_array($currantLang,$disabledLang) ? 'checked':'' }} >
            <label class="form-check-label" for="disable_lang"></label>
        </div>
    @endif
    @can('delete language')
        @if($currantLang != (!empty(env('default_language')) ? env('default_language') : 'en'))
            <div class="action-btn bg-danger mx-1">
                <form method="POST" action="{{ route('lang.destroy', $currantLang) }}" id="delete-form-{{ $currantLang }}">
                    @csrf
                    @method('DELETE')
                    <a href="#!" class=" btn btn-sm btn-danger align-items-center  show_confirm" data-toggle="tooltip" title='Delete'>
                        <span class="text-white"> <i class="ti ti-trash"></i></span>
                    </a>
                </form>
            </div>
        @endif
    @endcan
</div>
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-2">
            <div class="card sticky-top" style="top:30px">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    @foreach ($languages as $code => $lang)
                            <a href="{{ route('manage.language', [$code]) }}"
                                class="border-0 list-group-item list-group-item-action {{ $currantLang == $code ? 'active' : '' }}">
                                <span>{{ ucFirst($lang) }}</span><div class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-xl-10">
            <div class="card">
                <div class="card-body">

                    <ul class="nav nav-pills mb-3 row" id="pills-tab" role="tablist">
                        <li class="nav-item col-6">
                            <a class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" href="#home" role="tab" aria-controls="pills-home" aria-selected="true">{{ __('Labels')}}</a>
                        </li>
                        <li class="nav-item col-6">
                            <a class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">{{ __('Messages')}}</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="pills-home-tab">
                            <form method="post" action="{{route('store.language.data',[$currantLang])}}">
                                @csrf
                                <div class="row">
                                    @foreach($arrLabel as $label => $value)
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="example3cols1Input">{{$label}} </label>
                                                <input type="text" class="form-control" name="label[{{$label}}]" value="{{$value}}">
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="col-lg-12 text-end">
                                        <button class="btn btn-primary" type="submit">{{ __('Save Changes')}}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <form method="post" action="{{route('store.language.data',[$currantLang])}}">
                                @csrf
                                <div class="row">
                                    @foreach($arrMessage as $fileName => $fileValue)
                                        <div class="col-lg-12">
                                            <h5>{{ucfirst($fileName)}}</h5>
                                        </div>
                                        @foreach($fileValue as $label => $value)
                                            @if(is_array($value))
                                                @foreach($value as $label2 => $value2)
                                                    @if(is_array($value2))
                                                        @foreach($value2 as $label3 => $value3)
                                                            @if(is_array($value3))
                                                                @foreach($value3 as $label4 => $value4)
                                                                    @if(is_array($value4))
                                                                        @foreach($value4 as $label5 => $value5)
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label>{{$fileName}}.{{$label}}.{{$label2}}.{{$label3}}.{{$label4}}.{{$label5}}</label>
                                                                                    <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}][{{$label3}}][{{$label4}}][{{$label5}}]" value="{{$value5}}">
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <div class="col-lg-6">
                                                                            <div class="form-group">
                                                                                <label>{{$fileName}}.{{$label}}.{{$label2}}.{{$label3}}.{{$label4}}</label>
                                                                                <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}][{{$label3}}][{{$label4}}]" value="{{$value4}}">
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                <div class="col-lg-6">
                                                                    <div class="form-group">
                                                                        <label>{{$fileName}}.{{$label}}.{{$label2}}.{{$label3}}</label>
                                                                        <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}][{{$label3}}]" value="{{$value3}}">
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <label>{{$fileName}}.{{$label}}.{{$label2}}</label>
                                                                <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}][{{$label2}}]" value="{{$value2}}">
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>{{$fileName}}.{{$label}}</label>
                                                        <input type="text" class="form-control" name="message[{{$fileName}}][{{$label}}]" value="{{$value}}">
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </div>
                                <div class="col-lg-12 text-end">
                                    <button class="btn btn-primary" type="submit">{{ __('Save Changes')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script>
        $(document).on('click', '.lang-tab .nav-link', function() {
            $('.lang-tab .nav-link').removeClass('active');
            $('.tab-pane').removeClass('active');
            $(this).addClass('active');
            var id = $('.lang-tab .nav-link.active').attr('data-href');
            $(id).addClass('active');
        });
    </script>
    <script>
        $(document).on('change','#disable_lang',function(){
            var val = $(this).prop("checked");
            if(val == true){
                var langMode = 'on';
            }
            else{
            var langMode = 'off';
            }
            $.ajax({
                type:'POST',
                url: "{{route('disablelanguage')}}",
                datType: 'json',
                data:{
                    "_token": "{{ csrf_token() }}",
                    "mode":langMode,
                    "lang":"{{ $currantLang }}"
                },
                success : function(data){
                    show_toastr('Success',data.message, 'success')
                }
            });
        });
    </script>
@endpush

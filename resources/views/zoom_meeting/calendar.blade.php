@extends('layouts.admin')
@section('page-title')
    {{ __('Zoom Meetings Calender') }}
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/main.css') }}">
    {{-- <link rel="stylesheet" type="text/css" href="{{asset('assets/css/daterangepicker.css')}}"> --}}
@endpush

@section('title')
    {{ __('Zoom Meeting Calender') }}
@endsection
@php
    $settings = Utility::settings();
@endphp
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('zoom-meeting.index') }}">{{ __('Zoom Meeting') }}</a></li>
    <li class="breadcrumb-item">{{ __('Calender') }}</li>
@endsection

@section('action-btn')
    @can('manage zoom meeting')
        <a href="{{ route('zoom-meeting.index') }}" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip"
            data-bs-placement="top" title="{{ __('List view') }}"><i class="ti ti-list"></i>
        </a>
    @endcan

    @can('create zoom meeting')
        <a href="#" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
            title="{{ __('Create Zoom Meeting') }}" data-ajax-popup="true" data-size="lg"
            data-title="{{ __('Create Zoom Meeting') }}" data-url="{{ route('zoom-meeting.create') }}"><i
                class="ti ti-plus text-white"></i></a>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <h5>{{ __('Calendar') }}</h5>
                        </div>
                        @if (isset($settings['google_calender_enabled']) && $settings['google_calender_enabled'] == 'on')
                            <div class="col-lg-6">
                                <select class="form-control" name="calender_type" id="calender_type"
                                    style="float: right;width: 150px;" onchange="get_data()">
                                    <option value="goggle_calender">{{ __('Google Calender') }}</option>
                                    <option value="local_calender" selected="true">{{ __('Local Calender') }}</option>
                                </select>
                                <input type="hidden" id="path_admin" value="{{ url('/') }}">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div id="calendar" class="calendar" data-toggle="calendar"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">{{ _('Meetings') }}</h4>
                    <ul class="event-cards list-group list-group-flush mt-3 w-100">
                        @foreach ($month_meetings as $meetings)
                            <li class="list-group-item card mb-3">
                                <div class="row align-items-center justify-content-between">
                                    <div class="col-auto mb-3 mb-sm-0">
                                        <div class="d-flex align-items-center">
                                            <div class="theme-avtar bg-primary">
                                                <i class="ti ti-video"></i>
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="m-0">{{ $meetings->title }}</h6>
                                                <small class="text-muted">
                                                    {{ date('d M Y', strtotime($meetings->start_date)) }}
                                                    , {{ __('AT') }}
                                                    {{ date('h:i A', strtotime($meetings->start_date)) }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
    <script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>

    <script>
        (function() {
            var etitle;
            var etype;
            var etypeclass;
            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                },
                themeSystem: 'bootstrap',
                // slotDuration: '00:10:00',
                navLinks: true,
                droppable: true,
                selectable: true,
                selectMirror: true,
                editable: true,
                dayMaxEvents: true,
                handleWindowResize: true,
                height: 'auto',
                events: {!! $calendar !!}
            });
            calendar.render();

            $(document).on('click', ' .fc-daygrid-event', function(e) {
                if (!$(this).hasClass('deal')) {
                    e.preventDefault();
                    var event = $(this);
                    var title = $(this).find('.fc-event-title').html();
                    var size = 'md';
                    var url = $(this).attr('href');
                    $("#commonModal .modal-title").html(title);
                    $("#commonModal .modal-dialog").addClass('modal-' + size);

                    $.ajax({
                        url: url,
                        success: function(data) {
                            $('#commonModal .modal-body').html(data);
                            $("#commonModal").modal('show');
                        },
                        error: function(data) {
                            data = data.responseJSON;
                            show_toastr('Error', data.error, 'error')
                        }
                    });
                }
            });

        })();
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            get_data();
        });

        function get_data() {
            var calender_type = $('#calender_type :selected').val();
            $.ajax({
                url: $("#path_admin").val() + "/zoom-meeting/get_event_data",
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'calender_type': calender_type
                },
                success: function(data) {
                    // console.log(data);
                    (function() {
                        var etitle;
                        var etype;
                        var etypeclass;
                        var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                            headerToolbar: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'dayGridMonth,timeGridWeek,timeGridDay'
                            },
                            buttonText: {
                                timeGridDay: "{{ __('Day') }}",
                                timeGridWeek: "{{ __('Week') }}",
                                dayGridMonth: "{{ __('Month') }}"
                            },
                            slotLabelFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false,
                            },
                            themeSystem: 'bootstrap',
                            // slotDuration: '00:10:00',
                            navLinks: true,
                            droppable: true,
                            selectable: true,
                            selectMirror: true,
                            editable: true,
                            dayMaxEvents: true,
                            handleWindowResize: true,
                            height: 'auto',
                            timeFormat: 'H(:mm)',
                            events: data,
                        });

                        calendar.render();
                    })();
                }
            });
        }
    </script>

    <script src="{{ url('js/daterangepicker.js') }}"></script>
    <script type="text/javascript">
        $(document).on('change', '#course_id', function() {
            getStudents($(this).val());
        });
        function getStudents(id) {

            $("#students-div").html('');
            $('#students-div').append('<select class="form-control" id="student_id" name="students[]"  multiple></select>');

            $.get("{{ url('get-students') }}/" + id, function(data, status) {

                var list = '';
                $('#student_id').empty();
                if (data.length > 0) {
                    list += "<option value=''>  </option>";
                } else {
                    list += "<option value=''> {{ __('No Students') }} </option>";
                }
                $.each(data, function(i, item) {

                    list += "<option value='" + item.id + "'>" + item.name + "</option>"
                });
                $('#student_id').html(list);
                var multipleCancelButton = new Choices('#student_id', {
                    removeItemButton: true,
                });
            });
        }
    </script>
@endpush

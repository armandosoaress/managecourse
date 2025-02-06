@php
    $student = json_decode($students->details);

@endphp
<div class="row">
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Status')}}</b></div>
        <p class="text-muted mb-4">
            {{$student->status}}
        </p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Country')}} </b></div>
        <p class="text-muted mb-4">
            {{$student->country}}
        </p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Country Code')}} </b></div>
        <p class="text-muted mb-4">
            {{$student->countryCode}}
        </p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Region')}}</b></div>
        <p class="mt-1">{{$student->region}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Region Name')}}</b></div>
        <p class="mt-1">{{$student->regionName}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('City')}}</b></div>
        <p class="mt-1">{{$student->city}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Zip')}}</b></div>
        <p class="mt-1">{{$student->zip}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Latitude')}}</b></div>
        <p class="mt-1">{{$student->lat}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Longitude')}}</b></div>
        <p class="mt-1">{{$student->lon}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Timezone')}}</b></div>
        <p class="mt-1">{{$student->timezone}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Isp')}}</b></div>
        <p class="mt-1">{{$student->isp}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Org')}}</b></div>
        <p class="mt-1">{{$student->org}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('As')}}</b></div>
        <p class="mt-1">{{$student->as}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Query')}}</b></div>
        <p class="mt-1">{{$student->query}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Browser Name')}}</b></div>
        <p class="mt-1">{{$student->browser_name}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Os Name')}}</b></div>
        <p class="mt-1">{{$student->os_name}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Browser Language')}}</b></div>
        <p class="mt-1">{{$student->browser_language}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Device Type')}}</b></div>
        <p class="mt-1">{{$student->device_type}}</p>
    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Referrer Host')}}</b></div>
        <p class="mt-1">{{$student->referrer_host}}</p>

    </div>
    <div class="col-md-6 ">
        <div class="form-control-label"><b>{{__('Referrer Path')}}</b></div>
        <p class="mt-1">{{$student->referrer_path}}</p>
    </div>
</div>


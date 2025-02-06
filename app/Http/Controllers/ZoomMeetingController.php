<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ZoomMeeting;
use App\Models\Student;
use App\Models\User;
use App\Models\Utility;
use App\Models\Store;
use App\Traits\ZoomMeetingTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ZoomMeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use ZoomMeetingTrait;
    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;
    const MEETING_URL = "https://api.zoom.us/v2/";

    public function index()
    {
        if(Auth::user()->can('manage zoom meeting'))
        {
            $meetings = ZoomMeeting::where('store_id', Auth::user()->current_store)->with('getCourseInfo')->get();
            // $this->statusUpdate();

            return view('zoom_meeting.index', compact('meetings'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->can('create zoom meeting')) {
            $user = Auth::user();

            $courses = Course::where('store_id', Auth::user()->current_store)->pluck('title', 'id');
            $courses->prepend(__('Select Course'), '');

            return view('zoom_meeting.create', compact('courses'));
        }else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::user()->can('create zoom meeting')) {
            $store_id = Auth::user()->current_store;

            $data['topic'] = $request->title;
            $data['start_time'] = date('y:m:d H:i:s', strtotime($request->start_date));
            $data['duration'] = (int)$request->duration;
            $data['password'] = $request->password;
            $data['host_video'] = 0;
            $data['participant_video'] = 0;
            try
            {
                $meeting_create = $this->createmitting($data);
                \Log::info('Meeting');
                \Log::info((array)$meeting_create);
                if (isset($meeting_create['success']) &&  $meeting_create['success'] == true) {
                    $meeting_id = isset($meeting_create['data']['id']) ? $meeting_create['data']['id'] : 0;
                    $start_url = isset($meeting_create['data']['start_url']) ? $meeting_create['data']['start_url'] : '';
                    $join_url = isset($meeting_create['data']['join_url']) ? $meeting_create['data']['join_url'] : '';
                    $status = isset($meeting_create['data']['status']) ? $meeting_create['data']['status'] : '';

                    $new = new ZoomMeeting();
                    $new->title = $request->title;
                    $new->meeting_id = $meeting_id;
                    $new->store_id = $store_id;
                    $new->course_id = $request->id;
                    if(!empty($request->students)){
                        $new->student_id = implode(',', $request->students);
                    }
                    $new->start_date = date('y:m:d H:i:s', strtotime($request->start_date));
                    $new->duration = $request->duration;
                    $new->start_url = $start_url;
                    $new->password = $request->password;
                    $new->join_url = $join_url;
                    $new->status = $status;
                    $new->created_by = Auth::user()->id;
                    $new->save();

                    $user = Auth::user()->current_store;
                    $creator   = Store::where('id', $user)->get();

                    $uArr = [
                        'title' => $request->input('title'),
                        'store_name'  => $creator[0]->name,
                    ];
                    // slack //
                    $settings  = Utility::notifications(Auth::user()->current_store);
                    if (isset($settings['zoom_meeting_notification']) && $settings['zoom_meeting_notification'] == 1) {
                        Utility::send_slack_msg('new_zoom_meeting',$uArr);
                    }

                    // telegram //
                    $user = Auth::user()->current_store;
                    $creator   = Store::where('id', $user)->get();
                    $settings  = Utility::notifications(Auth::user()->current_store);
                    if (isset($settings['telegram_zoom_meeting_notification']) && $settings['telegram_zoom_meeting_notification'] == 1) {
                        Utility::send_telegram_msg('new_zoom_meeting',$uArr);
                    }

                    //webhook
                    $module = 'New Zoom Meeting';
                    $webhook =  Utility::webhookSetting($module);
                    if ($webhook) {
                        $parameter = json_encode($new);
                        // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                        $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                        if ($status == true) {
                            return redirect()->back()->with('success', __('Meeting created successfully!'));
                        } else {
                            return redirect()->back()->with('error', __('Webhook call failed.'));
                        }
                    }

                    if ($request->get('synchronize_type')  == 'google_calender') {
                        $type = 'zoom_meeting';
                        $request1 = new ZoomMeeting();

                        $request1->title = $request->title;

                        $request1->start_date = $request->start_date;
                        $request1->end_date = $request->start_date;
                        Utility::addCalendarData($request, $type);
                    }

                    return redirect()->back()->with('success', __('Meeting created successfully.'));
                } else {
                    return redirect()->back()->with('error', __('Meeting not created.'));
                }
            }
            catch(\Exception $e)
            {
                return redirect()->back()->with('error', __("invalide token."));
            }
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ZoomMeeting  $zoomMeeting
     * @return \Illuminate\Http\Response
     */
    public function show(ZoomMeeting $zoomMeeting)
    {
        // if($zoomMeeting->created_by == \Auth::user()->current_store)
        // {

        return view('zoom_meeting.view', compact('zoomMeeting'));
        // }
        // else
        // {
        //     return redirect()->back()->with('error', 'permission Denied');
        // }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ZoomMeeting  $zoomMeeting
     * @return \Illuminate\Http\Response
     */
    public function edit(ZoomMeeting $zoomMeeting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ZoomMeeting  $zoomMeeting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ZoomMeeting $zoomMeeting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ZoomMeeting  $zoomMeeting
     * @return \Illuminate\Http\Response
     */
    public function destroy(ZoomMeeting $zoomMeeting)
    {
        if (Auth::user()->can('delete zoom meeting')) {
            if (!empty($zoomMeeting)) {
                $zoomMeeting->delete();
                return redirect()->back()->with('success', __('Meeting deleted successfully.'));
            } else {
                return redirect()->back()->with('error', __('Meeting not found.'));
            }
        }else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function statusUpdate()
    {
        $meetings = ZoomMeeting::where('created_by', Auth::user()->current_store)->pluck('meeting_id');
        foreach ($meetings as $meeting) {
            $data = $this->get($meeting);
            if (isset($data['data']) && !empty($data['data'])) {
                $meeting = ZoomMeeting::where('meeting_id', $meeting)->update(['status' => $data['data']['status']]);
            }
        }
    }

    public function courseByStudentId($id)
    {
        // $courses = Student::select('id')->where('id',$id)->where('store_id', \Auth::user()->current_store)->get();
        $courses = Student::select('id')->where('courses_id', $id)->where('store_id', Auth::user()->current_store)->get();

        $students = [];
        foreach ($courses as $key => $value) {
            $student = Student::select('id', 'name')->where('id', $value->id)->first();
            $students1['id'] = $student->id;
            $students1['name'] = $student->name;
            $students[] = $students1;
        }

        return \Response::json($students);
    }

    public function calender()
    {
        if (Auth::user()->can('manage zoom meeting')) {
            // $user=\Auth::user();
            // if (\Auth::user()->type == 'Owner') {
                $meetings    = ZoomMeeting::where('store_id', Auth::user()->current_store)->get();
            // }

            $transdate = Carbon::now();
            $d = date_parse_from_format("date('m')", $transdate);
            $event = ZoomMeeting::where('start_date', $d)->get();

            $arrMeeting = [];
            foreach ($meetings as $zoomMeeting) {
                $arr['id']        = $zoomMeeting['id'];
                $arr['title']     = $zoomMeeting['title'];
                $arr['start']     = $zoomMeeting['start_date'];
                $arr['className'] = 'event-primary';
                $arr['url']       = route('zoom-meeting.show', $zoomMeeting['id']);
                $arrMeeting[]        = $arr;
            }

            $calendar = array_merge($arrMeeting);
            $calendar = str_replace('"[', '[', str_replace(']"', ']', json_encode($calendar)));

            $month_meetings    = ZoomMeeting::whereMonth('start_date', date('m'))->where('store_id', Auth::user()->current_store)->get();
            return view('zoom_meeting.calendar', compact('calendar', 'month_meetings'));
        }else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function get_event_data(Request $request)
    {
        $arrayJson = [];
        if($request->get('calender_type') == 'goggle_calender')
        {

            $type ='zoom_meeting';
            $arrayJson =  Utility::getCalendarData($type);

        }
        else
        {
            $data = ZoomMeeting::get();


            foreach($data as $val)
            {

                $end_date=date_create($val->end_date);
                date_add($end_date,date_interval_create_from_date_string("1 days"));
                $arrayJson[] = [
                    "id"=> $val->id,
                    "title" => $val->title,
                    "start" => $val->start_date,
                    "end" => date_format($end_date,"Y-m-d H:i:s"),
                    "className" => $val->color,
//                    "textColor" => '#FFF',
                    "allDay" => true,
                ];
            }
        }

        return $arrayJson;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ChapterFiles;
use App\Models\Course;
use App\Models\Faq;
use App\Models\PracticesFiles;
use App\Models\QuizSettings;
use App\Models\Subcategory;
use App\Models\Utility;
use App\Exports\CoursesExport;
use App\Models\Header;
use App\Models\Store;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('manage course'))
        {
            $user = Auth::user()->current_store;
            $courses    = Course::where('store_id',$user)->where('created_by', \Auth::user()->creatorId())->with('category_id')->get();
            $category    = Category::where('store_id',$user)->where('created_by', \Auth::user()->creatorId())->get()->pluck('name','id');
            return view('course.index',compact('courses','category'));
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
        if(\Auth::user()->can('create course'))
        {
            $user = Auth::user()->current_store;
            $category = Category::where('store_id',$user)->where('created_by', \Auth::user()->creatorId())->get();
            //  $quiz = QuizSettings::where('store_id',$user)->where('created_by', \Auth::user()->creatorId())->get()->pluck('title','id');
            $preview_type = [
                'Video File' => 'Video File',
                'Image'=> 'Image',
                'iFrame' => 'iFrame'
            ];
            $level = Utility::course_level();
            return view('course.create',compact('level','category','preview_type'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
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
        if(\Auth::user()->can('create course'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                'title' => 'required|max:120',
                                'lang' => 'required|max:120',
                                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
            $has_discount                = isset($request->has_discount) ? 'on' : 'off';
            $is_free                     = isset($request->is_free) ? 'on' : 'off';
            $is_preview                  = isset($request->is_preview) ? 'on' : null;
            $has_certificate             = isset($request->has_certificate) ? 'on' : null;
            $course                      = new Course();
            $course->title               = $request->title;
            $course->course_requirements = $request->course_requirements;
            $course->course_description	 = $request->course_description	;
            $course->level               = $request->level;
            $course->lang                = $request->lang;
            $course->duration            = $request->duration;
            $course->host                = $request->host;
            if($has_certificate == 'on'){
                $course->has_certificate = 'on';
            }else{
                $course->has_certificate = 'off';
            }

            if(isset($request->category))
            {
                $validator = \Validator::make($request->all(), [
                    'subcategory' => 'required',
                ]);
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }
                $course->type = "Course";
                $course->category = $request->category;
                $course->sub_category = implode(',',$request->subcategory);
            }

            if($request->type == 'Quiz'){
                if(!empty($request->quiz))
                {
                    $course['quiz'] = implode(',', $request->quiz);
                }
                else
                {
                    $course['quiz'] = $request->quiz;
                }
            }

            if($is_free == 'off')
            {
                $validator       = \Validator::make($request->all(), ['price' => 'required',]);
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }
                $course->price = $request->price;

                if($has_discount == 'on'){
                    $validator = \Validator::make($request->all(), ['discount' => 'required',]);
                    if($validator->fails())
                    {
                        $messages = $validator->getMessageBag();
                        return redirect()->back()->with('error', $messages->first());
                    }
                    $course->has_discount = 'on';
                    $course->discount = $request->discount;
                }else{
                    $course->has_discount = 'off';
                    $course->discount = null;
                }
            }else{
                $course->is_free = 'on';
                $course->price = null;
                $course->discount = null;
                $course->has_discount = 'off';
            }

            if($is_preview == 'on')
            {
                $course->is_preview = $request->is_preview;
                $course->preview_type = $request->preview_type;

                if(!empty($request->preview_image))
                {
                    //storage limit
                    $image_size = $request->file('preview_image')->getSize();
                    $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
                    if($result==1)
                    {
                        $filenameWithExt  = $request->File('preview_image')->getClientOriginalName();
                        $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension        = $request->file('preview_image')->getClientOriginalExtension();
                        $fileNameToStores = $filename . '_' . time() . '.' . $extension;

                        $settings = Utility::getStorageSetting();
                        if($settings['storage_setting']=='local'){
                            $dir  = 'uploads/preview_image/';
                        }
                        else{
                            $dir  = 'uploads/preview_image/';
                        }
                        $course->preview_content = $fileNameToStores;
                        $path = Utility::upload_file($request,'preview_image',$fileNameToStores,$dir,[]);

                        if($path['flag'] == 1){
                            $url = $path['url'];
                        }else{
                            return redirect()->back()->with('error', __($path['msg']));
                        }
                    }
                }
                if(!empty($request->preview_video))
                {
                    $image_size = $request->file('preview_video')->getSize();
                    $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
                    if($result==1)
                    {
                        // $filenameWithExt  = $request->File('preview_video')->getClientOriginalName();
                        // $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        // $extension        = $request->file('preview_video')->getClientOriginalExtension();
                        // $fileNameToStores = $filename . '_' . time() . '.' . $extension;
                        $ext = $request->file('preview_video')->getClientOriginalExtension();
                        $fileName = 'video_' . time() . rand() . '.' . $ext;

                        $settings = Utility::getStorageSetting();
                        if ($settings['storage_setting'] == 'local') {
                            $dir = 'uploads/preview_video/';
                        } else {
                            $dir = 'uploads/preview_video/';

                        }

                        $path = Utility::upload_file($request, 'preview_video', $fileName, $dir, []);
                        $course->preview_content = $fileName;
                        if ($path['flag'] == 1) {
                            $url = $path['url'];
                        } else {
                            return redirect()->back()->with('error', __($path['msg']));
                        }
                    }
                }
                if(!empty($request->preview_iframe))
                {
                    $course->preview_content = $request->preview_iframe;
                }
            }else{
                $course->is_preview = 'off';
            }

            if(!empty($request->thumbnail))
            {
                $image_size = $request->file('thumbnail')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
                if($result==1)
                {
                    $filenameWithExt  = $request->File('thumbnail')->getClientOriginalName();
                    $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension        = $request->file('thumbnail')->getClientOriginalExtension();
                    $fileNameToStores = $filename . '_' . time() . '.' . $extension;
                    $settings = Utility::getStorageSetting();
                    if($settings['storage_setting']=='local'){
                        $dir  = 'uploads/thumbnail/';
                    }
                    else{
                        $dir  = 'uploads/thumbnail/';
                    }
                    $course->thumbnail = $fileNameToStores;
                    $path = Utility::upload_file($request,'thumbnail',$fileNameToStores,$dir,[]);
                    if($path['flag'] == 1){
                        $url = $path['url'];
                    }else{
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                }
            }
            $course->featured_course = $request->featured_course;
            // $course->type = $request->type;
            $course->type = "Course";
            $course->status = 'Active';
            $course->meta_keywords = $request->meta_keywords;
            $course->meta_description = $request->meta_description;
            $course->store_id =  Auth::user()->current_store;;
            $course->created_by = Auth:: user()->creatorId();

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages );
            }

            if(!empty($request->meta_image))
            {
                $image_size = $request->file('meta_image')->getSize();
                $result = Utility::updateStorageLimit(Auth::user()->creatorId(), $image_size);
                if($result==1)
                {
                    $filenameWithExt  = $request->File('meta_image')->getClientOriginalName();
                    $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension        = $request->file('meta_image')->getClientOriginalExtension();
                    $fileNameToStoresmetaimage =  'meta_image'.'_' . time(). '.' . $extension;
                }
                $settings = Utility::getStorageSetting();
                if($settings['storage_setting']=='local'){
                    $dir  = 'uploads/meta_image/';
                }
                else{
                    $dir  = 'uploads/meta_image/';
                }
                $course->meta_image = $fileNameToStoresmetaimage;
                $path = Utility::upload_file($request,'meta_image',$fileNameToStoresmetaimage,$dir,[]);
                if($path['flag'] == 1){
                    $url = $path['url'];
                }else{
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }
            $course->save();
            $course_id = Crypt::encrypt($course->id);

            $user = Auth::user()->current_store;
            $creator   = Store::where('id',$user)->get();
            $uArr = [
                'course_title' => $request->input('title'),
                'store_name'  => $creator[0]->name,
            ];
            // slack //
            $settings  = Utility::notifications(Auth::user()->current_store);
            if(isset($settings['course_notification']) && $settings['course_notification'] ==1){
                Utility::send_slack_msg('new_course',$uArr);
            }

            // telegram //
            $settings  = Utility::notifications(Auth::user()->current_store);
            if(isset($settings['telegram_course_notification']) && $settings['telegram_course_notification'] ==1){
                Utility::send_telegram_msg('new_course',$uArr);
            }

            //webhook
            $module = 'New Course';
            $webhook =  Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($course);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status == true) {
                    return redirect()->route('course.edit',$course_id)->with('success', __('Course created successfully.'));
                } else {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }

            return redirect()->route('course.edit',$course_id)->with('success', __('Course created successfully!'). ((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        $quiz_id = explode(',',$course->quiz);
        $sub_id = explode(',',$course->sub_category);
        $quiz = QuizSettings::whereIn('id',$quiz_id)->get()->pluck('title')->toArray();
        $sub = Subcategory::whereIn('id',$sub_id)->get()->pluck('name')->toArray();
        return view('course.view',compact('course','quiz','sub'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(\Auth::user()->can('edit course'))
        {
            $user = Auth::user()->current_store;
            $course          = Course::find(Crypt::decrypt($id));
            $category        = Category::where('store_id',$user)->where('created_by', \Auth::user()->creatorId())->get()->pluck('name','id');
            $sub_category    = Subcategory::where('category',$course->category)->get()->pluck('name','id');
            $level           = Utility::course_level();
            $status          = Utility::status();
            $course_id       = $id;
            $headers         = Header::where('course',Crypt::decrypt($id))->get();
            $practices_files = PracticesFiles::where('course_id',Crypt::decrypt($id))->get();
            $faqs = Faq::where('course_id',Crypt::decrypt($id))->get();
            $preview_type = [
                'Video File' => 'Video File',
                'Image'=> 'Image',
                'iFrame' => 'iFrame'
            ];

            return view('course.edit',compact('practices_files','faqs','sub_category','course','category','level','status','course_id','headers','preview_type'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {

        if(Auth::user()->can('edit course'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                'title' => 'required|max:120',
                            ]
            );

            $has_discount        = isset($request->has_discount) ? 'on' : 'off';
            $is_free        = isset($request->is_free) ? 'on' : 'off';
            $is_preview        = isset($request->is_preview) ? 'on' : null;
            $has_certificate        = isset($request->has_certificate) ? 'on' : null;
            $course->title = $request->title;
            $course->course_requirements = $request->course_requirements;
            $course->course_description	 = $request->course_description	;
            $course->level = $request->level;
            $course->lang = $request->lang;
            $course->duration = $request->duration;
            $course->host = $request->host;
            if($has_certificate == 'on'){
                $course->has_certificate = 'on';
            }else{
                $course->has_certificate = 'off';
            }
            if($is_free == 'off'){
                $validator = \Validator::make($request->all(), ['price' => 'required',]);
                $course->is_free = 'off';
                $course->price = $request->price;

                if($has_discount == 'on'){
                    $validator = \Validator::make($request->all(), ['discount' => 'required',]);
                    $course->has_discount = 'on';
                    $course->discount = $request->discount;
                }else{
                    $course->has_discount = 'off';
                    $course->discount = null;
                }
            }else{
                $course->is_free = 'on';
                $course->price = null;
                $course->discount = null;
                $course->has_discount = 'off';
            }
            if(isset($request->category))
            {
                $course->category = $request->category;
            }
            if(isset($request->subcategory)){
                $course->sub_category = implode(',',$request->subcategory);
            }
            if($request->type == 'Quiz'){
                if(!empty($request->quiz))
                {
                    $course['quiz'] = implode(',', $request->quiz);
                }
                else
                {
                    $course['quiz'] = $request->quiz;
                }
            }else{
                $course->quiz = null;
            }
            if($is_preview == 'on')
            {
                $course->is_preview = $request->is_preview;
                $course->preview_type = $request->preview_type;
                if(!empty($request->preview_image))
                {
                    $dir  = 'uploads/preview_image/';
                    $file_path = $dir. $course->preview_image;
                    $image_size = $request->file('preview_image')->getSize();
                    $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                    if($result==1) {
                        Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                        $filenameWithExt  = $request->File('preview_image')->getClientOriginalName();
                        $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension        = $request->file('preview_image')->getClientOriginalExtension();
                        $fileNameToStores = $filename . '_' . time() . '.' . $extension;

                        $settings = Utility::getStorageSetting();
                        if($settings['storage_setting']=='local'){
                            $dir  = 'uploads/preview_image/';
                        }
                        else{
                            $dir  = 'uploads/preview_image/';
                        }
                        $course->preview_content = $fileNameToStores;
                        $path = Utility::upload_file($request,'preview_image',$fileNameToStores,$dir,[]);

                        if($path['flag'] == 1){
                            $url = $path['url'];
                        }else{
                            return redirect()->back()->with('error', __($path['msg']));
                        }
                    }
                }
                if(!empty($request->preview_video))
                {
                    $dir  = 'uploads/preview_video/';
                    $file_path = $dir. $course->preview_video;
                    $image_size = $request->file('preview_video')->getSize();
                    $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                    if($result==1) {
                        Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                        $filenameWithExt  = $request->File('preview_video')->getClientOriginalName();
                        $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        $extension        = $request->file('preview_video')->getClientOriginalExtension();
                        $fileNameToStores = $filename . '_' . time() . '.' . $extension;

                        $settings = Utility::getStorageSetting();
                        if($settings['storage_setting']=='local'){
                            $dir  = 'uploads/preview_video/';
                        }
                        else{
                            $dir  = 'uploads/preview_video/';
                        }
                        $course->preview_content = $fileNameToStores;
                        $path = Utility::upload_file($request,'preview_video',$fileNameToStores,$dir,[]);

                        if($path['flag'] == 1){
                            $url = $path['url'];
                        }else{
                            return redirect()->back()->with('error', __($path['msg']));
                        }
                    }
                }
            }else{
                $course->is_preview = 'off';
            }
            if(!empty($request->thumbnail))
            {
                $dir  = 'uploads/thumbnail/';
                $file_path = $dir.$course->thumbnail;
                $image_size = $request->file('thumbnail')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                if($result==1) {
                    Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                    $filenameWithExt  = $request->File('thumbnail')->getClientOriginalName();
                    $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension        = $request->file('thumbnail')->getClientOriginalExtension();
                    $fileNameToStores = $filename . '_' . time() . '.' . $extension;

                    $settings = Utility::getStorageSetting();
                    if($settings['storage_setting']=='local'){
                        $dir  = 'uploads/thumbnail/';
                    }
                    else{
                        $dir  = 'uploads/thumbnail/';
                    }
                    $course->thumbnail = $fileNameToStores;
                    $path = Utility::upload_file($request,'thumbnail',$fileNameToStores,$dir,[]);

                    if($path['flag'] == 1){
                        $url = $path['url'];
                    }else{
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                }
            }
            $course->featured_course = $request->featured_course;
            // $course->type = $request->type;
            $course->type = "Course";
            $course->status = $request->status;

            $course->created_by = \Auth:: user()->creatorId();
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
            }
            $course->update();

            return redirect()->back()->with('success', __('Course updated successfully!'). ((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function CourseSeoUpdate(Request $request, $id)
    {
        if(\Auth::user()->can('edit course'))
        {
            $course = Course::find($id);
            $course->meta_keywords = $request->meta_keywords;
            $course->meta_description = $request->meta_description;
            if(!empty($request->meta_image))
            {
                $dir  = 'uploads/meta_image/';
                $file_path = $dir . $course->meta_image;
                $image_size = $request->file('meta_image')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                if($result==1) {
                    Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                    $filenameWithExt  = $request->File('meta_image')->getClientOriginalName();
                    $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension        = $request->file('meta_image')->getClientOriginalExtension();
                    $fileNameToStoresmetaimage =  'meta_image'.'_' . $id. '.' . $extension;

                    $settings = Utility::getStorageSetting();
                    if($settings['storage_setting']=='local'){
                        $dir  = 'uploads/meta_image/';
                    }
                    else{
                        $dir  = 'uploads/meta_image/';
                    }
                    $course->meta_image = $fileNameToStoresmetaimage;
                    $path = Utility::upload_file($request,'meta_image',$fileNameToStoresmetaimage,$dir,[]);
                    if($path['flag'] == 1){
                        $url = $path['url'];
                    }else{
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                }
            }
            if(!empty($request->meta_image)){
                $course->meta_image = $fileNameToStoresmetaimage;
            }

            $course->update();
            return redirect()->back()->with('success', __('Course updated successfully!'). ((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        if(\Auth::user()->can('delete course'))
        {
            $meta_image = 'uploads/meta_image/'.$course->meta_image;
            $result = Utility::changeStorageLimit(\Auth::user()->creatorId(), $meta_image);

            $thumbnail = 'uploads/thumbnail/'.$course->thumbnail;
            $result = Utility::changeStorageLimit(\Auth::user()->creatorId(), $thumbnail);

            $preview_image = 'uploads/preview_image/'.$course->preview_image;
            $result = Utility::changeStorageLimit(\Auth::user()->creatorId(), $preview_image);

            $course->delete();
            return redirect()->back()->with('success', __('Course deleted successfully.'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getsubcategory(Request $request)
    {
        $user = Auth::user()->current_store;
        $subcategory = Subcategory::where('store_id',$user)->where('created_by', '=', \Auth::user()->creatorId())->where('category', $request->category_id)->get()->pluck('name', 'id')->toArray();
        return response()->json($subcategory);
    }

    public function practicesFiles(Request $request,$id)
    {
        if(\Auth::user()->can('upload practice file'))
        {
            $course_id = Crypt::decrypt($id);
            $file_name = [];

            if(!empty($request->file) && count($request->file) > 0)
            {
                foreach($request->file as $key => $file)
                {
                    $filenameWithExt = $file->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $file->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                    $file_name[]     = $fileNameToStore;
                    $settings = Utility::getStorageSetting();
                    if($settings['storage_setting']=='local'){
                        $dir  = 'uploads/practices/';
                    }
                    else{
                        $dir  = 'uploads/practices/';
                    }
                    $path = Utility::keyWiseUpload_file($request,'file',$fileNameToStore,$dir,$key,[]);
                    if($path['flag'] == 1){
                        $url = $path['url'];
                    }else{
                        return response()->json([
                                'status' => 'error',
                                'error' =>  __($path['msg']),
                            ]
                        );
                    }
                }
            }
            foreach($file_name as $file)
            {
                $uploded_files =
                    PracticesFiles::create(
                    [
                        'course_id' => $course_id,
                        'file_name' => $filename,
                        'files' => $file,
                    ]
                );
            }
            return response()->json([
                    'status' => 'success',
                    'success' =>  __('Successfully added!'),
                ]
            );
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function fileDelete($id)
    {
        if(\Auth::user()->can('delete practice file'))
        {
            $img_id = PracticesFiles::find($id);
            $dir = storage_path('uploads/practices/');
            if(!empty($img_id->files))
            {
                if(!file_exists($dir . $img_id->files))
                {
                    $content = DB::table('practices_files')->where('id ', '=', $id)->delete();
                    return response()->json(
                        [
                            'error' => __('File not exists in folder!'),
                            'id' => $id,
                        ]
                    );
                }
                else
                {
                    unlink($dir.$img_id->files);
                    DB::table('practices_files')->where('id', '=', $id)->delete();
                    return response()->json(
                        [
                            'success' => __('Record deleted successfully!'),
                            'id' => $id,
                        ]
                    );
                }
            }
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function editFileName($id)
    {
        if(\Auth::user()->can('edit practice file'))
        {
            $file_name = PracticesFiles::find($id);
            return view('course.editFileName',compact('file_name'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function updateFileName(Request $request,$id)
    {
        if(\Auth::user()->can('edit practice file'))
        {
            $file = PracticesFiles::find($id);
            $file->file_name = $request->file_name;
            $file->update();
            return redirect()->back()->with('success', __('Filename updated successfully') );
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function export()
    {
        if(\Auth::user()->can('export course'))
        {
            $name = 'course_' . date('Y-m-d i:h:s');
            $data = Excel::download(new CoursesExport(), $name . '.xlsx');ob_end_clean();

            return $data;
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

}

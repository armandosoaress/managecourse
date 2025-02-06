<?php

namespace App\Http\Controllers;

use App\Models\ChapterFiles;
use App\Models\Chapters;
use App\Models\Utility;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ChaptersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($course_id,$header_id)
    {
        if(\Auth::user()->can('create chapter'))
        {
            $chapter_type = Utility::chapter_type();
            return view('chapters.create',compact('header_id','chapter_type','course_id'));
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
    public function store(Request $request,$course_id,$header_id)
    {
        if(\Auth::user()->can('create chapter'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                'name' => 'required|max:120',
                            ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                $msg['flag'] = 'error';
                $msg['msg']  = $messages->first();
                return $msg;
            }
            $chapters = new Chapters();
            $chapters->header_id = $header_id;
            $chapters->name = $request->name;
            $chapters->duration = $request->duration;
            $chapters->course_id = Crypt::decrypt($course_id);

            //    if($request->is_preview == null){
            //        $chapters->is_preview = 'off';
            //    }else{
            //        $chapters->is_preview = $request->is_preview;
            //    }
            $chapters->chapter_description = $request->chapter_description;
            $chapters->type = $request->type;

            if(!empty($request->video_url)){
                $chapters->video_url = $request->video_url;
            }
            if(!empty($request->video_file)){
                $image_size = $request->file('video_file')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);
                if($result==1)
                {
                    $settings = Utility::getStorageSetting();
                    $video = $request->file('video_file');
                    $ext = $video->getClientOriginalExtension();
                    $fileName = 'video_' . time() . rand() . '.' . $ext;
                    if ($settings['storage_setting'] == 'local') {
                        $dir = 'uploads/chapters/';
                    } else {
                        $dir = 'uploads/chapters/';

                    }

                    $path = Utility::upload_file($request, 'video_file', $fileName, $dir, []);
                    $chapters->video_file = $fileName;
                    if ($path['flag'] == 1) {
                        $url = $path['url'];
                    } else {
                        $res = [
                            'flag' => 0,
                            'msg' => $path['msg'],
                        ];
                        return $res;
                    }
                }
            }
            if(!empty($request->iframe)){
                $chapters->iframe = $request->iframe;
            }
            if(!empty($request->text_content)){
                $chapters->text_content = $request->text_content;
            }
            $file_name = [];
            $error_msg=[];

            if(!empty($request->multiple_files) && count($request->multiple_files) > 0)
            {
                // $validator = \Validator::make($request->all(), [
                //     'multiple_files' => 'max:40000',
                // ]);
                // if($validator->fails())
                // {
                //     $msg = $validator->getMessageBag()->first();
                //     return $msg;
                // }
                foreach($request->multiple_files as $key => $file)
                {
                    $filenameWithExt = $file->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $file->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                    $settings = Utility::getStorageSetting();
                    $dir  = 'uploads/chapters/';
                    $path = Utility::keyWiseUpload_file($request,'multiple_files',$fileNameToStore,$dir,$key,[]);

                    if($path['flag'] == 1)
                    {
                        $url = $path['url'];
                        $file_name[] = $fileNameToStore;

                    }else{
                        $error_msg[] = $path['msg'];
                    }
                }

            }
            $chapters->save();
            foreach($file_name as $file)
            {
                $objStore = ChapterFiles::create(
                    [
                        'chapter_id' => $chapters->id,
                        'chapter_files' => $file,
                    ]
                );
            }

            if($error_msg ){
                $error_msg = count($error_msg). $error_msg[0];
            }else{
                $error_msg = '';
            }

            if(!empty($chapters))
            {
                $msg['flag'] = 'success';
                $msg['msg']  = __('Content Created Successfully').$error_msg;
            }
            else
            {
                $msg['flag'] = 'error';
                $msg['msg']  = __('Content Failed to Create');
            }

            return $msg;
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Chapters  $chapters
     * @return \Illuminate\Http\Response
     */
    public function show(Chapters $chapters)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Chapters  $chapters
     * @return \Illuminate\Http\Response
     */
    public function edit($course_id,$id,$header_id)
    {
        if(\Auth::user()->can('edit chapter'))
        {
            $chapters = Chapters::find($id);
            $chapter_type = Utility::chapter_type();
            $file = ChapterFiles::where('chapter_id',$id)->get();
            return view('chapters.edit',compact('chapters','chapter_type','header_id','file','course_id'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Chapters  $chapters
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chapters $chapters,$header_id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Chapters  $chapters
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,$course_id,$header_id)
    {
        if(\Auth::user()->can('delete chapter'))
        {
            $chapters = Chapters::find($id);
            if(!empty($chapters->video_file))
            {
                $video_file = 'uploads/chapters/'.$chapters->video_file;
                $result = Utility::changeStorageLimit(\Auth::user()->creatorId(), $video_file);
            }
            $contents = ChapterFiles::where('chapter_id',$id)->get();
            foreach($contents as $content){
                $dir = storage_path('uploads/chapters/'.$content->chapter_files);
                if(file_exists($dir)){
                    unlink($dir);
                }
            }
            ChapterFiles::where('chapter_id',$id)->delete();
            $chapters->delete();
            return redirect()->back()->with('success', __('Chapter deleted successfully.'));
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function fileDelete($id)
    {
        if(\Auth::user()->can('delete chapter'))
        {
            $img_id = ChapterFiles::find($id);
            $dir = storage_path('uploads/chapters/');
            if(!empty($img_id->chapter_files))
            {
                if(!file_exists($dir . $img_id->chapter_files))
                {
                    $content = DB::table('chapter_files')->where('id ', '=', $id)->delete();
                    return response()->json(
                        [
                            'error' => __('File not exists in folder!'),
                            'id' => $id,
                        ]
                    );
                }
                else
                {
                    unlink($dir.$img_id->chapter_files);
                    DB::table('chapter_files')->where('id', '=', $id)->delete();
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

    public function ContentsUpdate(Request $request, $id)
    {
        if(\Auth::user()->can('edit chapter'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                'name' => 'required|max:120',
                            ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                $msg['flag'] = 'error';
                $msg['msg']  = $messages->first();
                return $msg;
            }
            $chapters            = Chapters::find($id);
            $chapters->name      = $request->name;
            $chapters->duration  = $request->duration;
            //   if($request->is_preview == null)
            //   {
            //       $chapters->is_preview = 'off';
            //   }
            //   else
            //   {
            //       $chapters->is_preview = $request->is_preview;
            //   }
            $chapters->chapter_description = $request->chapter_description;
            $chapters->type                = $request->type;
            if(!empty($request->video_url))
            {
                $chapters->video_url = $request->video_url;
            }
            $file_name = [];
            if(!empty($request->video_file))
            {
                $dir  = 'uploads/chapters/';
                $file_path = $dir. $chapters->video_file;
                $image_size = $request->file('video_file')->getSize();
                $result = Utility::updateStorageLimit(\Auth::user()->creatorId(), $image_size);

                if($result==1) {
                    Utility::changeStorageLimit(\Auth::user()->creatorId(), $file_path);
                    $filenameWithExt  = $request->File('video_file')->getClientOriginalName();
                    $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension        = $request->file('video_file')->getClientOriginalExtension();
                    $fileNameToStores = $filename . '_' . time() . '.' . $extension;

                    $settings = Utility::getStorageSetting();
                    if($settings['storage_setting']=='local'){
                        $dir  = 'uploads/chapters/';
                    }
                    else{
                        $dir  = 'uploads/chapters/';
                    }
                    $chapters->video_file = $fileNameToStores;
                    $path = Utility::upload_file($request,'video_file',$fileNameToStores,$dir,[]);

                    if($path['flag'] == 1)
                    {
                        $url = $path['url'];
                        $file_name[] = $fileNameToStores;
                    }
                }
            }
            if(!empty($request->iframe))
            {
                $chapters->iframe = $request->iframe;
            }
            if(!empty($request->text_content))
            {
                $chapters->text_content = $request->text_content;
            }
            $file_name = [];
            if(!empty($request->multiple_files) && count($request->multiple_files) > 0)
            {
                foreach($request->multiple_files as $key => $file)
                {
                    $filenameWithExt = $file->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $file->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                    $settings = Utility::getStorageSetting();
                    $dir  = 'uploads/chapters/';
                    $path = Utility::keyWiseUpload_file($request,'multiple_files',$fileNameToStore,$dir,$key,[]);

                    if($path['flag'] == 1)
                    {
                        $objStore = ChapterFiles::create(
                            [
                                'chapter_id' => $chapters->id,
                                'chapter_files' => $fileNameToStore,
                            ]
                        );
                    }else{
                        $file_name[] = $path['msg'];
                    }

                }
                if($file_name ){
                $error_msg= count($file_name). $file_name[0];
                }else{
                    $error_msg= '';
                }
            }
            $chapters->update();


            if(!empty($chapters))
            {
                $msg['flag'] = 'success';
                $msg['msg']  = __('Content Updated Successfully');
            }
            else
            {
                $msg['flag'] = 'error';
                $msg['msg']  = __('Content Failed to Update');
            }

            return $msg;
        }
        else{
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


}

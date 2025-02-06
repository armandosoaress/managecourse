<?php

namespace App\Http\Controllers\Student\Auth;

use App\Models\Blog;
use App\Http\Controllers\Controller;
use App\Models\Utility;
use App\Models\PageOption;
use App\Models\Store;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class StudentForgotPasswordController extends Controller
{
    public function __construct()
    {
        if(Auth::check())
        {
            \App::setLocale(\Auth::user()->lang);
        }
    }

    public function showLinkRequestForm($slug)
    {
        $store          = Store::where('slug', $slug)->first();
        if(isset($store->lang))
        {
            $lang = session()->get('lang');

            if(!isset($lang))
            {
                session(['lang' => $store->lang]);
                $storelang=session()->get('lang');
                \App::setLocale(isset($storelang) ? $storelang : 'en');
            }
            else
            {
                session(['lang' => $lang]);
                $storelang=session()->get('lang');
                \App::setLocale(isset($storelang) ? $storelang : 'en');
            }
        }
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id);
        $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }

        $getStoreThemeSetting = Utility::getStoreThemeSetting($store->id, $store->theme_dir);
        $getStoreThemeSetting1 = [];

        if(!empty($getStoreThemeSetting['dashboard'])) {
            $getStoreThemeSetting = json_decode($getStoreThemeSetting['dashboard'], true);
            $getStoreThemeSetting1 = Utility::getStoreThemeSetting($store->id, $store->theme_dir);
        }
        if (empty($getStoreThemeSetting)) {
            $path = storage_path()."/uploads/" . $store->theme_dir . "/" . $store->theme_dir . ".json" ;
            $getStoreThemeSetting = json_decode(file_get_contents($path), true);
        }
        return view('storefront.' . $store->theme_dir . '.student.password',compact('store','page_slug_urls','slug','blog','demoStoreThemeSetting','getStoreThemeSetting'));
    }

    public function postStudentEmail(Request $request,$slug)
    {
        $request->validate(
            [
                'email' => 'required|email|exists:students',
            ]
        );

        $token = \Str::random(60);

        DB::table('password_resets')->insert(
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]
        );
        try{
            $store = Store::where('slug', $slug)->first();

            config(
                [
                    'mail.driver' => $store->mail_driver,
                    'mail.host' => $store->mail_host,
                    'mail.port' => $store->mail_port,
                    'mail.encryption' => $store->mail_encryption,
                    'mail.username' => $store->mail_username,
                    'mail.password' => $store->mail_password,
                    'mail.from.address' => $store->mail_from_address,
                    'mail.from.name' => $store->mail_from_name,
                ]
            );
            Mail::send(
                'storefront.'.$store->theme_dir.'.student.resetmail', ['token' => $token,'slug'=>$slug], function ($message) use ($request ,$store){
                    $message->from($store->mail_username, $store->mail_from_name);
                    $message->to($request->email);
                    $message->subject('Reset Password Notification');

                    // return back()->with('success', 'We have e-mailed your password reset link!');
                }
            );
        }catch(\Exception $e)
        {
            $smtp_error['status'] = false;
            $smtp_error['msg'] = $e->getMessage();

        }
        // return redirect()->back()->with('error', ((isset($smtp_error['status'])) ?   $smtp_error['msg'] : ''))->with('status', 'emails');
        return redirect()->back()->with('error', __('We have e-mailed your password reset link!') . ((isset($smtp_error['status'])) ? '<br> <span class="text-danger">' . $smtp_error['msg'] . '</span>' : ''));
    }

    public function getStudentPassword($slug,$token)
    {
        $store          = Store::where('slug', $slug)->first();
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id);
        $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }

        $getStoreThemeSetting = Utility::getStoreThemeSetting($store->id, $store->theme_dir);
        $getStoreThemeSetting1 = [];

        if(!empty($getStoreThemeSetting['dashboard'])) {
            $getStoreThemeSetting = json_decode($getStoreThemeSetting['dashboard'], true);
            $getStoreThemeSetting1 = Utility::getStoreThemeSetting($store->id, $store->theme_dir);
        }
        if (empty($getStoreThemeSetting)) {
            $path = storage_path()."/uploads/" . $store->theme_dir . "/" . $store->theme_dir . ".json" ;
            $getStoreThemeSetting = json_decode(file_get_contents($path), true);
        }

        return view('storefront.' . $store->theme_dir . '.student.newpassword', compact('token','slug','store','page_slug_urls','blog','demoStoreThemeSetting','getStoreThemeSetting'));
    }

    public function updateStudentPassword(Request $request,$slug)
    {
        $request->validate(
            [
                'email' => 'required|email|exists:students',
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required',

            ]
        );

        $updatePassword = DB::table('password_resets')->where(
            [
                'email' => $request->email,
                'token' => $request->token,
            ]
        )->first();

        if(!$updatePassword)
        {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        $user = Student::where('email', $request->email)->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return redirect()->route('student.loginform',$slug)->with('success', 'Your password has been changed.');

    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginDetail;
use App\Models\Utility;
use App\Models\User;
use App\Models\Plan;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function __construct()
    {
        if(!file_exists(storage_path() . "/installed"))
        {
            header('location:install');
            die;
        }
    }

    /*protected function authenticated(Request $request, $user)
    {
        if($user->delete_status == 1)
        {
            auth()->logout();
        }

        return redirect('/check');
    }*/

    public function store(LoginRequest $request)
    {
        if(Utility::getValByName('recaptcha_module') == 'yes')
        {
            $validation['g-recaptcha-response'] = 'required';
        }else
        {
            $validation=[];
        }
        $this->validate($request, $validation);

        $request->authenticate();

        $request->session()->regenerate();
        $user = Auth::user();


        $ip = $_SERVER['REMOTE_ADDR']; // your ip address here

        //  $ip = '49.36.83.154'; // This is static ip address

        $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
        if (isset($query['status']) &&  $query['status'] != 'fail') {
            $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
            if ($whichbrowser->device->type == 'bot') {
                return;
            }
            $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;

            /* Detect extra details about the user */
            $query['browser_name'] = $whichbrowser->browser->name ?? null;
            $query['os_name'] = $whichbrowser->os->name ?? null;
            $query['browser_language'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
            $query['device_type'] = Utility::get_device_type($_SERVER['HTTP_USER_AGENT']);
            $query['referrer_host'] = !empty($referrer['host']);
            $query['referrer_path'] = !empty($referrer['path']);

            isset($query['timezone'])?date_default_timezone_set($query['timezone']):'';

            $user = \Auth::user();
            $json = json_encode($query);
            if ($user->type != 'Owner' && $user->type != 'super admin') {
                $login_detail = LoginDetail::create([
                    'user_id' =>  $user->id,
                    'store_id' =>  $user->current_store,
                    'ip' => $ip,
                    'date' => date('Y-m-d H:i:s'),
                    'details' => $json,
                    'created_by' => \Auth::user()->creatorId(),
                ]);
            }
        }

        if($user->delete_status == 1)
        {
            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();
        }

        $user = Auth::user();
        if($user->type == 'Owner')
        {
            $plan = Plan::find($user->plan);

            if($plan)
            {
                if($plan->duration != 'lifetime')
                {
                    // $datetime1 = new \DateTime($user->plan_expire_date);
                    // $datetime2 = new \DateTime(date('Y-m-d'));
                    // //       $interval = $datetime1->diff($datetime2);
                    // $interval = $datetime2->diff($datetime1);
                    // $days = $interval->format('%r%a');

                    // if($days <= 0)

                    $datetime1 = $user->plan_expire_date;
                    $datetime2 = date('Y-m-d');
                    if(!empty($datetime1) && $datetime1 < $datetime2)
                    {
                        $user->assignPlan(1);

                        return redirect()->intended(RouteServiceProvider::HOME)->with('error', __('Your Plan is expired.'));
                    }
                }

            }
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function showLoginForm($lang = '')
    {
        if(empty($lang))
        {
            $lang = \App\Models\Utility::getValByName('default_language');
        }
        \App::setLocale($lang);

        return view('auth.login', compact('lang'));
    }

    public function showLinkRequestForm($lang = '')
    {
        if(empty($lang))
        {
            $lang = \App\Models\Utility::getValByName('default_language');
        }

        \App::setLocale($lang);

        /* return view('auth.forgot-password', compact('lang'));*/
       return view('auth.passwords.email', compact('lang'));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}


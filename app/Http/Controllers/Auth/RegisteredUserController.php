<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewUser;
use App\Models\Store;
use App\Models\User;
use App\Models\UserStore;
use App\Models\Plan;
use App\Models\Utility;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{

    // use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function create()
    {
        return view('auth.register');
    }


    protected function validator(array $data)
    {
        return Validator::make(
            $data, [
                    'name' => [
                        'required',
                        'string',
                        'max:255',
                    ],
                    'store_name' => [
                        'required',
                        'string',
                        'max:255',
                    ],
                    'email' => [
                        'required',
                        'string',
                        'email',
                        'max:255',
                        'unique:users',
                    ],
                    'password' => [
                        'required',
                        'string',
                        'min:8',
                        'confirmed',
                    ],
                ]
        );
    }


    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    public function store(Request $request)
    {
        if(Utility::getValByName('email_verification') == 'on')
        {
            if(Utility::getValByName('recaptcha_module') == 'yes')
            {
                $validation['g-recaptcha-response'] = 'required';
            }else
            {
                $validation=[];
            }
            $this->validate($request, $validation);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $objUser = User::create([
                'name' => $request->name,
                'email' =>$request->email,
                'password' => Hash::make($request->password),
                'type' => 'Owner',
                'lang' => Utility::getValByName('default_language'),
                'avatar' => 'avatar.png',
                'created_by' => 1,
            ]);


            Auth::login($objUser);

            try{
                Utility::getSMTPDetails(1);
                event(new Registered($objUser));
                $objStore = Store::create(
                    [
                        'created_by' => $objUser->id,
                        'name' => $request->store_name,
                        'email' => $request->email,
                        'logo' => !empty($settings['logo']) ? $settings['logo'] : 'logo.png',
                        'invoice_logo' => !empty($settings['logo']) ? $settings['logo'] : 'invoice_logo.png',
                        'lang' =>Utility::getValByName('default_language'),
                        'currency' => !empty($settings['currency_symbol']) ? $settings['currency_symbol'] : '$',
                        'currency_code' => !empty($settings['currency']) ? $settings['currency'] : 'USD',
                        'paypal_mode' => 'sandbox',
                    ]
                );
                $role_r = Role::findByName('Owner');
                $objUser->assignRole($role_r);
                $objStore->enable_storelink = 'on';

                $objStore->theme_dir            = 'theme1';
                $objStore->store_theme          = 'yellow-style.css';
                $objStore->header_name          = 'Course Certificate';
                $objStore->certificate_template = 'template1';
                $objStore->certificate_color    = 'b10d0d';
                $objStore->certificate_gradiant = 'color-one';
                $objStore->save();

                $objUser->current_store = $objStore->id;
                $objUser->plan          = Plan::first()->id;
                $objUser->save();
                UserStore::create(
                    [
                        'user_id' => $objUser->id,
                        'store_id' => $objStore->id,
                        'permission' => 'Owner',
                    ]
                );


            }catch(\Exception $e){

                $objUser->delete();

                return redirect()->route('register',$objUser->lang)->with('status', __('Email SMTP settings does not configure so please contact to your site admin.'));
            }
            return view('auth.verify');
        }
        else{
            if(Utility::getValByName('recaptcha_module') == 'yes')
            {
                $validation['g-recaptcha-response'] = 'required';
            }else
            {
                $validation=[];
            }
            $this->validate($request, $validation);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $objUser = User::create([
                'name' => $request->name,
                'email' =>$request->email,
                'password' => Hash::make($request->password),
                'type' => 'Owner',
                'lang' => Utility::getValByName('default_language'),
                'avatar' => 'avatar.png',
                'created_by' => 1,
            ]);


            Auth::login($objUser);

            // event(new Registered($objUser));
            $objStore = Store::create(
                [
                    'created_by' => $objUser->id,
                    'name' => $request->store_name,
                    'email' => $request->email,
                    'logo' => !empty($settings['logo']) ? $settings['logo'] : 'logo.png',
                    'invoice_logo' => !empty($settings['logo']) ? $settings['logo'] : 'invoice_logo.png',
                    'lang' => Utility::getValByName('default_language'),
                    'currency' => !empty($settings['currency_symbol']) ? $settings['currency_symbol'] : '$',
                    'currency_code' => !empty($settings['currency']) ? $settings['currency'] : 'USD',
                    'paypal_mode' => 'sandbox',
                ]
            );

            $objStore->enable_storelink = 'on';

            $objStore->theme_dir            = 'theme1';
            $objStore->store_theme          = 'yellow-style.css';
            $objStore->header_name          = 'Course Certificate';
            $objStore->certificate_template = 'template1';
            $objStore->certificate_color    = 'b10d0d';
            $objStore->certificate_gradiant = 'color-one';
            $objStore->save();

            $objUser->email_verified_at = date("H:i:s");
            $objUser->current_store = $objStore->id;
            $objUser->plan          = Plan::first()->id;
            $objUser->save();
            $role_r = Role::findByName('Owner');
            $objUser->assignRole($role_r);
            UserStore::create(
                [
                    'user_id' => $objUser->id,
                    'store_id' => $objStore->id,
                    'permission' => 'Owner',
                ]
            );
            try {
                Utility::getSMTPDetails(1);
                Mail::to($objUser->email)->send(new NewUser($objUser,$request['password']));

            } catch (\Throwable $th) {

            }
            return redirect(RouteServiceProvider::HOME);
        }
    }

    public function showRegistrationForm($lang = 'en')
    {
        if($lang == '')
        {
            $lang = \App\Models\Utility::getValByName('default_language');
        }
        \App::setLocale($lang);

        // return view('auth.register', compact('lang'));
        if(\App\Models\Utility::getValByName('signup')=='on'){
            return view('auth.register', compact('lang'));
        }
        else{
            return abort('404', 'Page not found');
        }
    }

}

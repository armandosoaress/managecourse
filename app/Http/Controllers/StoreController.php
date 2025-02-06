<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogSocial;
use App\Models\Category;
use App\Models\Chapters;
use App\Models\ChapterStatus;
use App\Models\Course;
use App\Models\Faq;
use App\Models\Header;
use App\Models\Order;
use App\Models\PageOption;
use App\Models\PixelFields;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\PracticesFiles;
use App\Models\ProductCoupon;
use App\Models\PurchasedCourse;
use App\Models\Ratting;
use App\Models\Store;
use App\Models\StoreThemeSettings;
use App\Models\Student;
use App\Models\User;
use App\Models\UserStore;
use App\Models\Utility;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentExport;
use App\Mail\NewUser;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

//use function Illuminate\Support\Facades\Request;


class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $lang = session()->get('lang');

        \App::setLocale(isset($lang) ? $lang : 'en');
    }

    public function index()
    {
        if(\Auth::user()->can('manage store')){
            if(\Auth::user()->type == 'super admin')
            {
                $users  = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 'Owner')->with('currentPlan')->get();
                $stores = Store::get();

                return view('admin_store.index', compact('stores', 'users'));
            }

        }
        else{
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->can('create store')){
            $user = Auth::user();
            $store_settings = Store::where('id', $user->current_store)->first();
            return view('admin_store.create',compact('store_settings'));
        }
        else{
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::user()->type == 'super admin')
        {
            if(\Auth::user()->can('create store')){
                $settings = Utility::settings();

                $validator = \Validator::make(
                    $request->all(),[
                        'email' => 'required|email|unique:users',
                    ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error',$messages->first());
                }

                $objUser = User::create(
                    [
                        'name' => $request['name'],
                        'email' => $request['email'],
                        'password' => Hash::make($request['password']),
                        'type' => 'Owner',
                        'lang' => !empty($settings['default_language']) ? $settings['default_language'] : 'en',
                        'avatar' => 'avatar.png',
                        'plan' => Plan::first()->id,
                        'created_by' => 1,
                    ]
                );
                $objStore   = Store::create(
                    [
                        'created_by' => $objUser->id,
                        'name' => $request['store_name'],
                        'logo' => !empty($settings['logo']) ? $settings['logo'] : 'logo.png',
                        'invoice_logo' => !empty($settings['logo']) ? $settings['logo'] : 'invoice_logo.png',
                        'lang' => !empty($settings['default_language']) ? $settings['default_language'] : 'en',
                        'currency' => !empty($settings['currency_symbol']) ? $settings['currency_symbol'] : '$',
                        'currency_code' => !empty($settings['currency']) ? $settings['currency'] : 'USD',
                        'paypal_mode' => 'sandbox',
                    ]
                );

                $objStore->enable_storelink = 'on';
                $objStore->theme_dir        = 'theme1';
                $objStore->store_theme      = 'yellow-style.css';
                $objStore->header_name          = 'Course Certificate';
                $objStore->certificate_template = 'template1';
                $objStore->certificate_color    = 'b10d0d';
                $objStore->certificate_gradiant = 'color-one';
                $objStore->save();
                $objUser->email_verified_at = date('Y-m-d H:i:s');
                $objUser->current_store = $objStore->id;
                $objUser->save();
                $objUser->assignRole('Owner');
                UserStore::create(
                    [
                        'user_id'  => $objUser->id,
                        'store_id' => $objStore->id,
                        'permission' => 'Owner',
                    ]
                );
                try{
                    Utility::getSMTPDetails(1);
                    $resp = Mail::to($objUser->email)->send(new NewUser($objUser,$request['password']));
                }
                catch(\Exception $e){
                    $resp['status'] = false;
                    $errorMessage = __('E-Mail has been not sent due to SMTP configuration');

                }

                return redirect()->back()->with('success', __('Successfully added!') . ((!empty($errorMessage)) ? '<br> <span class="text-danger">' . $errorMessage . '</span>' : ''));

            }
            else{
                return redirect()->back()->with('error', 'Permission denied.');
            }
        }
        else
        {
            // if(\Auth::user()->type == 'Owner')
            // {
                $user        = \Auth::user();
                $total_store = $user->countStore();
                $creator     = User::find($user->creatorId());
                $plan        = Plan::find($creator->plan);
                $settings    = Utility::settings();
                // $user['email_verified_at'] = date('Y-m-d H:i:s');
                if($total_store < $plan->max_stores || $plan->max_stores == -1)
                {
                    $objStore   = Store::create(
                        [
                            'created_by' =>  $user->creatorId(),
                            'name' => $request['store_name'],
                            'logo' => !empty($settings['logo']) ? $settings['logo'] : 'logo.png',
                            'invoice_logo' => !empty($settings['logo']) ? $settings['logo'] : 'invoice_logo.png',
                            'lang' => !empty($settings['default_language']) ? $settings['default_language'] : 'en',
                            'currency' => !empty($settings['currency_symbol']) ? $settings['currency_symbol'] : '$',
                            'currency_code' => !empty($settings['currency']) ? $settings['currency'] : 'USD',
                            'paypal_mode' => 'sandbox',
                        ]
                    );
                    $objStore->enable_storelink = 'on';
                    $objStore->theme_dir        = $request['themefile'];
                    $objStore->store_theme      = $request['theme_color'];
                    $objStore->header_name          = 'Course Certificate';
                    $objStore->certificate_template = 'template1';
                    $objStore->certificate_color    = 'b10d0d';
                    $objStore->certificate_gradiant = 'color-one';
                    $objStore->save();

                    Auth::user()->current_store = $objStore->id;
                    Auth::user()->save();
                    UserStore::create(
                        [
                            'user_id' => Auth::user()->id,
                            'store_id' => $objStore->id,
                            'permission' => 'Owner',
                        ]
                    );
                    $uArr = [
                        'store_name' => $request->input('store_name'),
                        'company_name'  => $creator->name,
                    ];

                    return redirect()->back()->with('Success', __('Successfully added!'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Your Store limit is over Please upgrade plan'));
                }
            // }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Store $store
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Store $store
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(\Auth::user()->can('edit store')){
            if(Auth::user()->type == 'super admin')
            {
                $user       = User::find($id);
                $user_store = UserStore::where('user_id', $id)->first();
                $store      = Store::where('id', $user_store->store_id)->first();

                return view('admin_store.edit', compact('store', 'user'));
            }
        }
        else{
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Store $store
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(\Auth::user()->can('edit store')){
            if(Auth::user()->type == 'super admin')
            {
                $store      = Store::find($id);
                $user_store = UserStore::where('store_id', $id)->first();
                $user       = User::where('id', $user_store->user_id)->first();

                $validator = \Validator::make(
                    $request->all(), [
                                    'name' => 'required|max:120',
                                    'store_name' => 'required|max:120',
                                ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $store['name']  = $request->store_name;
                $store['email'] = $request->email;
                $store->update();

                $user['name']  = $request->name;
                $user['email'] = $request->email;
                $user->update();

                return redirect()->back()->with('Success', 'Successfully Updated' . $request['store_name'] . ' added!');
            }
        }
        else{
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Store $store
     *
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        if(\Auth::user()->can('delete store')){
            $user       = User::find($id);
            $user_store = UserStore::where('user_id', $id)->first();
            $store      = Store::where('id', $user_store->store_id)->first();
            PageOption::where('store_id', $store->id)->delete();

            $store->delete();
            $user_store->delete();
            $user->delete();

            return redirect()->back()->with(
                'success', 'Store ' . $store->name . ' Deleted!'
            );
        }
        else{
            return redirect()->back()->with('error', 'Permission denied.');
        }

    }

    public function customDomain()
    {
        if(Auth::user()->type == 'super admin')
        {
            $serverName = str_replace(
                [
                    'http://',
                    'https://',
                ], '', env('APP_URL')
            );
            $serverIp   = gethostbyname($serverName);

            if($serverIp == $_SERVER['SERVER_ADDR'])
            {
                $serverIp;
            }
            else
            {
                $serverIp = request()->server('SERVER_ADDR');
            }
            $users  = User::where('created_by', '=', Auth::user()->creatorId())->where('type', '=', 'owner')->get();
            $stores = Store::where('enable_domain', 'on')->get();

            return view('admin_store.custom_domain', compact('users', 'stores', 'serverIp'));
        }
        else
        {
            return redirect()->back()->with('error', __('permission Denied'));
        }

    }

    public function subDomain()
    {
        if(Auth::user()->type == 'super admin')
        {
            $serverName = str_replace(
                [
                    'http://',
                    'https://',
                ], '', env('APP_URL')
            );
            $serverIp   = gethostbyname($serverName);

            if($serverIp != $serverName)
            {
                $serverIp;
            }
            else
            {
                $serverIp = request()->server('SERVER_ADDR');
            }
            $users  = User::where('created_by', '=', Auth::user()->creatorId())->where('type', '=', 'owner')->get();
            $stores = Store::where('enable_subdomain', 'on')->get();

            return view('admin_store.subdomain', compact('users', 'stores', 'serverIp'));
        }
        else
        {
            return redirect()->back()->with('error', __('permission Denied'));
        }

    }

    public function ownerstoredestroy($id)
    {
        if(Auth::user()->can('delete store'))
        {
            $user        = Auth::user();
            $store       = Store::find($id);
            $user_stores = UserStore::where('user_id', $user->id)->count();
            if($user_stores > 1)
            {
                UserStore::where('store_id', $id)->delete();
                PageOption::where('store_id', $id)->delete();
                $store->delete();

                $userstore = UserStore::where('user_id', $user->id)->first();

                $user->current_store = $userstore->id;
                $user->save();

                return redirect()->route('dashboard');
            }
            else
            {
                return redirect()->back()->with('error', __('You have only one store'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('permission Denied'));
        }
    }

    public function savestoresetting(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'name' => 'required|max:120',
                               'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                               'logo' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                               'invoice_logo' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc|max:20480',
                           ]
        );
        if($request->enable_domain == 'on')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'domains' => 'required',
                               ]
            );
        }
        if($request->enable_domain == 'enable_subdomain')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'subdomain' => 'required',
                               ]
            );
        }
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }
        if(!empty($request->logo))
        {
            $filenameWithExt = $request->file('logo')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('logo')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            if($settings['storage_setting']=='local'){
                $dir  = 'uploads/store_logo/';
            }
            else{
                $dir  = 'uploads/store_logo/';
            }
            $path = Utility::upload_file($request,'logo',$fileNameToStore,$dir,[]);
            if($path['flag'] == 1){
                $url = $path['url'];
            }
        }
        if(!empty($request->invoice_logo))
        {
            $extension              = $request->file('invoice_logo')->getClientOriginalExtension();
            $fileNameToStoreInvoice = 'invoice_logo' . '_' . $id . '.' . $extension;

            $settings = Utility::getStorageSetting();
            if($settings['storage_setting']=='local'){
                $dir  = 'uploads/store_logo/';
            }
            else{
                $dir  = 'uploads/store_logo/';
            }
            $path = Utility::upload_file($request,'invoice_logo',$fileNameToStoreInvoice,$dir,[]);
            if($path['flag'] == 1){
                $url = $path['url'];
            }
        }

        if(!empty($request->header_img))
        {
            $filenameWithExt      = $request->file('header_img')->getClientOriginalName();
            $filename             = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension            = $request->file('header_img')->getClientOriginalExtension();
            $fileNameToheader_img = $id . '_header_img_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            if($settings['storage_setting']=='local'){
                $dir  = 'uploads/store_logo/';
            }
            else{
                $dir  = 'uploads/store_logo/';
            }
            $path = Utility::upload_file($request,'header_img',$fileNameToheader_img,$dir,[]);
            if($path['flag'] == 1){
                $url = $path['url'];
            }
        }
        if(!empty($request->header_section_img))
        {
            $filenameWithExt              = $request->file('header_section_img')->getClientOriginalName();
            $filename                     = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension                    = $request->file('header_section_img')->getClientOriginalExtension();
            $fileNameToheader_section_img = $id . '_header_section_img_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            if($settings['storage_setting']=='local'){
                $dir  = 'uploads/store_logo/';
            }
            else{
                $dir  = 'uploads/store_logo/';
            }
            $path = Utility::upload_file($request,'header_section_img',$fileNameToheader_section_img,$dir,[]);
            if($path['flag'] == 1){
                $url = $path['url'];
            }

        }
        if(!empty($request->sub_img))
        {
            $filenameWithExt   = $request->file('sub_img')->getClientOriginalName();
            $filename          = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension         = $request->file('sub_img')->getClientOriginalExtension();
            $fileNameTosub_img = $id . '_sub_img_' . time() . '.' . $extension;

            $settings = Utility::getStorageSetting();
            if($settings['storage_setting']=='local'){
                $dir  = 'uploads/store_logo/';
            }
            else{
                $dir  = 'uploads/store_logo/';
            }
            $path = Utility::upload_file($request,'sub_img',$fileNameTosub_img,$dir,[]);
            if($path['flag'] == 1){
                $url = $path['url'];
            }
        }
        if($request->enable_domain == 'enable_domain')
        {
            // Remove the http://, www., and slash(/) from the URL
            $input = $request->domains;
            // If URI is like, eg. www.way2tutorial.com/
            $input = trim($input, '/');
            // If not have http:// or https:// then prepend it
            if(!preg_match('#^http(s)?://#', $input))
            {
                $input = 'http://' . $input;
            }
            $urlParts = parse_url($input);
            // Remove www.
            $domain_name = preg_replace('/^www\./', '', $urlParts['host']);
            // Output way2tutorial.com
        }
        if($request->enable_domain == 'enable_subdomain')
        {
            // Remove the http://, www., and slash(/) from the URL
            $input = env('APP_URL');

            // If URI is like, eg. www.way2tutorial.com/
            $input = trim($input, '/');
            // If not have http:// or https:// then prepend it
            if(!preg_match('#^http(s)?://#', $input))
            {
                $input = 'http://' . $input;
            }

            $urlParts = parse_url($input);

            // Remove www.
            $subdomain_name = preg_replace('/^www\./', '', $urlParts['host']);
            // Output way2tutorial.com
            $subdomain_name = $request->subdomain . '.' . $subdomain_name;
        }

        $store          = Store::find($id);
        $store['name']  = $request->name;
        $store['email'] = $request->email;
        if($request->enable_domain == 'enable_domain')
        {
            $store['domains'] = $domain_name;
        }

        $store['enable_storelink'] = ($request->enable_domain == 'enable_storelink' || empty($request->enable_domain)) ? 'on' : 'off';
        $store['enable_domain']    = ($request->enable_domain == 'enable_domain') ? 'on' : 'off';
        $store['enable_subdomain'] = ($request->enable_domain == 'enable_subdomain') ? 'on' : 'off';
        if($request->enable_domain == 'enable_subdomain')
        {
            $store['subdomain'] = $subdomain_name;
        }
        $store['about']                     = $request->about;
        $store['tagline']                   = $request->tagline;
        $store['storejs']                   = $request->storejs;
        $store['whatsapp']                  = $request->whatsapp;
        $store['facebook']                  = $request->facebook;
        $store['instagram']                 = $request->instagram;
        $store['twitter']                   = $request->twitter;
        $store['youtube']                   = $request->youtube;
        $store['google_analytic']           = $request->google_analytic;
        $store['fbpixel']                   = $request->fbpixel_code;
        $store['zoom_account_id']           = $request->zoom_account_id;
        $store['zoom_client_id']            = $request->zoom_client_id;
        $store['zoom_client_secret']        = $request->zoom_client_secret;
        $store['footer_note']               = $request->footer_note;
        $store['enable_header_img']         = $request->enable_header_img ?? 'off';
        $store['enable_header_section_img'] = $request->enable_header_section_img ?? 'off';
        if(!empty($fileNameToheader_img))
        {
            $store['header_img'] = $fileNameToheader_img;
        }
        if(!empty($fileNameToheader_section_img))
        {
            $store['header_section_img'] = $fileNameToheader_section_img;
        }
        $store['header_section_title']  = $request->header_section_title;
        $store['header_section_desc']   = $request->header_section_desc;
        $store['button_section_text']   = $request->button_section_text;
        $store['header_title']          = $request->header_title;
        $store['header_desc']           = $request->header_desc;
        $store['button_text']           = $request->button_text;
        $store['enable_subscriber']     = $request->enable_subscriber ?? 'off';
        $store['enable_rating']         = $request->enable_rating ?? 'off';
        $store['blog_enable']           = $request->blog_enable ?? 'off';
        if(!empty($fileNameTosub_img))
        {
            $store['sub_img'] = $fileNameTosub_img;
        }
        $store['subscriber_title'] = $request->subscriber_title;
        $store['sub_title']        = $request->sub_title;
        $store['address']          = $request->address;
        $store['city']             = $request->city;
        $store['state']            = $request->state;
        $store['zipcode']          = $request->zipcode;
        $store['country']          = $request->country;
        $store['lang']             = $request->store_default_language;
        if(!empty($fileNameToStore))
        {
            $store['logo'] = $fileNameToStore;
        }
        if(!empty($fileNameToStoreInvoice))
        {
            $store['invoice_logo'] = $fileNameToStoreInvoice;
        }
        $store['created_by'] = Auth::user()->creatorId();
        $store->update();

        return redirect()->back()->with('success', __('Store successfully Update.'));
    }

    public function storeSlug($slug)
    {
        $store   = Store::storeSlug($slug);
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

        if(!empty($slug))
        {
            if(!Auth::check())
            {
                visitor()->visit($slug);
            }
            if(Utility::StudentAuthCheck($slug) == false)
            {
                visitor()->visit($slug);
            }

            $userstore             = Store::userStore($store->id);
            $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);
            $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
            $blog                  = Blog::where('store_id', $store->id);

            if(empty($store) || $store == null)
            {
                return redirect()->back()->with('error', __('Store not available'));
            }
            session(['slug' => $slug]);
            $cart = session()->get($slug);
            /**/
            $courses               = Course::where('store_id', $store->id)->where('status', 'Active')->orderBy('id', 'DESC')->limit(4)->with('category_id','student_count','chapter_count')->get();
            $special_offer_courses = Course::where('store_id', $store->id)->where('status', 'Active')->orderBy('id', 'DESC')->first();
            $categories            = Category::where('store_id', $store->id)->orderBy('id', 'DESC')->limit(6)->get();
            $featured_course = Course::where('store_id',$store->id)->where('featured_course', 'on')->orderBy('id', 'DESC')->limit(3)->get();

            $total_item = 0;
            /**/
            if(isset($cart['products']))
            {
                foreach($cart['products'] as $item)
                {
                    if(isset($cart) && !empty($cart['products']))
                    {
                        $total_item = count($cart['products']);
                    }
                    else
                    {
                        $total_item = 0;
                    }
                }
            }
            if (isset($cart['wishlist']))
            {
                $wishlist = $cart['wishlist'];
            } else {
                $wishlist = [];
            }
            $lang = $store->lang;

            // json data
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
            $pixels = PixelFields::where('store_id',$store->id)->get();
            $pixelScript = [];
            foreach ($pixels as $pixel) {

                if ( !$pixel->disabled ) {
                    $pixelScript[] = pixelSourceCode( $pixel['platform'], $pixel['pixel_id'] );
                }
            }

            return view('storefront.' . $store->theme_dir . '.index', compact('featured_course', 'special_offer_courses', 'categories', 'demoStoreThemeSetting', 'store', 'categories', 'total_item', 'courses', 'page_slug_urls', 'blog', 'slug', 'wishlist', 'getStoreThemeSetting','getStoreThemeSetting1','pixelScript'));

        }else{
            return redirect('/');
        }

    }

    public function pageOptionSlug($slug, $page_slug=null)
    {
        if(!empty($page_slug))
        {
            $pageoption            = PageOption::where('slug', $page_slug)->first();
            if(!empty($pageoption))
            {
                $store                 = Store::where('id', $pageoption->store_id)->first();
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
                $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
                $blog                  = Blog::where('store_id', $store->id);
                $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);

                // json data
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

                return view('storefront.' . $store->theme_dir . '.pageslug', compact('pageoption', 'demoStoreThemeSetting', 'slug', 'store', 'page_slug_urls', 'blog', 'getStoreThemeSetting', 'getStoreThemeSetting1'));
            }
            else{
                return redirect('/');
            }

        }else{
            return redirect('/');
        }
    }

    public function StoreBlog($slug)
    {
        $store                 = Store::storeSlug($slug);
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

        $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
        $blogs                 = Blog::where('store_id', $store->id)->get();
        $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);

        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $category = Category::where('store_id', $store->id)->get();

        // $course             = App\Models\Course::find(Crypt::decrypt($course_id));

        // json data
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

        return view('storefront.' . $store->theme_dir . '.store_blog', compact('slug', 'demoStoreThemeSetting', 'store', 'page_slug_urls', 'blogs', 'category', 'getStoreThemeSetting', 'getStoreThemeSetting1'));
    }

    public function StoreBlogView($slug, $blog_id)
    {
        try {
            $blog_id  = \Illuminate\Support\Facades\Crypt::decrypt($blog_id);
        } catch(\RuntimeException $e) {
           return redirect()->back()->with('error',__('Blog not avaliable'));
        }
        // $blog_id               = Crypt::decrypt($blog_id);
        $store                 = Store::storeSlug($slug);
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
        $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
        $blogs                 = Blog::where('store_id', $store->id)->where('id', $blog_id)->first();
        $blog                  = Blog::where('store_id', $store->id)->where('id', $blog_id)->get();
        $blog_loop             = Blog::where('store_id', $store->id)->get();

        $socialblogs           = BlogSocial::where('store_id', $store->id)->first();
        $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);
        $socialblogsarr        = [];

        if(!empty($socialblogs))
        {
            $arrSocialDatas = $socialblogs->toArray();
            unset($arrSocialDatas['id'], $arrSocialDatas['enable_social_button'], $arrSocialDatas['store_id'], $arrSocialDatas['created_by'], $arrSocialDatas['created_at'], $arrSocialDatas['updated_at']);

            foreach($arrSocialDatas as $k => $v)
            {
                if($v == 'on')
                {
                    $newName = str_replace('enable_', '', $k);
                    array_push($socialblogsarr, strtolower($newName));
                }
            }
        }
        $socialblogsarr = json_encode($socialblogsarr);

        // json data
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

        return view('storefront.' . $store->theme_dir . '.store_blog_view', compact('blog', 'demoStoreThemeSetting', 'slug', 'store', 'page_slug_urls', 'blogs', 'blog_loop', 'socialblogs', 'socialblogsarr', 'getStoreThemeSetting', 'getStoreThemeSetting1'));
    }

    public function StoreCart($slug)
    {
        $store                 = Store::storeSlug($slug);
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

        $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
        $blog                  = Blog::where('store_id', $store->id);
        $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $cart = session()->get($slug);
        if(!empty($cart))
        {
            $products = $cart;
            if(Auth::guard('students')->check())
            {
                foreach($products['products'] as $k => $product)
                {
                    if(in_array($product['product_id'], Auth::guard('students')->user()->purchasedCourse()))
                    {
                        $this->delete_cart_item($slug, $product['product_id']);
                    }
                }
            }
        }
        else
        {
            $products = '';
        }
        // json data
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

        return view('storefront.' . $store->theme_dir . '.cart', compact('demoStoreThemeSetting', 'products', 'store', 'page_slug_urls', 'blog', 'slug', 'getStoreThemeSetting', 'getStoreThemeSetting1'));
    }


    public function userPayment($slug)
    {
        $cart = session()->get($slug);
        $store = Store::where('slug', $slug)->first();
        $order = Order::where('user_id', $store->id)->orderBy('id', 'desc')->first();
        $blog  = Blog::where('store_id', $store->id)->count();

        if(Auth::check())
        {
            $store_payments = Utility::getPaymentSetting();
        }
        else
        {
            $store_payments = Utility::getPaymentSetting($store->id);
        }

        $page_slug_urls = PageOption::where('store_id', $store->id)->get();

        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $cart = session()->get($slug);
        if(!empty($cart))
        {
            $products = $cart['products'];
        }
        else
        {
            return redirect()->back()->with('error', __('Please add to product into cart'));
        }

        if(!empty($cart['customer']))
        {
            $cust_details = $cart['customer'];
        }
        else
        {
            return redirect()->back()->with('error', __('Please add your information'));
        }
        $shipping_price = 0;

        $store     = Store::where('slug', $slug)->first();
        $tax_name  = [];
        $tax_price = [];
        $i         = 0;
        if(!empty($products))
        {
            if(!empty($cust_details))
            {
                foreach($products as $product)
                {
                    if($product['variant_id'] != 0)
                    {
                        foreach($product['tax'] as $key => $taxs)
                        {

                            if(!in_array($taxs['tax_name'], $tax_name))
                            {
                                $tax_name[]  = $taxs['tax_name'];
                                $price       = $product['variant_price'] * $product['quantity'] * $taxs['tax'] / 100;
                                $tax_price[] = $price;
                            }
                            else
                            {
                                $price                                                 = $product['variant_price'] * $product['quantity'] * $taxs['tax'] / 100;
                                $tax_price[array_search($taxs['tax_name'], $tax_name)] += $price;
                            }
                        }
                    }
                    else
                    {

                        foreach($product['tax'] as $key => $taxs)
                        {
                            if(!in_array($taxs['tax_name'], $tax_name))
                            {
                                $tax_name[]  = $taxs['tax_name'];
                                $price       = $product['price'] * $product['quantity'] * $taxs['tax'] / 100;
                                $tax_price[] = $price;
                            }
                            else
                            {
                                $price                                                 = $product['price'] * $product['quantity'] * $taxs['tax'] / 100;
                                $tax_price[array_search($taxs['tax_name'], $tax_name)] += $price;
                            }
                        }
                    }
                    $i++;
                }
                $encode_product = json_encode($products);
                $total_item     = $i;
                $taxArr['tax']  = $tax_name;
                $taxArr['rate'] = $tax_price;

                // For Url
                $pro_qty  = [];
                $pro_name = [];
                if(!empty($order))
                {
                    $order_id = '%23' . str_pad($order->id + 1, 4, "100", STR_PAD_LEFT);
                }
                else
                {
                    $order_id = '%23' . str_pad(0 + 1, 4, "100", STR_PAD_LEFT);
                }

                foreach($products as $item)
                {
                    $pro_qty[] = $item['quantity'] . ' x ' . $item['product_name'];
                }

                $url = 'https://api.whatsapp.com/send?phone=' . env('WHATSAPP_NUMBER') . '&text=Hi%2C%0AWelcome+to+%2A' . $store->name . '%2A%2C%0AYour+order+is+confirmed+%26+your+order+no.+is+' . $order_id . '%0AYour+order+detail+is%3A%0A%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%0A+' . join("+%2C%0A", $pro_qty) . '+%0A+%0A%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%7E%0ATo+collect+the+order+you+need+to+show+the+receipt+at+the+counter.%0A%0AThanks+' . $store->name . '%0A%0A';

                return view('storefront.' . $store->theme_dir . '.payment', compact('products', 'order', 'cust_details', 'store', 'taxArr', 'total_item', 'encode_product', 'url', 'shipping_price', 'page_slug_urls', 'store_payments', 'blog'));
            }
            else
            {
                return redirect()->back()->with('error', __('Please fill your details.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Please add to product into cart.'));
        }
    }

    public function addToCart(Request $request, $product_id, $slug, $variant_id = 0)
    {
        if($request->ajax())
        {
            $store = Store::where('slug', $slug)->get();
            if(empty($store))
            {
                return redirect()->back()->with('error', __('Store not available'));
            }

            $product = Course::find($product_id);
            $cart    = session()->get($slug);

            if(!empty($product->thumbnail))
            {
                $pro_img = Utility::get_file('uploads/thumbnail/' . $product->thumbnail);
            }
            else
            {
                $pro_img = '';
            }

            $productname  = $product->title;
            $productprice = $product->price != 0 ? $product->price : 0;


            $time = time();
            // if cart is empty then this the first product
            if(!$cart || !$cart['products'])
            {

                if($variant_id > 0)
                {
                    $cart['products'][$time] = [
                        "product_id" => $product->id,
                        "product_name" => $productname,
                        "image" => $pro_img,
                        "price" => $productprice,
                        "id" => $product_id,
                        'variant_id' => $variant_id,
                    ];
                }
                else if($variant_id <= 0)
                {
                    $cart['products'][$time] = [
                        "product_id" => $product->id,
                        "product_name" => $productname,
                        "image" => $pro_img,
                        "price" => $productprice,
                        "id" => $product_id,
                        'variant_id' => 0,
                    ];
                }
                session()->put($slug, $cart);

                return response()->json(
                    [
                        'code' => 200,
                        'status' => 'Success',
                        'success' => $productname . __(' added to cart successfully!'),
                        'cart' => $cart['products'],
                        'item_count' => count($cart['products']),
                    ]
                );
            }

            // if cart not empty then check if this product exist then increment quantity

            if($variant_id > 0)
            {
                $key = false;
                foreach($cart['products'] as $k => $value)
                {
                    if($variant_id == $value['variant_id'])
                    {
                        $key = $k;
                    }
                }

                if($key !== false && isset($cart['products'][$key]['variant_id']) && $cart['products'][$key]['variant_id'] != 0)
                {
                    if(isset($cart['products'][$key]))
                    {
                        $cart['products'][$key]['quantity']         = $cart['products'][$key]['quantity'] + 1;
                        $cart['products'][$key]['variant_subtotal'] = $cart['products'][$key]['variant_price'] * $cart['products'][$key]['quantity'];

                        session()->put($slug, $cart);

                        return response()->json(
                            [
                                'code' => 200,
                                'status' => 'Success',
                                'success' => $productname . __(' added to cart successfully!'),
                                'cart' => $cart['products'],
                                'item_count' => count($cart['products']),
                            ]
                        );
                    }
                }
            }
            else if($variant_id <= 0)
            {
                $key = false;

                foreach($cart['products'] as $k => $value)
                {
                    if($product_id == $value['product_id'])
                    {
                        $key = $k;
                    }
                }

                if($key !== false)
                {
                    session()->put($slug, $cart);

                    return response()->json(
                        [
                            'code' => 200,
                            'status' => 'Error',
                            'exists' => 'exists',
                            'error' => $productname . __(' is Already in Cart!'),
                            'cart' => $cart['products'],
                            'item_count' => count($cart['products']),
                        ]
                    );
                }
            }
        }

        // if item not exist in cart then add to cart with quantity = 1
        if($variant_id > 0)
        {
            $cart['products'][$time] = [
                "product_id" => $product->id,
                "product_name" => $productname,
                "image" => $pro_img,
                "price" => $productprice,
                "id" => $product_id,
                'variant_id' => $variant_id,
            ];
        }
        else if($variant_id <= 0)
        {
            $cart['products'][$time] = [
                "product_id" => $product->id,
                "product_name" => $productname,
                "image" => $pro_img,
                "price" => $productprice,
                "id" => $product_id,
                'variant_id' => 0,
            ];
        }
        session()->put($slug, $cart);

        return response()->json(
            [
                'code' => 200,
                'status' => 'Success',
                'success' => $productname . __(' added to cart successfully!'),
                'cart' => $cart['products'],
                'item_count' => count($cart['products']),
            ]
        );
    }

    public function delete_cart_item($slug, $id, $variant_id = 0)
    {
        $cart = session()->get($slug);

        foreach($cart['products'] as $key => $product)
        {
            if(($variant_id > 0 && $cart['products'][$key]['variant_id'] == $variant_id))
            {
                unset($cart['products'][$key]);
            }
            else if($cart['products'][$key]['product_id'] == $id && $variant_id == 0)
            {
                unset($cart['products'][$key]);
            }

        }

        $cart['products'] = array_values($cart['products']);

        session()->put($slug, $cart);

        return redirect()->back()->with('success', __('Item successfully Deleted.'));
    }

    public function complete($slug, $order_id)
    {
        try {
            $order_id                  = Crypt::decrypt($order_id);
        } catch(\RuntimeException $e) {
           return redirect()->back()->with('error',__('Permission denied.'));
        }
        $order = Order::where('id', $order_id)->first();
        $store = Store::storeSlug($slug);

        return view('storefront.' . $store->theme_dir . '.complete', compact('slug', 'store', 'order_id', 'order'));
    }

    public function userorder($slug, $order_id)
    {
        try {
            $id    = Crypt::decrypt($order_id);
        }catch (\Throwable $th) {
           return redirect()->back()->with('error',__('Order not avaliable'));
        }
        $store = Store::storeSlug($slug);
        $order = Order::where('id', $id)->first();


        $order_products = json_decode($order->course);
        if(!empty($order_products))
        {
            $sub_total = 0;
            foreach($order_products as $product)
            {
                $totalprice = $product->price;
                $sub_total  += $totalprice;
            }
        }

        if($order->discount_price == 'undefined'){
            $discount_price = 0;
        }else{
            $discount_price = str_replace('-' . $store->currency, '', $order->discount_price);
        }

        if(!empty($discount_price))
        {
            $grand_total = $sub_total - $discount_price;
        }
        else
        {
            $discount_price = 0;
            $grand_total    = $sub_total;
        }
        $student_data = Student::where('id', $order->student_id)->first();
        $order_id     = Crypt::encrypt($order->id);

        if(!empty($coupon))
        {
            if($coupon->enable_flat == 'on')
            {
                $discount_value = $coupon->flat_discount;
            }
            else
            {
                $discount_value = ($grand_total / 100) * $coupon->discount;
            }
        }

        return view('storefront.' . $store->theme_dir . '.userorder', compact('slug', 'student_data', 'discount_price', 'order', 'store', 'grand_total', 'order_products', 'sub_total', 'order_id'));
    }

    public function bank_transfer(Request $request, $slug)
    {
        $validator  = \Validator::make(
            $request->all(),
            [
                'bank_transfer_invoice' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response()->json(
                [
                    'status' => 'Error',
                    'success' => $messages->first()
                ]
            );
        }
        $filenameWithExt  = $request->file('bank_transfer_invoice')->getClientOriginalName();
        $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        $extension        = $request->file('bank_transfer_invoice')->getClientOriginalExtension();
        $fileNameToStores = $filename . '_' . time() . '.' . $extension;
        $dir              = storage_path('uploads/bank_invoice/');
        if(!file_exists($dir))
        {
            mkdir($dir, 0777, true);
        }
        $path = $request->file('bank_transfer_invoice')->storeAs('uploads/bank_invoice/', $fileNameToStores);


        $store          = Store::where('slug', $slug)->first();
        // $products       = $request['product'];
        $order_id       = $request['order_id'];
        $cart           = session()->get($slug);
        $products = $cart['products'];

        $discount_price = 0;
        if(!empty($request->coupon_id))
        {
            $coupon         = ProductCoupon::where('id', $request->coupon_id)->first();
            $discount_price = str_replace('-' . $store->currency, '', $request->dicount_price);
        }
        else
        {
            $coupon = '';
        }

        $product_name   = [];
        $product_id     = [];
        $sub_totalprice = 0;
        // $products = (!empty($products)) ? json_decode($products, true) : [];
        if(!empty($products))
        {
            foreach($products as $key => $product)
            {
                $product_name[] = $product['product_name'];
                $product_id[]   = $product['id'];
                $sub_totalprice += $product['price'];
            }
        }

        if(!empty($coupon))
        {
            if($coupon['enable_flat'] == 'off')
            {
                $discount_value = ($sub_totalprice / 100) * $coupon['discount'];
                $totalprice     = $sub_totalprice - $discount_value;
            }
            else
            {
                $discount_value = $coupon['flat_discount'];
                $totalprice     = $sub_totalprice - $discount_value;
            }
        }

        $totalprice = $sub_totalprice - (float)$discount_price;
        if(!empty($product))
        {
            $student_id            = Auth::guard('students')->user()->id;
            $order                 = new Order();
            $order->order_id       = $order_id;
            $order->name           = Auth::guard('students')->user()->name;
            $order->card_number    = '';
            $order->card_exp_month = '';
            $order->card_exp_year  = '';
            $order->student_id     = $student_id;
            $order->course         = json_encode($products);
            $order->price          = $totalprice;
            $order->coupon         = $request->coupon_id;
            $order->coupon_json    = json_encode($coupon);
            $order->discount_price = $request->dicount_price;
            $order->price_currency = $store->currency_code;
            $order->payment_type   = __('Bank Transfer');
            $order->txn_id         = '';
            $order->payment_status = 'pending';
            $order->receipt        = $path;
            $order->store_id       = $store['id'];
            $order->save();



            $uArr = [
                'order_id' => $order_id,
                'store_name'  => $store['name'],
            ];
            // slack //
            $settings  = Utility::notifications($store->id);
            if(isset($settings['order_notification']) && $settings['order_notification'] ==1){
                Utility::send_slack_msg('new_order',$uArr,$store->created_by);
            }

            // telegram //
            $settings  = Utility::notifications($store->id);
            if(isset($settings['telegram_order_notification']) && $settings['telegram_order_notification'] ==1){
                Utility::send_telegram_msg('new_order',$uArr,$store->created_by);
            }


            //webhook
            $module = 'New Order';
            $webhook =  Utility::webhookSetting($module,$store->created_by);
            if ($webhook) {
                $parameter = $order;
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status == true) {
                    $m2 = response()->json(
                        [
                            'status' => 'success',
                            'success' => __('Your Order request send successfully'),
                            'order_id' => Crypt::encrypt($order->id),
                        ]
                    );
                } else {
                    return redirect()->back()->with('error', __('Webhook call failed.'));
                }
            }
            $msg = response()->json(
                [
                    'status' => 'success',
                    'success' => __('Your Order request send successfully'),
                    'order_id' => Crypt::encrypt($order->id),
                ]
            );
           session()->forget($slug);

           return $msg;
        }
        else
        {
            return redirect()->back()->with('error', __('failed'));
        }
    }

    public function bank_transfer_user_show($order_id)
    {
        $store_settings = Store::where('id', Auth::user()->current_store)->first();
        $order = Order::find($order_id);
        return view('orders.status_view', compact('order','store_settings'));
    }

    public function BankStatusEdit(Request $request, $order_id)
    {
        $order = Order::find($order_id);

        $order->payment_status = $request->status;
        $order->update();

        if($order->payment_status == 'Approved')
        {
            $product = $order->course;
            $products = json_decode($product);

            foreach($products as $course_id)
            {
                $purchased_course = new PurchasedCourse();
                $purchased_course->course_id  = $course_id->product_id;
                $purchased_course->student_id = $order->student_id;
                $purchased_course->order_id   = $order->id;
                $purchased_course->save();

                $student=Student::where('id',$purchased_course->student_id)->first();
                $student->courses_id=$purchased_course->course_id;
                $student->save();
            }
        }

        return redirect()->back()->with('success', __('Course payment successfully updated.'));

    }

    public function grid()
    {
        if(Auth::user()->type == 'super admin')
        {
            $users  = User::where('created_by', '=', Auth::user()->creatorId())->where('type', '=', 'owner')->get();
            $stores = Store::get();

            return view('user.grid', compact('users', 'stores'));
        }
        else
        {
            return redirect()->back()->with('error', __('permission Denied'));
        }

    }

    public function upgradePlan($user_id)
    {
        if(Auth::user()->type == 'super admin')
        {
            $user = User::find($user_id);
            $plans = Plan::get();

            return view('user.plan', compact('user', 'plans'));
        }
    }

    public function activePlan($user_id, $plan_id)
    {
        if(Auth::user()->type == 'super admin')
        {
            $payment_setting = Utility::getAdminPaymentSetting();
            $user       = User::find($user_id);
            $assignPlan = $user->assignPlan($plan_id);
            $plan       = Plan::find($plan_id);
            if($assignPlan['is_success'] == true && !empty($plan))
            {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                PlanOrder::create(
                    [
                        'order_id' => $orderID,
                        'name' => null,
                        'card_number' => null,
                        'card_exp_month' => null,
                        'card_exp_year' => null,
                        'plan_name' => $plan->name,
                        'plan_id' => $plan->id,
                        'price' => $plan->price,
                        'price_currency' => $payment_setting['currency'],
                        'txn_id' => '',
                        'payment_status' => 'succeeded',
                        'receipt' => null,
                        'payment_type' => __('Manually'),
                        'user_id' => $user->id,
                    ]
                );

                return redirect()->back()->with('success', __('Plan successfully upgraded.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Plan fail to upgrade.'));
            }
        }

    }

    public function storedit($id)
    {
        if(Auth::user()->type == 'super admin')
        {
            $user       = User::find($id);
            $user_store = UserStore::where('user_id', $id)->first();
            $store      = Store::where('id', $user_store->store_id)->first();

            return view('admin_store.edit', compact('store', 'user'));
        }
        else
        {
            return redirect()->back()->with('error', __('permission Denied'));
        }
    }

    public function storeupdate(Request $request, $id)
    {
        $user      = User::find($id);
        $validator = \Validator::make(
            $request->all(), [
                               'username' => 'required|max:120',
                               'name' => 'required|max:120',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $user['username']   = $request->username;
        $user['name']       = $request->name;
        $user['title']      = $request->title;
        $user['phone']      = $request->phone;
        $user['gender']     = $request->gender;
        $user['is_active']  = ($request->is_active == 'on') ? 1 : 0;
        $user['user_roles'] = $request->user_roles;
        $user->update();

        Stream::create(
            [
                'user_id' => Auth::user()->id,
                'created_by' => Auth::user()->creatorId(),
                'log_type' => 'updated',
                'remark' => json_encode(
                    [
                        'owner_name' => Auth::user()->username,
                        'title' => 'user',
                        'stream_comment' => '',
                        'user_name' => $request->name,
                    ]
                ),
            ]
        );

        return redirect()->back()->with('success', __('User Successfully Updated.'));
    }

    public function storedestroy($id)
    {
        if(Auth::user()->type == 'super admin')
        {
            $user      = User::find($id);
            $userstore = UserStore::where('user_id', $user->id)->first();
            $store     = Store::where('id', $userstore->store_id)->first();
            PageOption::where('store_id', $store->id)->delete();

            $user->delete();
            $userstore->delete();
            $store->delete();

            return redirect()->back()->with('success', __('User Store Successfully Deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function changeCurrantStore($storeID)
    {
        if(Auth::user()->can('change store'))
        {
            $objStore = Store::find($storeID);
            if($objStore->is_active)
            {
                $objUser                = Auth::user();
                $objUser->current_store = $storeID;
                $objUser->update();

                return redirect()->route('dashboard')->with('success', __('Store Change Successfully!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Store is locked'));
            }
        }
        else{
            return redirect()->back()->with('error', __('permission Denied'));
        }
    }

    /*LMS*/
    public function ViewCourse($slug, $course_id)
    {
        $store                 = Store::storeSlug($slug);
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
        $blog                  = Blog::where('store_id', $store->id);
        $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
        $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }

        try {
            $course   = Course::find(\Illuminate\Support\Facades\Crypt::decrypt($course_id));
        } catch(\RuntimeException $e) {
           return redirect()->back()->with('error',__('Course not avaliable'));
        }

        if(empty($course) || $course == null)
        {
            return redirect()->back()->with('error', __('Course not available'));
        }
        $course = Course::where('id',$course->id)->first();
        $more_by_category   = Course::where('store_id',$store->id)->where('category', $course->category)->with('chapter_count','student_count')->limit(4)->get();
        $category_name      = Category::find($course->category);
        $faqs               = Faq::where('course_id', Crypt::decrypt($course_id))->get();
        $chapter            = Chapters::where('course_id', $course->id)->get();
        $header             = Header::where('course', $course->id)->get();
        $tutor_course_count = Course::where('store_id', $store->id)->where('created_by', $course->created_by)->where('status', 'Active')->get();
        $tutor_course_rel   = Course::where('store_id', $store->id)->where('created_by', $course->created_by)->where('status', 'Active')->limit(2)->get();
        /*Course Rating*/
        $course_ratings = Ratting::where('course_id', $course->id)->get();
        $ratting        = Ratting::where('course_id', $course->id)->where('rating_view', 'on')->sum('ratting');

        $user_count     = Ratting::where('course_id', $course->id)->where('rating_view', 'on')->count();

        $course['meta_keywords']     = Course::where('id', $course->id)->select('meta_keywords')->get();
        $course['meta_description']  = Course::where('id', $course->id)->select('meta_description')->get();

        if($user_count > 0)
        {
            $avg_rating = number_format($ratting / $user_count, 1);
        }
        else
        {
            $avg_rating = number_format($ratting / 1, 1);

        }
        /*Tutor Rating*/
        $tutor_id           = $tutor_course_count->pluck('created_by')->first();
        $tutor_ratings      = Ratting::where('tutor_id', $tutor_id)->where('slug', $slug)->get();
        $tutor_sum_ratting  = Ratting::where('tutor_id', $tutor_id)->where('slug', $slug)->where('rating_view', 'on')->sum('ratting');
        $tutor_count_rating = Ratting::where('tutor_id', $tutor_id)->where('slug', $slug)->where('rating_view', 'on')->count();
        if($tutor_count_rating > 0)
        {
            $avg_tutor_rating = number_format($tutor_sum_ratting / $tutor_count_rating, 1);
        }
        else
        {
            $avg_tutor_rating = number_format($tutor_sum_ratting / 1, 1);
        }
        $headers = $header;
        $header_first = $header->pluck('id')->first();

        $tutor = User::find($tutor_id);

        // json data
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

        return view('storefront.' . $store->theme_dir . '.viewcourse', compact('category_name', 'more_by_category', 'header_first', 'demoStoreThemeSetting', 'faqs', 'blog', 'slug', 'tutor_id', 'tutor_count_rating', 'avg_tutor_rating', 'tutor_ratings', 'course_ratings', 'avg_rating', 'store', 'page_slug_urls', 'course', 'chapter', 'header', 'tutor_course_count', 'tutor_course_rel', 'tutor', 'user_count', 'getStoreThemeSetting', 'getStoreThemeSetting1', 'headers'));
    }

    public function tutor($slug, $tutor_id)
    {
        try {
            $tutor_id   = \Illuminate\Support\Facades\Crypt::decrypt($tutor_id);
        } catch(\RuntimeException $e) {
           return redirect()->back()->with('error',__('Tutor not avaliable'));
        }
        $store                 = Store::storeSlug($slug);
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
        $blog                  = Blog::where('store_id', $store->id);
        $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
        $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $tutor = User::find($tutor_id);

        $course = Course::find($tutor_id);

        $tutor_course = Course::where('store_id', $store->id)->where('created_by', $tutor_id)->where('status', 'Active')->with('category_id')->first();
        $courses      = Course::where('store_id', $store->id)->where('created_by', $tutor_id)->where('status', 'Active')->get();

        $tutor_ratings = Ratting::where('tutor_id', $tutor_id)->where('slug', $slug)->get();
        $ratting       = Ratting::where('tutor_id', $tutor_id)->where('slug', $slug)->where('rating_view', 'on')->sum('ratting');
        $user_count    = Ratting::where('tutor_id', $tutor_id)->where('slug', $slug)->where('rating_view', 'on')->count();
        if($user_count > 0)
        {
            $avg_rating = number_format($ratting / $user_count, 1);
        }
        else
        {
            $avg_rating = number_format($ratting / 1, 1);

        }

        // json data
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

        return view('storefront.' . $store->theme_dir . '.tutor', compact('demoStoreThemeSetting', 'tutor_course', 'blog', 'user_count', 'tutor', 'tutor_ratings', 'avg_rating', 'store', 'page_slug_urls', 'courses', 'course', 'slug', 'getStoreThemeSetting', 'getStoreThemeSetting1'));
    }

    public function searchData($slug, $search_data)
    {
        $store          = Store::where('slug', $slug)->first();
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id);
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }

        if(!empty($search_data))
        {
            $output = '';
            $query  = $search_data;
            if($query != '')
            {
                $data = Course::where('store_id', $store->id)->where('title', 'like', '%' . $query . '%')->where('status', 'Active')->get();
            }
            else
            {
                $data = Course::where('store_id', $store->id)->where('status', 'Active')->get();
            }

            $total_row = $data->count();
            $output    .= view('storefront.' . $store->theme_dir . '.search.searchData', compact('blog', 'data', 'total_row', 'store'))->render();
            $data      = array(
                'table_data' => $output,
                'total_data' => $total_row,
                'slug' => $slug,
                'page_slug_urls' => $page_slug_urls,
            );

            return json_encode($data);
        }
        else
        {
            $data = array(
                'page_slug_urls' => $page_slug_urls,
                'slug' => $slug,
            );

            return json_encode($data);
        }

    }

    public function search($slug, Request $request, $search_category = null)
    {
        if($search_category != null)
        {
            try {
                $search_category = Crypt::decrypt($search_category);
            } catch(\RuntimeException $e) {
               return redirect()->back()->with('error',__('Category not avaliable'));
            }
        }
        $search_d              = ($request->all() != null) ? $request->search : null;
        $store                 = Store::storeSlug($slug);
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
        $blog                  = Blog::where('store_id', $store->id);
        $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
        $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $category = Category::where('store_id', $store->id)->get();

        if($search_d == null && $search_category == null)
        {
            $courses = Course::where('store_id', $store->id)->where('status', 'Active')->with('category_id')->get();
        }
        else if($search_category != null)
        {
            $courses = Course::where('store_id', $store->id)->where('status', 'Active')->where('category', $search_category)->with('category_id')->get();
        }
        else
        {
            $courses = Course::where('store_id', $store->id)->where('status', 'Active')->where('title', 'like', '%' . $search_d . '%')->with('category_id')->get();
        }

        // json data
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

        return view('storefront.' . $store->theme_dir . '.search.index', compact('search_category', 'demoStoreThemeSetting', 'blog', 'store', 'page_slug_urls', 'courses', 'search_d', 'category', 'slug', 'getStoreThemeSetting', 'getStoreThemeSetting1'));
    }

    public function filter($slug, Request $request)
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
        $blog           = Blog::where('store_id', $store->id)->count();
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();

        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $is_free     = [
            'off' => 'off',
            'on' => 'on',
        ];
        $level       = Utility::course_level();
        $search_data = '';
        $category    = Category::where('store_id', $store->id)->get()->pluck('id');
        if($search_data != 'null' && !empty($request->all()))
        {
            $output = '';
            $data   = Course::where('store_id', $store->id)->whereIn('level', (!empty($request->level) ? $request->level : $level))->whereIn('category', (!empty($request->checked) ? $request->checked : $category))->whereIn('is_free', (!empty($request->is_free) ? $request->is_free : $is_free))->where('status', 'Active')->get();
            if(!empty($request->is_free) && empty($request->level) && empty($request->checked))
            {
                $data = Course::where('store_id', $store->id)->whereIn('is_free', (!empty($request->is_free) ? $request->is_free : $is_free))->get();
            }
            if(!empty($request->level) && empty($request->is_free) && empty($request->checked))
            {
                $data = Course::where('store_id', $store->id)->whereIn('level', (!empty($request->level) ? $request->level : $level))->get();
            }
            if(!empty($request->checked) && empty($request->level) && empty($request->is_free))
            {
                $data = Course::where('store_id', $store->id)->whereIn('category', (!empty($request->checked) ? $request->checked : $category))->get();
            }
            $total_row = $data->count();
            $output    .= view('storefront.' . $store->theme_dir . '.search.filter', compact('blog', 'data', 'total_row', 'store', 'slug'))->render();
            $data      = array(
                'table_data' => $output,
                'total_row' => $total_row,
                'slug' => $slug,
                'page_slug_urls' => $page_slug_urls,
            );

            return json_encode($data);
        }
        else
        {
            $output    = '';
            $data      = Course::where('store_id', $store->id)->where('status', 'Active')->get();
            $total_row = $data->count();
            $output    .= view('storefront.' . $store->theme_dir . '.search.filter', compact('blog', 'data', 'total_row', 'store', 'slug'))->render();
            $data      = array(
                'table_data' => $output,
                'total_row' => $total_row,
                'slug' => $slug,
                'page_slug_urls' => $page_slug_urls,
            );

            return json_encode($data);
        }

    }

    public function checkout($slug, $courses_id, $total)
    {
        $cart                  = session()->get($slug);
        try {
            $c_id                  = Crypt::decrypt($courses_id);
        } catch(\RuntimeException $e) {
           return redirect()->back()->with('error',__('Permission denied.'));
        }

        $store                 = Store::where('slug', $slug)->first();
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
        $order                 = Order::where('store_id', $store->id)->orderBy('id', 'desc')->first();
        $blog                  = Blog::where('store_id', $store->id);
        $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
        $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);

        // json data
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


        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        if(!empty($cart))
        {
            $products = $cart['products'];
        }
        else
        {
            return redirect()->back()->with('error', __('Please add to product into cart'));
        }
        if(!empty($order))
        {
            $order_id = '%23' . str_pad($order->id + 1, 4, "100", STR_PAD_LEFT);
        }
        else
        {
            $order_id = '%23' . str_pad(0 + 1, 4, "100", STR_PAD_LEFT);

        }
        if(!empty(Auth::guard('students')->user()))
        {
            $course = Course::where('store_id', $store->id)->whereIn('id', json_decode($c_id))->where('status', 'Active')->get();
            if(Auth::check())
            {
                $store_payments = Utility::getPaymentSetting();
            }
            else
            {
                $store_payments = Utility::getPaymentSetting($store->id);
            }

            $encode_product = json_encode($products);
            if($total > 0)
            {
                return view('storefront.' . $store->theme_dir . '.checkout', compact('order_id', 'demoStoreThemeSetting', 'order', 'encode_product', 'store_payments', 'blog', 'slug', 'course', 'page_slug_urls', 'store'));
            }
            else
            {
                if($products)
                {
                    $student               = Auth::guard('students')->user();
                    $order                 = new Order();
                    $order->order_id       = time();
                    $order->name           = $student->name;
                    $order->card_number    = '';
                    $order->card_exp_month = '';
                    $order->card_exp_year  = '';
                    $order->student_id     = $student->id;
                    $order->course         = json_encode($products);
                    $order->price          = 0;
                    $order->price_currency = $store->currency_code;
                    $order->txn_id         = '';
                    $order->payment_type   = 'Free';
                    $order->payment_status = 'success';
                    $order->receipt        = '';
                    $order->store_id       = $store['id'];
                    $order->save();


                    foreach($products as $course_id)
                    {
                        $purchased_course = new PurchasedCourse();
                        $purchased_course->course_id  = $course_id['product_id'];
                        $purchased_course->student_id = $student->id;
                        $purchased_course->order_id   = $order->id;
                        $purchased_course->save();

                        $student=Student::where('id',$purchased_course->student_id)->first();
                        $student->courses_id=$purchased_course->course_id;
                        $student->save();
                    }
                    session()->forget($slug);

                    return redirect()->route(
                        'store-complete.complete', [
                                                     $store->slug,
                                                     Crypt::encrypt($order->id),
                                                 ]
                    )->with('success', __('Transaction has been success'));

                }
                else
                {
                    return redirect()->back()->with('error', __('Cart is empty'));
                }
            }



        }
        else
        {
            $is_cart = true;

            return view('storefront.' . $store->theme_dir . '.user.login', compact('blog', 'demoStoreThemeSetting', 'slug', 'store', 'page_slug_urls', 'is_cart', 'getStoreThemeSetting','getStoreThemeSetting1'));
        }
    }

    public function userCreate($slug)
    {
        $store                 = Store::where('slug', $slug)->first();
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
        $blog                  = Blog::where('store_id', $store->id);
        $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
        $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }

        // json data
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

        return view('storefront.' . $store->theme_dir . '.user.create', compact('blog', 'demoStoreThemeSetting', 'slug', 'store', 'page_slug_urls','getStoreThemeSetting','getStoreThemeSetting1'));
    }

    protected function userStore($slug, Request $request)
    {
        $store          = Store::where('slug', $slug)->first();
        $page_slug_urls = PageOption::where('store_id', $store->id)->get();
        $blog           = Blog::where('store_id', $store->id);

        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $validate = Validator::make(
            $request->all(), [
                               'name' => [
                                   'required',
                                   'string',
                                   'max:255',
                               ],
                               'phone_number' => [
                                   'required',
                                   'max:255',
                               ],
                               'email' => [
                                   'required',
                                   'string',
                                   'email',
                                   'max:255',
                               ],
                               'password' => [
                                   'required',
                                   'string',
                                   'min:8',
                                   'confirmed',
                               ],
                           ]
        );
        $vali     = Student::where('email', $request->email)->where('store_id', $store->id)->where('phone_number', $request->phone_number)->count();
        if($validate->fails())
        {
            $message = $validate->getMessageBag();

            return redirect()->back()->with('error', $message->first());
        }
        elseif($vali > 0)
        {
            return redirect()->back()->with('error', __('Email already exists'));
        }

        $student               = new Student();
        $student->name         = $request->name;
        $student->email        = $request->email;
        $student->phone_number = $request->phone_number;
        $student->password     = Hash::make($request->password);
        $student->lang         = !empty($settings['default_language']) ? $settings['default_language'] : 'en';
        $student->avatar       = 'avatar.png';
        $student->store_id     = $store->id;

        $student->save();

        return redirect()->route('student.home', $slug)->with('success', __('Account Created Successfully.'));
    }

    public function studentHome($slug)
    {
        $store                 = Store::storeSlug($slug);
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
        $blog                  = Blog::where('store_id', $store->id);
        $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
        $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);
        if(empty($store))
        {
            return redirect()->back()->with('error', __('Store not available'));
        }
        $purchased_course = Course::where('store_id', $store->id)->where('status', 'Active')->with('category_id')->get();

        // json data
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

        return view('storefront.' . $store->theme_dir . '.student.index', compact('purchased_course', 'demoStoreThemeSetting', 'blog', 'slug', 'store', 'page_slug_urls', 'getStoreThemeSetting', 'getStoreThemeSetting1'));
    }

    public function wishlist($slug, $id)
    {
        if(Utility::StudentAuthCheck($slug) == false)
        {

            return response()->json(
                [
                    'code' => 200,
                    'status' => 'error',
                    'error' => 'You need to login',
                ]
            );
        }
        else
        {

            $wl             = new Wishlist();
            $wishlist_count = Wishlist::where('course_id', $id)->where('student_id', Auth::guard('students')->id())->count();
            $wishlist_count_no = Wishlist::where('student_id', Auth::guard('students')->id())->count();

            if($wishlist_count > 0)
            {

                return response()->json(
                    [
                        'code' => 200,
                        'status' => 'error',
                        'error' => 'Already in wishlist',
                    ]
                );
            }
            else
            {
                $wl->course_id  = $id;
                $wl->student_id = Auth::guard('students')->id();
                $wl->save();

                return response()->json(
                    [
                        'code' => 200,
                        'status' => 'Success',
                        'success' => 'Added to wishlist',
                        'item_count' => $wishlist_count_no + 1,
                    ]
                );
            }
        }
    }

    public function wishlistpage($slug)
    {

        if(Utility::StudentAuthCheck($slug) == false)
        {
            return redirect($slug . '/student-login');
        }
        else
        {
            $store                 = Store::storeSlug($slug);
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
            $blog                  = Blog::where('store_id', $store->id);
            $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
            $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);
            if(empty($store))
            {
                return redirect()->back()->with('error', __('Store not available'));
            }
            $courses = Course::where('store_id', $store->id)->where('status', 'Active')->get();

            // json data
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

            return view('storefront.' . $store->theme_dir . '.student.wishlist', compact('blog', 'demoStoreThemeSetting', 'slug', 'page_slug_urls', 'page_slug_urls', 'store', 'courses', 'getStoreThemeSetting', 'getStoreThemeSetting1'));
        }
    }

    public function removeWishlist($slug, $id)
    {

        if(Utility::StudentAuthCheck($slug) == false)
        {
            return redirect()->back()->with('error', __('You need to login!'));
        }
        else
        {
            $wishlist_count = Wishlist::where('course_id', $id)->where('student_id', Auth::guard('students')->id());
            $wishlist_count->delete();

            return redirect()->back()->with('success', __('Successfully Removed!'));
        }
    }

    public function fullscreen($slug, $course_id, $chapter_id = null, $type = null)
    {
        if(Utility::StudentAuthCheck($slug) == false)
        {
            return redirect()->back()->with('error', __('You need to login!'));
        }
        else if(in_array(Crypt::decrypt($course_id), Auth::guard('students')->user()->purchasedCourse()))
        {
            $store = Store::storeSlug($slug);
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
            if($chapter_id != null)
            {
                $chapter_id = Crypt::decrypt($chapter_id);
            }
            $course_id = Crypt::decrypt($course_id);
            $courses   = Course::find($course_id);
            $headers   = Header::where('course', $course_id)->get();

            $blog                  = Blog::where('store_id', $store->id);
            $page_slug_urls        = PageOption::where('store_id', $store->id)->get();
            $demoStoreThemeSetting = Utility::demoStoreThemeSetting($store->id);

            /*NEXT PREVIOUS*/
            $c = Chapters::where('course_id', $courses->id);
            if($c->count() == 0)
            {
                return redirect()->back()->with('error', __('No Chapters Available!'));
            }
            $last_next       = $c->orderBy('id', 'desc')->first();
            $last_previous   = Chapters::where('course_id', $courses->id)->first();
            $current_chapter = '';
            $next            = '';
            $previous        = '';

            /*PROGRESS*/
            $student_id      = Auth::guard('students')->user()->id;
            $cs_complete     = ChapterStatus::where('student_id', $student_id)->where('course_id', $course_id)->where('status', 'Active')->get();
            $ChapterStatuss  = ChapterStatus::where('student_id', $student_id)->where('course_id', $course_id)->get();
            // $cs_complete     = $cs->where('status', 'Active')->get();
            // $ChapterStatuss  = $cs->get();
            $a               = 100 / $c->count();
            $progress        = (int)($a * $cs_complete->count());
            $practices_files = PracticesFiles::where('course_id', $course_id)->get();

            $cs              = ChapterStatus::where('student_id', $student_id)->where('course_id', $course_id);
            $cs_incomplete   = $cs->where('status', 'Inactive')->get();
            $cer_download    = $cs->first();

            if($type == 'next')
            {
                $next            = Chapters::where('id', '>', $chapter_id)->where('course_id', $course_id)->min('id');
                $current_chapter = Chapters::find($next);
            }
            else if($type == 'previous')
            {
                $previous        = Chapters::where('id', '<', $chapter_id)->where('course_id', $course_id)->max('id');
                $current_chapter = Chapters::find($previous);
            }
            else if(!empty($chapter_id))
            {
                $current_chapter = Chapters::find($chapter_id);
            }
            else
            {
                $current_chapter = $last_previous;
            }

            if(!empty($current_chapter))
            {
                $previous_chapter = '';
                $previous_id        = Chapters::where('id', '<', $current_chapter->id)->where('course_id', $course_id)->max('id');
                if(!empty($previous_id))
                {
                    $previous_chapter   = Chapters::find($previous_id);
                }
                $next_chapter = '';
                $next_id            = Chapters::where('id', '>', $current_chapter->id)->where('course_id', $course_id)->min('id');
                if(!empty($next_id))
                {
                    $next_chapter       = Chapters::find($next_id);
                }
            }
            // json data
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
        }
        else
        {
            return redirect()->back()->with('error', __('You need to Purchase this course!'));
        }

        return view('storefront.' . $store->theme_dir . '.fullscreen', compact('practices_files', 'ChapterStatuss', 'demoStoreThemeSetting', 'blog', 'page_slug_urls', 'progress', 'previous', 'next', 'current_chapter', 'chapter_id', 'course_id', 'headers', 'store', 'courses', 'slug', 'last_next', 'last_previous', 'cer_download', 'cs_incomplete', 'getStoreThemeSetting', 'getStoreThemeSetting1', 'previous_chapter', 'next_chapter'));
    }

    public function checkbox($chapter_id, $course_id, $slug)
    {
        if(Utility::StudentAuthCheck($slug) == false)
        {
            return response()->json(
                [
                    'code' => 200,
                    'status' => 'Error',
                    'error' => __('You need to login.....'),
                ]
            );
        }
        else
        {
            $id = Auth::guard('students')->user()->id;
            ChapterStatus::updateOrCreate(
                [
                    'student_id' => $id,
                    'chapter_id' => $chapter_id,
                    'course_id' => $course_id,
                ], [
                    'student_id' => $id,
                    'chapter_id' => $chapter_id,
                    'course_id' => $course_id,
                    'status' => 'Active',
                ]
            );

            $student_id     = Auth::guard('students')->user()->id;
            $chapters       = Chapters::where('course_id', $course_id);
            $chapter_status = ChapterStatus::where('student_id', $student_id)->where('course_id', $course_id);
            $active_count   = $chapter_status->where('status', 'Active')->get()->count();
            $sum            = 100 / $chapters->count();
            $progress       = (int)($sum * $active_count);

            return response()->json(
                [
                    'code' => 200,
                    'status' => 'Success',
                    'success' => __('Watched'),
                    'progress' => $progress,
                ]
            );
        }
    }

    public function removeCheckbox($chapter_id, $course_id, $slug)
    {
        if(Utility::StudentAuthCheck($slug) == false)
        {
            return response()->json(
                [
                    'code' => 200,
                    'status' => 'Error',
                    'error' => __('You need to login'),
                ]
            );
        }
        else
        {
            $id = Auth::guard('students')->user()->id;
            ChapterStatus::updateOrCreate(
                [
                    'student_id' => $id,
                    'chapter_id' => $chapter_id,
                    'course_id' => $course_id,
                ], [
                    'student_id' => $id,
                    'chapter_id' => $chapter_id,
                    'course_id' => $course_id,
                    'status' => 'Inactive',
                ]
            );

            $student_id     = Auth::guard('students')->user()->id;
            $chapters       = Chapters::where('course_id', $course_id);
            $chapter_status = ChapterStatus::where('student_id', $student_id)->where('course_id', $course_id);
            $active_count   = $chapter_status->where('status', 'Active')->get()->count();
            $sum            = 100 / $chapters->count();
            $progress       = (int)($sum * $active_count);

            return response()->json(
                [
                    'code' => 200,
                    'status' => 'Success',
                    'success' => __('Unwatched'),
                    'progress' => $progress,
                ]
            );
        }
    }

    /*STORE EDIT*/
    public function StoreEdit(Request $request, $slug)
    {
        $store = Store::where('slug', $slug)->first();

        /*HEADER*/
        if(isset($request->enable_header_img) && $request->enable_header_img == 'on')
        {
            /*HEADER*/
            if(!empty($request->header_img))
            {
                $filenameWithExt  = $request->file('header_img')->getClientOriginalName();
                $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension        = $request->file('header_img')->getClientOriginalExtension();
                $fileNameToStores = $filename . '_' . time() . '.' . $extension;
                $dir              = storage_path('uploads/store_logo/');

                if(!file_exists($dir))
                {
                    mkdir($dir, 0777, true);
                }

                $path = $request->file('header_img')->storeAs('uploads/store_logo/', $fileNameToStores);
            }

            $post['enable_header_img'] = $request->enable_header_img;
            $post['header_title']      = $request->header_title;
            $post['header_desc']       = $request->header_desc;
            $post['button_text']       = $request->button_text;
            if(!empty($fileNameToStores))
            {
                $post['header_img'] = $fileNameToStores;
            }
        }
        else
        {
            $post['enable_header_img'] = 'off';
        }

        /*HEADER SECTION*/
        if(isset($request->enable_header_section_img) && $request->enable_header_section_img == 'on')
        {
            if(!empty($request->header_section_img))
            {
                $filenameWithExt  = $request->file('header_section_img')->getClientOriginalName();
                $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension        = $request->file('header_section_img')->getClientOriginalExtension();
                $fileNameToStores = $filename . '_' . time() . '.' . $extension;
                $dir              = storage_path('uploads/store_logo/');

                if(!file_exists($dir))
                {
                    mkdir($dir, 0777, true);
                }
                $path = $request->file('header_section_img')->storeAs('uploads/store_logo/', $fileNameToStores);
            }

            $post['enable_header_section_img'] = $request->enable_header_section_img;
            $post['header_section_title']      = $request->header_section_title;
            $post['header_section_desc']       = $request->header_section_desc;
            $post['button_section_text']       = $request->button_section_text;
            $post['button_section_url']        = $request->button_section_url;
            if(!empty($fileNameToStores))
            {
                $post['header_section_img'] = $fileNameToStores;
            }
        }
        else
        {
            $post['enable_header_section_img'] = 'off';
        }

        /*FOOTER 1*/
        if(isset($request->enable_footer_note) && $request->enable_footer_note == 'on')
        {
            if(!empty($request->footer_logo))
            {
                $filenameWithExt  = $request->file('footer_logo')->getClientOriginalName();
                $filename         = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension        = $request->file('footer_logo')->getClientOriginalExtension();
                $fileNameToStores = $filename . '_' . time() . '.' . $extension;
                $dir              = storage_path('uploads/store_logo/');

                if(!file_exists($dir))
                {
                    mkdir($dir, 0777, true);
                }

                $path = $request->file('footer_logo')->storeAs('uploads/store_logo/', $fileNameToStores);
            }
            $post['enable_footer_note'] = $request->enable_footer_note;

            /*QUICK LINK 1*/
            if(isset($request->enable_quick_link1) && $request->enable_quick_link1 == 'on')
            {
                $post['enable_quick_link1'] = $request->enable_quick_link1;

                $post['quick_link_header_name1'] = $request->quick_link_header_name1;
                $post['quick_link_name11']       = $request->quick_link_name11;
                $post['quick_link_url11']        = $request->quick_link_url11;

                $post['quick_link_name12'] = $request->quick_link_name12;
                $post['quick_link_url12']  = $request->quick_link_url12;

                $post['quick_link_name13'] = $request->quick_link_name13;
                $post['quick_link_url13']  = $request->quick_link_url13;

                $post['quick_link_name14'] = $request->quick_link_name14;
                $post['quick_link_url14']  = $request->quick_link_url14;
            }
            else
            {
                $post['enable_quick_link1'] = 'off';
            }

            /*QUICk LINK 2*/
            if(isset($request->enable_quick_link2) && $request->enable_quick_link2 == 'on')
            {
                $post['enable_quick_link2'] = $request->enable_quick_link2;

                $post['quick_link_header_name2'] = $request->quick_link_header_name2;
                $post['quick_link_name21']       = $request->quick_link_name21;
                $post['quick_link_url21']        = $request->quick_link_url21;

                $post['quick_link_name22'] = $request->quick_link_name22;
                $post['quick_link_url22']  = $request->quick_link_url22;

                $post['quick_link_name23'] = $request->quick_link_name23;
                $post['quick_link_url23']  = $request->quick_link_url23;

                $post['quick_link_name24'] = $request->quick_link_name24;
                $post['quick_link_url24']  = $request->quick_link_url24;
            }
            else
            {
                $post['enable_quick_link2'] = 'off';
            }
            /*QUICK LINK 3*/
            if(isset($request->enable_quick_link3) && $request->enable_quick_link3 == 'on')
            {
                $post['enable_quick_link3'] = $request->enable_quick_link3;

                $post['quick_link_header_name3'] = $request->quick_link_header_name3;
                $post['quick_link_name31']       = $request->quick_link_name31;
                $post['quick_link_url31']        = $request->quick_link_url31;

                $post['quick_link_name32'] = $request->quick_link_name32;
                $post['quick_link_url32']  = $request->quick_link_url32;

                $post['quick_link_name33'] = $request->quick_link_name33;
                $post['quick_link_url33']  = $request->quick_link_url33;

                $post['quick_link_name34'] = $request->quick_link_name34;
                $post['quick_link_url34']  = $request->quick_link_url34;
            }
            else
            {

                $post['enable_quick_link3'] = 'off';
            }
            if(!empty($fileNameToStores))
            {
                $post['footer_logo'] = $fileNameToStores;
            }
        }
        else
        {
            $post['enable_footer_note'] = 'off';
        }

        /*FOOTER 2*/
        if(isset($request->enable_footer) && $request->enable_footer == 'on')
        {
            $post['enable_footer'] = $request->enable_footer;
            $post['email']         = $request->email;
            $post['whatsapp']      = $request->whatsapp;
            $post['facebook']      = $request->facebook;
            $post['instagram']     = $request->instagram;
            $post['twitter']       = $request->twitter;
            $post['youtube']       = $request->youtube;
            $post['footer_note']   = $request->footer_note;
            $post['storejs']       = $request->storejs;
        }
        else
        {
            $post['enable_footer'] = 'off';
        }


        //Brand Logo Setting
        if(isset($request->enable_brand_logo) && $request->enable_brand_logo == 'on')
        {
            $post['enable_brand_logo'] = $request->enable_brand_logo;
        }
        else
        {
            $post['enable_brand_logo'] = 'off';
        }

        if(isset($request->file) && !empty($request->file))
        {
            $file_name = [];
            if(!empty($request->file) && count($request->file) > 0)
            {
                $i = 0;
                foreach($request->file as $file)
                {
                    $i++;
                    $filenameWithExt = $file->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME) . '_brand';
                    $extension       = $file->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . $i . time() . '.' . $extension;
                    $file_name[]     = $fileNameToStore;
                    $dir             = storage_path('uploads/store_logo/');
                    if(!file_exists($dir))
                    {
                        mkdir($dir, 0777, true);
                    }
                    $path = $file->storeAs('uploads/store_logo/', $fileNameToStore);
                }
            }

            if(!empty($file_name) && count($file_name) > 0)
            {
                $post['brand_logo'] = implode(',', $file_name);
            }
        }

        //Categories
        if(isset($request->enable_categories) && $request->enable_categories == 'on')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'categories' => 'required',
                                   'categories_title' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['enable_categories'] = $request->enable_categories;
            $post['categories']        = !empty($request->categories) ? $request->categories : '';
            $post['categories_title']  = !empty($request->categories_title) ? $request->categories_title : '';
        }
        else
        {
            $post['enable_categories'] = 'off';
        }

        // FEATURED COURSE
        if(isset($request->enable_featuerd_course) && $request->enable_featuerd_course == 'on')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'featured_title' => 'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['enable_featuerd_course'] = $request->enable_featuerd_course;
            $post['featured_title']         = !empty($request->featured_title) ? $request->featured_title : '';
        }
        else
        {
            $post['enable_featuerd_course'] = 'off';
        }

        foreach($post as $key => $data)
        {
            $arr = [
                'name' => $key,
                'value' => $data,
                'store_id' => $store->id,
                'created_by' => Auth::user()->creatorId(),
            ];

            StoreThemeSettings::updateOrCreate(
                [
                    'name' => $key,
                    'store_id' => $store->id,
                ], $arr
            );

        }

        return redirect()->back()->with('success', __('Successfully Saved!'));
    }

    public function brandfileDelete($slug, $name)
    {
        $store = Store::where('slug', $slug)->where('created_by', Auth::user()->id)->first();
        $getStoreThemeSetting = Utility::getStoreThemeSetting($store->id,$slug, $name);
        $dir                  = storage_path('uploads/store_logo/');
        $brandarray           = explode(',', $getStoreThemeSetting['brand_logo']);
        if(!empty($name))
        {
            foreach($brandarray as $k => $val)
            {
                if($val == $name)
                {
                    if(!file_exists($dir . $name))
                    {
                        unset($brandarray[$k]);
                        $brand_logo_update        = StoreThemeSettings::where('name', 'brand_logo')->where('store_id', $store->id)->first();
                        $brand_logo_update->value = implode(',', $brandarray);
                        $brand_logo_update->save();

                        return response()->json(
                            [
                                'error' => __('File not exists in folder!'),
                                'id' => $name,
                            ]
                        );
                    }
                    else
                    {
                        unlink($dir . $name);
                        unset($brandarray[$k]);
                        $post['brand_logo']       = implode(',', $brandarray);
                        $brand_logo_update        = StoreThemeSettings::where('name', 'brand_logo')->where('store_id', $store->id)->first();
                        $brand_logo_update->value = implode(',', $brandarray);
                        $brand_logo_update->save();

                        return response()->json(
                            [
                                'success' => __('Record deleted successfully!'),
                                'name' => $name,
                            ]
                        );
                    }
                }

            }
        }

    }
    public function image_delete(Request $request)
    {

        $file_path = $request->image;
        $result = Utility::changeStorageLimit(Auth::user()->creatorId(), $file_path);
        if (File::exists(base_path($request->image))) {
            File::delete(base_path($request->image));
        }

        $return['status'] = 'success';
        return response()->json($return);

    }

    public function changeTheme(Request $request, $slug)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'theme_color' => 'required',
                               'themefile' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $store                = Store::find($slug);
        $store['store_theme'] = $request->theme_color;
        $store['theme_dir']   = $request->themefile;
        $store->save();

        return redirect()->back()->with('success', __('Theme Successfully Updated.'));
    }

    public function certificatedl(Request $request,$course_id)
    {
        $objUser  = Auth::guard('students')->user();
        $settings = Store::saveCertificate();
        $gradiant = $settings->certificate_gradiant;
        $chap_id = Chapters::select('id','course_id','duration')->where('course_id',$course_id)->get();
        $times   = Chapters::pluck('duration')->toArray();

        $totaltime = str_replace(':', '.', Utility::sum_time($chap_id));

        $hours  = floor($totaltime/60);
        $minute = floor($totaltime%60);
        $total_hour = sprintf('%02d:%02d', $hours, $minute);

        $course = Course::all();
        $stud   = Student::all();

        // $user   = Student::where('id', $objUser->id)->first();
        $course_id = Course::where('id',$course_id)->first();
        $student                = new \stdClass();
        $student->name          = $objUser->name;
        $student->course_name   = !empty($course_id->title)?$course_id->title:'-';
        $student->course_time   = $total_hour;

        return view('settings.templates.' . $settings->certificate_template, compact('gradiant','settings','stud','course','student','total_hour','chap_id'));
    }

    public function ownerPassword($id)
    {
        // if(\Auth::user()->type == 'super admin')
        // {
            $eId        = \Crypt::decrypt($id);
            $user = User::find($eId);

            $employee = User::where('id', $eId)->first();

            return view('admin_store.reset', compact('user', 'employee'));
        // }

    }

    public function ownerPasswordReset(Request $request, $id){
        $validator = \Validator::make(
            $request->all(), [
                               'password' => 'required|confirmed|same:password_confirmation',
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $user   = User::where('id', $id)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->back()->with('success', 'Owner Password successfully updated.');

    }

    public function paymentwallstoresession(Request $request,$slug)
    {
        $store = Store::where('slug',$slug)->first();
        $cart = session()->get($slug);
        if(\Auth::check())
        {
            $store_payment_setting = Utility::getPaymentSetting();
        }
        else
        {
            $store_payment_setting = Utility::getPaymentSetting($store->id);
        }
        if(!empty($cart))
        {
            $products = $cart['products'];
        }
        else
        {
            return redirect()->back()->with('error', __('Please add to product into cart'));
        }
        if(isset($cart['coupon']['data_id']))
        {
            $coupon = ProductCoupon::where('id', $cart['coupon']['data_id'])->first();
        }
        else
        {
            $coupon = '';
        }
        $product_name   = [];
        $product_id     = [];
        $totalprice     = 0;
        $sub_totalprice = 0;

        foreach($products as $key => $product)
        {
            $product_name[] = $product['product_name'];
            $product_id[]   = $product['id'];
            $sub_totalprice += $product['price'];
            $totalprice     += $product['price'];
        }

        return redirect()->route('paymentwall.callback',[$slug,"totalprice"=>$totalprice]);
    }

    public function Editproducts($slug, $theme)
    {
        $store = Store::where('slug', $slug)->first();
        $getStoreThemeSetting = Utility::getStoreThemeSetting($store->id, $theme);
        $getStoreThemeSetting1 = [];

        if( empty($getStoreThemeSetting) || empty(trim($getStoreThemeSetting['dashboard'])) ) {
            //json file
            $path = storage_path()."/uploads/" . $store->theme_dir . "/" . $store->theme_dir . ".json" ;
            $getStoreThemeSetting = json_decode(file_get_contents($path), true);
        } else {
            $getStoreThemeSetting = json_decode($getStoreThemeSetting['dashboard'], true);
            $getStoreThemeSetting1 = Utility::getStoreThemeSetting($store->id, $theme);
        }

        return view('settings.edit_theme', compact('store', 'theme', 'getStoreThemeSetting','getStoreThemeSetting1'));
    }

    public function StoreEditProduct(Request $request, $slug, $theme)
    {
        $store = Store::where('slug', $slug)->first();
        $getStoreThemeSetting = Utility::getStoreThemeSetting($store->id, $theme);
        if(!empty($getStoreThemeSetting['dashboard'])) {
            $getStoreThemeSetting = json_decode($getStoreThemeSetting['dashboard'], true);
        }

        $json = $request->array;
        foreach ($json as $key => $jsn) {

            foreach ($jsn['inner-list'] as $IN_key => $js) {
                if ($js['field_type'] == 'multi file upload') {
                    if (!empty($js['multi_image'])) {
                        foreach ($js['multi_image'] as $file) {
                            $image_size = $file->getSize();
                            $result = Utility::updateStorageLimit(Auth::user()->creatorId(), $image_size);
                            if($result == 1){
                                $filenameWithExt = $file->getClientOriginalName();
                                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME) . '_brand';
                                $extension = $file->getClientOriginalExtension();
                                $fileNameToStore = $IN_key . '_' . rand(10, 100) . '_' . date('ymd') . time() . '.' . $extension;
                                $file_name[] = $fileNameToStore;
                                $settings = Utility::getStorageSetting();
                                if($settings['storage_setting']=='local'){
                                    $dir = 'uploads/'. $store->theme_dir . '/header';
                                }
                                else{
                                    $dir = 'uploads/'. $store->theme_dir . '/header';
                                }
                                $path = Utility::multi_json_upload_file($file,'field_default_text',$fileNameToStore,$dir,[]);
                                if($path['flag'] == 1){
                                    $url = $path['url'];
                                }else{
                                    return redirect()->back()->with('error', __($path['msg']));
                                }
                                $new_path = $store->theme_dir . '/header/' . $fileNameToStore;
                                $json[$key]['inner-list'][$IN_key]['image_path'][] = $new_path;
                            }
                            $next_key_p_image = !empty($key_file) ? $key_file : 0;
                        }
                        if (!empty($jsn['prev_image'])) {
                            foreach ($jsn['prev_image'] as $p_key => $p_value) {
                                // $next_key_p_image = $next_key_p_image + 1;
                                $json[$key]['inner-list'][$IN_key]['image_path'][] = $p_value;
                            }
                        }
                    }else {
                        if(!empty($jsn['prev_image'])) {
                            foreach ($jsn['prev_image'] as $p_key => $p_value) {
                                $json[$key]['inner-list'][$IN_key]['image_path'][] = $p_value;
                            }
                        }
                    }
                }
                if($js['field_type'] == 'photo upload')
                {
                    if ($jsn['array_type'] == 'multi-inner-list')
                    {
                        for ($i = 0; $i < $jsn['loop_number']; $i++)
                        {
                            if (!empty($json[$key][$js['field_slug']][$i]['image']) && gettype($json[$key][$js['field_slug']][$i]['image']) == 'object')
                            {
                                $file = $json[$key][$js['field_slug']][$i]['image'];
                                $filePath = 'uploads/'. $store->theme_dir . '/header';
                                if(!empty($getStoreThemeSetting)){
                                    $oldFile = $getStoreThemeSetting[$key][$js['field_slug']][$i];
                                    if(array_key_exists("image",$oldFile)){
                                        $filename = $getStoreThemeSetting[$key][$js['field_slug']][$i]['image'];
                                        $filePath = 'uploads/'.$filename;
                                    }
                                    else{
                                        $filePath = 'uploads/'. $store->theme_dir . '/header';
                                    }
                                }
                                $image_size = $file->getSize();
                                $result = Utility::updateStorageLimit(Auth::user()->creatorId(), $image_size);
                                if($result == 1){
                                    $result = Utility::updateStorageLimit(Auth::user()->creatorId(), $image_size);

                                    $filenameWithExt = $file->getClientOriginalName();
                                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME) ;
                                    $extension = $file->getClientOriginalExtension();
                                    $fileNameToStore = $i.'_'.rand(10,100).'_'.date('ymd') .time() .  '.'.$extension;
                                    $file_name[] = $fileNameToStore;
                                    $settings = Utility::getStorageSetting();
                                    if($settings['storage_setting']=='local'){
                                        $dir = 'uploads/'. $store->theme_dir . '/header';
                                    }
                                    else{
                                        $dir = 'uploads/'.$store->theme_dir . '/header' ;
                                    }
                                    $path = Utility::multi_json_upload_file($file,'field_default_text',$fileNameToStore,$dir,[]);
                                    if($path['flag'] == 1){
                                        $url = $path['url'];
                                    }else{
                                        return redirect()->back()->with('error', __($path['msg']));
                                    }

                                    // $path = $file->storeAs('uploads/'.$store->theme_dir . '/header', $fileNameToStore);
                                    if (!empty($file_name) && count($file_name) > 0) {
                                        $json[$key][$js['field_slug']][$i]['field_prev_text'] = $store->theme_dir . '/header/' . $fileNameToStore;
                                        $json[$key][$js['field_slug']][$i]['image'] = '';
                                    }
                                }
                            } else {
                                $json[$key][$js['field_slug']][$i]['image'] = '';
                            }
                        }
                    } else {
                        if (gettype($js['field_default_text']) == 'object') {
                            $file = $js['field_default_text'];
                            $filenameWithExt = $file->getClientOriginalName();
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME) ;
                            $extension = $file->getClientOriginalExtension();
                            $fileNameToStore = $filename  .date('ymd') .time() .  '.'.$extension;
                            $file_name[] = $fileNameToStore;
                            $filePath = 'uploads/'. $store->theme_dir . '/header';
                            if(!empty($getStoreThemeSetting)){
                                if($getStoreThemeSetting[$key]['inner-list'][$IN_key]['field_default_text'] == $getStoreThemeSetting[$key]['inner-list'][$IN_key]['field_prev_text']){
                                    $filePath = 'uploads/'. $store->theme_dir . '/header';
                                }
                                else{
                                    $oldFile = $getStoreThemeSetting[$key]['inner-list'][$IN_key]['field_default_text'];
                                    $filePath = 'uploads/'. $oldFile ;
                                }
                            }
                            $image_size = $file->getSize();
                            $result = Utility::updateStorageLimit(Auth::user()->creatorId(), $image_size);
                            if($result == 1){
                                Utility::changeStorageLimit(Auth::user()->creatorId(),$filePath);
                                $settings = Utility::getStorageSetting();
                                if($settings['storage_setting']=='local'){
                                    $dir = 'uploads/'.$store->theme_dir . '/header';
                                }
                                else{
                                    $dir = 'uploads/'.$store->theme_dir . '/header' ;
                                }

                                $path = Utility::json_upload_file($js,'field_default_text',$fileNameToStore,$dir,[]);
                            }
                            if (!empty($file_name) && count($file_name) > 0) {
                                // $post['Thumbnail Image'] = implode('', $file_name);
                                $post['Thumbnail Image'] =  $file_name;
                                foreach( $post['Thumbnail Image'] as $v){
                                    $headerImage = $store->theme_dir . '/header/' . $v;
                                    // $headerImage = $store->theme_dir . '/header/' . $post['Thumbnail Image'];
                                }
                                $json[$key]['inner-list'][$IN_key]['field_default_text'] = $headerImage;
                            }
                        }
                    }
                }
            }
        }
        $json1 = json_encode($json);
        $store = Store::where('slug', $slug)->where('created_by', Auth::user()->id)->first();

        $where_array = [
            'name' => 'dashboard',
            'store_id' => $store->id,
            'theme_name' => $store->theme_dir,
        ];

        $update_create_array = [
            'name' => 'dashboard',
            'value' => $json1,
            'type' => null,
            'store_id' => $store->id,
            'theme_name' => $store->theme_dir,
            'created_by' => Auth::user()->creatorId(),
        ];
        if(!empty($json1)) {
            StoreThemeSettings::updateOrCreate($where_array , $update_create_array);
        }

        return redirect()->back()->with('success', __('Successfully Saved!'). ((isset($result) && $result!=1) ? '<br> <span class="text-danger">' . $result . '</span>' : ''));
    }

    public function studentindex()
    {
        if (Auth::user()->can('manage student')) {
            $user  = Auth::user();
            $students = Student::where('store_id',$user->current_store)->get();

            return view('student.index', compact('students'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function studentShow($id)
    {
        if (Auth::user()->can('show student')) {
            $orders = Order::where('student_id',$id)->get();
            return view('orders.index', compact('orders'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function studentExport()
    {
        if (Auth::user()->can('export student')) {
            $name = 'student_' . date('Y-m-d i:h:s');
            $data = Excel::download(new StudentExport(), $name . '.xlsx'); ob_end_clean();
            return $data;
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function themeindex()
    {
        if(Auth::user()->can('manage store settings')){
            $user                 = Auth::user();
            $store_settings = Store::where('id', $user->current_store)->first();

            return view('admin_store.theme', compact('store_settings'));
        }
        else{
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

}


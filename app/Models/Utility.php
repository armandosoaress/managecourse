<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;
use Illuminate\Support\Str;
use Jenssegers\Date\Date;
use phpDocumentor\Reflection\Types\Float_;
use function Cassandra\Type;
use Spatie\GoogleCalendar\Event as GoogleEvent;
use Carbon\Carbon;
use PhpParser\Node\Stmt\Return_;

class Utility extends Model
{
    private static $fetchsettings = null;
    private static $languages = null;
    private static $colorset = null;
    private static $dateformate = null;
    private static $fetchthemesetting = null;
    public function createSlug($table, $title, $id = 0)
    {
        // Normalize the title
        $slug = Str::slug($title, '-');
        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.
        $allSlugs = $this->getRelatedSlugs($table, $slug, $id);
        // If we haven't used it before then we are all good.
        if(!$allSlugs->contains('slug', $slug))
        {
            return $slug;
        }
        // Just append numbers like a savage until we find not used.
        for($i = 1; $i <= 100; $i++)
        {
            $newSlug = $slug . '-' . $i;
            if(!$allSlugs->contains('slug', $newSlug))
            {
                return $newSlug;
            }
        }
        throw new \Exception('Can not create a unique slug');
    }

    protected function getRelatedSlugs($table, $slug, $id = 0)
    {
        return DB::table($table)->select()->where('slug', 'like', $slug . '%')->where('id', '<>', $id)->get();
    }

    public static function settings($user_id = null)
    {
        if(self::$fetchsettings === null)
        {
            $data = DB::table('settings');

            if(Auth::check())
            {
                if(Auth::user()->type=='super admin'){
                    $data=$data->where('created_by','=',\Auth::user()->creatorId())->where('store_id','0')->get();
                    if(count($data)==0){
                        $data =DB::table('settings')->where('created_by', '=', 1 )->get();
                    }
                }else{
                    $data=$data->where('created_by','=',\Auth::user()->creatorId())->where('store_id',Auth::user()->current_store)->get();

                    if(count($data)==0){
                    $data =DB::table('settings')->where('created_by', '=', 1 )->get();
                    }
                }
            }
            else
            {
                $data->where('created_by', '=', 1);
                $data = $data->get();
            }

            self::$fetchsettings = $data;
        }

        $settings = [
            "currency_symbol_position" => "pre",
            "currency_symbol" => "",
            "currency" => "",
            "site_date_format" => "M j, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "company_email" => "",
            "company_email_from_name" => "",
            "invoice_prefix" => "#INV",
            "invoice_color" => "ffffff",
            "quote_template" => "template1",
            "quote_color" => "ffffff",
            "salesorder_template" => "template1",
            "salesorder_color" => "ffffff",
            "bill_prefix" => "#BILL",
            "bill_color" => "fffff",
            "quote_prefix" => "#QUO",
            "salesorder_prefix" => "#SOP",
            "vender_prefix" => "#VEND",
            "footer_title" => "",
            "footer_notes" => "",
            "invoice_template" => "template1",
            "bill_template" => "template1",
            "default_language" => "en",
            "decimal_number" => "2",
            "tax_type" => "VAT",
            "shipping_display" => "on",
            "footer_link_1" => "Support",
            "footer_value_1" => "#",
            "footer_link_2" => "Terms",
            "footer_value_2" => "#",
            "footer_link_3" => "Privacy",
            "footer_value_3" => "#",
            "display_landing_page" => "on",
            "title_text" => "",
            "footer_text" => "",
            "company_logo" => "",
            "company_favicon" => "",
            "signup" => "on",
            "color" => "theme-3",
            "cust_theme_bg" => "on",
            "cust_darklayout" => "off",
            "dark_logo" => "logo-dark.png",
            "light_logo" => "logo-light.png",
            "company_logo_light" => "logo-light.png",
            "company_logo_dark" => "logo-dark.png",
            "SITE_RTL" => "off",
            "google_calender_enabled" => "off",
            "storage_setting" => "local",
            "local_storage_validation" => "jpg,jpeg,png,xlsx,xls,csv,pdf",
            "local_storage_max_upload_size" => "2048000",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url" => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",
            "email_verification"=>"on",
            "enable_cookie"=>"on",
            "necessary_cookies"=>"on",
            "cookie_logging"=>"on",
            "cookie_title"=>"We use cookies!",
            "cookie_description"=>"Hi, this website uses essential cookies to ensure its proper operation and tracking cookies to understand how you interact with it",
            "strictly_cookie_title"=>"Strictly necessary cookies",
            "strictly_cookie_description"=>"These cookies are essential for the proper functioning of my website. Without these cookies, the website would not work properly",
            "more_information_description"=>"For any queries in relation to our policy on cookies and your choices, please contact us",
            "contactus_url"=>"#",
            "chatgpt_key"=>"",
            "timezone"=>"Asia/Kolkata",
            "mail_driver" => "smtp",
            "mail_host" => "smtp.mailtrap.io",
            "mail_port" => "2525",
            "mail_username" => "",
            "mail_password" => "",
            "mail_encryption" => "",
            "mail_from_address" => "",
            "mail_from_name" => "",
            "recaptcha_module" => "no",
            "google_recaptcha_key" => "",
            "google_recaptcha_secret" => "",
        ];
        foreach(self::$fetchsettings as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function AdminSettings()
    {
        $user = User::where('type','super admin')->first();
        return self::settings($user->id);
    }
    // google calander code


    public static function templateData()
    {
        $arr              = [];
        $arr['colors']    =[
            [
                'hex'=>'b10d0d',
                'gradiant'=>'color-one'
            ],
            [
                'hex'=>'554360',
                'gradiant'=>'color-two'
            ],
            [
                'hex'=>'2a475b',
                'gradiant'=>'color-three'
            ],
            [
                'hex'=>'6f0000',
                'gradiant'=>'color-four'
            ],
            [
                'hex'=>'1d7280',
                'gradiant'=>'color-five'
            ],
            [
                'hex'=>'365476',
                'gradiant'=>'color-six'
            ],
            [
                'hex'=>'414345',
                'gradiant'=>'color-seven'
            ],
            [
                'hex'=>'237a57',
                'gradiant'=>'color-eight'
            ],
            [
                'hex'=>'734b6d',
                'gradiant'=>'color-nine'
            ],
            [
                'hex'=>'aa076b',
                'gradiant'=>'color-ten'
            ],
        ];

        $arr['templates'] = [
            "template1" => "Certificate 1",
            "template2" => "Certificate 2",
        ];

        return $arr;
    }

    public static function languages()
    {
        if(self::$languages === null)
        {
            $languages=Utility::langList();
            if(\Schema::hasTable('languages')){
                $settings = Utility::langSetting();
                if(!empty($settings['disable_lang'])){
                    $disabledlang =explode(',', $settings['disable_lang']);
                    $languages = Language::whereNotIn('code',$disabledlang)->pluck('fullName','code');
                }
                else{
                    $languages = Language::pluck('fullName','code');
                }
            }
            self::$languages = $languages;
        }
        return self::$languages;
    }

    public static function getValByName($key)
    {
        $setting = Utility::settings();

        if(!isset($setting[$key]) || empty($setting[$key]))
        {
            $setting[$key] = '';
        }

        return $setting[$key];
    }

    public static function getPaymentSetting($store_id = null)
    {
        $data     = DB::table('store_payment_settings');
        $settings = [];
        if(Auth::check())
        {
            $store_id = Auth::user()->current_store;
            $data     = $data->where('store_id', '=', $store_id);

        }
        else
        {
            $data = $data->where('store_id', '=', $store_id);
        }
        $data = $data->get();
        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function getAdminPaymentSetting()
    {
        $data     = DB::table('admin_payment_settings');
        $settings = [];
        if(Auth::check())
        {
            $user_id = 1;
            $data    = $data->where('created_by', '=', $user_id);

        }
        $data = $data->get();
        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str     = file_get_contents($envFile);
        if(count($values) > 0)
        {
            foreach($values as $envKey => $envValue)
            {
                $keyPosition       = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine           = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                // If key does not exist, add it
                if(!$keyPosition || !$endOfLinePosition || !$oldLine)
                {
                    $str .= "{$envKey}='{$envValue}'\n";
                }
                else
                {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";
        if(!file_put_contents($envFile, $str))
        {
            return false;
        }

        return true;
    }

    public static function priceFormat($price)
    {
        $settings = Utility::settings();
        $price  =  floatval($price);

        if(Auth::check() && Auth::User()->type == 'Owner')
        {
            $user     = Auth::user()->current_store;
            $settings = \Auth::user()->currentstore($user);

            if($settings['currency_symbol_position'] == "pre" && $settings['currency_symbol_space'] == "with")
            {
                return $settings['currency'] . ' ' . number_format($price, 2);
            }
            elseif($settings['currency_symbol_position'] == "pre" && $settings['currency_symbol_space'] == "without")
            {
                return $settings['currency'] . number_format($price, 2);
            }
            elseif($settings['currency_symbol_position'] == "post" && $settings['currency_symbol_space'] == "with")
            {
                return number_format($price, 2) . ' ' . $settings['currency'];
            }
            elseif($settings['currency_symbol_position'] == "post" && $settings['currency_symbol_space'] == "without")
            {
                return number_format($price, 2) . $settings['currency'];
            }
        }
        else
        {
            $slug = session()->get('slug');
            $payment_setting = Utility::getAdminPaymentSetting();
            if(!empty($slug))
            {
                $store = Store::storeSlug($slug);

                if($store['currency_symbol_position'] == "pre" && $store['currency_symbol_space'] == "with")
                {
                    return $store['currency'] . ' ' . number_format($price, 2);
                }
                elseif($store['currency_symbol_position'] == "pre" && $store['currency_symbol_space'] == "without")
                {
                    return $store['currency'] . number_format($price, 2);
                }
                elseif($store['currency_symbol_position'] == "post" && $store['currency_symbol_space'] == "with")
                {
                    return number_format($price, 2) . ' ' . $store['currency'];
                }
                elseif($store['currency_symbol_position'] == "post" && $store['currency_symbol_space'] == "without")
                {
                    return number_format($price, 2) . $store['currency'];
                }
            }

            //  return (($settings['currency_symbol_position'] == "pre") ? $settings['currency_symbol'] : '') . number_format($price, 2) . (($settings['currency_symbol_position'] == "post") ? $settings['currency_symbol'] : '');
            return (($settings['currency_symbol_position'] == "pre") ? $payment_setting['currency_symbol'] : '') . number_format($price, Utility::getValByName('decimal_number')) . (($settings['currency_symbol_position'] == "post") ? $payment_setting['currency_symbol'] : '');
        }
    }

    public static function currencySymbol($settings)
    {
        return $settings['site_currency_symbol'];
    }

    public static function timeFormat($settings, $time)
    {
        return date($settings['site_date_format'], strtotime($time));
    }

    public static function dateFormat($date)
    {
        if(self::$dateformate === null)
        {
            $settings = self::settings();
            $date_formate =  date($settings['site_date_format'], strtotime($date));

            self::$dateformate = $date_formate;
        }
        return self::$dateformate;
    }

    // get font-color code accourding to bg-color
    public static function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3)
        {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        }
        else
        {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array(
            $r,
            $g,
            $b,
        );

        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values

    }

    public static function getFontColor($color_code)
    {
        $rgb = self::hex2rgb($color_code);
        $R   = $G = $B = $C = $L = $color = '';

        $R = (floor($rgb[0]));
        $G = (floor($rgb[1]));
        $B = (floor($rgb[2]));

        $C = [
            $R / 255,
            $G / 255,
            $B / 255,
        ];

        for($i = 0; $i < count($C); ++$i)
        {
            if($C[$i] <= 0.03928)
            {
                $C[$i] = $C[$i] / 12.92;
            }
            else
            {
                $C[$i] = pow(($C[$i] + 0.055) / 1.055, 2.4);
            }
        }

        $L = 0.2126 * $C[0] + 0.7152 * $C[1] + 0.0722 * $C[2];

        if($L > 0.179)
        {
            $color = 'black';
        }
        else
        {
            $color = 'white';
        }

        return $color;
    }

    public static function delete_directory($dir)
    {
        if(!file_exists($dir))
        {
            return true;
        }
        if(!is_dir($dir))
        {
            return unlink($dir);
        }
        foreach(scandir($dir) as $item)
        {
            if($item == '.' || $item == '..')
            {
                continue;
            }
            if(!self::delete_directory($dir . DIRECTORY_SEPARATOR . $item))
            {
                return false;
            }
        }

        return rmdir($dir);
    }

    public static function getSuperAdminValByName($key)
    {
        $data = DB::table('settings');
        $data = $data->where('name', '=', $key);
        $data = $data->first();
        if(!empty($data))
        {
            $record = $data->value;
        }
        else
        {
            $record = '';
        }

        return $record;
    }

    /*LMS GO*/
    public static function status()
    {

        $status = [
            'Active' => 'Active',
            'Inactive' => 'Inactive',
        ];

        return $status;
    }

    public static function course_level()
    {

        $level = [
            'Beginner' => 'Beginner',
            'Intermediate' => '	Intermediate',
            'Expert' => 'Expert',
        ];

        return $level;
    }

    public static function lang()
    {

        $lang = [
            'English' => 'English',
            'Arabic' => 'Arabic',
            'Danish' => 'Danish',
            'German' => 'German',
            'Spanish' => 'Spanish',
            'French' => 'French',
            'Italian' => 'Italian',
            'Japanese' => 'Japanese',
            'Dutch' => 'Dutch',
            'Polish' => 'Polish',
            'Russian' => 'Russian',
        ];

        return $lang;
    }

    public static function chapter_type()
    {

        $type = [
            'Video Url' => 'Video Url',
            'Video File' => 'Video File',
            'iFrame' => 'iFrame',
            'Text Content' => 'Text Content',
        ];

        return $type;
    }

    public static function StudentAuthCheck($slug = null)
    {
        if($slug == null)
        {
            $slug = \Request::segment(1);
        }
        $auth_student = Auth::guard('students')->user();
        if(!empty($auth_student))
        {
            $store = Store::storeSlug($slug);
            $student  = Student::studentAuth($store->id);
            if($student > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /*STORE EDIT*/
    public static function demoStoreThemeSetting($store_id = null)
    {
        if(!empty($store_id))
        {
            $data = StoreThemeSettings::where('store_id', $store_id)->get();
        }
        else
        {
            $data = [];
        }

        $settings = [
            /*HEADER*/
            "enable_header_img" => "on",
            "header_title" => "Knowledge",
            "header_desc" => "The only true wisdom is in knowing you know nothing.",
            "button_text" => "Explore Courses",
            "header_img" => "default_header_img.jpg",

            /*HEADER SECTION*/
            "enable_header_section_img" => "on",
            "header_section_title" => "Knowledge",
            "header_section_desc" => "The only true wisdom is in knowing you know nothing.",
            "button_section_text" => "Contact me",
            "button_section_url" => "#button",
            "header_section_img" => "default_section_img.jpg",

            /*FOOTER 1*/
            "enable_footer_note" => "on",
            "enable_quick_link1" => "on",
            "enable_quick_link2" => "on",
            "enable_quick_link3" => "on",
            "enable_footer_desc" => "on",

            "quick_link_header_name1" => "Account",
            "quick_link_header_name2" => "About",
            "quick_link_header_name3" => "Company",
            "footer_desc" => "Purpose is a unique and beautiful collection of UI elements that are all flexible and modular. A complete and customizable solution to building the website of your dreams.",

            "quick_link_name11" => "Profile",
            "quick_link_name12" => "Settings",
            "quick_link_name13" => "Notifications",
            "quick_link_name14" => "Notifications",


            "quick_link_name21" => "Services",
            "quick_link_name22" => "Contact",
            "quick_link_name23" => "Careers",
            "quick_link_name24" => "Careers",

            "quick_link_name31" => "Terms",
            "quick_link_name32" => "Privacy",
            "quick_link_name33" => "Support",
            "quick_link_name34" => "Support",

            "quick_link_url11" => "#Profile",
            "quick_link_url12" => "#Settings",
            "quick_link_url13" => "#Notifications",
            "quick_link_url14" => "#Notifications",

            "quick_link_url21" => "#Services",
            "quick_link_url22" => "#Contact",
            "quick_link_url23" => "#Careers",
            "quick_link_url24" => "#Careers",

            "quick_link_url31" => "#Terms",
            "quick_link_url32" => "#Privacy",
            "quick_link_url33" => "#Support",
            "quick_link_url34" => "#Support",


            /*FOOTER LOGO*/
            "footer_logo" => "default_footer_logo.png",

            /*FOOTER 2*/
            "enable_footer" => "on",
            "email" => "test@test.com",
            "whatsapp" => "https://api.whatsapp.com/",
            "facebook" => "https://www.facebook.com/",
            "instagram" => "https://www.instagram.com/",
            "twitter" => "https://twitter.com/",
            "youtube" => "https://www.youtube.com/",
            "footer_note" => "© 2021 My Store. All rights reserved",
            "storejs" => "<script>console.log('hello');</script>",

            "enable_brand_logo" => "on",
            "brand_logo" => implode(
                ',', [
                       'brand_logo.png',
                       'brand_logo.png',
                       'brand_logo.png',
                       'brand_logo.png',
                       'brand_logo.png',
                       'brand_logo.png',
                   ]
            ),

            "enable_categories" => "on",
            "categories" => "Categories",
            "categories_title" => "There is only that moment and the incredible certainty that everything under the sun has been written by one hand only.",

            "enable_featuerd_course" => "on",
            "featured_title" => "Featured Course",

        ];

        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function getStoreThemeSetting($store_id = null, $theme = null)
    {
        if(self::$fetchthemesetting === null)
        {
            $data     = DB::table('store_theme_setting');
            $settings = [];

            if(Auth::check())
            {
                $store_id = Auth::user()->current_store;
                // $theme1 = StoreThemeSettings::where('store_id', $store_id)->first();
                // $theme = $theme1['theme_name'];
                $data     = $data->where('store_id', '=', $store_id)->where('theme_name', '=', $theme);
                // $data     = $data->where('store_id', '=', $store_id);
            }
            else
            {
                $data = $data->where('store_id', '=', $store_id)->where('theme_name', '=', $theme);
                // $data = $data->where('store_id', '=', $store_id);
            }
            $data = $data->get();
            self::$fetchthemesetting = $data;
        }

        foreach(self::$fetchthemesetting as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }


    public static function themeOne()
    {
        $arr = [];

        $arr = [
            'theme1' => [
                'yellow-style.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme1/Home.png')),
                    'color' => 'fbd593',
                ],
                'blue-style.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme1/Home-1.png')),
                    'color' => 'aac8e3',
                ],
                'green-style.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme1/Home-2.png')),
                    'color' => 'bdd683',
                ],
            ],

            'theme2' => [
                'dark-blue-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme2/Home.png')),
                    'color' => '1E56E7',
                ],
                'dark-green-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme2/Home-1.png')),
                    'color' => '34e89e',
                ],
                'dark-pink-color.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme2/Home-2.png')),
                    'color' => '8C366C',
                ],
            ],

            'theme3' => [
                'light-blue-style.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme3/Home.png')),
                    'color' => '1DB2F8',
                ],
                'light-pink-style.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme3/Home-1.png')),
                    'color' => '39065A',
                ],
                'light-green-style.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme3/Home-2.png')),
                    'color' => '50C0C7',
                ],
            ],

            'theme4' => [
                'green-blue-style.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme4/Home.png')),
                    'color' => '06D4AE',
                ],
                'green-black-style.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme4/Home-1.png')),
                    'color' => '00727a',
                ],
                'blue-black-style.css' => [
                    'img_path' => asset(Storage::url('uploads/store_theme/theme4/Home-2.png')),
                    'color' => '8FE3CF',
                ],
            ],

        ];

        return $arr;
    }


    // get date format
    public static function getDateFormated($date, $time = false)
    {
        if(!empty($date) && $date != '0000-00-00')
        {
            if($time == true)
            {
                return date("d M Y H:i A", strtotime($date));
            }
            else
            {
                return date("d M Y", strtotime($date));
            }
        }
        else
        {
            return '';
        }
    }

    public static function success_res($msg = "", $args = array())
    {
        $msg       = $msg == "" ? "success" : $msg;
        $msg_id    = 'success.' . $msg;
        $converted = \Lang::get($msg_id, $args);
        $msg       = $msg_id == $converted ? $msg : $converted;
        $json      = array(
            'flag' => 1,
            'msg' => $msg,
        );

        return $json;
    }

    public static function error_res($msg = "", $args = array())
    {
        $msg       = $msg == "" ? "error" : $msg;
        $msg_id    = 'error.' . $msg;
        $converted = \Lang::get($msg_id, $args);
        $msg       = $msg_id == $converted ? $msg : $converted;
        $json      = array(
            'flag' => 0,
            'msg' => $msg,
        );

        return $json;
    }

    public static function notifications($store_id=null)
    {
        // $data = Notification::get();
        if(Auth::check())
        {
            $userId = Auth::user()->current_store;

            $data = Notification::where('store_id', $userId)->get();
        }
        else
        {
            $data = Notification::where('store_id', $store_id)->get();
        }
        // $data = Notification::where('store_id',$store_id)->get();
        $notifications = [

        ];

        foreach($data as $row)
        {
            $notifications[$row->name] = $row->value;
        }

        return $notifications;
    }

    public static function send_slack_msg($slug,$obj,$user_id=null)
    {
        $notification_template = NotificationTemplates::where('slug',$slug)->first();

        if (!empty($notification_template) && !empty($obj))
        {
            if(!empty($user_id))
            {
                $user = User::find($user_id);
            }
            else
            {
                $user = Auth::user();
            }
            $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user->lang)->where('created_by', '=', $user->id)->first();

            if(empty($curr_noti_tempLang))
            {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user->lang)->first();
            }
            if(empty($curr_noti_tempLang))
            {
                $curr_noti_tempLang       = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', 'en')->first();
            }
            if (!empty($curr_noti_tempLang) && !empty($curr_noti_tempLang->content))
            {
                $msg = self::replaceVariable($curr_noti_tempLang->content, $obj);
            }
        }
        if (isset($msg))
        {
            $settings = Utility::notifications($user->current_store);
            try {
                if (isset($settings['slack_webhook']) && !empty($settings['slack_webhook'])) {
                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $settings['slack_webhook']);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['text' => $msg]));

                    $headers = array();
                    $headers[] = 'Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = curl_exec($ch);
                    if (curl_errno($ch)) {
                        return 'Error:' . curl_error($ch);
                    }
                    curl_close($ch);
                }
            } catch (\Exception $e) {
            }
        }

    }

    public static function send_telegram_msg($slug,$obj,$user_id=null)
    {
        $notification_template = NotificationTemplates::where('slug',$slug)->first();

        if (!empty($notification_template) && !empty($obj))
        {
            if(!empty($user_id))
            {
                $user = User::find($user_id);
            }
            else
            {
                $user = Auth::user();
            }
            $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user->lang)->where('created_by', '=', $user->id)->first();

            if(empty($curr_noti_tempLang))
            {
                $curr_noti_tempLang = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', $user->lang)->first();
            }
            if(empty($curr_noti_tempLang))
            {
                $curr_noti_tempLang       = NotificationTemplateLangs::where('parent_id', '=', $notification_template->id)->where('lang', 'en')->first();
            }
            if (!empty($curr_noti_tempLang) && !empty($curr_noti_tempLang->content))
            {
                $msg = self::replaceVariable($curr_noti_tempLang->content, $obj);
            }
        }
        $settings  = Utility::notifications($user->current_store);

        if (isset($msg))
        {
            try{
                // Set your Bot ID and Chat ID.
                $telegrambot    = $settings['telegram_accestoken'];
                $telegramchatid = $settings['telegram_chatid'];
                // Function call with your own text or variable
                $url     = 'https://api.telegram.org/bot' . $telegrambot . '/sendMessage';
                $data    = array(
                    'chat_id' => $telegramchatid,
                    'text' => $msg,
                );
                $options = array(
                    'http' => array(
                        'method' => 'POST',
                        'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
                        'content' => http_build_query($data),
                    ),
                );
                $context = stream_context_create($options);
                $result  = file_get_contents($url, false, $context);
                $url     = $url;
            }
            catch(\Exception $e){
            }
        }

    }

    // Return timesheet sum of array
    public static function sum_time($times)
    {
        $m_h = 0;
        foreach ($times as $time) {
            $time=!empty($time->duration)?$time->duration:'00:00';
            sscanf($time, '%d:%d', $hour, $min);
            $m_h += $hour * 60 + $min;

        }
        if ($h = floor($m_h / 60)) {
            $m_h %= 60;
        }
        return sprintf('%02d:%02d', $h, $m_h);

    }

    public static function colorset()
    {
        if(self::$colorset === null)
        {
            if(Auth::user())
            {
                if(Auth::user()->type == 'super admin')
                {
                    $user = Auth::user();
                    $setting = DB::table('settings')->where('created_by', $user->id)->where('store_id','0')->pluck('value', 'name')->toArray();
                }
                else
                {
                    $setting = DB::table('settings')->where('created_by', \Auth::user()->creatorId())->where('store_id',\Auth::user()->current_store)->pluck('value', 'name')->toArray();
                }
            }
            else
            {
                $user = User::where('type','super admin')->first();
                $setting = DB::table('settings')->where('created_by',$user->id)->pluck('value','name')->toArray();
            }
            if(!isset($setting['color']))
            {
                $setting = Utility::settings();
            }
            self::$colorset = $setting;
        }
        return self::$colorset;
    }

    public static function GetLogo()
    {
        $setting = Utility::colorset();

        if(Auth::user() && Auth::user()->type != 'super admin')
        {
            if(Auth::user()->current_store)
            {
                if(Utility::getValByName('cust_darklayout') == 'on')
                {

                    return Utility::getValByName('company_logo_light');
                }
                else
                {
                    return Utility::getValByName('company_logo_dark');
                }
            }
        }
        else
        {
            if(Utility::getValByName('cust_darklayout') == 'on')
            {

                return Utility::getValByName('light_logo');
            }
            else
            {
                return Utility::getValByName('dark_logo');
            }
        }
    }

    public static function upload_file($request, $key_name, $name, $path, $custom_validation = [])
    {
        try {
            $settings = Utility::settings();


            if (!empty($settings['storage_setting'])) {

                if ($settings['storage_setting'] == 'wasabi') {

                    config(
                        [
                            'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                            'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                            'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                            'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                            'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com',
                        ]
                    );

                    $max_size = !empty($settings['wasabi_max_upload_size']) ? $settings['wasabi_max_upload_size'] : '2048';
                    $mimes = !empty($settings['wasabi_storage_validation']) ? $settings['wasabi_storage_validation'] : '';

                } else if ($settings['storage_setting'] == 's3') {
                    config(
                        [
                            'filesystems.disks.s3.key' => $settings['s3_key'],
                            'filesystems.disks.s3.secret' => $settings['s3_secret'],
                            'filesystems.disks.s3.region' => $settings['s3_region'],
                            'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                            'filesystems.disks.s3.use_path_style_endpoint' => false,
                        ]
                    );
                    $max_size = !empty($settings['s3_max_upload_size']) ? $settings['s3_max_upload_size'] : '2048';
                    $mimes = !empty($settings['s3_storage_validation']) ? $settings['s3_storage_validation'] : '';

                } else {
                    $max_size = !empty($settings['local_storage_max_upload_size']) ? $settings['local_storage_max_upload_size'] : '2048';

                    $mimes = !empty($settings['local_storage_validation']) ? $settings['local_storage_validation'] : '';
                }


                $file = $request->$key_name;
                if (count($custom_validation) > 0) {
                    $validation = $custom_validation;
                } else {
                    $validation = [
                        'mimes:' . $mimes,
                        'max:' . $max_size,
                    ];

                }

                $validator = \Validator::make($request->all(), [
                    $key_name => $validation[0],
                ]);
                if ($validator->fails()) {
                    $res = [
                        'flag' => 0,
                        'msg' => $validator->messages()->first(),
                    ];
                    return $res;
                } else {

                    $name = $name;

                    if ($settings['storage_setting'] == 'local') {

                        $request->$key_name->move(storage_path($path), $name);
                        $path = $path . $name;

                    } else if($settings['storage_setting'] == 'wasabi') {

                        $path = \Storage::disk('wasabi')->putFileAs(
                            $path,
                            $file,
                            $name
                        );

                        // $path = $path.$name;
                    } else if ($settings['storage_setting'] == 's3') {

                        $path = \Storage::disk('s3')->putFileAs(
                            $path,
                            $file,
                            $name
                        );
                        // $path = $path.$name;
                    }

                    $res = [
                        'flag' => 1,
                        'msg' => 'success',
                        'url' => $path,
                    ];
                    return $res;
                }

            } else {
                $res = [
                    'flag' => 0,
                    'msg' => __('Please set proper configuration for storage.'),
                ];
                return $res;
            }

        } catch (\Exception $e) {
            $res = [
                'flag' => 0,
                'msg' => $e->getMessage(),
            ];
            return $res;
        }
    }

    public static function json_upload_file($json, $key_name, $name, $path, $custom_validation = [])
    {
        $request = [
            $key_name => $json[$key_name]
        ];

        try {
            $settings = Utility::getStorageSetting();

            if (!empty($settings['storage_setting'])) {

                if ($settings['storage_setting'] == 'wasabi') {

                    config(
                        [
                            'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                            'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                            'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                            'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                            'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com',
                        ]
                    );

                    $max_size = !empty($settings['wasabi_max_upload_size']) ? $settings['wasabi_max_upload_size'] : '2048';
                    $mimes = !empty($settings['wasabi_storage_validation']) ? $settings['wasabi_storage_validation'] : '';

                } else if ($settings['storage_setting'] == 's3') {
                    config(
                        [
                            'filesystems.disks.s3.key' => $settings['s3_key'],
                            'filesystems.disks.s3.secret' => $settings['s3_secret'],
                            'filesystems.disks.s3.region' => $settings['s3_region'],
                            'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                            'filesystems.disks.s3.use_path_style_endpoint' => false,
                        ]
                    );
                    $max_size = !empty($settings['s3_max_upload_size']) ? $settings['s3_max_upload_size'] : '2048';
                    $mimes = !empty($settings['s3_storage_validation']) ? $settings['s3_storage_validation'] : '';

                } else {
                    $max_size = !empty($settings['local_storage_max_upload_size']) ? $settings['local_storage_max_upload_size'] : '2048';

                    $mimes = !empty($settings['local_storage_validation']) ? $settings['local_storage_validation'] : '';
                }

                $file = $json[$key_name];


                if (count($custom_validation) > 0) {
                    $validation = $custom_validation;
                } else {

                    $validation = [
                        'mimes:' . $mimes,
                        'max:' . $max_size,

                    ];

                }

                $validator = \Validator::make($request, [

                    $key_name => $validation,
                ]);

                if ($validator->fails()) {
                    $res = [
                        'flag' => 0,
                        'msg' => $validator->messages()->first(),
                    ];
                    return $res;
                } else {

                    $name = $name;

                    if ($settings['storage_setting'] == 'local') {
                        $json[$key_name]->move(storage_path($path), $name);
                        $path = $path . $name;

                    } else if ($settings['storage_setting'] == 'wasabi') {

                        $path = \Storage::disk('wasabi')->putFileAs(
                            $path,
                            $file,
                            $name
                        );

                        // $path = $path.$name;
                    } else if ($settings['storage_setting'] == 's3') {

                        $path = \Storage::disk('s3')->putFileAs(
                            $path,
                            $file,
                            $name
                        );

                    }

                    $res = [
                        'flag' => 1,
                        'msg' => 'success',
                        'url' => $path,
                    ];
                    return $res;
                }

            } else {
                $res = [
                    'flag' => 0,
                    'msg' => __('Please set proper configuration for storage.'),
                ];
                return $res;
            }

        } catch (\Exception $e) {
            $res = [
                'flag' => 0,
                'msg' => $e->getMessage(),
            ];
            return $res;
        }
    }

    public static function multi_json_upload_file($json, $key_name, $name, $path, $custom_validation = [])
    {
        $request = [
            $key_name => $json
        ];

        try {
            $settings = Utility::settings();

            if (!empty($settings['storage_setting'])) {

                if ($settings['storage_setting'] == 'wasabi') {

                    config(
                        [
                            'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                            'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                            'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                            'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                            'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com',
                        ]
                    );

                    $max_size = !empty($settings['wasabi_max_upload_size']) ? $settings['wasabi_max_upload_size'] : '2048';
                    $mimes = !empty($settings['wasabi_storage_validation']) ? $settings['wasabi_storage_validation'] : '';

                } else if ($settings['storage_setting'] == 's3') {
                    config(
                        [
                            'filesystems.disks.s3.key' => $settings['s3_key'],
                            'filesystems.disks.s3.secret' => $settings['s3_secret'],
                            'filesystems.disks.s3.region' => $settings['s3_region'],
                            'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                            'filesystems.disks.s3.use_path_style_endpoint' => false,
                        ]
                    );
                    $max_size = !empty($settings['s3_max_upload_size']) ? $settings['s3_max_upload_size'] : '2048';
                    $mimes = !empty($settings['s3_storage_validation']) ? $settings['s3_storage_validation'] : '';

                } else {
                    $max_size = !empty($settings['local_storage_max_upload_size']) ? $settings['local_storage_max_upload_size'] : '2048';

                    $mimes = !empty($settings['local_storage_validation']) ? $settings['local_storage_validation'] : '';
                }

                $file = $request[$key_name];


                if (count($custom_validation) > 0) {
                    $validation = $custom_validation;
                } else {

                    $validation = [
                        'mimes:' . $mimes,
                        'max:' . $max_size,

                    ];

                }

                $validator = \Validator::make($request, [

                    $key_name =>$validation
                ]);


                if ($validator->fails()) {
                    $res = [
                        'flag' => 0,
                        'msg' => $validator->messages()->first(),
                    ];
                    return $res;
                } else {

                    $name = $name;

                    if ($settings['storage_setting'] == 'local') {
                        $request[$key_name]->move(storage_path($path), $name);
                        $path = $path . $name;

                    } else if ($settings['storage_setting'] == 'wasabi') {

                        $path = \Storage::disk('wasabi')->putFileAs(
                            $path,
                            $file,
                            $name
                        );

                        // $path = $path.$name;
                    } else if ($settings['storage_setting'] == 's3') {

                        $path = \Storage::disk('s3')->putFileAs(
                            $path,
                            $file,
                            $name
                        );

                    }

                    $res = [
                        'flag' => 1,
                        'msg' => 'success',
                        'url' => $path,
                    ];
                    return $res;
                }

            } else {
                $res = [
                    'flag' => 0,
                    'msg' => __('Please set proper configuration for storage.'),
                ];
                return $res;
            }

        } catch (\Exception $e) {
            $res = [
                'flag' => 0,
                'msg' => $e->getMessage(),
            ];
            return $res;
        }
    }

    public static function get_file($path)
    {
        $settings = Utility::settings();

        try {
            if ($settings['storage_setting'] == 'wasabi') {
                config(
                    [
                        'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                        'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                        'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                        'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                        'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com',
                    ]
                );
            } elseif ($settings['storage_setting'] == 's3') {
                config(
                    [
                        'filesystems.disks.s3.key' => $settings['s3_key'],
                        'filesystems.disks.s3.secret' => $settings['s3_secret'],
                        'filesystems.disks.s3.region' => $settings['s3_region'],
                        'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                        'filesystems.disks.s3.use_path_style_endpoint' => false,
                    ]
                );
            }


            return \Storage::disk($settings['storage_setting'])->url($path);
            // return url('/').\Storage::disk($settings['storage_setting'])->url($path);
        } catch (\Throwable $th) {
            return '';
        }

    }

    public static function getStorageSetting()
    {
        if(self::$fetchsettings === null)
        {
            $data = DB::table('settings');
            $data = $data->where('created_by', '=', 1);
            $data = $data->get();
            self::$fetchsettings = $data;
        }
            $settings = [
                "storage_setting" => "local",
                "local_storage_validation" => "jpg,jpeg,png",
                "local_storage_max_upload_size" => "",
                "s3_key" => "",
                "s3_secret" => "",
                "s3_region" => "",
                "s3_bucket" => "",
                "s3_url" => "",
                "s3_endpoint" => "",
                "s3_max_upload_size" => "",
                "s3_storage_validation" => "",
                "wasabi_key" => "",
                "wasabi_secret" => "",
                "wasabi_region" => "",
                "wasabi_bucket" => "",
                "wasabi_url" => "",
                "wasabi_root" => "",
                "wasabi_max_upload_size" => "",
                "wasabi_storage_validation" => "",
            ];

        foreach (self::$fetchsettings as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function keyWiseUpload_file($request, $key_name, $name, $path, $data_key, $custom_validation = [])
    {
        $multifile = [
            $key_name => $request->file($key_name)[$data_key],
        ];

        try {
            $settings = Utility::settings();

            if (!empty($settings['storage_setting'])) {

                if ($settings['storage_setting'] == 'wasabi') {

                    config(
                        [
                            'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                            'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                            'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                            'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                            'filesystems.disks.wasabi.endpoint' => 'https://s3.' . $settings['wasabi_region'] . '.wasabisys.com',
                        ]
                    );

                    $max_size = !empty($settings['wasabi_max_upload_size']) ? $settings['wasabi_max_upload_size'] : '2048';
                    $mimes = !empty($settings['wasabi_storage_validation']) ? $settings['wasabi_storage_validation'] : '';

                } else if ($settings['storage_setting'] == 's3') {
                    config(
                        [
                            'filesystems.disks.s3.key' => $settings['s3_key'],
                            'filesystems.disks.s3.secret' => $settings['s3_secret'],
                            'filesystems.disks.s3.region' => $settings['s3_region'],
                            'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                            'filesystems.disks.s3.use_path_style_endpoint' => false,
                        ]
                    );
                    $max_size = !empty($settings['s3_max_upload_size']) ? $settings['s3_max_upload_size'] : '2048';
                    $mimes = !empty($settings['s3_storage_validation']) ? $settings['s3_storage_validation'] : '';

                } else {
                    $max_size = !empty($settings['local_storage_max_upload_size']) ? $settings['local_storage_max_upload_size'] : '2048';

                    $mimes = !empty($settings['local_storage_validation']) ? $settings['local_storage_validation'] : '';
                }

                $file = $request->$key_name;

                if (count($custom_validation) > 0) {
                    $validation = $custom_validation;
                } else {

                    $validation = [
                        'mimes:' . $mimes,
                        'max:' . $max_size,
                    ];

                }

                $validator = \Validator::make($multifile, [
                    $key_name => $validation,
                ]);

                if ($validator->fails()) {
                    $res = [
                        'flag' => 0,
                        'msg' => $validator->messages()->first(),
                    ];
                    return $res;
                } else {

                    $name = $name;

                    if ($settings['storage_setting'] == 'local') {

                        \Storage::disk()->putFileAs(
                            $path,
                            $request->file($key_name)[$data_key],
                            $name
                        );

                        $path = $path . $name;
                    } else if ($settings['storage_setting'] == 'wasabi') {

                        $path = \Storage::disk('wasabi')->putFileAs(
                            $path,
                            $file,
                            $name
                        );

                        // $path = $path.$name;

                    } else if ($settings['storage_setting'] == 's3') {

                        $path = \Storage::disk('s3')->putFileAs(
                            $path,
                            $file,
                            $name
                        );

                    }

                    $res = [
                        'flag' => 1,
                        'msg' => 'success',
                        'url' => $path,
                    ];
                    return $res;
                }

            } else {
                $res = [
                    'flag' => 0,
                    'msg' => __('Please set proper configuration for storage.'),
                ];
                return $res;
            }

        } catch (\Exception $e) {
            $res = [
                'flag' => 0,
                'msg' => $e->getMessage(),
            ];
            return $res;
        }
    }

    public static function StorageSettings()
    {

        $data = DB::table('settings');

            $data->where('created_by', '=', 1);
            $data = $data->get();

        $settings = [
            "site_currency" => "USD",
            "site_currency_symbol" => "$",
            "currency_symbol_position" => "pre",
            "logo_dark" => "logo-dark.png",
            "logo_light" => "logo-light.png",
            "currency_symbol" => "",
            "currency" => "",
            "site_date_format" => "M j, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "company_email" => "",
            "company_email_from_name" => "",
            "invoice_prefix" => "#INV",
            "invoice_color" => "ffffff",
            "quote_template" => "template1",
            "quote_color" => "ffffff",
            "salesorder_template" => "template1",
            "salesorder_color" => "ffffff",
            "proposal_prefix" => "#PROP",
            "proposal_color" => "fffff",
            "bill_prefix" => "#BILL",
            "bill_color" => "fffff",
            "quote_prefix" => "#QUO",
            "salesorder_prefix" => "#SOP",
            "vender_prefix" => "#VEND",
            "footer_title" => "",
            "footer_notes" => "",
            "invoice_template" => "template1",
            "bill_template" => "template1",
            "proposal_template" => "template1",
            "default_language" => "en",

            "decimal_number" => "2",
            "tax_type" => "VAT",
            "shipping_display" => "on",
            "footer_link_1" => "Support",
            "footer_value_1" => "#",
            "footer_link_2" => "Terms",
            "footer_value_2" => "#",
            "footer_link_3" => "Privacy",
            "footer_value_3" => "#",
            "display_landing_page" => "on",
            "title_text" => "",
            "footer_text" => "",
            "company_logo_light" => "logo-light.png",
            "company_logo_dark" => "logo-dark.png",
            "company_favicon" => "",
            "signup_button" => "on",
            "cust_theme_bg" => "on",
            "cust_darklayout" => "off",
            "color" => "theme-3",
            "SITE_RTL" => "off",
            "is_checkout_login_required" => "on",
            "storage_setting" => "local",
            "local_storage_validation" => "jpg,jpeg,png,xlsx,xls,csv,pdf",
            "local_storage_max_upload_size" => "2048000",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url" => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",
            "email_verification"=>"on",
        ];

        foreach ($data as $row) {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    //For Google Celender

        public static function colorCodeData($type)
    {
        if($type == 'event')
        {
            return 1;
        }
        elseif ($type == 'zoom_meeting')
        {
            return 2;
        }
        elseif ($type == 'task')
        {
            return 3;
        }
        elseif ($type == 'appointment')
        {
            return 11;
        }
        elseif ($type == 'rotas')
        {
            return 3;
        }
        elseif ($type == 'holiday')
        {
            return 4;
        }
        elseif ($type == 'call')
        {
            return 10;
        }
        elseif ($type == 'meeting')
        {
            return 5;
        }
        elseif ($type == 'leave')
        {
            return 6;
        }
        elseif ($type == 'work_order')
        {
            return 7;
        }
        elseif ($type == 'lead')
        {
            return 7;
        }
        elseif ($type == 'deal')
        {
            return 8;
        }
        elseif ($type == 'interview_schedule')
        {
            return 9;
        }
        else{
            return 11;
        }


    }

    public static $colorCode=[
        1=>'bg-warning',
        2=>'bg-secondary',
        3=>'bg-success',
        4=>'bg-warning',
        5=>'bg-danger',
        6=>'bg-dark',
        7=>'bg-black',
        8=>'bg-info',
        9=>'bg-secondary',
        10=>'bg-success',
        11=>'bg-warning',

    ];

    public static function googleCalendarConfig()
    {
        $setting = Utility::settings();
        $path = storage_path($setting['google_calender_json_file']);
        config([
            'google-calendar.default_auth_profile' => 'service_account',
            'google-calendar.auth_profiles.service_account.credentials_json' => $path,
            'google-calendar.auth_profiles.oauth.credentials_json' => $path,
            'google-calendar.auth_profiles.oauth.token_json' => $path,
            'google-calendar.calendar_id' => isset($setting['google_clender_id'])?$setting['google_clender_id']:'',
            'google-calendar.user_to_impersonate' => '',

        ]);
    }

    public static function addCalendarData($request , $type)
    {
        Self::googleCalendarConfig();

        $event = new GoogleEvent();
        $event->name = $request->title;
        $event->startDateTime = Carbon::parse($request->start_date);
        $event->endDateTime = Carbon::parse($request->start_date);
        $event->colorId = Self::colorCodeData($type);

        $event->save();
    }

    public static function getCalendarData( $type)
    {

        Self::googleCalendarConfig();

        $data= GoogleEvent::get();

        $type=Self::colorCodeData($type);
        $arrayJson = [];
        foreach($data as $val)
        {
            $end_date=date_create($val->endDateTime);
            date_add($end_date,date_interval_create_from_date_string("1 days"));

            if($val->colorId=="$type"){

                $arrayJson[] = [
                    "id"=> $val->id,
                    "title" => $val->summary,
                    "start" => $val->startDateTime,
                    "end" => date_format($end_date,"Y-m-d H:i:s"),
                    "className" => Self::$colorCode[$type],
                    "allDay" => true,
                ];
            }

        }

        return $arrayJson;
    }

    public static function pixel_plateforms(){
        $plateforms = [
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'linkedin' => 'Linkedin',
            'pinterest' => 'Pinterest',
            'quora' => 'Quora',
            'bing' => 'Bing',
            'google-adwords' => 'Google Adwords',
            'google-analytics' => 'Google Analytics',
            'snapchat' => 'Snapchat',
            'tiktok' => 'Tiktok',
        ];

        return $plateforms;
    }

    public static function GetCacheSize()
    {
        $file_size = 0;
        foreach (\File::allFiles(storage_path('/framework')) as $file)
        {
            $file_size += $file->getSize();
        }
        $file_size = number_format($file_size / 1000000, 4);
        return $file_size;
    }

    public static function get_device_type($user_agent) {
        $mobile_regex = '/(?:phone|windows\s+phone|ipod|blackberry|(?:android|bb\d+|meego|silk|googlebot) .+? mobile|palm|windows\s+ce|opera mini|avantgo|mobilesafari|docomo)/i';
        $tablet_regex = '/(?:ipad|playbook|(?:android|bb\d+|meego|silk)(?! .+? mobile))/i';

        if(preg_match_all($mobile_regex, $user_agent)) {
            return 'mobile';
        } else {

            if(preg_match_all($tablet_regex, $user_agent)) {
                return 'tablet';
            } else {
                return 'desktop';
            }

        }
    }

    public static function webhookSetting($module,$user_id=null)
    {
        if (!empty($user_id)) {
            $user = User::find($user_id);
        } else {
            $user = Auth::user();
        }
        $webhook = Webhook::where('module', $module)->where('created_by', '=', $user->id)->where('store_id', '=', $user->current_store)->first();
        if (!empty($webhook)) {
            $url = $webhook->url;
            $method = $webhook->method;
            $reference_url  = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $data['method'] = $method;
            $data['reference_url'] = $reference_url;
            $data['url'] = $url;
            return $data;
        }
        return false;
    }
    public static function WebhookCall($url = null, $parameter = null, $method = 'POST')
    {
        if (!empty($url) && !empty($parameter)) {
            try {
                $curlHandle = curl_init($url);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $parameter);
                curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, strtoupper($method));
                $curlResponse = curl_exec($curlHandle);
                curl_close($curlHandle);
                if (empty($curlResponse)) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Throwable $th) {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function replaceVariable($content, $obj)
    {
        $arrVariable = [
            '{course_title}',
            '{store_name}',
            '{order_id}',
            '{title}',
        ];
        $arrValue    = [
            'course_title' => '-',
            'store_name' => '-',
            'order_id' => '-',
            'title' => '-',
        ];

        foreach ($obj as $key => $val) {
            $arrValue[$key] = $val;
        }

        $settings = Utility::settings();
        $company_name = $settings['company_name'];

        $arrValue['app_name']     =  $company_name;
        $arrValue['company_name'] = self::settings()['company_name'];
        $arrValue['app_url']      = '<a href="' . env('APP_URL') . '" target="_blank">' . env('APP_URL') . '</a>';

        return str_replace($arrVariable, array_values($arrValue), $content);
    }

    // start for (plans) storage limit - for file upload size
    public static function updateStorageLimit($company_id, $image_size)
    {
        $image_size = number_format($image_size / 1048576, 2);

        $user   = User::find($company_id);
        $plan   = Plan::find($user->plan);
        $total_storage = $user->storage_limit + $image_size;
        if($plan->storage_limit <= $total_storage && $plan->storage_limit != -1)
        {

            $error= __('Plan storage limit is over so please upgrade the plan.');
            return $error;
        }
        else{
            $user->storage_limit = $total_storage;
        }

        $user->save();
        return 1;

    }

    public static function changeStorageLimit($company_id, $file_path)
    {
        $files =  \File::glob(storage_path($file_path));
        $fileSize = 0;
        foreach($files as $file){
            $fileSize += \File::size($file);
        }

        $image_size = number_format($fileSize / 1048576, 2);

        $user   = User::find($company_id);
        $plan   = Plan::find($user->plan);

        $total_storage = $user->storage_limit - $image_size;

        $user->storage_limit = $total_storage;
        $user->save();

        $status = false;
        foreach($files as $key => $file)
        {
            if(\File::exists($file))
            {
                $status = \File::delete($file);
            }
        }

        return true;

    }
    // end for (plans) storage limit - for file upload size

    public static function flagOfCountry(){
        $arr = [
            'ar' => '🇦🇪 ar',
            'da' => '🇩🇰 da',
            'de' => '🇩🇪 de',
            'es' => '🇪🇸 es',
            'fr' => '🇫🇷 fr',
            'it' =>  '🇮🇹 it',
            'ja' => '🇯🇵 ja',
            'nl' => '🇳🇱 nl',
            'pl'=> '🇵🇱 pl',
            'ru' => '🇷🇺 ru',
            'pt' => '🇵🇹 pt',
            'en' => '🇮🇳 en',
            'tr' => '🇹🇷 tr',
            'pt-br' => '🇵🇹 pt-br',
        ];
        return $arr;
    }

    public static function plansetting($user_id=null)
    {
        if(Auth::check()){
            $user = Auth::user();
        }
        else{
            $user = User::where('id',$user_id)->first();
        }

        if(Auth::user()->type != 'Owner'){
            $user = User::where('id',$user->created_by)->first();
        }

        $plan = Plan::where('id',$user->plan)->first();
        return $plan;
    }

    public static function languagecreate(){
        $languages=Utility::langList();
        foreach($languages as $key => $lang)
        {
            $languageExist = Language::where('code',$key)->first();
            if(empty($languageExist))
            {
                $language = new Language();
                $language->code = $key;
                $language->fullName = $lang;
                $language->save();
            }
        }
    }

    public static function langList(){
        $languages = [
            "ar" => "Arabic",
            "zh" => "Chinese",
            "da" => "Danish",
            "de" => "German",
            "en" => "English",
            "es" => "Spanish",
            "fr" => "French",
            "he" => "Hebrew",
            "it" => "Italian",
            "ja" => "Japanese",
            "nl" => "Dutch",
            "pl" => "Polish",
            "pt" => "Portuguese",
            "ru" => "Russian",
            "tr" => "Turkish",
            "pt-br"=>"Portuguese(Brazil)"
        ];
        return $languages;
    }

    public static function langSetting()
    {
        if(self::$fetchsettings === null)
        {
            $data = DB::table('settings');
            $data = $data->where('created_by', '=', 1)->get();
            self::$fetchsettings = $data;
        }
        $settings= [];
        foreach (self::$fetchsettings as $row) {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    public static function getSMTPDetails($user_id)
    {
        $settings = Utility::settings($user_id);
        if ($settings) {
            config([
                'mail.default'                   => isset($settings['mail_driver'])       ? $settings['mail_driver']       : '',
                'mail.mailers.smtp.host'         => isset($settings['mail_host'])         ? $settings['mail_host']         : '',
                'mail.mailers.smtp.port'         => isset($settings['mail_port'])         ? $settings['mail_port']         : '',
                'mail.mailers.smtp.encryption'   => isset($settings['mail_encryption'])   ? $settings['mail_encryption']   : '',
                'mail.mailers.smtp.username'     => isset($settings['mail_username'])     ? $settings['mail_username']     : '',
                'mail.mailers.smtp.password'     => isset($settings['mail_password'])     ? $settings['mail_password']     : '',
                'mail.from.address'              => isset($settings['mail_from_address']) ? $settings['mail_from_address'] : '',
                'mail.from.name'                 => isset($settings['mail_from_name'])    ? $settings['mail_from_name']    : '',
            ]);

            return $settings;
        } else {
            return redirect()->back()->with('Email SMTP settings does not configured so please contact to your site admin.');
        }
    }
}

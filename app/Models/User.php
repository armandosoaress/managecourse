<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable implements MustVerifyEmail
{

    use HasRoles;
    use Notifiable;
    use Impersonate;
    private static $currentuser = null;
    private static $currentstore = null;
    private static $currentlanguage = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var arra
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_store',
        'type',
        'lang',
        'created_by',
        'is_disable',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function creatorId()
    {
        if($this->type == 'Owner' || $this->type == 'super admin')
        {
            return $this->id;
        }
        else
        {
            return $this->created_by;
        }
    }



    public function creator_id()
    {
        if($this->type == 'Owner')
        {
            return $this->created_by;
        }
        else
        {
            return $this->created_by;
        }
    }

    public function currentLanguages()
    {
        return $this->lang;
    }

    public function countCompany()
    {
        return User::where('type', '=', 'Owner')->where('created_by', '=', $this->creatorId())->count();
    }

    public function countPaidCompany()
    {
        return User::where('type', '=', 'Owner')->whereNotIn(
            'plan', [
                      0,
                      1,
                  ]
        )->where('created_by', '=', \Auth::user()->id)->count();
    }

    public function assignPlan($planID)
    {
        $plan = Plan::find($planID);
        if($plan)
        {
            $this->plan = $plan->id;
            if($plan->duration == 'Month')
            {
                $this->plan_expire_date = Carbon::now()->addMonths(1)->isoFormat('YYYY-MM-DD');
            }
            elseif($plan->duration == 'Year')
            {
                $this->plan_expire_date = Carbon::now()->addYears(1)->isoFormat('YYYY-MM-DD');
            }
            else if($plan->duration == 'lifetime')
            {
                $this->plan_expire_date = null;
            }
            // else{
            //     $this->plan_expire_date = null;
            // }
            $this->save();

            $users    = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'super admin')->get();
            $courses = Course::where('created_by', '=', \Auth::user()->creatorId())->get();
            $stores   = Store::where('created_by', '=', \Auth::user()->creatorId())->get();


            if($plan->max_stores == -1)
            {
                foreach($stores as $store)
                {
                    $store->is_active = 1;
                    $store->save();
                }
            }
            else
            {
                $storeCount = 0;
                foreach($stores as $store)
                {
                    $storeCount++;
                    if($storeCount <= $plan->max_stores)
                    {
                        $store->is_active = 1;
                        $store->save();
                    }
                    else
                    {
                        $store->is_active = 0;
                        $store->save();
                    }
                }
            }

            if($plan->max_courses == -1)
            {
                foreach($courses as $course)
                {
                    $course->featured_course = 'on';
                    $course->save();
                }
            }
            else
            {
                $courseCount = 0;
                foreach($courses as $course)
                {
                    $courseCount++;
                    if($courseCount <= $plan->max_courses)
                    {
                        $course->featured_course = 'on';
                        $course->save();
                    }
                    else
                    {
                        $course->featured_course = 'off';
                        $course->save();
                    }
                }
            }

            return ['is_success' => true];
        }
        else
        {
            return [
                'is_success' => false,
                'error' => 'Plan is deleted.',
            ];
        }
    }

    public function countCourses()
    {
        return Course::where('created_by', '=', $this->creatorId())->count();
    }

    public function countStores($id)
    {
        return Store::where('created_by', $id)->count();
    }

    public function countStore()
    {
        return Store::where('created_by', '=', $this->creatorId())->count();
    }
    public function countUsers()
    {
        return User::where('type', '!=', 'super admin')->where('type', '!=', 'company')->where('current_store', '=', $this->current_store)->where('created_by', '=', $this->creatorId())->count();
    }
    public function currentPlan()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'plan');
    }

    public function currentlang()
    {
        return $this->hasOne('App\Models\Language', 'code', 'lang');
    }

    public function stores()
    {
        return $this->belongsToMany('App\Models\Store', 'user_stores', 'user_id', 'store_id')->withPivot('permission');
    }

    public function dateFormat($date)
    {
        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($date));
    }

    public function currentuser(){
        if(is_null(self::$currentuser)){
            $user =  User::find($this->creatorId());
            self::$currentuser = $user;
        }
        return self::$currentuser;
    }

    public function currentstore($store_id=null){
        if(is_null(self::$currentstore)){
            if(\Auth::check())
            {
                $store_id = \Auth::user()->current_store;
            }
            $store = Store::find($store_id);
            self::$currentstore = $store;
        }
        return self::$currentstore;
    }
}

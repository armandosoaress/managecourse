<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use Notifiable;
    private static $purchasedcourse = null;
    private static $authstudent = null;
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'password',
        'store_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function course_wl()
    {
        return $this->belongsToMany(
            'App\Models\Course', 'wishlists', 'student_id', 'course_id'
        );
    }

    public function course_purchased()
    {
        return $this->belongsToMany(
            'App\Models\Course', 'purchased_courses', 'student_id', 'course_id'
        );
    }

    public function purchasedCourse()
    {
        if(self::$purchasedcourse === null)
        {
            $purchasecourse = $this->hasMany('App\Models\PurchasedCourse', 'student_id', 'id')->get()->pluck('course_id')->toArray();
            self::$purchasedcourse = $purchasecourse;
        }
        return self::$purchasedcourse;
    }

    public function Coursename()
    {
        return $this->hasOne('App\Models\Course', 'id', 'title');
    }

    public static function studentAuth($store_id)
    {
        if(is_null(self::$authstudent))
        {
            $auth_student = \Auth::guard('students')->user();
            $student =  Student::where('store_id',$store_id)->where('email',$auth_student->email)->count();
            self::$authstudent = $student;
        }
        return self::$authstudent;
    }
}

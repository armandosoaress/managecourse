<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Wishlist extends Model
{
    private static $wishlist = null;
    protected $fillable = [
        'course_id',
        'student_id',
    ];

    public static function wishCount()
    {
        if(is_null(self::$wishlist))
        {
            $wishlist =  Wishlist::where('student_id', Auth::guard('students')->id())->count();
            self::$wishlist = $wishlist;
        }
        return self::$wishlist;
    }

}


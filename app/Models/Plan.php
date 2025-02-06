<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'duration',
        'max_stores',
        'max_courses',
        'max_users',
        'storage_limit',
        'enable_custdomain',
        'enable_custsubdomain',
        'additional_page',
        'blog',
        'enable_chatgpt',
        'image',
        'description',
    ];

    public static $arrDuration = [
        'lifetime' => 'lifetime',
        'Month' => 'Per Month',
        'Year' => 'Per Year',
    ];

    private static $fetchpurchaseplan=null;

    public static function total_plan()
    {
        return Plan::count();
    }

    public static function most_purchese_plan()
    {
        if(is_null(self::$fetchpurchaseplan))
        {
            $free_plan = Plan::where('price', '<=', 0)->first()->id;
            $user = User:: select('plans.name', 'plans.id', \DB::raw('count(*) as total'))->join('plans', 'plans.id', '=', 'users.plan')->where('type', '=', 'owner')->where('plan', '!=', $free_plan)->orderBy('total', 'Desc')->groupBy('plans.name', 'plans.id')->first();

            self::$fetchpurchaseplan = $user;
        }
        return self::$fetchpurchaseplan;
    }

    public function transkeyword()
    {
        $arr = [
            __('Per Month'),
            __('Per Year'),
            __('Year'),
        ];
    }
}

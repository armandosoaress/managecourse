<?php

namespace Database\Seeders;

use App\Models\Utility;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if(\Request::route()->getName()!='LaravelUpdater::database')
        {
            $this->call(UsersTableSeeder::class);
            $this->call(PlansTableSeeder::class);
            $this->call(NotificationSeeder::class);
            $this->call(AiTemplateSeeder::class);
        }else{
            Utility::languagecreate();
        }
        Artisan::call('module:migrate LandingPage');
        Artisan::call('module:seed LandingPage');
    }
}


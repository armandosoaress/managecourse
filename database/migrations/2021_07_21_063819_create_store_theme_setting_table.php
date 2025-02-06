<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreThemeSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_theme_setting', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('name/pagename');
            $table->text('value')->nullable()->comment('value/json_value');
            $table->string('type')->nullable();
            $table->string('theme_name')->nullable();
            $table->integer('store_id');
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_theme_settings');
    }
}

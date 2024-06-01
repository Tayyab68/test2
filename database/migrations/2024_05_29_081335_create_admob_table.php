<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admob', function (Blueprint $table) {
            $table->id();
            $table->string('publisher_id');
            $table->string('admob_app_id');
            $table->string('banner_id');
            $table->string('intersial_id');
            $table->string('native_id');
            $table->string('rewarded_id');
            $table->int('type');
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
        Schema::dropIfExists('admob');
    }
};

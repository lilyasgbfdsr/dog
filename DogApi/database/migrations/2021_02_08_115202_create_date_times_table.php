<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDateTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('date_times', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('sort')->default(0)->comment('排序字段');
            $table->string('start_time', 16)->nullable()->comment('开始时间段');
            $table->string('end_time', 16)->nullable()->comment('结束时间段');
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
        Schema::dropIfExists('date_times');
    }
}

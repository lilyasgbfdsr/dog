<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDataReserveInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('data_reserve_info', function(Blueprint $table)
		{
			$table->increments('id')->comment('id');
			$table->integer('int_time')->unsigned()->comment('预约日期');
			$table->integer('add_time')->unsigned()->comment('添加时间');
			$table->boolean('hour')->default(0)->comment('预约时间段');
			$table->boolean('type')->default(0)->comment('预约类型：1天 2时间段');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('data_reserve_info');
	}

}

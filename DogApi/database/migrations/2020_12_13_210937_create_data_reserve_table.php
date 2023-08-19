<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDataReserveTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('data_reserve', function(Blueprint $table)
		{
			$table->increments('id')->comment('id');
			$table->char('reserve_id', 32)->comment('预约uuid');
			$table->integer('int_time')->unsigned()->comment('预约日期');
			$table->string('name')->comment('预约姓名');
			$table->char('telephone', 11)->comment('电话');
			$table->boolean('number')->comment('预约人数');
			$table->integer('add_time')->unsigned()->comment('添加时间');
			$table->string('openid')->nullable()->comment('微信唯一id');
			$table->boolean('hour')->comment('预约时间段');
			$table->boolean('operate_status')->default(0)->comment('操作：1为修改 2为取消');
			$table->boolean('check_status')->default(2)->comment('签到：1为签到');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('data_reserve');
	}

}

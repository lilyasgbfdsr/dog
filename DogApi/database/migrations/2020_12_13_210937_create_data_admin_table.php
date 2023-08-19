<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDataAdminTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('data_admin', function(Blueprint $table)
		{
			$table->increments('id')->comment('id主键');
			$table->char('guid', 32)->comment('管理员guid');
			$table->string('username')->comment('用户ID');
			$table->char('password', 32)->comment('密码');
			$table->boolean('status')->comment('状态:1为启用 2为禁用');
			$table->char('token', 32)->comment('token值');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('data_admin');
	}

}

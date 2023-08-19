<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDataConfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('data_config', function(Blueprint $table)
		{
			$table->boolean('id')->primary()->comment('id主键');
			$table->string('name', 32)->comment('配置名称');
			$table->boolean('status')->default(0)->comment('状态');
			$table->integer('value')->unsigned()->default(0)->comment('值');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('data_config');
	}

}

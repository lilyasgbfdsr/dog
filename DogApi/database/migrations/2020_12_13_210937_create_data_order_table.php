<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDataOrderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('data_order', function(Blueprint $table)
		{
			$table->increments('id')->comment('id');
			$table->char('reserve_id', 32)->comment('预约uuid');
			$table->char('order_id', 32)->comment('订单号');
			$table->decimal('order_price', 10)->comment('订单金额');
			$table->boolean('status')->comment('订单状态:1为已支付 2为未支付');
			$table->string('appid', 32)->nullable()->comment('公众号appid');
			$table->string('bank_type', 32)->nullable()->comment('付款银行:OTHERS其他（银行卡以外）');
			$table->integer('cash_fee')->nullable()->comment('现金支付金额:分');
			$table->string('fee_type', 8)->nullable()->comment('货币种类:默认人民币：CNY');
			$table->char('is_subscribe', 1)->nullable()->comment('是否关注公众账号:Y-关注，N-未关注');
			$table->string('mch_id', 32)->nullable()->comment('商户号');
			$table->string('nonce_str', 32)->nullable()->comment('随机字符串');
			$table->string('openid')->nullable()->comment('用户标识');
			$table->string('out_trade_no', 32)->nullable()->comment('商户订单号');
			$table->string('result_code', 16)->nullable()->comment('业务结果');
			$table->string('sign', 32)->nullable()->comment('签名');
			$table->string('time_end', 16)->nullable()->comment('支付完成时间');
			$table->integer('total_fee')->unsigned()->nullable()->comment('订单金额');
			$table->string('trade_type', 16)->nullable()->comment('交易类型:JSAPI、NATIVE、APP');
			$table->string('transaction_id', 64)->nullable()->comment('微信支付订单号');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('data_order');
	}

}

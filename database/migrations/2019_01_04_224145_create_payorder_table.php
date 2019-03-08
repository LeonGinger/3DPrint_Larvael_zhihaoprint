<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayorderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payorders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trade_no',32)->comment('订单编号');
            $table->string('tenat_id',32)->comment('商户id');
            $table->string('customer_id',32)->comment('客户id');
            $table->string('product_id',32)->comment('商品id');
			$table->float('total_fee', 8, 2)->comment('订单金额');
            $table->string('status',10)->nullable()->comment('支付状态');
			$table->dateTime('paid_at')->nullable()->comment('完成支付时间');
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
        Schema::dropIfExists('payorders');
    }
}

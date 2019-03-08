<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluates', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('tenat_id')->comment('商家id');
			$table->integer('customer_id')->comment('客户id');
			$table->integer('order_id')->comment('订单id');
			$table->integer('manger')->comment('项目经理id');
			$table->integer('pojer')->comment('项目工程师id');
			$table->integer('saler')->comment('销售id');
			$table->text('content')->comment('评价内容');
			$table->tinyInteger('pingjia')->comment('评价');
			$table->tinyInteger('manger_star')->comment('项目经理星级');
			$table->tinyInteger('pojer_star')->comment('项目工程师星级');
			$table->tinyInteger('saler_star')->comment('销售工程师星级');
			$table->boolean('anonymous')->default(0)->comment('是否匿名');
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
        Schema::dropIfExists('evaluates');
    }
}

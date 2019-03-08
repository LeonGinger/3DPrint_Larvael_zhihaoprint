<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'orders';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tenat_id')->index()->comment('商家ID，外键');
            $table->integer('customer_id')->index()->comment('客户ID，外键');
            $table->string('no')->comment('编号');
            $table->timestamps();
        });
        add_table_comment($table_name, '订单');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

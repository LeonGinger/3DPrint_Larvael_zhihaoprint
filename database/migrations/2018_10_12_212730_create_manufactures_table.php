<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManufacturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'manufactures';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->index()->comment('订单ID，外键');
            $table->integer('customer_id')->index()->comment('客户ID，外键');
            $table->string('no')->comment('编号');
            $table->tinyInteger('status')->comment('状态');
            $table->string('qrcode_url')->comment('二维码地址');
            $table->timestamps();
        });
        add_table_comment($table_name, '生产任务书');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manufactures');
    }
}

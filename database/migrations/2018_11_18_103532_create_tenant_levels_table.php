<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'tenant_levels';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('名称');
            $table->integer('customer_numbers')->comment('客户数量');
            $table->integer('order_numbers')->comment('订单数量');
            $table->decimal('price')->comment('价格');
            $table->boolean('is_enable')->comment('启用状态');
            $table->timestamps();
        });
        add_table_comment($table_name, '商家等级');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenant_levels');
    }
}

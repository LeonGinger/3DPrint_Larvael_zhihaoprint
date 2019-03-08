<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'customers';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tenat_id')->index()->comment('商家ID，外键');
            $table->integer('customer_type_id')->index()->comment('客户类别ID，外键');
            $table->string('no')->comment('编号');
            $table->string('name')->comment('名称');
            $table->string('address')->comment('地址');
            $table->string('linkman')->comment('联系人');
            $table->tinyInteger('status')->comment('状态');
            $table->string('ticket_info')->comment('开票信息');
            $table->string('consignee_info')->comment('收货人信息');
            $table->timestamps();
        });
        add_table_comment($table_name, '客户');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}

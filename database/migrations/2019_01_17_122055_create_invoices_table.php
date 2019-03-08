<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tablename = 'invoices';
        Schema::create($tablename, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tenat_id')->index()->comment('外键。商户ID');
            $table->string('rise')->comment('发票抬头');
            $table->string('tax_number')->comment('税务登记号');
            $table->string('addressee')->comment('接收人');
            $table->string('phone')->comment('联系手机');
            $table->string('province')->comment('省');
            $table->string('city')->comment('市');
            $table->string('region')->comment('区/县');
            $table->string('address')->comment('详细地址');
            $table->string('base_account_number')->nullable()->comment('基本户开户账号');
            $table->string('base_account_bank')->nullable()->comment('基本户开户银行');
            $table->string('company_number')->nullable()->comment('公司联系方式');
            $table->string('company_register_address')->nullable()->comment('公司注册地址');
            $table->tinyInteger('status')->comment('状态。1：待受理；2：受理中；3；拒绝受理；4：已完成；');
            $table->string('feedback')->nullable()->comment('备注信息');
            $table->string('file_url')->nullable()->comment('上传文件');
            $table->timestamp('handle_time')->nullable()->comment('处理时间');
            $table->timestamps();
        });
        add_table_comment($tablename, '发票');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}

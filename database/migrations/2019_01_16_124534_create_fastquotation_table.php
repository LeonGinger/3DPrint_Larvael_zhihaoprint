<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFastquotationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fastquotations', function (Blueprint $table) {
            $table->increments('id');
			$table->string('tenat_id',32)->comment('商户id');
			$table->string('fast_id')->comment('快速报价单编码');
			$table->string('qrcode_url',1000)->nullable()->comment('快速报价单二维码地址');
			$table->string('parts')->comment('零件列表');
			$table->text('fp')->nullable()->comment('客户区域');
			$table->text('qt')->nullable()->comment('货物交付以及结算');
			$table->text('qs')->nullable()->comment('零件检验质量标准');
			$table->decimal('total', 18, 2)->comment('合计金额');
			$table->decimal('taxation', 8, 2)->nullable()->comment('税费');
			$table->decimal('freight', 8, 2)->nullable()->comment('运费');
			$table->softDeletes();
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
        Schema::dropIfExists('fastquotations');
    }
}

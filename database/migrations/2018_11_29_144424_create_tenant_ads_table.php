<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant_ads', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('tenant_id')->comment('商家id');
			$table->string('page',100)->comment('页面');
			$table->string('loca',100)->comment('位置');
			$table->string('imgurl',100)->comment('图片目录');
            $table->timestamps();
        });
		add_table_comment('tenant_ads', '商家广告位');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenant_ads');
    }
}

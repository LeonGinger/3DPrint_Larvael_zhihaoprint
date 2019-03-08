<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant_temps', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('tenant_id')->comment('商家id');
			$table->text('text1')->comment('零件检验质量标准模板文字');
			$table->text('text2')->comment('货物交付以及结算区域模板文字');
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
        Schema::dropIfExists('tenant_temps');
    }
}

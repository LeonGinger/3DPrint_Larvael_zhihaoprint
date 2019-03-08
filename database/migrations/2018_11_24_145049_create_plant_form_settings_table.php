<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlantFormSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'plant_form_settings';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->longText('baojia_template_text1')->nullable()->comment('零件检验质量标准模板文字');
            $table->longText('baojia_template_text2')->nullable()->comment('货物交付以及结算区域模板文字');
            $table->string('ad_header_url')->nullable()->comment('页眉广告图片URL');
            $table->string('ad_footer_url')->nullable()->comment('页脚广告图片URL');
            $table->timestamps();
        });
        add_table_comment($table_name, '平台设置');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plant_form_settings');
    }
}

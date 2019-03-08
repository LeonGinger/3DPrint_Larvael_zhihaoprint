<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tablename = 'news_types';
        Schema::create($tablename, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('名称');
            $table->tinyInteger('sort')->default(0)->comment('排序。越大越靠前');
            $table->boolean('is_enable')->default(true)->comment('是否启用');
            $table->timestamps();
        });
        add_table_comment($tablename, '新闻类别');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news_types');
    }
}

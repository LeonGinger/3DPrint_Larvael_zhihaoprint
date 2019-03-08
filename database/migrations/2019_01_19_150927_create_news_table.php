<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tablename = 'news';
        Schema::create($tablename, function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('标题');
            $table->string('sub_title')->nullable()->comment('副标题');
            $table->longText('content')->nullable()->comment('内容');
            $table->string('author')->nullable()->comment('作者');
            $table->integer('views_count')->nullable()->comment('浏览量');
            $table->tinyInteger('sort')->default(0)->comment('排序。越大越靠前');
            $table->boolean('is_show')->default(true)->comment('是否显示');
            $table->timestamps();
        });
        add_table_comment($tablename, '新闻');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}

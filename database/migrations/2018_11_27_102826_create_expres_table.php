<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		$table_name = 'expres';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->string('exname',100)->comment('快递公司');
            $table->string('code',10)->comment('日志');
            $table->tinyInteger('status')->default(0)->comment('状态');
            $table->timestamps();
        });
        add_table_comment($table_name, '快递公司');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expres');
    }
}

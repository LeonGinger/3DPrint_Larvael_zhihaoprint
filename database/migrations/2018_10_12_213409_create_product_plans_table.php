<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'product_plans';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('manufacture_id')->index()->comment('生产任务书ID，外键');
            $table->integer('part_id')->index()->comment('零件ID，外键');
            $table->timestamps();
        });
        add_table_comment($table_name, '生产计划');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_plans');
    }
}

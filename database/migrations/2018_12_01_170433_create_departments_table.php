<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->increments('id');
			$table->string('depname')->comment('部门名称');
			$table->string('depcode')->comment('部门编码');
			$table->tinyInteger('status')->default(1)->comment('状态');
            $table->timestamps();
        });
		add_table_comment('departments', '部门列表');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
}

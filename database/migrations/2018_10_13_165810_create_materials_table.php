<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'materials';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('名称');
            $table->decimal('price')->comment('价格');
            $table->string('density')->comment('密度');
            $table->tinyInteger('status')->comment('状态');
            $table->timestamps();
        });
        add_table_comment($table_name, '成型材质');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('materials');
    }
}

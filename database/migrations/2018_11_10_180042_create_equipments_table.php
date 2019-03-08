<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('mold_id')->index()->comment('工艺ID，外键');
			$table->string('mname')->comment('设备型号');
			$table->string('marc')->comment('成型范围');
			$table->string('mmaker')->comment('制造商名称');
            $table->timestamps();
        });
		add_table_comment('equipments', '成型设备');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipments');
    }
}

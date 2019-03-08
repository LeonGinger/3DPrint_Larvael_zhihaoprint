<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'warehouses';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('part_id')->index()->comment('零件ID，外键');
            $table->integer('tenat_id')->index()->comment('商户ID，外键');
            $table->string('name')->comment('名称');
            $table->timestamp('storage_date')->comment('入库时间');
            $table->timestamp('outage_date')->comment('出库时间');
            $table->tinyInteger('status')->comment('状态');
            $table->timestamps();
        });
        add_table_comment($table_name, '仓库');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouses');
    }
}

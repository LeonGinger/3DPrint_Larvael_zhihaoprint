<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFastpartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fastpart', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fq_id')->comment('订单ID，外键');
            $table->integer('material_id')->comment('成型材质ID，外键');
            $table->string('name')->comment('名称');
            $table->string('diagram')->comment('简图');
            $table->string('volume_size')->comment('体积尺寸');
            $table->tinyInteger('status')->comment('状态');
            $table->string('coefficient')->comment('系数');
            $table->decimal('price')->comment('价格');
            $table->integer('product_num')->comment('生产数量');
            $table->text('requirements')->nullable()->comment('处理要求');
            $table->text('remark')->nullable()->comment('备注');
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
        Schema::dropIfExists('fastpart');
    }
}

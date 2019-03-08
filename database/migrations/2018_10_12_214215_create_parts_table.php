<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'parts';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->index()->comment('订单ID，外键');
            $table->integer('material_id')->index()->comment('成型材质ID，外键');
            $table->integer('surface_id')->index()->comment('表面处理ID，外键');
            $table->integer('molding_process_id')->index()->comment('成型工艺ID，外键');
            $table->string('name')->comment('名称');
            $table->string('diagram')->comment('简图');
            $table->string('volume_size')->comment('体积尺寸');
            $table->tinyInteger('status')->comment('状态');
            $table->string('coefficient')->comment('系数');
            $table->decimal('price')->comment('价格');
            $table->integer('product_num')->comment('生成数量');
            $table->text('requirements')->nullable()->comment('处理要求');
            $table->text('remark')->nullable()->comment('备注');
            $table->string('delivery_no')->comment('送货单号');
            $table->timestamp('start_date')->comment('启动日期');
            $table->timestamp('due_date')->comment('交货日期');
            $table->timestamps();
        });
        add_table_comment($table_name, '零件');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parts');
    }
}

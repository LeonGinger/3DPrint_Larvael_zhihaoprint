<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		$table_name = 'deliverys';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('part_id')->comment('零件ID，外键');
            $table->string('delivery_note_id',2000)->nullable()->comment('送货单ID列表，外键');
            $table->text('log')->nullable()->comment('日志');
            $table->tinyInteger('status')->default(0)->comment('状态');
            $table->timestamps();
        });
        add_table_comment($table_name, '送货单拆分');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliverys');
    }
}

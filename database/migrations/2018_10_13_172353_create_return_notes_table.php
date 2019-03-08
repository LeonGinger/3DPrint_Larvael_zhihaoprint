<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturnNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'return_notes';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->index()->comment('订单ID，外键');
            $table->integer('part_id')->index()->comment('零件ID，外键');
            $table->integer('commit_user_id')->index()->comment('提交员ID，外键');
            $table->integer('handle_user_id')->index()->comment('处理人员ID，外键');
            $table->tinyInteger('returned_type')->comment('退货方式');
            $table->tinyInteger('status')->comment('状态');
            $table->string('remark')->comment('说明');
            $table->timestamps();
        });
        add_table_comment($table_name, '退货单');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('return_notes');
    }
}

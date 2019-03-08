<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'comments';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->index()->comment('订单ID，外键');
            $table->integer('customer_id')->index()->comment('客户ID，外键');
            $table->tinyInteger('starts')->comment('星级。1-5');
            $table->tinyInteger('status')->comment('状态');
            $table->string('content')->comment('评价内容');
            $table->timestamps();
        });
        add_table_comment($table_name, '评论');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}

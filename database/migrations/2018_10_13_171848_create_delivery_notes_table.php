<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'delivery_notes';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->string('distribution_mode')->comment('配送方式');
            $table->string('express_no')->comment('快递单号');
            $table->tinyInteger('status')->comment('状态');
            $table->timestamps();
        });
        add_table_comment($table_name, '送货单');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_notes');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'quotations';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->string('no')->comment('编号');
            $table->decimal('tax')->comment('税额');
            $table->tinyInteger('status')->comment('状态');
            $table->string('qrcode_url')->comment('二维码地址');
            $table->timestamps();
        });
        add_table_comment($table_name, '报价单');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotations');
    }
}

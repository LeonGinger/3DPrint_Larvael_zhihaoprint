<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'customer_types';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pid')->index()->comment('父级ID');
            $table->string('name')->comment('名称');
            $table->timestamps();
        });
        add_table_comment($table_name, '客户分类');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_types');
    }
}

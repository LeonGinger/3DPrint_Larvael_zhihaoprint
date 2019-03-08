<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'tenant_types';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('名称');
            $table->text('intro')->nullable()->comment('说明介绍');
            $table->timestamps();
        });
        add_table_comment($table_name, '商家行业');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenant_types');
    }
}

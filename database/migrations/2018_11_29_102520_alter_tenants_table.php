<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('tenants', function (Blueprint $table) {
		   $table->string('address','2000')->after('tenant_type_id')->nullable()->comment('详细地址');
		   $table->string('area')->after('tenant_type_id')->nullable()->comment('区');
		   $table->string('city')->after('tenant_type_id')->nullable()->comment('市');
			$table->string('province')->after('tenant_type_id')->nullable()->comment('省');
			
			$table->string('daihao','500')->after('name')->nullable()->comment('企业简称');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

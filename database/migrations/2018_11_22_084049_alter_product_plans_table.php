<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::table('product_plans', function (Blueprint $table) {
			$table->tinyInteger('status')->default(0)->comment('状态');
			$table->timestamp('pics')->after('part_id')->nullable()->comment('图册');
			$table->timestamp('end_data')->after('part_id')->nullable()->comment('结束日期');
			$table->timestamp('start_data')->after('part_id')->nullable()->comment('开始日期');
			$table->timestamp('jh_data')->after('part_id')->nullable()->comment('交货日期');
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

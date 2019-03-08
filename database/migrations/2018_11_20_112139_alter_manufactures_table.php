<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterManufacturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::table('manufactures', function (Blueprint $table) {
			$table->dropColumn('order_id');
			$table->text('qrcode_url')->change();
			$table->text('parts')->after('no')->comment('零件列表');
			$table->integer('prouse_id')->after('no')->comment('生产工程师id');
			$table->softDeletes();
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

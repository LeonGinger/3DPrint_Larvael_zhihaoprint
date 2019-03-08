<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
			$table->string('freight')->after('no')->comment('运费');
			$table->string('taxation')->after('no')->comment('税费');
			$table->string('parts')->after('no')->comment('零件列表');
			$table->integer('saler')->after('no')->comment('销售工程师');
			$table->integer('pojer')->after('no')->comment('项目工程师');
			$table->integer('manger')->after('no')->comment('项目经理');
			$table->integer('invoaddr')->after('no')->comment('发票寄送地址');
			$table->integer('postaddr')->after('no')->comment('零件寄送地址');
			$table->string('lkmans')->after('no')->comment('联系人列表');
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

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
		Schema::table('quotations', function (Blueprint $table) {
			$table->integer('order_id')->after('id')->comment('订单id');
			$table->text('qrcode_url')->change();
			$table->text('qt')->after('tax')->comment('货物交付以及结算');
			$table->text('qs')->after('tax')->comment('零件检验质量标准');
			$table->decimal('total', 18, 2)->after('tax')->comment('合计金额');
			$table->softDeletes();
			$table->dropColumn('tax');
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

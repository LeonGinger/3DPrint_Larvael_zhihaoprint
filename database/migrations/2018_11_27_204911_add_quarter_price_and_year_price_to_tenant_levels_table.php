<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuarterPriceAndYearPriceToTenantLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_levels', function (Blueprint $table) {
            $table->decimal('quarter_price')->after('price')->default(0)->comment('季度付费金额');
            $table->decimal('year_price')->after('quarter_price')->default(0)->comment('年度付费金额');
            $table->dropColumn('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenant_levels', function (Blueprint $table) {
            $table->dropColumn('quarter_price', 'year_price');
        });
    }
}

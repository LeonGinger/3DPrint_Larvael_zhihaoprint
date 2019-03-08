<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'tenants';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->string('no')->comment('编号');
            $table->string('name')->comment('名称');
            $table->string('linkman')->comment('联系人');
            $table->string('phone')->comment('联系电话');
            $table->timestamp('expired_at')->comment('过期时间');
            $table->tinyInteger('level')->comment('等级');
            $table->tinyInteger('status')->comment('状态');
            $table->tinyInteger('weapp_openid')->comment('微信OPENID');
            $table->timestamps();
        });
        add_table_comment($table_name, '商家');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenats');
    }
}

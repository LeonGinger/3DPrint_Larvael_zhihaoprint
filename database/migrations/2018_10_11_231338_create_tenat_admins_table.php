<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenatAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'tenat_admins';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tenat_id')->index()->comment('所属商户ID');
            $table->string('account')->comment('账号');
            $table->string('password')->comment('密码');
            $table->string('openid')->comment('微信OPENID');
            $table->timestamp('last_login_at')->comment('最后登录日期');
            $table->string('action_log')->nullable()->comment('操作日志');
            $table->timestamps();
        });
        add_table_comment($table_name, '商家管理员');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenat_admins');
    }
}

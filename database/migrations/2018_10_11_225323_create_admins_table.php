<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table_name = 'admins';
        Schema::create($table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('名称');
            $table->string('password')->comment('密码');
            $table->timestamp('last_login_at')->comment('最后登录日期');
            $table->string('action_log')->nullable()->comment('操作日志');
            $table->timestamps();
        });
        add_table_comment($table_name, '管理员');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}

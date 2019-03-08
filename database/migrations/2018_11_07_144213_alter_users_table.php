<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('users', function (Blueprint $table) {
			$table->integer('dep_ids')->comment('部门id');
			$table->integer('pr_id')->comment('权限id');
			$table->string('name',100)->comment('用户名称');
			$table->string('password',100)->comment('密码');
			$table->string('sale',10)->comment('盐');
			$table->timestamp('last_login_at')->comment('最后登录时间');
			$table->string('action_log')->comment('操作日志');
			$table->softDeletesTz();
			$table->timestamps();
		});
        //
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

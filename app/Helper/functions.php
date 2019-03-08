<?php

if(!function_exists('add_table_comment')) {
    /**
     * 给数据表添加注释
     * @param string $table_name 数据表名称
     * @param string $comment 注释
     */
    function add_table_comment($table_name, $comment) {
        DB::statement("ALTER TABLE `{$table_name}` comment '{$comment}'");
    }
}
